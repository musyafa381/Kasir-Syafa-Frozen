<?php

namespace Tests\Feature;

use App\Livewire\Admin\Setting\ReceiptSettingManager;
use App\Models\ReceiptSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ReceiptSettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_receipt_settings_page(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.settings.receipt'));
        $response->assertStatus(200);
        $response->assertSee('Pengaturan Struk Kasir');
    }

    public function test_cashier_cannot_access_receipt_settings_page(): void
    {
        $cashier = User::factory()->create([
            'role' => 'cashier',
            'is_active' => true,
        ]);

        $response = $this->actingAs($cashier)->get(route('admin.settings.receipt'));
        $response->assertRedirect(route('pos.index'));
    }

    public function test_admin_can_update_receipt_settings(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(ReceiptSettingManager::class)
            ->set('store_name', 'Syafa Frozen Official')
            ->set('store_address', 'Jl. Baru No. 123, Yogyakarta')
            ->set('store_phone', '081234567890')
            ->set('header_notes', 'NIB: 123456789')
            ->set('footer_notes', 'Terima kasih atas kunjungan Anda.')
            ->set('footer_link', 'kasirpintar.co.id/e-receipt/syafafrozen')
            ->set('show_cashier_name', true)
            ->set('show_customer_name', true)
            ->set('default_paper_size', '80mm')
            ->call('save')
            ->assertDispatched('swal:toast');

        $setting = ReceiptSetting::first();
        $this->assertEquals('Syafa Frozen Official', $setting->store_name);
        $this->assertEquals('Jl. Baru No. 123, Yogyakarta', $setting->store_address);
        $this->assertEquals('081234567890', $setting->store_phone);
        $this->assertEquals('80mm', $setting->default_paper_size);
        $this->assertTrue((bool)$setting->show_customer_name);
    }
}
