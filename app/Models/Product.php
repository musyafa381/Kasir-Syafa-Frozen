<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'supplier_id',
        'name',
        'slug',
        'barcode',
        'box_barcode',
        'description',
        'image',
        'cost_price',
        'selling_price',
        'loyalty_price',
        'box_selling_price',
        'box_loyalty_price',
        'stock',
        'min_stock_alert',
        'unit',
        'pieces_per_box',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'cost_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'loyalty_price' => 'decimal:2',
            'box_selling_price' => 'decimal:2',
            'box_loyalty_price' => 'decimal:2',
            'stock' => 'integer',
            'min_stock_alert' => 'integer',
            'pieces_per_box' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name) . '-' . Str::random(4);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function transactionItems(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function stockMutations(): HasMany
    {
        return $this->hasMany(StockMutation::class);
    }

    public function outletProducts(): HasMany
    {
        return $this->hasMany(OutletProduct::class);
    }

    public function outlets(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Outlet::class, 'outlet_products')
            ->withPivot(['selling_price', 'loyalty_price', 'box_selling_price', 'box_loyalty_price', 'cost_price', 'stock', 'min_stock_alert'])
            ->withTimestamps();
    }

    public function getOutletProduct(?int $outletId = null): ?OutletProduct
    {
        if (!$outletId) {
            return null;
        }

        if ($this->relationLoaded('outletProducts')) {
            return $this->outletProducts->firstWhere('outlet_id', $outletId);
        }

        return $this->outletProducts()->where('outlet_id', $outletId)->first();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock(Builder $query): Builder
    {
        return $query->whereColumn('stock', '<=', 'min_stock_alert');
    }

    public function isLowStock(?int $outletId = null): bool
    {
        if ($outletId) {
            $op = $this->getOutletProduct($outletId);
            if ($op) {
                $alert = $op->min_stock_alert ?? $this->min_stock_alert;
                return $op->stock <= $alert;
            }
        }
        return $this->stock <= $this->min_stock_alert;
    }

    public function hasBoxOption(): bool
    {
        return !empty($this->pieces_per_box) && $this->pieces_per_box > 1 && !empty($this->box_selling_price) && $this->box_selling_price > 0;
    }

    public function getEffectivePrice(bool $isLoyalty = false, ?int $outletId = null): float
    {
        $op = $this->getOutletProduct($outletId);

        if ($op) {
            if ($isLoyalty && $op->loyalty_price && $op->loyalty_price > 0) {
                return (float) $op->loyalty_price;
            }
            if ($op->selling_price && $op->selling_price > 0) {
                return (float) $op->selling_price;
            }
        }

        if ($isLoyalty && $this->loyalty_price && $this->loyalty_price > 0) {
            return (float) $this->loyalty_price;
        }

        return (float) $this->selling_price;
    }

    public function getBoxEffectivePrice(bool $isLoyalty = false, ?int $outletId = null): float
    {
        $op = $this->getOutletProduct($outletId);

        if ($op) {
            if ($isLoyalty && $op->box_loyalty_price && $op->box_loyalty_price > 0) {
                return (float) $op->box_loyalty_price;
            }
            if ($op->box_selling_price && $op->box_selling_price > 0) {
                return (float) $op->box_selling_price;
            }
        }

        if ($isLoyalty && $this->box_loyalty_price && $this->box_loyalty_price > 0) {
            return (float) $this->box_loyalty_price;
        }

        if ($this->box_selling_price && $this->box_selling_price > 0) {
            return (float) $this->box_selling_price;
        }

        $pieces = $this->pieces_per_box ?: 1;
        return (float) ($this->getEffectivePrice($isLoyalty, $outletId) * $pieces);
    }

    public function getStockForOutlet(?int $outletId = null): int
    {
        if (!$outletId) {
            return (int) $this->stock;
        }

        $op = $this->getOutletProduct($outletId);
        return $op ? (int) $op->stock : (int) $this->stock;
    }
}
