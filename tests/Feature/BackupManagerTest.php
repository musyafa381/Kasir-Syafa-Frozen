<?php

namespace Tests\Feature;

use App\Livewire\Admin\Backup\BackupManager;
use App\Models\BackupLog;
use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class BackupManagerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $cashier;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->cashier = User::factory()->create([
            'role' => 'cashier',
            'is_active' => true,
        ]);

        $category = Category::create([
            'name' => 'Sosis & Bakso',
            'slug' => 'sosis-bakso',
        ]);

        $this->product = Product::create([
            'category_id' => $category->id,
            'name' => 'Sosis Sapi Jumbo',
            'code' => 'SS-JUMBO',
            'cost_price' => 25000,
            'selling_price' => 35000,
            'stock' => 50,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_access_backup_page(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.backup.index'))
            ->assertStatus(200)
            ->assertSee('Backup Data Transaksi & Google Drive')
            ->assertSee('Buat Arsip Backup Transaksi Baru');
    }

    public function test_cashier_cannot_access_backup_page(): void
    {
        $this->actingAs($this->cashier)
            ->get(route('admin.backup.index'))
            ->assertRedirect(route('pos.index'));
    }

    public function test_admin_can_generate_monthly_transaction_backup_excel(): void
    {
        // 1. Create completed transaction in current month
        $trx = Transaction::create([
            'invoice_number' => 'INV-20260918-0001',
            'user_id' => $this->cashier->id,
            'status' => 'completed',
            'subtotal' => 70000,
            'discount_amount' => 5000,
            'tax_amount' => 0,
            'grand_total' => 65000,
            'paid_amount' => 100000,
            'change_amount' => 35000,
            'payment_status' => 'paid',
            'payment_method' => 'cash',
            'created_at' => now(),
        ]);

        TransactionItem::create([
            'transaction_id' => $trx->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'quantity' => 2,
            'price' => 35000,
            'cost_price' => 25000,
            'subtotal' => 70000,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(BackupManager::class)
            ->set('backupMode', 'monthly')
            ->set('selectedMonth', (int) now()->format('m'))
            ->set('selectedYear', (int) now()->format('Y'))
            ->set('backupFormat', 'excel')
            ->call('generateBackup')
            ->assertStatus(200);

        // Verify BackupLog was created
        $this->assertDatabaseHas('backup_logs', [
            'total_transactions' => 1,
            'total_amount' => 65000,
            'backup_format' => 'excel',
        ]);

        $log = BackupLog::where('backup_format', 'excel')->first();
        $this->assertNotNull($log);
        Storage::disk('local')->assertExists($log->file_path);
        $this->assertStringContainsString('SYAFA FROZEN FOOD', Storage::disk('local')->get($log->file_path));
    }

    public function test_admin_can_generate_monthly_transaction_backup_csv(): void
    {
        // 1. Create completed transaction in current month
        $trx = Transaction::create([
            'invoice_number' => 'INV-20260918-0001',
            'user_id' => $this->cashier->id,
            'status' => 'completed',
            'subtotal' => 70000,
            'discount_amount' => 5000,
            'tax_amount' => 0,
            'grand_total' => 65000,
            'paid_amount' => 100000,
            'change_amount' => 35000,
            'payment_status' => 'paid',
            'payment_method' => 'cash',
            'created_at' => now(),
        ]);

        TransactionItem::create([
            'transaction_id' => $trx->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'quantity' => 2,
            'price' => 35000,
            'cost_price' => 25000,
            'subtotal' => 70000,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(BackupManager::class)
            ->set('backupMode', 'monthly')
            ->set('selectedMonth', (int) now()->format('m'))
            ->set('selectedYear', (int) now()->format('Y'))
            ->set('backupFormat', 'csv')
            ->call('generateBackup')
            ->assertStatus(200);

        // Verify BackupLog was created
        $this->assertDatabaseHas('backup_logs', [
            'total_transactions' => 1,
            'total_amount' => 65000,
            'backup_format' => 'csv',
        ]);

        $log = BackupLog::where('backup_format', 'csv')->first();
        $this->assertNotNull($log);
        Storage::disk('local')->assertExists($log->file_path);
    }

    public function test_admin_can_generate_transaction_backup_json(): void
    {
        // 1. Create completed transaction
        $trx = Transaction::create([
            'invoice_number' => 'INV-20260918-0002',
            'user_id' => $this->cashier->id,
            'status' => 'completed',
            'subtotal' => 35000,
            'discount_amount' => 0,
            'tax_amount' => 0,
            'grand_total' => 35000,
            'paid_amount' => 35000,
            'change_amount' => 0,
            'payment_status' => 'paid',
            'payment_method' => 'qris',
            'created_at' => now(),
        ]);

        TransactionItem::create([
            'transaction_id' => $trx->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'quantity' => 1,
            'price' => 35000,
            'cost_price' => 25000,
            'subtotal' => 35000,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(BackupManager::class)
            ->set('backupMode', 'custom')
            ->set('startDate', now()->subDays(1)->toDateString())
            ->set('endDate', now()->toDateString())
            ->set('backupFormat', 'json')
            ->call('generateBackup')
            ->assertStatus(200);

        $this->assertDatabaseHas('backup_logs', [
            'total_transactions' => 1,
            'backup_format' => 'json',
        ]);

        $log = BackupLog::where('backup_format', 'json')->first();
        $this->assertNotNull($log);
        Storage::disk('local')->assertExists($log->file_path);
    }

    public function test_admin_can_download_and_delete_backup_log(): void
    {
        $this->actingAs($this->admin);

        // Fake file and log
        $filePath = 'backups/test_backup.csv';
        Storage::disk('local')->put($filePath, 'fake-csv-content');

        $log = BackupLog::create([
            'filename' => 'test_backup.csv',
            'file_path' => $filePath,
            'file_size' => 100,
            'period_label' => 'September 2026',
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->endOfMonth()->toDateString(),
            'total_transactions' => 5,
            'total_amount' => 150000,
            'backup_format' => 'csv',
            'user_id' => $this->admin->id,
        ]);

        // Test download
        Livewire::test(BackupManager::class)
            ->call('downloadBackup', $log->id)
            ->assertFileDownloaded('test_backup.csv');

        // Test delete
        Livewire::test(BackupManager::class)
            ->call('deleteBackup', $log->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('backup_logs', ['id' => $log->id]);
        Storage::disk('local')->assertMissing($filePath);
    }
}
