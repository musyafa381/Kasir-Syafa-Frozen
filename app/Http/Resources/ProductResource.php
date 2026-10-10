<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Format output JSON produk yang konsisten untuk Flutter app.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'barcode' => $this->barcode,
            'description' => $this->description,
            'image' => $this->image ? asset('storage/' . $this->image) : null,
            'cost_price' => (float) $this->cost_price,
            'selling_price' => (float) $this->selling_price,
            'loyalty_price' => (float) $this->loyalty_price,
            'stock' => (int) $this->stock,
            'unit' => $this->unit,
            'is_low_stock' => $this->isLowStock(),
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                    'slug' => $this->category->slug,
                ];
            }),
        ];
    }
}
