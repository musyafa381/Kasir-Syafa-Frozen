<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\EmployeeLoan;
use App\Models\Expense;
use App\Models\Payroll;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeePayrollTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_employee_index_as_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $response = $this->actingAs($admin)->get(route('admin.employees.index'));
        $response->assertStatus(200);
        $response->assertSee('Karyawan & Staf');
    }

    public function test_can_view_employee_detail_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $employee = Employee::create([
            'employee_code' => 'EMP-TEST',
            'name' => 'Karyawan Uji Coba',
            'position' => 'Staff',
            'employment_status' => 'tetap',
            'base_salary' => 3000000,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.employees.show', $employee->id));
        $response->assertStatus(200);
        $response->assertSee('Karyawan Uji Coba');
    }

    public function test_can_view_create_and_edit_employee_pages(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $employee = Employee::create([
            'employee_code' => 'EMP-TEST3',
            'name' => 'Karyawan Edit Test',
            'position' => 'Staff',
            'employment_status' => 'tetap',
            'base_salary' => 2700000,
            'is_active' => true,
        ]);

        // 1. Page Create
        $responseCreate = $this->actingAs($admin)->get(route('admin.employees.create'));
        $responseCreate->assertStatus(200);
        $responseCreate->assertSee('Tambah Karyawan Baru');

        // 2. Page Edit
        $responseEdit = $this->actingAs($admin)->get(route('admin.employees.edit', $employee->id));
        $responseEdit->assertStatus(200);
        $responseEdit->assertSee('Karyawan Edit Test');
    }

    public function test_payroll_processes_loan_deduction_and_creates_opex_expense(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $employee = Employee::create([
            'employee_code' => 'EMP-TEST2',
            'name' => 'Budi Santoso',
            'position' => 'Kasir',
            'employment_status' => 'tetap',
            'base_salary' => 2500000,
            'is_active' => true,
        ]);

        // 1. Berikan Kasbon 500.000
        $loan = EmployeeLoan::create([
            'employee_id' => $employee->id,
            'loan_number' => EmployeeLoan::generateLoanNumber(),
            'loan_date' => now()->format('Y-m-d'),
            'amount' => 500000,
            'paid_amount' => 0,
            'remaining_amount' => 500000,
            'status' => 'unpaid',
            'reason' => 'Pinjaman darurat',
            'created_by' => $admin->id,
        ]);

        $this->assertEquals(500000, $employee->fresh()->total_remaining_loan);

        // 2. Test Livewire Payroll Form (Create)
        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Livewire\Admin\Employee\EmployeePayrollForm::class, ['employeeId' => $employee->id])
            ->set('payroll_base_salary', 2500000)
            ->set('allowance_amount', 100000)
            ->set('loan_deduction', 200000) // potong kasbon 200rb
            ->set('other_deduction', 0)
            ->set('period_month', 9)
            ->set('period_year', 2026)
            ->set('payment_date', '2026-09-18')
            ->set('payroll_payment_method', 'transfer')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.employees.show', $employee->id));

        // 3. Verifikasi Payroll Record
        $payroll = Payroll::where('employee_id', $employee->id)->first();
        $this->assertNotNull($payroll);
        $this->assertEquals(2400000, $payroll->net_salary); // (2.500.000 + 100.000) - 200.000 = 2.400.000

        // 4. Verifikasi Kasbon terpotong
        $loanFresh = $loan->fresh();
        $this->assertEquals(200000, $loanFresh->paid_amount);
        $this->assertEquals(300000, $loanFresh->remaining_amount);
        $this->assertEquals('partial', $loanFresh->status);
        $this->assertEquals(300000, $employee->fresh()->total_remaining_loan);

        // 5. Verifikasi Otomatis Masuk ke Beban OPEX
        $expense = Expense::where('id', $payroll->expense_id)->first();
        $this->assertNotNull($expense);
        $this->assertEquals('salary_labor', $expense->category);
        $this->assertEquals(2400000, $expense->amount);
        $this->assertStringContainsString('Budi Santoso', $expense->title);

        // 6. Test Edit Slip Gaji via Payroll Form
        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Livewire\Admin\Employee\EmployeePayrollForm::class, [
                'employeeId' => $employee->id,
                'payrollId' => $payroll->id,
            ])
            ->set('allowance_amount', 200000) // bonus bertambah 100rb
            ->call('save')
            ->assertHasNoErrors();

        $this->assertEquals(2500000, $payroll->fresh()->net_salary);
        $this->assertEquals(2500000, $expense->fresh()->amount);

        // 7. Test Cetak Slip Gaji Page
        $responsePrint = $this->actingAs($admin)->get(route('admin.employees.payroll.print', [
            'employeeId' => $employee->id,
            'payrollId' => $payroll->id,
        ]));
        $responsePrint->assertStatus(200);
        $responsePrint->assertSee($payroll->payroll_number);
    }

    public function test_can_edit_and_pay_employee_loan(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $employee = Employee::create([
            'employee_code' => 'EMP-TEST4',
            'name' => 'Siti Rahma',
            'position' => 'Staff',
            'employment_status' => 'tetap',
            'base_salary' => 2800000,
            'is_active' => true,
        ]);

        // Catat kasbon
        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Livewire\Admin\Employee\EmployeeDetail::class, ['id' => $employee->id])
            ->set('loan_amount', 300000)
            ->set('loan_date', '2026-09-18')
            ->set('loan_reason', 'Pinjaman darurat')
            ->call('saveLoan')
            ->assertHasNoErrors();

        $loan = EmployeeLoan::where('employee_id', $employee->id)->first();
        $this->assertNotNull($loan);
        $this->assertEquals(300000, $loan->amount);

        // Edit kasbon
        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Livewire\Admin\Employee\EmployeeDetail::class, ['id' => $employee->id])
            ->call('openEditLoanModal', $loan->id)
            ->set('edit_loan_amount', 400000)
            ->set('edit_loan_reason', 'Pinjaman darurat berobat')
            ->call('saveEditLoan')
            ->assertHasNoErrors();

        $this->assertEquals(400000, $loan->fresh()->amount);
        $this->assertEquals(400000, $loan->fresh()->remaining_amount);

        // Bayar tunai kasbon
        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Livewire\Admin\Employee\EmployeeDetail::class, ['id' => $employee->id])
            ->call('openPayLoanModal', $loan->id)
            ->set('pay_loan_amount', 400000)
            ->set('pay_loan_date', '2026-09-18')
            ->call('savePayLoan')
            ->assertHasNoErrors();

        $this->assertEquals(0, $loan->fresh()->remaining_amount);
        $this->assertEquals('paid', $loan->fresh()->status);
    }
}
