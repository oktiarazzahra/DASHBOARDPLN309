<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerData;
use App\Models\PowerData;
use App\Models\RevenueData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SyncStatusController extends Controller
{
    /**
     * Get last sync status and check if data changed
     */
    public function status(Request $request)
    {
        $year = $request->get('year', 2025);
        $clientLastUpdate = $request->get('last_update', null);
        
        // Get latest update timestamps from each table
        $customerLastUpdate = CustomerData::byYear($year)->latest('updated_at')->first()?->updated_at;
        $powerLastUpdate = PowerData::byYear($year)->latest('updated_at')->first()?->updated_at;
        $revenueLastUpdate = RevenueData::byYear($year)->latest('updated_at')->first()?->updated_at;
        
        // Get the most recent update time
        $latestUpdate = collect([
            $customerLastUpdate,
            $powerLastUpdate,
            $revenueLastUpdate
        ])->filter()->max();
        
        $latestUpdateTimestamp = $latestUpdate ? $latestUpdate->timestamp : null;
        $hasChanges = false;
        
        // Check if there are changes since client's last update
        if ($clientLastUpdate && $latestUpdateTimestamp) {
            $hasChanges = $latestUpdateTimestamp > (int)$clientLastUpdate;
        }
        
        return response()->json([
            'success' => true,
            'has_changes' => $hasChanges,
            'last_update' => $latestUpdateTimestamp,
            'last_update_readable' => $latestUpdate?->diffForHumans() ?? 'Never',
            'year' => $year,
            'data_counts' => [
                'customers' => CustomerData::byYear($year)->count(),
                'power' => PowerData::byYear($year)->count(),
                'revenue' => RevenueData::byYear($year)->count(),
            ]
        ])
        ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
    }
    
    /**
     * Trigger manual sync data ULP (untuk testing atau force sync)
     */
    public function triggerSync(Request $request)
    {
        $year = $request->get('year', 2025);
        
        try {
            // Clear any cache before sync
            \Cache::flush();
            
            // Get count before sync
            $beforeCustomer = CustomerData::byYear($year)->count();
            $beforePower = PowerData::byYear($year)->count();
            $beforeRevenue = RevenueData::byYear($year)->count();
            
            // Run sync command and capture output
            \Artisan::call('data:auto-sync', ['--year' => $year]);
            $output = \Artisan::output();
            
            // Get count after sync
            $afterCustomer = CustomerData::byYear($year)->count();
            $afterPower = PowerData::byYear($year)->count();
            $afterRevenue = RevenueData::byYear($year)->count();
            
            // Get latest update time
            $latestUpdate = CustomerData::byYear($year)->latest('updated_at')->first();
            
            return response()->json([
                'success' => true,
                'message' => 'ULP data sync completed',
                'year' => $year,
                'timestamp' => now()->toDateTimeString(),
                'before' => [
                    'customer' => $beforeCustomer,
                    'power' => $beforePower,
                    'revenue' => $beforeRevenue,
                ],
                'after' => [
                    'customer' => $afterCustomer,
                    'power' => $afterPower,
                    'revenue' => $afterRevenue,
                ],
                'synced' => [
                    'customer' => $afterCustomer - $beforeCustomer,
                    'power' => $afterPower - $beforePower,
                    'revenue' => $afterRevenue - $beforeRevenue,
                ],
                'latest_update' => $latestUpdate?->updated_at?->toDateTimeString(),
                'output' => $output
            ])
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage(),
                'error_details' => $e->getTraceAsString()
            ], 500)
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
        }
    }
}
