<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_name',
        'store_address',
        'store_phone',
        'header_notes',
        'footer_notes',
        'footer_link',
        'show_cashier_name',
        'show_customer_name',
        'default_paper_size',
    ];

    protected $casts = [
        'show_cashier_name' => 'boolean',
        'show_customer_name' => 'boolean',
    ];

    public static function getSettings(): self
    {
        return static::firstOrCreate(
            ['id' => 1],
            [
                'store_name' => 'Syafa frozen food',
                'store_address' => 'Jl. Piyungan - Ngoro Oro, Jatigrit, Srimartani, Kec. Piyungan, Kabupaten Bantul, Daerah Istimewa Yogyakarta 55792, Indonesia',
                'store_phone' => '089649399694',
                'header_notes' => '',
                'footer_notes' => 'Terima kasih telah berbelanja!',
                'footer_link' => 'kasirpintar.co.id/e-receipt/syafafrozen',
                'show_cashier_name' => true,
                'show_customer_name' => false,
                'default_paper_size' => '58mm',
            ]
        );
    }
}
