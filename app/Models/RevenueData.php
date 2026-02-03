<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RevenueData extends Model
{
    protected $table = 'revenue_data';
    
    protected $fillable = [
        'ulp_code',
        'ulp_name',
        'month',
        'year',
        'data_type',
        'kwh_jual',
        'rp_pendapatan',
        'rp_per_kwh',
    ];

    protected $casts = [
        'kwh_jual' => 'integer',
        'rp_pendapatan' => 'integer',
        'rp_per_kwh' => 'decimal:2',
    ];

    // Scope untuk filter berdasarkan tahun
    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }

    // Scope untuk filter berdasarkan ULP
    public function scopeByUlp($query, $ulpCode)
    {
        return $query->where('ulp_code', $ulpCode);
    }

    // Scope untuk filter berdasarkan bulan
    public function scopeByMonth($query, $month)
    {
        return $query->where('month', $month);
    }

    // Scope untuk filter data bulanan
    public function scopeBulanan($query)
    {
        return $query->where('data_type', 'bulanan');
    }

    // Scope untuk filter data kumulatif
    public function scopeKumulatif($query)
    {
        return $query->where('data_type', 'kumulatif');
    }
}
