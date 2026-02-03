<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerData extends Model
{
    use HasFactory;

    protected $table = 'customer_data';

    protected $fillable = [
        'ulp_code',
        'ulp_name',
        'month',
        'year',
        'data_type',
        'customer_count',
    ];

    protected $casts = [
        'customer_count' => 'integer',
        'year' => 'integer',
    ];

    /**
     * Scope untuk filter berdasarkan ULP
     */
    public function scopeByUlp($query, $ulpCode)
    {
        return $query->where('ulp_code', $ulpCode);
    }

    /**
     * Scope untuk filter berdasarkan tahun
     */
    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Scope untuk filter berdasarkan tipe data
     */
    public function scopeByType($query, $type)
    {
        return $query->where('data_type', $type);
    }

    /**
     * Scope untuk data bulanan
     */
    public function scopeBulanan($query)
    {
        return $query->where('data_type', 'bulanan');
    }

    /**
     * Scope untuk data kumulatif
     */
    public function scopeKumulatif($query)
    {
        return $query->where('data_type', 'kumulatif');
    }
}
