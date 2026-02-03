<?php

namespace App\Services;

use App\Models\MonitoringData;
use Revolution\Google\Sheets\Facades\Sheets;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GoogleSheetsService
{
    protected $spreadsheetId;
    protected $sheetName;

    public function __construct()
    {
        $this->spreadsheetId = config('google.sheets.spreadsheet_id');
        $this->sheetName = config('google.sheets.sheet_name', 'Sheet1');
    }

    /**
     * Ambil data dari Google Sheets
     */
    public function fetchData()
    {
        try {
            $sheets = Sheets::spreadsheet($this->spreadsheetId)
                ->sheet($this->sheetName)
                ->get();

            $header = $sheets->pull(0); // Baris pertama sebagai header
            $data = Sheets::collection($header, $sheets);

            return $data;
        } catch (\Exception $e) {
            Log::error('Error fetching Google Sheets data: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Sync data dari Google Sheets ke database
     */
    public function syncToDatabase()
    {
        try {
            $data = $this->fetchData();
            $syncedCount = 0;

            foreach ($data as $row) {
                // Sesuaikan dengan struktur spreadsheet Anda
                $monitoringData = [
                    'location' => $row['location'] ?? $row['lokasi'] ?? null,
                    'status' => $row['status'] ?? null,
                    'voltage' => $row['voltage'] ?? $row['tegangan'] ?? null,
                    'current' => $row['current'] ?? $row['arus'] ?? null,
                    'power' => $row['power'] ?? $row['daya'] ?? null,
                    'energy' => $row['energy'] ?? $row['energi'] ?? null,
                    'alert_type' => $row['alert_type'] ?? $row['tipe_alert'] ?? null,
                    'description' => $row['description'] ?? $row['deskripsi'] ?? null,
                    'recorded_at' => isset($row['recorded_at']) || isset($row['tanggal']) 
                        ? Carbon::parse($row['recorded_at'] ?? $row['tanggal']) 
                        : now(),
                ];

                // Update atau create data
                MonitoringData::updateOrCreate(
                    [
                        'location' => $monitoringData['location'],
                        'recorded_at' => $monitoringData['recorded_at'],
                    ],
                    $monitoringData
                );

                $syncedCount++;
            }

            Log::info("Synced {$syncedCount} records from Google Sheets");
            return $syncedCount;

        } catch (\Exception $e) {
            Log::error('Error syncing Google Sheets data: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Ambil statistik untuk dashboard
     */
    public function getStatistics()
    {
        return [
            'total_records' => MonitoringData::count(),
            'active_locations' => MonitoringData::distinct('location')->count('location'),
            'critical_alerts' => MonitoringData::where('alert_type', 'critical')->count(),
            'total_power' => MonitoringData::sum('power'),
            'total_energy' => MonitoringData::sum('energy'),
            'latest_sync' => MonitoringData::latest('updated_at')->first()?->updated_at,
        ];
    }

    /**
     * Ambil data terbaru
     */
    public function getRecentData($limit = 10)
    {
        return MonitoringData::latest('recorded_at')->take($limit)->get();
    }

    /**
     * Ambil data berdasarkan lokasi
     */
    public function getDataByLocation($location)
    {
        return MonitoringData::byLocation($location)
            ->latest('recorded_at')
            ->get();
    }

    /**
     * Ambil data berdasarkan status
     */
    public function getDataByStatus($status)
    {
        return MonitoringData::byStatus($status)
            ->latest('recorded_at')
            ->get();
    }
}
