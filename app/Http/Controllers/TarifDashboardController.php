<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TarifDashboardController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('year', 2025);
        $month = $request->input('month', null);
        
        // Redirect jika tahun > 2025
        if ($year > 2025) {
            return redirect('/tarif?year=2025');
        }
        
        // DATA DETAIL PER TARIF
        $detailData = DB::table('tarif_customer_data')
            ->select(
                'tarif_code',
                'tarif_name',
                'tarif_category',
                DB::raw('SUM(total_customers) as customers')
            )
            ->where('year', $year)
            ->when($month !== null && $month !== '', function($q) use ($month) {
                return $q->where('month', $month);
            })
            ->groupBy('tarif_code', 'tarif_name', 'tarif_category')
            ->orderBy('tarif_category')
            ->orderBy('tarif_name')
            ->get();
        
        // Tambahkan data power, kwh, rp untuk setiap tarif
        foreach ($detailData as $tarif) {
            // Power
            $power = DB::table('tarif_power_data')
                ->where('year', $year)
                ->where('tarif_code', $tarif->tarif_code)
                ->when($month !== null && $month !== '', function($q) use ($month) {
                    return $q->where('month', $month);
                })
                ->sum('total_power');
            $tarif->power = $power;
            
            // kWh
            $kwh = DB::table('tarif_revenue_data')
                ->where('year', $year)
                ->where('data_type', 'kwh')
                ->where('tarif_code', $tarif->tarif_code)
                ->when($month !== null && $month !== '', function($q) use ($month) {
                    return $q->where('month', $month);
                })
                ->sum('value');
            $tarif->kwh = $kwh;
            
            // Rp
            $rp = DB::table('tarif_revenue_data')
                ->where('year', $year)
                ->where('data_type', 'rp')
                ->where('tarif_code', $tarif->tarif_code)
                ->when($month !== null && $month !== '', function($q) use ($month) {
                    return $q->where('month', $month);
                })
                ->sum('value');
            $tarif->rp = $rp;
            
            // Rp/kWh
            $tarif->rp_per_kwh = $kwh > 0 ? $rp / $kwh : 0;
        }
        
        return view('tarif.index', compact(
            'year',
            'month',
            'detailData'
        ));
    }
}
