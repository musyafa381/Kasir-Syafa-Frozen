<?php

use App\Livewire\Admin\Backup\BackupManager;
use App\Livewire\Admin\CashierShift\CashierShiftIndex;
use App\Livewire\Admin\Category\CategoryDetail;
use App\Livewire\Admin\Category\CategoryIndex;
use App\Livewire\Admin\Customer\CustomerIndex;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Discount\DiscountIndex;
use App\Livewire\Admin\Discount\DiscountPosterPrint;
use App\Livewire\Admin\Employee\EmployeeDetail;
use App\Livewire\Admin\Employee\EmployeeForm;
use App\Livewire\Admin\Employee\EmployeeIndex;
use App\Livewire\Admin\Employee\EmployeePayrollForm;
use App\Livewire\Admin\Employee\EmployeePayrollPrint;
use App\Livewire\Admin\Finance\AssetIndex;
use App\Livewire\Admin\Finance\ExpenseIndex;
use App\Livewire\Admin\Finance\FinancialAiAdvisor;
use App\Livewire\Admin\Product\ProductForm;
use App\Livewire\Admin\Product\ProductIndex;
use App\Livewire\Admin\Report\AccountsPayableReport;
use App\Livewire\Admin\Report\InventoryValuationReport;
use App\Livewire\Admin\Report\ProfitLossReport;
use App\Livewire\Admin\Report\SalesReport;
use App\Livewire\Admin\Stock\StockAdjustment;
use App\Livewire\Admin\Tax\TaxIndex;
use App\Livewire\Admin\Purchase\PurchaseCreate;
use App\Livewire\Admin\Purchase\PurchaseIndex;
use App\Livewire\Admin\Purchase\RestockPlanner;
use App\Livewire\Admin\Supplier\SupplierIndex;
use App\Livewire\Admin\Transaction\ReceivableIndex;
use App\Livewire\Admin\Transaction\SaleReturnIndex;
use App\Livewire\Admin\Transaction\TransactionIndex;
use App\Livewire\Admin\User\UserIndex;
use App\Livewire\Auth\Login;
use App\Livewire\Pos\PosTerminal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Redirect Home
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('login');
});

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

// Logout Route
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->middleware('auth')->name('logout');

// Authenticated Routes (Semua yang login bisa akses POS)
Route::middleware(['auth'])->group(function () {
    Route::get('/pos', PosTerminal::class)->name('pos.index');
});

// ============================================================
// Shared Routes: Admin + Kasir
// Menu: Dashboard, Promo/Pajak/Member, Produk/Persediaan, Pemasok/Pembelian
// ============================================================
Route::middleware(['auth', 'role:admin,cashier'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Promo, Pajak & Member
    Route::get('/customers', CustomerIndex::class)->name('customers.index');
    Route::get('/discounts', DiscountIndex::class)->name('discounts.index');
    Route::get('/discounts/{id}/poster', DiscountPosterPrint::class)->name('discounts.poster');
    Route::get('/taxes', TaxIndex::class)->name('taxes.index');

    // Produk & Persediaan
    Route::get('/products', ProductIndex::class)->name('products.index');
    Route::get('/products/create', ProductForm::class)->name('products.create');
    Route::get('/products/{id}/edit', ProductForm::class)->name('products.edit');
    Route::get('/categories', CategoryIndex::class)->name('categories.index');
    Route::get('/categories/{category:slug}', CategoryDetail::class)->name('categories.show');
    Route::get('/stock', StockAdjustment::class)->name('stock.index');
    Route::get('/reports/valuasi-stok', InventoryValuationReport::class)->name('reports.inventory-valuation');

    // Pemasok & Pembelian
    Route::get('/suppliers', SupplierIndex::class)->name('suppliers.index');
    Route::get('/purchases', PurchaseIndex::class)->name('purchases.index');
    Route::get('/purchases/create', PurchaseCreate::class)->name('purchases.create');
    Route::get('/purchases/restock-planner', RestockPlanner::class)->name('purchases.restock');
    Route::get('/reports/buku-hutang', AccountsPayableReport::class)->name('reports.accounts-payable');

    // Shared Print & Export Routes
    Route::prefix('print')->name('print.')->group(function () {
        Route::get('/invoices/{id}', [\App\Http\Controllers\Admin\ReportExportController::class, 'printFormalInvoice'])->name('invoice.formal');
        Route::get('/inventory-valuation', [\App\Http\Controllers\Admin\ReportExportController::class, 'printInventoryValuation'])->name('inventory-valuation.report');
        Route::get('/inventory-valuation/excel', [\App\Http\Controllers\Admin\ReportExportController::class, 'exportInventoryValuationExcel'])->name('inventory-valuation.excel');
        Route::get('/stock-opname-sheet', [\App\Http\Controllers\Admin\ReportExportController::class, 'printStockOpnameSheet'])->name('stock-opname.sheet');
        Route::get('/purchases/{id}/po', [\App\Http\Controllers\Admin\ReportExportController::class, 'printPurchaseOrder'])->name('purchases.po');
        Route::get('/accounts-payable', [\App\Http\Controllers\Admin\ReportExportController::class, 'printAccountsPayable'])->name('accounts-payable.report');
        Route::get('/accounts-payable/excel', [\App\Http\Controllers\Admin\ReportExportController::class, 'exportAccountsPayableExcel'])->name('accounts-payable.excel');
        Route::get('/products/import-template', [\App\Http\Controllers\Admin\ReportExportController::class, 'downloadProductTemplate'])->name('products.import-template');
    });
});

