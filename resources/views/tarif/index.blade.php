<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pengusahaan 309 - Per Tarif</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: linear-gradient(135deg, #f5f9f9 0%, #e8f4f4 100%);
            font-family: -apple-system, BlinkMacSystemFont, 'Inter', 'Segoe UI', sans-serif;
            color: #1e293b;
            min-height: 100vh;
        }
        .navbar {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            border-bottom: none;
            padding: 0.75rem 0;
            box-shadow: 0 4px 12px rgba(23, 162, 184, 0.15);
        }
        .navbar-brand {
            font-size: 1.25rem;
            font-weight: 700;
            color: #ffffff !important;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .nav-tabs {
            border-bottom: none;
            background: rgba(255,255,255,0.15);
            border-radius: 8px;
            padding: 0.25rem;
        }
        .nav-tabs .nav-link {
            color: rgba(255,255,255,0.8);
            border: none;
            border-radius: 6px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .nav-tabs .nav-link:hover {
            color: #ffffff;
            background: rgba(255,255,255,0.2);
        }
        .nav-tabs .nav-link.active {
            color: #00695c;
            background: #ffffff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .detail-table-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
        
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        
        .detail-table thead th {
            background-color: #00897b;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: 600;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .detail-table tbody td {
            padding: 10px 8px;
            border-bottom: 1px solid #e0e0e0;
            text-align: left;
        }
        
        .detail-table tbody tr:hover {
            background-color: #f5f5f5;
        }
        
        .subtotal-row {
            background-color: #e0f2f1 !important;
            font-weight: 600;
        }
        
        .subtotal-row td {
            border-top: 2px solid #00897b !important;
            border-bottom: 2px solid #00897b !important;
        }
        
        .grand-total-row {
            background-color: #00897b !important;
            color: white !important;
            font-weight: 700;
        }
        
        .grand-total-row td {
            border: none !important;
            padding: 14px 8px !important;
        }
        
        /* Category colors */
        .category-s { background-color: rgba(0, 137, 123, 0.05); }
        .category-r { background-color: rgba(38, 198, 218, 0.05); }
        .category-b { background-color: rgba(255, 167, 38, 0.05); }
        .category-i { background-color: rgba(171, 71, 188, 0.05); }
        .category-p { background-color: rgba(239, 83, 80, 0.05); }
        .category-t { background-color: rgba(102, 187, 106, 0.05); }
        .category-c { background-color: rgba(66, 165, 245, 0.05); }
        .category-l { background-color: rgba(255, 202, 40, 0.05); }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    @php
        // $availableYears sudah di-pass dari controller
        if (!isset($availableYears)) $availableYears = [2025, 2026];
    @endphp

    <nav class="navbar">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="#">
                <i class="bi bi-lightning-charge-fill"></i> Data Pengusahaan 309
            </a>
            <div class="d-flex align-items-center gap-3">
                <!-- Tab Navigation -->
                <ul class="nav nav-tabs mb-0">
                    <li class="nav-item">
                        <a class="nav-link" href="/?year={{ $year }}">
                            <i class="bi bi-geo-alt-fill"></i> Per ULP
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/tarif?year={{ $year }}">
                            <i class="bi bi-tags-fill"></i> 309 Per Tarif
                        </a>
                    </li>
                </ul>
                
                <span style="color: rgba(255,255,255,0.9); font-size: 0.875rem; font-weight: 500;">
                    <i class="bi bi-calendar3"></i> Tahun:
                </span>
                <select id="yearSelector" class="form-select" style="width: 110px; padding: 0.4rem 0.75rem; font-size: 0.875rem; border-radius: 8px;" onchange="changeYear(this.value)">
                    @foreach($availableYears as $availableYear)
                    <option value="{{ $availableYear }}" {{ $availableYear == $year ? 'selected' : '' }}>{{ $availableYear }}</option>
                    @endforeach
                </select>
                <button id="syncNowBtn" onclick="syncNow()" title="Sync data dari spreadsheet sekarang" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.5); color: white; border-radius: 8px; padding: 0.4rem 0.75rem; font-size: 0.875rem; cursor: pointer; display: flex; align-items: center; gap: 5px; white-space: nowrap;">
                    <i class="bi bi-arrow-clockwise" id="syncNowIcon"></i> Sync
                </button>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4 py-3">
        <!-- Month and ULP Filter -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex align-items-center gap-3 p-3" style="background: #ffffff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                    <span style="color: #00695c; font-size: 1rem; font-weight: 700;">
                        <i class="bi bi-funnel-fill"></i> Filter:
                    </span>
                    
                    <!-- ULP Filter -->
                    <div class="d-flex align-items-center gap-2">
                        <label style="color: #00695c; font-weight: 600; font-size: 0.875rem;">ULP:</label>
                        <select id="ulpSelector" class="form-select" style="width: 180px; padding: 0.6rem 0.9rem; font-size: 0.875rem; border: 2px solid #b2dfdb; border-radius: 8px; font-weight: 600; color: #00695c; background: #e0f2f1;">
                            <option value="">Semua ULP</option>
                            @foreach($ulpList as $ulpItem)
                            <option value="{{ $ulpItem->ulp_code }}" {{ $ulp == $ulpItem->ulp_code ? 'selected' : '' }}>
                                {{ $ulpItem->ulp_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Month Filter -->
                    <div class="d-flex align-items-center gap-2">
                        <label style="color: #00695c; font-weight: 600; font-size: 0.875rem;">Bulan:</label>
                        <select id="monthSelector" class="form-select" style="width: 150px; padding: 0.6rem 0.9rem; font-size: 0.875rem; border: 2px solid #b2dfdb; border-radius: 8px; font-weight: 600; color: #00695c; background: #e0f2f1;">
                            <option value="">Semua Bulan</option>
                            <option value="0" {{ $month == '0' ? 'selected' : '' }}>Januari</option>
                            <option value="1" {{ $month == '1' ? 'selected' : '' }}>Februari</option>
                            <option value="2" {{ $month == '2' ? 'selected' : '' }}>Maret</option>
                            <option value="3" {{ $month == '3' ? 'selected' : '' }}>April</option>
                            <option value="4" {{ $month == '4' ? 'selected' : '' }}>Mei</option>
                            <option value="5" {{ $month == '5' ? 'selected' : '' }}>Juni</option>
                            <option value="6" {{ $month == '6' ? 'selected' : '' }}>Juli</option>
                            <option value="7" {{ $month == '7' ? 'selected' : '' }}>Agustus</option>
                            <option value="8" {{ $month == '8' ? 'selected' : '' }}>September</option>
                            <option value="9" {{ $month == '9' ? 'selected' : '' }}>Oktober</option>
                            <option value="10" {{ $month == '10' ? 'selected' : '' }}>November</option>
                            <option value="11" {{ $month == '11' ? 'selected' : '' }}>Desember</option>
                        </select>
                    </div>
                    
                    <span style="color: #64748b; font-size: 0.813rem;">
                        Pilih ULP dan/atau bulan untuk filter data
                    </span>
                </div>
            </div>
        </div>

        @if($detailData->isEmpty())
        <!-- Empty state: belum ada data untuk tahun ini -->
        <div class="detail-table-container" style="text-align: center; padding: 60px 20px;">
            <i class="bi bi-inbox" style="font-size: 3rem; color: #94a3b8;"></i>
            <h4 style="color: #64748b; margin-top: 16px;">Belum ada data per tarif untuk tahun {{ $year }}</h4>
            <p style="color: #94a3b8; margin-top: 8px;">
                Data akan muncul otomatis setelah kolom tahun {{ $year }} diisi di Google Spreadsheet<br>
                dan tombol <strong>Sync</strong> ditekan.
            </p>
        </div>
        @else

        <!-- PELANGGAN PER TARIF -->
        <div class="detail-table-container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 style="margin-bottom: 0; color: #00897b;">
                    <i class="bi bi-people-fill"></i> PELANGGAN PER TARIF
                </h3>
                <button onclick="downloadTableAsExcel('pelanggan')" class="btn btn-sm" style="background: #00897b; color: white;">
                    <i class="bi bi-file-earmark-excel"></i> Download Excel
                </button>
            </div>
            <div style="overflow-x: auto; max-height: 600px; overflow-y: auto;">
                <table class="detail-table">
                    <thead>
                        <tr>
                            <th>Kategori</th>
                            <th>Tarif</th>
                            <th style="text-align: right;">Jumlah Pelanggan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $currentCategory = null;
                            $categoryTotal = 0;
                        @endphp
                        @foreach($detailData as $tarif)
                            @if($currentCategory !== null && $currentCategory !== $tarif->tarif_category)
                                <tr class="subtotal-row">
                                    <td colspan="2"><strong>JUMLAH {{ $currentCategory }}</strong></td>
                                    <td style="text-align: right;"><strong>{{ number_format($categoryTotal) }}</strong></td>
                                </tr>
                                @php $categoryTotal = 0; @endphp
                            @endif
                            
                            <tr class="category-{{ strtolower($tarif->tarif_category) }}">
                                <td>{{ $tarif->tarif_category }}</td>
                                <td>{{ $tarif->tarif_name }}</td>
                                <td style="text-align: right;">{{ number_format($tarif->customers) }}</td>
                            </tr>
                            
                            @php
                                $currentCategory = $tarif->tarif_category;
                                $categoryTotal += $tarif->customers;
                            @endphp
                        @endforeach
                        
                        @if($currentCategory !== null)
                            <tr class="subtotal-row">
                                <td colspan="2"><strong>JUMLAH {{ $currentCategory }}</strong></td>
                                <td style="text-align: right;"><strong>{{ number_format($categoryTotal) }}</strong></td>
                            </tr>
                        @endif
                        
                        <tr class="grand-total-row">
                            <td colspan="2"><strong>TOTAL KESELURUHAN</strong></td>
                            <td style="text-align: right;"><strong>{{ number_format($detailData->sum('customers')) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- DAYA PER TARIF -->
        <div class="detail-table-container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 style="margin-bottom: 0; color: #00897b;">
                    <i class="bi bi-lightning-charge-fill"></i> DAYA PER TARIF
                </h3>
                <button onclick="downloadTableAsExcel('daya')" class="btn btn-sm" style="background: #00897b; color: white;">
                    <i class="bi bi-file-earmark-excel"></i> Download Excel
                </button>
            </div>
            <div style="overflow-x: auto; max-height: 600px; overflow-y: auto;">
                <table class="detail-table">
                    <thead>
                        <tr>
                            <th>Kategori</th>
                            <th>Tarif</th>
                            <th style="text-align: right;">Total Daya (VA)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $currentCategory = null;
                            $categoryTotal = 0;
                        @endphp
                        @foreach($detailData as $tarif)
                            @if($currentCategory !== null && $currentCategory !== $tarif->tarif_category)
                                <tr class="subtotal-row">
                                    <td colspan="2"><strong>JUMLAH {{ $currentCategory }}</strong></td>
                                    <td style="text-align: right;"><strong>{{ number_format($categoryTotal) }}</strong></td>
                                </tr>
                                @php $categoryTotal = 0; @endphp
                            @endif
                            
                            <tr class="category-{{ strtolower($tarif->tarif_category) }}">
                                <td>{{ $tarif->tarif_category }}</td>
                                <td>{{ $tarif->tarif_name }}</td>
                                <td style="text-align: right;">{{ number_format($tarif->power) }}</td>
                            </tr>
                            
                            @php
                                $currentCategory = $tarif->tarif_category;
                                $categoryTotal += $tarif->power;
                            @endphp
                        @endforeach
                        
                        @if($currentCategory !== null)
                            <tr class="subtotal-row">
                                <td colspan="2"><strong>JUMLAH {{ $currentCategory }}</strong></td>
                                <td style="text-align: right;"><strong>{{ number_format($categoryTotal) }}</strong></td>
                            </tr>
                        @endif
                        
                        <tr class="grand-total-row">
                            <td colspan="2"><strong>TOTAL KESELURUHAN</strong></td>
                            <td style="text-align: right;"><strong>{{ number_format($detailData->sum('power')) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- KWH JUAL PER TARIF -->
        <div class="detail-table-container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 style="margin-bottom: 0; color: #00897b;">
                    <i class="bi bi-speedometer2"></i> KWH JUAL PER TARIF
                </h3>
                <button onclick="downloadTableAsExcel('kwh')" class="btn btn-sm" style="background: #00897b; color: white;">
                    <i class="bi bi-file-earmark-excel"></i> Download Excel
                </button>
            </div>
            <div style="overflow-x: auto; max-height: 600px; overflow-y: auto;">
                <table class="detail-table">
                    <thead>
                        <tr>
                            <th>Kategori</th>
                            <th>Tarif</th>
                            <th style="text-align: right;">Total kWh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $currentCategory = null;
                            $categoryTotal = 0;
                        @endphp
                        @foreach($detailData as $tarif)
                            @if($currentCategory !== null && $currentCategory !== $tarif->tarif_category)
                                <tr class="subtotal-row">
                                    <td colspan="2"><strong>JUMLAH {{ $currentCategory }}</strong></td>
                                    <td style="text-align: right;"><strong>{{ number_format($categoryTotal) }}</strong></td>
                                </tr>
                                @php $categoryTotal = 0; @endphp
                            @endif
                            
                            <tr class="category-{{ strtolower($tarif->tarif_category) }}">
                                <td>{{ $tarif->tarif_category }}</td>
                                <td>{{ $tarif->tarif_name }}</td>
                                <td style="text-align: right;">{{ number_format($tarif->kwh) }}</td>
                            </tr>
                            
                            @php
                                $currentCategory = $tarif->tarif_category;
                                $categoryTotal += $tarif->kwh;
                            @endphp
                        @endforeach
                        
                        @if($currentCategory !== null)
                            <tr class="subtotal-row">
                                <td colspan="2"><strong>JUMLAH {{ $currentCategory }}</strong></td>
                                <td style="text-align: right;"><strong>{{ number_format($categoryTotal) }}</strong></td>
                            </tr>
                        @endif
                        
                        <tr class="grand-total-row">
                            <td colspan="2"><strong>TOTAL KESELURUHAN</strong></td>
                            <td style="text-align: right;"><strong>{{ number_format($detailData->sum('kwh')) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- RP PENDAPATAN PER TARIF -->
        <div class="detail-table-container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 style="margin-bottom: 0; color: #00897b;">
                    <i class="bi bi-cash-stack"></i> RP PENDAPATAN PER TARIF
                </h3>
                <button onclick="downloadTableAsExcel('rp')" class="btn btn-sm" style="background: #00897b; color: white;">
                    <i class="bi bi-file-earmark-excel"></i> Download Excel
                </button>
            </div>
            <div style="overflow-x: auto; max-height: 600px; overflow-y: auto;">
                <table class="detail-table">
                    <thead>
                        <tr>
                            <th>Kategori</th>
                            <th>Tarif</th>
                            <th style="text-align: right;">Total Pendapatan (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $currentCategory = null;
                            $categoryTotal = 0;
                        @endphp
                        @foreach($detailData as $tarif)
                            @if($currentCategory !== null && $currentCategory !== $tarif->tarif_category)
                                <tr class="subtotal-row">
                                    <td colspan="2"><strong>JUMLAH {{ $currentCategory }}</strong></td>
                                    <td style="text-align: right;"><strong>Rp {{ number_format($categoryTotal) }}</strong></td>
                                </tr>
                                @php $categoryTotal = 0; @endphp
                            @endif
                            
                            <tr class="category-{{ strtolower($tarif->tarif_category) }}">
                                <td>{{ $tarif->tarif_category }}</td>
                                <td>{{ $tarif->tarif_name }}</td>
                                <td style="text-align: right;">Rp {{ number_format($tarif->rp) }}</td>
                            </tr>
                            
                            @php
                                $currentCategory = $tarif->tarif_category;
                                $categoryTotal += $tarif->rp;
                            @endphp
                        @endforeach
                        
                        @if($currentCategory !== null)
                            <tr class="subtotal-row">
                                <td colspan="2"><strong>JUMLAH {{ $currentCategory }}</strong></td>
                                <td style="text-align: right;"><strong>Rp {{ number_format($categoryTotal) }}</strong></td>
                            </tr>
                        @endif
                        
                        <tr class="grand-total-row">
                            <td colspan="2"><strong>TOTAL KESELURUHAN</strong></td>
                            <td style="text-align: right;"><strong>Rp {{ number_format($detailData->sum('rp')) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    <script>
        // Auto-sync functionality (polling every 5 seconds)
        let lastUpdate = null;
        let syncInterval = null;
        
        function checkForUpdates() {
            const year = document.getElementById('yearSelector').value;
            const params = new URLSearchParams({
                year: year,
                last_update: lastUpdate || ''
            });
            
            fetch('/api/tarif/sync-status?' + params)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        lastUpdate = data.last_update;
                        
                        // If there are changes, reload the page
                        if (data.has_changes) {
                            console.log('Data updated! Reloading...');
                            location.reload();
                        }
                    }
                })
                .catch(error => {
                    console.error('Sync check error:', error);
                });
        }
        
        // Start polling when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Initial check
            checkForUpdates();
            
            // Poll every 5 seconds
            syncInterval = setInterval(checkForUpdates, 5000);
        });
        
        // Stop polling when page unloads
        window.addEventListener('beforeunload', function() {
            if (syncInterval) {
                clearInterval(syncInterval);
            }
        });
        
        async function syncNow() {
            const year = document.getElementById('yearSelector').value;
            const btn = document.getElementById('syncNowBtn');
            const icon = document.getElementById('syncNowIcon');
            
            btn.disabled = true;
            icon.style.animation = 'spin 1s linear infinite';
            
            // Show loading overlay
            const overlay = document.getElementById('loadingOverlay');
            const overlayText = document.getElementById('loadingText');
            if (overlay) {
                overlayText.textContent = `Menyinkronkan data ${year}...`;
                overlay.style.display = 'flex';
            }
            
            try {
                await fetch('/api/tarif/trigger-sync', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ year: year })
                });
                
                if (overlayText) overlayText.textContent = '✓ Sync berhasil! Memuat ulang...';
                setTimeout(() => window.location.reload(), 1000);
            } catch (e) {
                if (overlay) overlay.style.display = 'none';
                btn.disabled = false;
                icon.style.animation = '';
                alert('Sync gagal, coba lagi');
            }
        }

        function changeYear(year) {
            const month = document.getElementById('monthSelector').value;
            const ulp = document.getElementById('ulpSelector').value;
            let url = '?year=' + year;
            if (month) url += '&month=' + month;
            if (ulp) url += '&ulp=' + ulp;
            window.location.href = url;
        }
        
        document.getElementById('monthSelector').addEventListener('change', function() {
            const month = this.value;
            const year = document.getElementById('yearSelector').value;
            const ulp = document.getElementById('ulpSelector').value;
            let url = '?year=' + year;
            if (month) url += '&month=' + month;
            if (ulp) url += '&ulp=' + ulp;
            window.location.href = url;
        });
        
        document.getElementById('ulpSelector').addEventListener('change', function() {
            const ulp = this.value;
            const year = document.getElementById('yearSelector').value;
            const month = document.getElementById('monthSelector').value;
            let url = '?year=' + year;
            if (month) url += '&month=' + month;
            if (ulp) url += '&ulp=' + ulp;
            window.location.href = url;
        });

        // Function to download table as Excel
        function downloadTableAsExcel(tableName) {
            const tables = {
                'pelanggan': 0,
                'daya': 1,
                'kwh': 2,
                'rp': 3
            };
            
            const tableIndex = tables[tableName];
            const table = document.querySelectorAll('.detail-table')[tableIndex];
            
            if (!table) {
                alert('Tabel tidak ditemukan');
                return;
            }
            
            let html = '<html><head><meta charset="utf-8"><style>table{border-collapse:collapse;width:100%}th,td{border:1px solid #ddd;padding:8px;text-align:left}th{background-color:#00897b;color:white;font-weight:bold}</style></head><body>';
            html += '<h2>Data Pengusahaan 309 - ' + tableName.toUpperCase() + ' PER TARIF</h2>';
            html += '<p>Tanggal: ' + new Date().toLocaleDateString('id-ID') + '</p>';
            html += table.outerHTML;
            html += '</body></html>';
            
            const blob = new Blob(['\ufeff' + html], {
                type: 'application/vnd.ms-excel'
            });
            
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `309-per-tarif-${tableName}-${new Date().toISOString().split('T')[0]}.xls`;
            link.click();
            window.URL.revokeObjectURL(url);
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
