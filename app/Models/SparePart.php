<?php

namespace App\Models;

use Database\Factories\SparePartFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SparePart extends Model
{
    /** @use HasFactory<SparePartFactory> */
    use HasFactory;

    protected $fillable = [
        'category_id',
        'part_code',
        'name',
        'part_number',
        'stock_quantity',
        'min_stock_alert',
        'unit',
        'unit_cost',
        'location_bin',
        'compatibility_notes',
        'sop_replacement_ref',
    ];

    protected function casts(): array
    {
        return [
            'stock_quantity' => 'integer',
            'min_stock_alert' => 'integer',
            'unit_cost' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(DcCategory::class, 'category_id');
    }

    public function usages(): HasMany
    {
        return $this->hasMany(SparePartUsage::class, 'spare_part_id');
    }

    public function scopeCriticalStock(Builder $query): Builder
    {
        return $query->whereColumn('stock_quantity', '<=', 'min_stock_alert');
    }

    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->min_stock_alert;
    }
}
