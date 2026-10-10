<?php

namespace Tests\Feature;

use App\Models\Outlet;
use App\Models\OutletProduct;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiBranchPricingTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $cashier;
    protected Outlet $mainOutlet;
    protected Outlet $branchOutlet;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mainOutlet = Outlet::create([
            'code' => 'PST-01',
            'name' => 'Cabang Utama (Pusat)',
            'address' => 'Jl. Utama No. 1',
            'phone' => '08111111111',
            'is_main' => true,
            'is_active' => true,
        ]);

        $this->branchOutlet = Outlet::create([
            'code' => 'CBG-02',
            'name' => 'Cabang Timur',
            'address' => 'Jl. Timur No. 2',
            'phone' => '08222222222',
            'is_main' => false,
            'is_active' => true,
        ]);

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'outlet_id' => $this->mainOutlet->id,
            'is_active' => true,
        ]);

        $this->cashier = User::factory()->create([
            'role' => 'cashier',
            'outlet_id' => $this->branchOutlet->id,
            'is_active' => true,
        ]);

        // Product master price: 20.000 (Reguler), 18.000 (Member), Stock: 100
        $this->product = Product::create([
            'name' => 'Nugget Ayam Premium 500g',
            'slug' => 'nugget-ayam-premium-500g',
            'barcode' => '8991234567890',
            'cost_price' => 15000,
            'selling_price' => 20000,
            'loyalty_price' => 18000,
            'stock' => 100,
            'min_stock_alert' => 10,
            'unit' => 'pack',
            'is_active' => true,
        ]);

        // Main Outlet Product: Stock 60, Selling Price 20.000
        OutletProduct::create([
            'outlet_id' => $this->mainOutlet->id,
            'product_id' => $this->product->id,
            'selling_price' => 20000,
            'loyalty_price' => 18000,
            'cost_price' => 15000,
            'stock' => 60,
            'min_stock_alert' => 10,
        ]);

        // Branch Outlet Product: Custom higher price 22.000 (Reguler), 20.000 (Member), Stock: 30
        OutletProduct::create([
            'outlet_id' => $this->branchOutlet->id,
            'product_id' => $this->product->id,
            'selling_price' => 22000,
            'loyalty_price' => 20000,
            'cost_price' => 15000,
            'stock' => 30,
            'min_stock_alert' => 5,
        ]);
    }

    public function test_admin_can_access_outlet_management_page(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.outlets.index'))
            ->assertStatus(200)
            ->assertSee('Cabang Utama (Pusat)')
            ->assertSee('Cabang Timur');
    }

    public function test_product_returns_correct_branch_effective_price_and_stock(): void
    {
        // 1. Check default / Main Outlet
        $this->assertEquals(20000, $this->product->getEffectivePrice(false, $this->mainOutlet->id));
        $this->assertEquals(18000, $this->product->getEffectivePrice(true, $this->mainOutlet->id));
        $this->assertEquals(60, $this->product->getStockForOutlet($this->mainOutlet->id));

        // 2. Check Branch Outlet custom pricing and stock
        $this->assertEquals(22000, $this->product->getEffectivePrice(false, $this->branchOutlet->id));
        $this->assertEquals(20000, $this->product->getEffectivePrice(true, $this->branchOutlet->id));
        $this->assertEquals(30, $this->product->getStockForOutlet($this->branchOutlet->id));
    }

    public function test_pos_terminal_initializes_with_assigned_cashier_outlet(): void
    {
        $this->actingAs($this->cashier)
            ->get(route('pos.index'))
            ->assertStatus(200)
            ->assertSee('Cabang Timur');
    }
}
