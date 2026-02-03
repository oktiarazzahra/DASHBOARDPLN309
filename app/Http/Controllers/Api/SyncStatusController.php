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
        ]);
    }
    
    /**
     * Trigger manual sync (untuk testing atau force sync)
     */
    public function triggerSync(Request $request)
    {
        $year = $request->get('year', 2025);
        
        try {
            // Queue the sync commands
            \Artisan::call('data:auto-sync', ['--year' => $year]);
            
            return response()->json([
                'success' => true,
                'message' => 'Sync triggered successfully',
                'year' => $year
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
