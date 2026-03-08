<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Sync - Dashboard PLN 309</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .diff-green { background: #d4edda; color: #155724; font-weight: bold; }
        .diff-red { background: #f8d7da; color: #721c24; font-weight: bold; }
        .diff-yellow { background: #fff3cd; color: #856404; font-weight: bold; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1>🔍 Debug Sync - Tahun {{ $year }}</h1>
        <p class="text-muted">Halaman ini menjalankan sync dan menampilkan detail output</p>
        
        <a href="{{ route('dashboard.index') }}" class="btn btn-secondary mb-3">← Kembali ke Dashboard</a>
        <a href="{{ route('debug.data') }}" class="btn btn-info mb-3">📊 Lihat Data</a>
        <a href="{{ route('debug.sync', ['year' => $year]) }}" class="btn btn-warning mb-3">🔄 Sync Lagi</a>
        
        <hr>
        
        <!-- Comparison Table -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5>📊 Perbandingan Data (Before vs After Sync)</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Data Type</th>
                            <th>BEFORE Sync</th>
                            <th>AFTER Sync</th>
                            <th>Difference</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Customer Data</strong></td>
                            <td>{{ number_format($beforeCustomer) }}</td>
                            <td>{{ number_format($afterCustomer) }}</td>
                            <td class="{{ $afterCustomer > $beforeCustomer ? 'diff-green' : ($afterCustomer < $beforeCustomer ? 'diff-red' : 'diff-yellow') }}">
                                {{ $afterCustomer - $beforeCustomer >= 0 ? '+' : '' }}{{ number_format($afterCustomer - $beforeCustomer) }}
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Power Data</strong></td>
                            <td>{{ number_format($beforePower) }}</td>
                            <td>{{ number_format($afterPower) }}</td>
                            <td class="{{ $afterPower > $beforePower ? 'diff-green' : ($afterPower < $beforePower ? 'diff-red' : 'diff-yellow') }}">
                                {{ $afterPower - $beforePower >= 0 ? '+' : '' }}{{ number_format($afterPower - $beforePower) }}
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Revenue Data</strong></td>
                            <td>{{ number_format($beforeRevenue) }}</td>
                            <td>{{ number_format($afterRevenue) }}</td>
                            <td class="{{ $afterRevenue > $beforeRevenue ? 'diff-green' : ($afterRevenue < $beforeRevenue ? 'diff-red' : 'diff-yellow') }}">
                                {{ $afterRevenue - $beforeRevenue >= 0 ? '+' : '' }}{{ number_format($afterRevenue - $beforeRevenue) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                @if($afterCustomer == $beforeCustomer && $afterPower == $beforePower && $afterRevenue == $beforeRevenue)
                <div class="alert alert-warning">
                    <strong>⚠️ TIDAK ADA PERUBAHAN!</strong><br>
                    Jumlah data sama sebelum dan sesudah sync. Kemungkinan:
                    <ul class="mb-0 mt-2">
                        <li>Data di Google Sheets tidak berubah</li>
                        <li>Format Google Sheets salah (harus ada "BULANAN" dan tahun "{{ $year }}")</li>
                        <li>Data sudah up-to-date</li>
                        <li>Service account tidak punya akses ke sheet</li>
                    </ul>
                </div>
                @else
                <div class="alert alert-success">
                    <strong>✅ DATA BERHASIL DI-SYNC!</strong><br>
                    Total perubahan: {{ abs($afterCustomer - $beforeCustomer) + abs($afterPower - $beforePower) + abs($afterRevenue - $beforeRevenue) }} records
                </div>
                @endif
            </div>
        </div>
        
        <!-- Latest Update Time -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5>🕒 Waktu Update Terakhir</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <td><strong>BEFORE Sync:</strong></td>
                        <td>{{ $beforeLatest?->updated_at ?? 'No data' }}</td>
                    </tr>
                    <tr>
                        <td><strong>AFTER Sync:</strong></td>
                        <td>{{ $afterLatest?->updated_at ?? 'No data' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Changed:</strong></td>
                        <td class="{{ $beforeLatest?->updated_at != $afterLatest?->updated_at ? 'diff-green' : 'diff-yellow' }}">
                            {{ $beforeLatest?->updated_at != $afterLatest?->updated_at ? 'YES ✅ Data di-update!' : 'NO - Tidak ada perubahan' }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Sync Output -->
        <div class="card mb-4">
            <div class="card-header bg-dark text-white">
                <h5>📄 Sync Command Output</h5>
            </div>
            <div class="card-body">
                <pre>{{ $output }}</pre>
            </div>
        </div>
        
        <!-- Recent Data Sample -->
        @if($recentData->count() > 0)
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5>📋 5 Data Terbaru (Sample)</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>ULP</th>
                            <th>Month</th>
                            <th>Total</th>
                            <th>Updated At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentData as $data)
                        <tr>
                            <td>{{ $data->ulp_code }} - {{ $data->ulp_name }}</td>
                            <td>{{ $data->month }}</td>
                            <td>{{ number_format($data->total) }}</td>
                            <td>{{ $data->updated_at }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
        
        <!-- Troubleshooting -->
        <div class="card mb-4">
            <div class="card-header bg-warning">
                <h5>💡 Troubleshooting</h5>
            </div>
            <div class="card-body">
                <h6>Jika Difference = 0 (tidak ada perubahan):</h6>
                <ol>
                    <li><strong>Cek Google Sheets:</strong>
                        <ul>
                            <li>Harus ada kata <strong>"BULANAN"</strong> di atas tabel</li>
                            <li>Harus ada tahun <strong>"{{ $year }}"</strong> di dekat kata BULANAN</li>
                            <li>Format: <code>Kode ULP | Nama ULP | JAN | FEB | MAR ...</code></li>
                            <li>Angka dalam ribuan: 50 = 50,000 pelanggan (bukan 50.000 dengan titik)</li>
                        </ul>
                    </li>
                    <li><strong>Cek Service Account:</strong>
                        <ul>
                            <li>Email: dashboardpln@dashboardpln309.iam.gserviceaccount.com</li>
                            <li>Sudah di-share ke Google Sheets dengan role "Viewer"?</li>
                        </ul>
                    </li>
                    <li><strong>Lihat Output Sync:</strong>
                        <ul>
                            <li>Cari kata "synced" di output di atas</li>
                            <li>Jika ada "0 records synced" → format salah atau tidak ada data</li>
                        </ul>
                    </li>
                </ol>
                
                <h6 class="mt-3">Jika Updated_at Tidak Berubah:</h6>
                <ul>
                    <li>Data di Google Sheets SAMA dengan data di database</li>
                    <li>Sync tidak menemukan perubahan, jadi tidak update timestamp</li>
                    <li>Coba ubah angka di Google Sheets, lalu sync lagi</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
