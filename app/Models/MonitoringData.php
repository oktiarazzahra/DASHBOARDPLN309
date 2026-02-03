<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoringData extends Model
{
    use HasFactory;

    protected $table = 'monitoring_data';

    protected $fillable = [
        'location',
        'status',
        'voltage',
        'current',
        'power',
        'energy',
        'alert_type',
        'description',
        'recorded_at',
    ];

    protected $casts = [
        'voltage' => 'decimal:2',
        'current' => 'decimal:2',
        'power' => 'decimal:2',
        'energy' => 'decimal:2',
        'recorded_at' => 'datetime',
    ];

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk filter berdasarkan lokasi
     */
    public function scopeByLocation($query, $location)
    {
        return $query->where('location', $location);
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('recorded_at', [$startDate, $endDate]);
    }
}
