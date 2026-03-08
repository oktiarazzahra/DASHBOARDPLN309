<?php

namespace App\Http\Controllers;

use App\Models\CustomerData;
use App\Models\PowerData;
use App\Models\RevenueData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class DebugSyncController extends Controller
{
    /**
     * Manual sync dengan output detail untuk debugging
     */
    public function sync(Request $request)
    {
        $year = (int)$request->get('year', 2026);
        
        // Get data BEFORE sync
        $beforeCustomer = CustomerData::where('year', $year)->count();
        $beforePower = PowerData::where('year', $year)->count();
        $beforeRevenue = RevenueData::where('year', $year)->count();
        $beforeLatest = CustomerData::where('year', $year)->latest('updated_at')->first();
        
        // Run sync and capture output
        Artisan::call('data:auto-sync', ['--year' => $year]);
        $output = Artisan::output();
        
        // Get data AFTER sync
        $afterCustomer = CustomerData::where('year', $year)->count();
        $afterPower = PowerData::where('year', $year)->count();
        $afterRevenue = RevenueData::where('year', $year)->count();
        $afterLatest = CustomerData::where('year', $year)->latest('updated_at')->first();
        
        // Get sample data
        $recentData = CustomerData::where('year', $year)
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();
        
        return response()
            ->view('debug.sync', compact(
                'year',
                'beforeCustomer',
                'beforePower',
                'beforeRevenue',
                'afterCustomer',
                'afterPower',
                'afterRevenue',
                'beforeLatest',
                'afterLatest',
                'output',
                'recentData'
            ))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}
