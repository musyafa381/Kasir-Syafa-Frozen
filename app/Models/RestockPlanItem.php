<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestockPlanItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'restock_plan_id',
        'product_id',
        'product_name',
        'current_stock',
        'min_stock_alert',
        'quantity_ordered',
        'unit',
        'unit_cost',
        'subtotal',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'current_stock' => 'integer',
            'min_stock_alert' => 'integer',
            'quantity_ordered' => 'integer',
            'unit_cost' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function restockPlan(): BelongsTo
    {
        return $this->belongsTo(RestockPlan::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
