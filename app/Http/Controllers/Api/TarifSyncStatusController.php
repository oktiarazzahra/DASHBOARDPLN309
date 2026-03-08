<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TarifSyncStatusController extends Controller
{
    /**
     * Get last sync status and check if tarif data changed
     */
    public function status(Request $request)
    {
        $year = $request->get('year', 2025);
        $clientLastUpdate = $request->get('last_update', null);
        
        // Get latest update timestamps from each tarif table
        $customerLastUpdate = DB::table('tarif_customer_data')
            ->where('year', $year)
            ->latest('updated_at')
            ->first()?->updated_at;
            
        $powerLastUpdate = DB::table('tarif_power_data')
            ->where('year', $year)
            ->latest('updated_at')
            ->first()?->updated_at;
            
        $revenueLastUpdate = DB::table('tarif_revenue_data')
            ->where('year', $year)
            ->latest('updated_at')
            ->first()?->updated_at;
        
        // Get the most recent update time
        $latestUpdate = collect([
            $customerLastUpdate,
            $powerLastUpdate,
            $revenueLastUpdate
        ])->filter()->max();
        
        $latestUpdateTimestamp = $latestUpdate ? strtotime($latestUpdate) : null;
        $hasChanges = false;
        
        // Check if there are changes since client's last update
        if ($clientLastUpdate && $latestUpdateTimestamp) {
            $hasChanges = $latestUpdateTimestamp > (int)$clientLastUpdate;
        }
        
        return response()->json([
            'success' => true,
            'has_changes' => $hasChanges,
            'last_update' => $latestUpdateTimestamp,
            'last_update_readable' => $latestUpdate ? \Carbon\Carbon::parse($latestUpdate)->diffForHumans() : 'Never',
            'year' => $year,
            'data_counts' => [
                'customers' => DB::table('tarif_customer_data')->where('year', $year)->count(),
                'power' => DB::table('tarif_power_data')->where('year', $year)->count(),
                'revenue' => DB::table('tarif_revenue_data')->where('year', $year)->count(),
            ]
        ])
        ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
    }
    
    /**
     * Trigger manual sync untuk tarif data (termasuk tarif per ULP)
     */
    public function triggerSync(Request $request)
    {
        $year = $request->get('year', 2025);
        
        try {
            // Run the tarif sync command (data per tarif)
            \Artisan::call('sync:tarif', ['--year' => $year]);
            
            // Run the tarif per ULP sync command
            \Artisan::call('sync:tarif-ulp', ['--year' => $year]);
            
            return response()->json([
                'success' => true,
                'message' => 'Tarif and Tarif ULP sync triggered successfully',
                'year' => $year
            ])
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage()
            ], 500)
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
        }
    }
}
