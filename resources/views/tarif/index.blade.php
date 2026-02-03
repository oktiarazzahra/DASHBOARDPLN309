<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard PLN 309 Per Tarif</title>
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
    </style>
</head>
<body>
    @php
        $availableYears = [2025];
    @endphp

    <nav class="navbar">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="#">
                <i class="bi bi-lightning-charge-fill"></i> Dashboard PLN 309
            </a>
            <div class="d-flex align-items-center gap-3">
                <!-- Tab Navigation -->
                <ul class="nav nav-tabs mb-0">
                    <li class="nav-item">
                        <a class="nav-link" href="/?year={{ $year }}">Per ULP</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/tarif?year={{ $year }}">309 Per Tarif</a>
                    </li>
                </ul>
                
                <select id="yearSelector" class="form-select" style="width: 110px; padding: 0.4rem 0.75rem; font-size: 0.875rem; border-radius: 8px;" onchange="changeYear(this.value)">
                    @foreach($availableYears as $availableYear)
                    <option value="{{ $availableYear }}" {{ $availableYear == $year ? 'selected' : '' }}>{{ $availableYear }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4 py-3">
        <!-- Month Filter -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex align-items-center gap-3 p-3" style="background: #ffffff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                    <span style="color: #00695c; font-size: 1rem; font-weight: 700;">
                        <i class="bi bi-funnel-fill"></i> Filter Bulan:
                    </span>
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
                    <span style="color: #64748b; font-size: 0.813rem;">
                        Pilih bulan untuk melihat data spesifik tarif
                    </span>
                </div>
            </div>
        </div>

        <!-- PELANGGAN PER TARIF -->
        <div class="detail-table-container">
            <h3 style="margin-bottom: 15px; color: #00897b;">
                <i class="bi bi-people-fill"></i> PELANGGAN PER TARIF
            </h3>
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
            <h3 style="margin-bottom: 15px; color: #00897b;">
                <i class="bi bi-lightning-charge-fill"></i> DAYA PER TARIF
            </h3>
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
            <h3 style="margin-bottom: 15px; color: #00897b;">
                <i class="bi bi-speedometer2"></i> KWH JUAL PER TARIF
            </h3>
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
            <h3 style="margin-bottom: 15px; color: #00897b;">
                <i class="bi bi-cash-stack"></i> RP PENDAPATAN PER TARIF
            </h3>
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
        
        function changeYear(year) {
            const month = document.getElementById('monthSelector').value;
            let url = '?year=' + year;
            if (month) url += '&month=' + month;
            window.location.href = url;
        }
        
        document.getElementById('monthSelector').addEventListener('change', function() {
            const month = this.value;
            const year = document.getElementById('yearSelector').value;
            let url = '?year=' + year;
            if (month) url += '&month=' + month;
            window.location.href = url;
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
