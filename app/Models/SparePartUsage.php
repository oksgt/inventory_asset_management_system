<?php

namespace App\Models;

use Database\Factories\SparePartUsageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SparePartUsage extends Model
{
    /** @use HasFactory<SparePartUsageFactory> */
    use HasFactory;

    protected $fillable = [
        'maintenance_log_id',
        'spare_part_id',
        'quantity_used',
        'unit_cost',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity_used' => 'integer',
            'unit_cost' => 'decimal:2',
        ];
    }

    public function maintenanceLog(): BelongsTo
    {
        return $this->belongsTo(MaintenanceLog::class, 'maintenance_log_id');
    }

    public function sparePart(): BelongsTo
    {
        return $this->belongsTo(SparePart::class, 'spare_part_id');
    }
}
