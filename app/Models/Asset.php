<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_code',
        'user_id',
        'name',
        'category',
        'brand',
        'specs',
        'location',
        'purchase_date',
        'purchase_cost',
        'condition',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'purchase_cost' => 'decimal:2',
            'purchase_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeFreezers(Builder $query): Builder
    {
        return $query->where('category', 'freezer_refrigeration');
    }

    public function scopeActiveCondition(Builder $query): Builder
    {
        return $query->whereIn('condition', ['good', 'fair']);
    }

    public static function getCategoryLabel(string $category): string
    {
        return match ($category) {
            'freezer_refrigeration' => 'Mesin Freezer & Pendingin',
            'pos_hardware' => 'Hardware Kasir & Komputer',
            'operational_equipment' => 'Peralatan Operasional (Genset/Timbangan)',
            'furniture_facility' => 'Fasilitas & Rak Display Toko',
            default => 'Peralatan Lainnya',
        };
    }

    public static function getConditionLabel(string $condition): string
    {
        return match ($condition) {
            'good' => 'Berfungsi Sangat Baik',
            'fair' => 'Berfungsi Normal',
            'maintenance' => 'Sedang Dalam Servis / Perbaikan',
            'broken' => 'Rusak / Tidak Berfungsi',
            default => 'Normal',
        };
    }

    public static function generateAssetCode(): string
    {
        $prefix = 'AST-';
        $last = self::orderBy('id', 'desc')->first();

        if (!$last) {
            $number = 1;
        } else {
            $lastNumber = (int) filter_var($last->asset_code, FILTER_SANITIZE_NUMBER_INT);
            $number = $lastNumber + 1;
        }

        return $prefix . str_pad((string) $number, 4, '0', STR_PAD_LEFT);
    }
}
