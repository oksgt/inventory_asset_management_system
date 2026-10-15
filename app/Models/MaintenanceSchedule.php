<?php

namespace App\Models;

use Database\Factories\MaintenanceScheduleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MaintenanceSchedule extends Model
{
    /** @use HasFactory<MaintenanceScheduleFactory> */
    use HasFactory;

    protected $fillable = [
        'component_id',
        'title',
        'maintenance_type',
        'priority',
        'scheduled_date',
        'sop_reference',
        'assigned_technician',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_date' => 'date',
        ];
    }

    public function component(): BelongsTo
    {
        return $this->belongsTo(DcComponent::class, 'component_id');
    }

    public function log(): HasOne
    {
        return $this->hasOne(MaintenanceLog::class, 'schedule_id');
    }
}
