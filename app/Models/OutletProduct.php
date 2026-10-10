<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutletProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'outlet_id',
        'product_id',
        'selling_price',
        'loyalty_price',
        'box_selling_price',
        'box_loyalty_price',
        'cost_price',
        'stock',
        'min_stock_alert',
    ];

    protected function casts(): array
    {
        return [
            'selling_price' => 'decimal:2',
            'loyalty_price' => 'decimal:2',
            'box_selling_price' => 'decimal:2',
            'box_loyalty_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'stock' => 'integer',
            'min_stock_alert' => 'integer',
        ];
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
