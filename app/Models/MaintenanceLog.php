<?php

namespace App\Models;

use Database\Factories\MaintenanceLogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaintenanceLog extends Model
{
    /** @use HasFactory<MaintenanceLogFactory> */
    use HasFactory;

    protected $fillable = [
        'component_id',
        'schedule_id',
        'title',
        'performed_at',
        'technician_name',
        'action_type',
        'actions_taken',
        'findings',
        'health_before',
        'health_after',
        'total_cost',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'performed_at' => 'datetime',
            'health_before' => 'integer',
            'health_after' => 'integer',
            'total_cost' => 'decimal:2',
        ];
    }

    public function component(): BelongsTo
    {
        return $this->belongsTo(DcComponent::class, 'component_id');
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(MaintenanceSchedule::class, 'schedule_id');
    }

    public function sparePartUsages(): HasMany
    {
        return $this->hasMany(SparePartUsage::class, 'maintenance_log_id');
    }
}
