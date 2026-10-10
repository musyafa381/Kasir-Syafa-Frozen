<?php

namespace App\Livewire\Admin\Setting;

use App\Models\ReceiptSetting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Pengaturan Struk Kasir - Syafa Frozen Food')]
class ReceiptSettingManager extends Component
{
    public string $store_name = '';
    public ?string $store_address = '';
    public ?string $store_phone = '';
    public ?string $header_notes = '';
    public ?string $footer_notes = '';
    public ?string $footer_link = '';
    public bool $show_cashier_name = true;
    public bool $show_customer_name = false;
    public string $default_paper_size = '58mm';

    public string $preview_paper_size = '58mm';

    protected function rules(): array
    {
        return [
            'store_name' => 'required|string|max:100',
            'store_address' => 'nullable|string|max:500',
            'store_phone' => 'nullable|string|max:50',
            'header_notes' => 'nullable|string|max:500',
            'footer_notes' => 'nullable|string|max:500',
            'footer_link' => 'nullable|string|max:255',
            'show_cashier_name' => 'boolean',
            'show_customer_name' => 'boolean',
            'default_paper_size' => 'required|in:58mm,80mm',
        ];
    }

    protected $messages = [
        'store_name.required' => 'Nama toko wajib diisi.',
        'default_paper_size.required' => 'Ukuran kertas default wajib dipilih.',
    ];

    public function mount(): void
    {
        $settings = ReceiptSetting::getSettings();
        $this->store_name = $settings->store_name;
        $this->store_address = $settings->store_address;
        $this->store_phone = $settings->store_phone;
        $this->header_notes = $settings->header_notes;
        $this->footer_notes = $settings->footer_notes;
        $this->footer_link = $settings->footer_link;
        $this->show_cashier_name = (bool) $settings->show_cashier_name;
        $this->show_customer_name = (bool) $settings->show_customer_name;
        $this->default_paper_size = $settings->default_paper_size ?? '58mm';
        $this->preview_paper_size = $this->default_paper_size;
    }

    public function save(): void
    {
        $this->validate();

        $settings = ReceiptSetting::getSettings();
        $settings->update([
            'store_name' => $this->store_name,
            'store_address' => $this->store_address,
            'store_phone' => $this->store_phone,
            'header_notes' => $this->header_notes,
            'footer_notes' => $this->footer_notes,
            'footer_link' => $this->footer_link,
            'show_cashier_name' => $this->show_cashier_name,
            'show_customer_name' => $this->show_customer_name,
            'default_paper_size' => $this->default_paper_size,
        ]);

        $this->dispatch('swal:toast', [
            'type' => 'success',
            'title' => 'Pengaturan struk berhasil disimpan!'
        ]);
    }

    public function resetToDefaults(): void
    {
        $this->store_name = 'Syafa frozen food';
        $this->store_address = 'Jl. Piyungan - Ngoro Oro, Jatigrit, Srimartani, Kec. Piyungan, Kabupaten Bantul, Daerah Istimewa Yogyakarta 55792, Indonesia';
        $this->store_phone = '089649399694';
        $this->header_notes = '';
        $this->footer_notes = 'Terima kasih telah berbelanja!';
        $this->footer_link = 'kasirpintar.co.id/e-receipt/syafafrozen';
        $this->show_cashier_name = true;
        $this->show_customer_name = false;
        $this->default_paper_size = '58mm';
        $this->preview_paper_size = '58mm';

        $this->save();
    }

    public function render()
    {
        return view('livewire.admin.setting.receipt-setting-manager');
    }
}