// ============================================================
// Admin-Only Routes (Restricted - Owner / Admin Only)
// Menu: Penjualan/Transaksi, Arus Keuangan, Pengaturan, Backup
// ============================================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Penjualan & Transaksi
    Route::get('/transactions', TransactionIndex::class)->name('transactions.index');
    Route::get('/shifts', CashierShiftIndex::class)->name('shifts.index');
    Route::get('/returns', SaleReturnIndex::class)->name('returns.index');
    Route::get('/receivables', ReceivableIndex::class)->name('receivables.index');
    Route::get('/reports', SalesReport::class)->name('reports.index');
    Route::get('/reports/laba-rugi', ProfitLossReport::class)->name('reports.profit-loss');

    // Arus Keuangan & Operasional
    Route::get('/expenses', ExpenseIndex::class)->name('expenses.index');
    Route::get('/assets', AssetIndex::class)->name('assets.index');
    Route::get('/finance/ai-advisor', FinancialAiAdvisor::class)->name('finance.ai-advisor');
    Route::get('/employees', EmployeeIndex::class)->name('employees.index');
    Route::get('/employees/create', EmployeeForm::class)->name('employees.create');
    Route::get('/employees/{id}', EmployeeDetail::class)->name('employees.show');
    Route::get('/employees/{id}/edit', EmployeeForm::class)->name('employees.edit');
    Route::get('/employees/{employeeId}/payroll/create', EmployeePayrollForm::class)->name('employees.payroll.create');
    Route::get('/employees/{employeeId}/payroll/{payrollId}/edit', EmployeePayrollForm::class)->name('employees.payroll.edit');
    Route::get('/employees/{employeeId}/payroll/{payrollId}/print', EmployeePayrollPrint::class)->name('employees.payroll.print');

    // Pengaturan & Backup
    Route::get('/outlets', \App\Livewire\Admin\Outlet\OutletIndex::class)->name('outlets.index');
    Route::get('/users', UserIndex::class)->name('users.index');
    Route::get('/settings/receipt', \App\Livewire\Admin\Setting\ReceiptSettingManager::class)->name('settings.receipt');
    Route::get('/settings/store', \App\Livewire\Admin\Setting\StoreInformation::class)->name('settings.store');
    Route::get('/backup', BackupManager::class)->name('backup.index');

    // Admin-Only Print & Export Routes
    Route::prefix('print')->name('print.')->group(function () {
        Route::get('/sales-report', [\App\Http\Controllers\Admin\ReportExportController::class, 'printSalesReport'])->name('sales.report');
        Route::get('/sales-report/excel', [\App\Http\Controllers\Admin\ReportExportController::class, 'exportSalesExcel'])->name('sales.excel');
        Route::get('/profit-loss', [\App\Http\Controllers\Admin\ReportExportController::class, 'printProfitLoss'])->name('profit-loss.report');
        Route::get('/profit-loss/excel', [\App\Http\Controllers\Admin\ReportExportController::class, 'exportProfitLossExcel'])->name('profit-loss.excel');
        Route::get('/receivables/{id}/invoice', [\App\Http\Controllers\Admin\ReportExportController::class, 'printReceivableInvoice'])->name('receivables.invoice');
        Route::get('/receivables/book', [\App\Http\Controllers\Admin\ReportExportController::class, 'printReceivableBook'])->name('receivables.book');
        Route::get('/receivables/excel', [\App\Http\Controllers\Admin\ReportExportController::class, 'exportReceivablesExcel'])->name('receivables.excel');
        Route::get('/payroll-summary', [\App\Http\Controllers\Admin\ReportExportController::class, 'printPayrollSummary'])->name('payroll.summary');
        Route::get('/payroll-summary/excel', [\App\Http\Controllers\Admin\ReportExportController::class, 'exportPayrollSummaryExcel'])->name('payroll.excel');
    });
});

