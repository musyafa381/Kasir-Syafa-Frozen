<?php

namespace App\Livewire\Admin\Setting;

use App\Models\Outlet;
use App\Models\ReceiptSetting;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\User;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Informasi Toko - Syafa Frozen Food')]
class StoreInformation extends Component
{
    public function render()
    {
        $receiptSetting = ReceiptSetting::getSettings();
        $mainOutlet = Outlet::where('is_main', true)->first() ?: Outlet::first();
        $outlets = Outlet::where('is_active', true)->get();
        
        $totalProducts = Product::count();
        $totalCustomers = Customer::count();
        $totalStaff = User::where('is_active', true)->count();
        $totalTransactions = Transaction::where('status', 'completed')->count();

        return view('livewire.admin.setting.store-information', [
            'settings' => $receiptSetting,
            'mainOutlet' => $mainOutlet,
            'outlets' => $outlets,
            'totalProducts' => $totalProducts,
            'totalCustomers' => $totalCustomers,
            'totalStaff' => $totalStaff,
            'totalTransactions' => $totalTransactions,
        ]);
    }
}
