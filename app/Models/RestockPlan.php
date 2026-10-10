<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RestockPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_number',
        'supplier_id',
        'user_id',
        'purchase_id',
        'status', // draft, ordered, received, cancelled
        'total_estimated_cost',
        'notes',
        'ordered_at',
        'received_at',
    ];

    protected function casts(): array
    {
        return [
            'total_estimated_cost' => 'decimal:2',
            'ordered_at' => 'datetime',
            'received_at' => 'datetime',
        ];
    }

    public static function generatePlanNumber(): string
    {
        $today = Carbon::now()->format('Ymd');
        $prefix = "PO-PLAN-{$today}-";

        $lastPlan = self::where('plan_number', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastPlan) {
            $lastNumber = (int) substr($lastPlan->plan_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $newNumber;
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(RestockPlanItem::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', ['draft', 'ordered']);
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', 'draft');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->where('status', 'ordered');
    }

    public function scopeReceived(Builder $query): Builder
    {
        return $query->where('status', 'received');
    }

    /**
     * Generate WhatsApp order text URL
     */
    public function getWhatsappUrlAttribute(): ?string
    {
        if (!$this->supplier || !$this->supplier->whatsapp_number) {
            return null;
        }

        $phone = $this->supplier->whatsapp_number;
        $supplierName = $this->supplier->name;
        $contactPerson = $this->supplier->contact_person ? " (Up: {$this->supplier->contact_person})" : '';

        $message = "Halo *{$supplierName}*{$contactPerson},\n";
        $message .= "Kami dari *Syafa Frozen Food* ingin memesan stok produk:\n\n";
        $message .= "📋 *DAFTAR PESANAN RESTOCK*\n";
        $message .= "No. Ref: *{$this->plan_number}*\n";
        $message .= "Tanggal: " . ($this->created_at ? $this->created_at->format('d/m/Y H:i') : date('d/m/Y')) . "\n\n";

        $i = 1;
        foreach ($this->items as $item) {
            $message .= "{$i}. {$item->product_name}: *{$item->quantity_ordered} {$item->unit}*\n";
            $i++;
        }

        $totalItems = $this->items->count();
        $totalQty = $this->items->sum('quantity_ordered');
        $message .= "\nTotal: *{$totalItems} jenis produk* ({$totalQty} unit)\n";

        if (!empty($this->notes)) {
            $message .= "Catatan: _{$this->notes}_\n";
        }

        $message .= "\nMohon konfirmasi ketersediaan barang dan perkiraan pengirimannya ya kak. Terima kasih! 🙏";

        return "https://api.whatsapp.com/send?phone={$phone}&text=" . rawurlencode($message);
    }

    public function getWhatsappMessageAttribute(): string
    {
        $supplierName = $this->supplier ? $this->supplier->name : 'Supplier';
        $contactPerson = ($this->supplier && $this->supplier->contact_person) ? " (Up: {$this->supplier->contact_person})" : '';

        $message = "Halo *{$supplierName}*{$contactPerson},\n";
        $message .= "Kami dari *Syafa Frozen Food* ingin memesan stok produk:\n\n";
        $message .= "📋 *DAFTAR PESANAN RESTOCK*\n";
        $message .= "No. Ref: *{$this->plan_number}*\n";
        $message .= "Tanggal: " . ($this->created_at ? $this->created_at->format('d/m/Y H:i') : date('d/m/Y')) . "\n\n";

        $i = 1;
        foreach ($this->items as $item) {
            $message .= "{$i}. {$item->product_name}: *{$item->quantity_ordered} {$item->unit}*\n";
            $i++;
        }

        $totalItems = $this->items->count();
        $totalQty = $this->items->sum('quantity_ordered');
        $message .= "\nTotal: *{$totalItems} jenis produk* ({$totalQty} unit)\n";

        if (!empty($this->notes)) {
            $message .= "Catatan: _{$this->notes}_\n";
        }

        $message .= "\nMohon konfirmasi ketersediaan barang dan perkiraan pengirimannya ya kak. Terima kasih! 🙏";

        return $message;
    }
}
