<?php

namespace App\Models;

use Database\Factories\DcCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DcCategory extends Model
{
    /** @use HasFactory<DcCategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'icon',
        'maintenance_interval_days',
        'description',
    ];

    public function components(): HasMany
    {
        return $this->hasMany(DcComponent::class, 'category_id');
    }

    public function spareParts(): HasMany
    {
        return $this->hasMany(SparePart::class, 'category_id');
    }
}
