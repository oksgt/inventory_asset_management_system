<?php

namespace App\Models;

use Database\Factories\BackupLogFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BackupLog extends Model
{
    /** @use HasFactory<BackupLogFactory> */
    use HasFactory;

    protected $fillable = [
        'backup_name',
        'backup_type',
        'storage_location',
        'file_path',
        'file_size_bytes',
        'checksum_sha256',
        'is_encrypted',
        'encryption_cipher',
        'retention_days',
        'status',
        'tested_at',
        'test_result',
        'verified_by',
    ];

    protected function casts(): array
    {
        return [
            'file_size_bytes' => 'integer',
            'is_encrypted' => 'boolean',
            'retention_days' => 'integer',
            'tested_at' => 'datetime',
        ];
    }

    public function scopeSuccessful(Builder $query): Builder
    {
        return $query->whereIn('status', ['success', 'verified']);
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size_bytes;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2).' GB';
        }
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2).' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2).' KB';
        }

        return $bytes.' B';
    }
}
