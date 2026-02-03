<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PowerData extends Model
{
    protected $table = 'power_data';

    protected $fillable = [
        'ulp_code',
        'ulp_name',
        'month',
        'year',
        'data_type',
        'power_va',
    ];

    protected $casts = [
        'power_va' => 'integer',
        'year' => 'integer',
    ];

    /**
     * Scope: Filter by ULP code
     */
    public function scopeByUlp($query, $ulpCode)
    {
        return $query->where('ulp_code', $ulpCode);
    }

    /**
     * Scope: Filter by year
     */
    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Scope: Filter by data type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('data_type', $type);
    }

    /**
     * Scope: Get only bulanan data
     */
    public function scopeBulanan($query)
    {
        return $query->where('data_type', 'bulanan');
    }

    /**
     * Scope: Get only kumulatif data
     */
    public function scopeKumulatif($query)
    {
        return $query->where('data_type', 'kumulatif');
    }
}
