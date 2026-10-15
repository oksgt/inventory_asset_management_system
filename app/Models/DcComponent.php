<?php

namespace App\Models;

use Database\Factories\DcComponentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class DcComponent extends Model
{
    /** @use HasFactory<DcComponentFactory> */
    use HasFactory;

    protected $fillable = [
        'category_id',
        'component_code',
        'name',
        'model',
        'serial_number',
        'rack_location',
        'rack_unit',
        'ip_address',
        'status',
        'condition_percent',
        'vendor_sla',
        'last_maintenance_at',
        'next_maintenance_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'condition_percent' => 'integer',
            'last_maintenance_at' => 'datetime',
            'next_maintenance_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(DcCategory::class, 'category_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(MaintenanceSchedule::class, 'component_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(MaintenanceLog::class, 'component_id')->latest('performed_at');
    }

    public function scopeOperational(Builder $query): Builder
    {
        return $query->where('status', 'operational');
    }

    public function scopeWarning(Builder $query): Builder
    {
        return $query->whereIn('status', ['warning', 'maintenance_required']);
    }

    /**
     * Memanggil MySQL Stored Function fn_calculate_component_health()
     */
    public function getCalculatedHealthFromSql(): int
    {
        $result = DB::selectOne('SELECT fn_calculate_component_health(?) AS health', [$this->id]);

        return (int) ($result->health ?? $this->condition_percent);
    }

    /**
     * Memanggil MySQL Stored Function fn_get_component_maintenance_cost()
     */
    public function getTotalCostFromSql(): float
    {
        $result = DB::selectOne('SELECT fn_get_component_maintenance_cost(?) AS total_cost', [$this->id]);

        return (float) ($result->total_cost ?? 0.0);
    }
}
