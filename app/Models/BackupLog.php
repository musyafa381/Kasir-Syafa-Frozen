<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BackupLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'file_path',
        'file_size',
        'period_label',
        'start_date',
        'end_date',
        'total_transactions',
        'total_amount',
        'backup_format',
        'google_drive_link',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
            'total_transactions' => 'integer',
            'total_amount' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = (int) $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }
}
