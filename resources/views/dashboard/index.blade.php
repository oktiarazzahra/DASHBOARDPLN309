<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Dashboard PLN 309</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
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
        .stat-card {
            background: #ffffff;
            border: none;
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            height: 100%;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(23, 162, 184, 0.15);
        }
        .stat-value {
            font-size: 1.625rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0.4rem 0;
        }
        .stat-label {
            font-size: 0.813rem;
            color: #64748b;
            font-weight: 500;
        }
        .chart-card {
            background: #ffffff;
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        .chart-card:hover {
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }
        .table-card {
            background: #ffffff;
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .table-header {
            padding: 1.25rem 1.5rem;
            background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 100%);
            border-bottom: none;
        }
        .table-header h6 {
            font-size: 1rem;
            font-weight: 700;
            color: #00695c;
            margin: 0;
        }
        .table {
            margin: 0;
            font-size: 0.875rem;
        }
        .table th {
            background: #f0fdfa;
            color: #00897b;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
            padding: 1rem 1.5rem;
        }
        .table td {
            padding: 1rem 1.5rem;
            border-color: #e0f2f1;
            color: #37474f;
            font-weight: 500;
        }
        #yearSelector {
            cursor: pointer;
            font-weight: 600;
            color: #ffffff;
            background: rgba(255,255,255,0.2) !important;
            border: 1px solid rgba(255,255,255,0.3) !important;
            transition: all 0.2s ease;
        }
        #yearSelector:hover {
            background: rgba(255,255,255,0.3) !important;
            border-color: rgba(255,255,255,0.5) !important;
        }
        #yearSelector:focus {
            outline: none;
            background: rgba(255,255,255,0.3) !important;
            border-color: #ffffff !important;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.2);
        }
        #yearSelector option {
            color: #0f172a;
            background: #ffffff;
        }
        /* Toast Notification */
        .toast-container {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 9999;
        }
        .custom-toast {
            background: #ffffff;
            border: none;
            border-left: 4px solid #17a2b8;
            border-radius: 10px;
            padding: 1rem 1.25rem;
            box-shadow: 0 10px 30px rgba(23, 162, 184, 0.2);
            min-width: 320px;
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn {
            from { transform: translateX(400px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .sync-indicator {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 0.9rem;
            background: rgba(255,255,255,0.95);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 8px;
            font-size: 0.75rem;
            color: #00897b;
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .sync-indicator.syncing {
            background: rgba(255,235,59,0.95);
            border-color: rgba(255,235,59,0.5);
            color: #f57c00;
        }
        .spinner-border-sm {
            width: 12px;
            height: 12px;
            border-width: 2px;
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
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container-fluid px-4">
            <span class="navbar-brand">
                <i class="bi bi-lightning-charge-fill"></i> Data Pengusahaan 309
            </span>
            <div class="d-flex align-items-center gap-3">
                <!-- Tab Navigation -->
                <ul class="nav nav-tabs mb-0">
                    <li class="nav-item">
                        <a class="nav-link active" href="/?year={{ $year }}">
                            <i class="bi bi-geo-alt-fill"></i> Per ULP
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/tarif?year={{ $year }}">
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

    <!-- Toast Notification Container -->
    <div class="toast-container" id="toastContainer"></div>

    <div class="container-fluid px-4 py-3">
        <!-- Month Filter -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex align-items-center gap-3 p-3" style="background: #ffffff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                    <span style="color: #00695c; font-size: 1rem; font-weight: 700;">
                        <i class="bi bi-funnel-fill"></i> Filter Bulan:
                    </span>
                    <select id="monthSelector" class="form-select" style="width: 150px; padding: 0.6rem 0.9rem; font-size: 0.875rem; border: 2px solid #b2dfdb; border-radius: 8px; font-weight: 600; color: #00695c; background: #e0f2f1;">
                        <option value="0">Januari</option>
                        <option value="1">Februari</option>
                        <option value="2">Maret</option>
                        <option value="3">April</option>
                        <option value="4">Mei</option>
                        <option value="5">Juni</option>
                        <option value="6">Juli</option>
                        <option value="7">Agustus</option>
                        <option value="8">September</option>
                        <option value="9">Oktober</option>
                        <option value="10">November</option>
                        <option value="11" selected>Desember</option>
                    </select>
                    <span style="color: #64748b; font-size: 0.813rem;">
                        Pilih bulan untuk melihat data spesifik
                    </span>
                </div>
            </div>
        </div>

        <!-- Main Charts Row (2x2 Grid) -->
        <div class="row g-3 mb-3">
            <!-- Distribusi Pelanggan -->
            <div class="col-lg-6">
                <div class="chart-card" style="padding: 1.25rem; height: 450px;">
                    <div class="mb-2 d-flex justify-content-between align-items-center">
                        <div>
                            <div style="font-size: 0.938rem; font-weight: 600; color: #0f172a; margin-bottom: 0.25rem;">Distribusi Pelanggan</div>
                            <div style="font-size: 0.75rem; color: #64748b;">Proporsi per ULP</div>
                        </div>
                        <button onclick="downloadChart('customerChart', 'pelanggan')" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-download"></i>
                        </button>
                    </div>
                    <!-- Total Pelanggan -->
                    <div class="text-center mb-3" style="padding: 1rem; background: linear-gradient(135deg, #e0f2f1 0%, #b2dfdb 100%); border-radius: 10px;">
                        <div style="font-size: 0.75rem; color: #00695c; font-weight: 600; margin-bottom: 0.25rem;">TOTAL PELANGGAN</div>
                        <div id="totalCustomers" style="font-size: 1.75rem; font-weight: 700; color: #00897b;">-</div>
                    </div>
                    <div style="position: relative; height: 240px;">
                        <canvas id="customerChart"></canvas>
                    </div>
                    <div id="customerLegend" class="mt-2" style="font-size: 0.75rem; color: #64748b;"></div>
                </div>
            </div>

            <!-- Daya Tersambung -->
            <div class="col-lg-6">
                <div class="chart-card" style="padding: 1.25rem; height: 450px;">
                    <div class="mb-2 d-flex justify-content-between align-items-center">
                        <div>
                            <div style="font-size: 0.938rem; font-weight: 600; color: #0f172a; margin-bottom: 0.25rem;">Distribusi Daya Tersambung</div>
                            <div style="font-size: 0.75rem; color: #64748b;">Total daya terpasang per ULP (kVA)</div>
                        </div>
                        <button onclick="downloadChart('powerChart', 'daya-tersambung')" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-download"></i>
                        </button>
                    </div>
                    <div style="position: relative; height: 320px;">
                        <canvas id="powerChart"></canvas>
                    </div>
                    <div id="powerLegend" class="mt-2" style="font-size: 0.75rem; color: #64748b;"></div>
                </div>
            </div>
        </div>

        <!-- Revenue Charts (kWh & Rp Pendapatan) -->
        <div class="row g-3 mb-3">
            <div class="col-lg-6">
                <div class="chart-card" style="padding: 1.25rem; height: 450px;">
                    <div class="mb-2 d-flex justify-content-between align-items-center">
                        <div>
                            <div style="font-size: 0.938rem; font-weight: 600; color: #0f172a; margin-bottom: 0.25rem;">Distribusi kWh Jual</div>
                            <div style="font-size: 0.75rem; color: #64748b;">Proporsi penjualan energi per ULP (Juta kWh)</div>
                        </div>
                        <button onclick="downloadChart('kwhChart', 'kwh-jual')" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-download"></i>
                        </button>
                    </div>
                    <div style="position: relative; height: 320px;">
                        <canvas id="kwhChart"></canvas>
                    </div>
                    <div id="kwhLegend" class="mt-2" style="font-size: 0.75rem; color: #64748b;"></div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="chart-card" style="padding: 1.25rem; height: 450px;">
                    <div class="mb-2 d-flex justify-content-between align-items-center">
                        <div>
                            <div style="font-size: 0.938rem; font-weight: 600; color: #0f172a; margin-bottom: 0.25rem;">Distribusi Rp Pendapatan</div>
                            <div style="font-size: 0.75rem; color: #64748b;">Proporsi pendapatan per ULP (Milyar)</div>
                        </div>
                        <button onclick="downloadChart('rpChart', 'rp-pendapatan')" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-download"></i>
                        </button>
                    </div>
                    <div style="position: relative; height: 320px;">
                        <canvas id="rpChart"></canvas>
                    </div>
                    <div id="rpLegend" class="mt-2" style="font-size: 0.75rem; color: #64748b;"></div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="row">
            <div class="col-12">
                <div class="table-card">
                    <div class="table-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Ringkasan Data per ULP</h6>
                        <button onclick="downloadTableAsExcel()" class="btn btn-sm" style="background: #00897b; color: white;">
                            <i class="bi bi-file-earmark-excel"></i> Download Excel
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr id="summaryTableHeader">
                                    <th style="width: 180px;">Metrik</th>
                                    <!-- ULP headers will be populated by JavaScript -->
                                </tr>
                            </thead>
                            <tbody id="summaryTableBody">
                                <!-- Will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const customerByUlp = {!! json_encode($customerByUlp) !!};
        const powerByUlp = {!! json_encode($powerByUlp) !!};
        const revenueByUlp = {!! json_encode($revenueByUlp) !!};
        
        // Modern teal/turquoise color palette (inspired by finance dashboard)
        const colors = [
            '#00897b', // Teal
            '#26c6da', // Cyan
            '#ffa726', // Orange
            '#ab47bc', // Purple
            '#ef5350', // Red
            '#66bb6a'  // Green
        ];

        // Register datalabels plugin globally
        Chart.register(ChartDataLabels);

        // Customer Chart - Pie/Doughnut
        const customerCtx = document.getElementById('customerChart').getContext('2d');
        
        // Calculate total customers per ULP for the whole year
        const ulpTotals = customerByUlp.map(ulp => {
            return {
                name: ulp.ulp_name,
                total: ulp.data.reduce((sum, val) => sum + val, 0),
                monthlyData: ulp.data
            };
        });

        const customerChart = new Chart(customerCtx, {
            type: 'doughnut',
            data: {
                labels: ulpTotals.map(ulp => ulp.name),
                datasets: [{
                    data: ulpTotals.map(ulp => ulp.monthlyData[11]), // Start with December data
                    backgroundColor: colors,
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        top: 80,
                        bottom: 80,
                        left: 120,
                        right: 120
                    }
                },
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: 1500,
                    easing: 'easeInOutQuart'
                },
                plugins: {
                    legend: {
                        display: false // Hide legend karena sudah ada label di chart
                    },
                    tooltip: {
                        enabled: false // Disable tooltip karena label sudah visible
                    },
                    datalabels: {
                        display: true,
                        color: '#0f172a',
                        font: {
                            size: 12,
                            weight: 'bold',
                            family: '-apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif'
                        },
                        formatter: function(value, context) {
                            const label = context.chart.data.labels[context.dataIndex];
                            // Hapus prefix "ULP" dari nama
                            const cleanLabel = label.replace(/^ULP\s+/i, '');
                            // Format: NAMA\n123,456 (tanpa persentase)
                            return cleanLabel + '\n' + value.toLocaleString('id-ID');
                        },
                        textAlign: 'center',
                        anchor: 'end',
                        align: 'end',
                        offset: 15,
                        clip: false,
                        clamp: false,
                        borderRadius: 6,
                        backgroundColor: function(context) {
                            return '#ffffff';
                        },
                        borderColor: function(context) {
                            return context.dataset.backgroundColor[context.dataIndex];
                        },
                        borderWidth: 2,
                        padding: {
                            top: 8,
                            bottom: 8,
                            left: 12,
                            right: 12
                        }
                    }
                }
            },
            plugins: [{
                // Custom plugin untuk leader lines
                id: 'leaderLines',
                afterDatasetDraw: function(chart, args, options) {
                    const ctx = chart.ctx;
                    const meta = args.meta;
                    const dataset = chart.data.datasets[args.index];
                    
                    meta.data.forEach((element, index) => {
                        const model = element;
                        const value = dataset.data[index];
                        
                        if (value <= 0) return;
                        
                        // Calculate positions
                        const centerX = model.x;
                        const centerY = model.y;
                        const radius = model.outerRadius;
                        const startAngle = model.startAngle;
                        const endAngle = model.endAngle;
                        const midAngle = startAngle + (endAngle - startAngle) / 2;
                        
                        // Point on the arc
                        const x1 = centerX + Math.cos(midAngle) * (radius + 5);
                        const y1 = centerY + Math.sin(midAngle) * (radius + 5);
                        
                        // Extended point for the line (longer for label)
                        const extendLength = 30;
                        const x2 = centerX + Math.cos(midAngle) * (radius + extendLength);
                        const y2 = centerY + Math.sin(midAngle) * (radius + extendLength);
                        
                        // Horizontal line extension
                        const lineExtend = 15;
                        const x3 = x2 + (Math.cos(midAngle) > 0 ? lineExtend : -lineExtend);
                        
                        // Draw line
                        ctx.save();
                        ctx.beginPath();
                        ctx.strokeStyle = dataset.backgroundColor[index];
                        ctx.lineWidth = 2.5;
                        ctx.setLineDash([]);
                        
                        // Line from arc to extended point
                        ctx.moveTo(x1, y1);
                        ctx.lineTo(x2, y2);
                        
                        // Horizontal extension
                        ctx.lineTo(x3, y2);
                        
                        ctx.stroke();
                        ctx.restore();
                    });
                }
            }, ChartDataLabels]
        });

        // Initialize total pelanggan display (December data)
        const initialTotalCustomers = ulpTotals.reduce((sum, ulp) => sum + ulp.monthlyData[11], 0);
        document.getElementById('totalCustomers').textContent = initialTotalCustomers.toLocaleString('id-ID');

        // Month selector functionality - affects ALL charts and summary
        document.getElementById('monthSelector').addEventListener('change', function() {
            const monthIndex = parseInt(this.value);
            
            // Update Customer Chart (Pie)
            customerChart.data.datasets[0].data = ulpTotals.map(ulp => ulp.monthlyData[monthIndex]);
            customerChart.update();
            
            // Update total pelanggan display
            const totalCustomers = ulpTotals.reduce((sum, ulp) => sum + ulp.monthlyData[monthIndex], 0);
            document.getElementById('totalCustomers').textContent = totalCustomers.toLocaleString('id-ID');
            
            // Update Power Chart (Pie) - show proportions for selected month
            powerChart.data.datasets[0].data = powerByUlp.map(ulp => ulp.data[monthIndex] / 1000);
            powerChart.update();
            
            // Update kWh Chart (Pie) - show proportions for selected month
            kwhChart.data.datasets[0].data = revenueByUlp.map(ulp => ulp.kwh_data[monthIndex]);
            kwhChart.update();
            
            // Update Rp Chart (Pie) - show proportions for selected month
            rpChart.data.datasets[0].data = revenueByUlp.map(ulp => ulp.rp_data[monthIndex] / 1000000000);
            rpChart.update();
            
            // Update summary table for selected month
            updateSummaryTableForMonth(monthIndex);
        });
        
        // Function to update summary table based on selected month
        function updateSummaryTableForMonth(monthIndex) {
            const tbody = document.getElementById('summaryTableBody');
            
            const customersData = [];
            const powerData = [];
            const kwhData = [];
            const rpData = [];
            const rpPerKwhData = [];
            
            customerByUlp.forEach((customer, index) => {
                const power = powerByUlp[index];
                const revenue = revenueByUlp[index];
                
                const monthCustomers = customer.data[monthIndex];
                const monthPower = power.data[monthIndex] / 1000; // kVA
                const monthKwh = revenue.kwh_data[monthIndex];
                const monthRp = revenue.rp_data[monthIndex];
                const rpPerKwh = monthKwh > 0 ? monthRp / monthKwh : 0;
                
                customersData.push(monthCustomers.toLocaleString('id-ID'));
                powerData.push(monthPower.toLocaleString('id-ID', {maximumFractionDigits: 0}));
                kwhData.push((monthKwh / 1000000).toFixed(2) + ' M');
                rpData.push('Rp ' + (monthRp / 1000000000).toFixed(2) + ' M');
                rpPerKwhData.push('Rp ' + rpPerKwh.toLocaleString('id-ID', {maximumFractionDigits: 0}));
            });
            
            const months = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
            
            tbody.innerHTML = `
                <tr>
                    <td style="font-weight: 500; color: #64748b;">Pelanggan (${months[monthIndex]})</td>
                    ${customersData.map(val => `<td class="text-end">${val}</td>`).join('')}
                </tr>
                <tr>
                    <td style="font-weight: 500; color: #64748b;">Daya (${months[monthIndex]})</td>
                    ${powerData.map(val => `<td class="text-end">${val} kVA</td>`).join('')}
                </tr>
                <tr>
                    <td style="font-weight: 500; color: #64748b;">kWh Jual (${months[monthIndex]})</td>
                    ${kwhData.map(val => `<td class="text-end">${val}</td>`).join('')}
                </tr>
                <tr>
                    <td style="font-weight: 500; color: #64748b;">Rp Pendapatan (${months[monthIndex]})</td>
                    ${rpData.map(val => `<td class="text-end">${val}</td>`).join('')}
                </tr>
                <tr>
                    <td style="font-weight: 500; color: #64748b;">Rp/kWh (${months[monthIndex]})</td>
                    ${rpPerKwhData.map(val => `<td class="text-end">${val}</td>`).join('')}
                </tr>
            `;
        }
        


        // Power Chart - Horizontal Bar (Comparison for December initially)
        const powerCtx = document.getElementById('powerChart').getContext('2d');
        
        const powerChart = new Chart(powerCtx, {
            type: 'bar',
            data: {
                labels: powerByUlp.map(ulp => ulp.ulp_name.replace(/^ULP\s+/i, '')),
                datasets: [{
                    label: 'Daya Tersambung (kVA)',
                    data: powerByUlp.map(ulp => ulp.data[11] / 1000), // December data in kVA
                    backgroundColor: colors,
                    borderColor: colors,
                    borderWidth: 0,
                    borderRadius: 6
                }]
            },
            options: {
                indexAxis: 'y', // Horizontal bar
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    datalabels: {
                        display: false
                    },
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 12,
                        titleFont: { size: 13, weight: '600' },
                        bodyFont: { size: 12 },
                        callbacks: {
                            label: function(context) {
                                return context.parsed.x.toLocaleString('id-ID', {maximumFractionDigits: 0}) + ' kVA';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { 
                            color: '#f1f5f9',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#64748b',
                            font: { size: 11 },
                            callback: function(value) {
                                return (value / 1000).toFixed(0) + 'K';
                            }
                        },
                        border: { display: false }
                    },
                    y: {
                        grid: { display: false },
                        ticks: { 
                            color: '#64748b',
                            font: { size: 11, weight: '500' }
                        },
                        border: { display: false }
                    }
                }
            }
        });

        // kWh Chart - Vertical Bar (Comparison for December initially)
        const kwhCtx = document.getElementById('kwhChart').getContext('2d');
        
        const kwhChart = new Chart(kwhCtx, {
            type: 'bar',
            data: {
                labels: revenueByUlp.map(ulp => ulp.ulp_name.replace(/^ULP\s+/i, '')),
                datasets: [{
                    label: 'kWh Jual',
                    data: revenueByUlp.map(ulp => ulp.kwh_data[11]), // December data
                    backgroundColor: colors,
                    borderColor: colors,
                    borderWidth: 0,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    datalabels: {
                        display: false
                    },
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 12,
                        titleFont: { size: 13, weight: '600' },
                        bodyFont: { size: 12 },
                        callbacks: {
                            label: function(context) {
                                const valueInM = (context.parsed.y / 1000000).toFixed(2);
                                return valueInM + ' M kWh';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { 
                            color: '#64748b',
                            font: { size: 11, weight: '500' },
                            maxRotation: 45,
                            minRotation: 0
                        },
                        border: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { 
                            color: '#f1f5f9',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#64748b',
                            font: { size: 11 },
                            callback: function(value) {
                                return (value / 1000000).toFixed(0) + 'M';
                            }
                        },
                        border: { display: false }
                    }
                }
            }
        });

        // Rp Pendapatan Chart - Vertical Bar (Comparison for December initially)
        const rpCtx = document.getElementById('rpChart').getContext('2d');
        
        const rpChart = new Chart(rpCtx, {
            type: 'bar',
            data: {
                labels: revenueByUlp.map(ulp => ulp.ulp_name.replace(/^ULP\s+/i, '')),
                datasets: [{
                    label: 'Rp Pendapatan',
                    data: revenueByUlp.map(ulp => ulp.rp_data[11] / 1000000000), // December data in Milyar
                    backgroundColor: colors,
                    borderColor: colors,
                    borderWidth: 0,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    datalabels: {
                        display: false
                    },
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 12,
                        titleFont: { size: 13, weight: '600' },
                        bodyFont: { size: 12 },
                        callbacks: {
                            label: function(context) {
                                return 'Rp ' + context.parsed.y.toLocaleString('id-ID', {maximumFractionDigits: 2}) + ' M';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { 
                            color: '#64748b',
                            font: { size: 11, weight: '500' },
                            maxRotation: 45,
                            minRotation: 0
                        },
                        border: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { 
                            color: '#f1f5f9',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#64748b',
                            font: { size: 11 },
                            callback: function(value) {
                                return 'Rp ' + value.toFixed(0) + 'M';
                            }
                        },
                        border: { display: false }
                    }
                }
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            populateSummaryTable();
        });

        function populateSummaryTable() {
            const thead = document.getElementById('summaryTableHeader');
            const tbody = document.getElementById('summaryTableBody');
            const lastMonthIndex = 11;
            
            // Build header row with ULP names
            let headerHTML = '<th style="width: 180px;">Metrik</th>';
            customerByUlp.forEach((customer, index) => {
                headerHTML += `
                    <th class="text-end">
                        <div style="display: flex; align-items: center; justify-content: flex-end;">
                            <div style="width: 8px; height: 8px; border-radius: 2px; background-color: ${colors[index]}; margin-right: 0.5rem;"></div>
                            <span style="font-weight: 600; font-size: 0.75rem;">${customer.ulp_name}</span>
                        </div>
                    </th>
                `;
            });
            thead.innerHTML = headerHTML;
            
            // Prepare data arrays
            const customersData = [];
            const powerData = [];
            const kwhData = [];
            const rpData = [];
            const rpPerKwhData = [];
            
            customerByUlp.forEach((customer, index) => {
                const power = powerByUlp[index];
                const revenue = revenueByUlp[index];
                
                const latestCustomers = customer.data[lastMonthIndex];
                const latestPower = power.data[lastMonthIndex] / 1000; // kVA
                const latestKwh = revenue.kwh_data[lastMonthIndex];
                const latestRp = revenue.rp_data[lastMonthIndex];
                const rpPerKwh = latestKwh > 0 ? latestRp / latestKwh : 0;
                
                customersData.push(latestCustomers.toLocaleString('id-ID'));
                powerData.push(latestPower.toLocaleString('id-ID', {maximumFractionDigits: 0}));
                kwhData.push((latestKwh / 1000000).toFixed(2) + ' M');
                rpData.push('Rp ' + (latestRp / 1000000000).toFixed(2) + ' M');
                rpPerKwhData.push('Rp ' + rpPerKwh.toLocaleString('id-ID', {maximumFractionDigits: 0}));
            });
            
            // Build rows
            tbody.innerHTML = `
                <tr>
                    <td style="font-weight: 500; color: #64748b;">Pelanggan (Des)</td>
                    ${customersData.map(val => `<td class="text-end">${val}</td>`).join('')}
                </tr>
                <tr>
                    <td style="font-weight: 500; color: #64748b;">Daya (Des)</td>
                    ${powerData.map(val => `<td class="text-end">${val} kVA</td>`).join('')}
                </tr>
                <tr>
                    <td style="font-weight: 500; color: #64748b;">kWh Jual (Des)</td>
                    ${kwhData.map(val => `<td class="text-end">${val}</td>`).join('')}
                </tr>
                <tr>
                    <td style="font-weight: 500; color: #64748b;">Rp Pendapatan (Des)</td>
                    ${rpData.map(val => `<td class="text-end">${val}</td>`).join('')}
                </tr>
                <tr>
                    <td style="font-weight: 500; color: #64748b;">Rp/kWh (Des)</td>
                    ${rpPerKwhData.map(val => `<td class="text-end">${val}</td>`).join('')}
                </tr>
            `;
        }

        // Function to change year - auto sync dari spreadsheet lalu reload page
        async function syncNow() {
            const year = document.getElementById('yearSelector').value;
            const btn = document.getElementById('syncNowBtn');
            const icon = document.getElementById('syncNowIcon');
            
            btn.disabled = true;
            icon.style.animation = 'spin 1s linear infinite';
            icon.style.display = 'inline-block';
            
            const loadingToast = document.createElement('div');
            loadingToast.className = 'custom-toast';
            loadingToast.id = 'syncNowToast';
            loadingToast.innerHTML = `
                <div class="d-flex align-items-center gap-2">
                    <div class="spinner-border spinner-border-sm" style="color: #17a2b8;" role="status"></div>
                    <div>
                        <div style="font-weight: 600; color: #0f172a; font-size: 0.875rem;">Menyinkronkan data ${year}...</div>
                        <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">Mengambil data terbaru dari spreadsheet</div>
                    </div>
                </div>
            `;
            document.getElementById('toastContainer').appendChild(loadingToast);
            
            try {
                await Promise.all([
                    fetch('/api/trigger-sync', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ year: year })
                    }),
                    fetch('/api/tarif/trigger-sync', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ year: year })
                    })
                ]);
                
                loadingToast.innerHTML = `
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle-fill" style="color: #10b981; font-size: 1.25rem;"></i>
                        <div>
                            <div style="font-weight: 600; color: #0f172a; font-size: 0.875rem;">✓ Sync berhasil! Memuat ulang...</div>
                        </div>
                    </div>
                `;
                // Force reload dengan bypass cache menggunakan timestamp
                setTimeout(() => {
                    location.href = location.origin + location.pathname + '?year=' + year + '&t=' + Date.now();
                }, 1000);
            } catch (e) {
                loadingToast.innerHTML = `<div style="color: #ef4444; font-weight: 600;">Sync gagal, coba lagi</div>`;
                btn.disabled = false;
                icon.style.animation = '';
                setTimeout(() => loadingToast.remove(), 2000);
            }
        }

        function changeYear(year) {
            window.location.href = `/?year=${year}`;
        }

        // ========== SUPER FAST REAL-TIME SYNC (5 detik polling) ==========
        
        let lastUpdateTimestamp = null;
        let isChecking = false;
        let consecutiveNoChanges = 0;

        // Toast notification function
        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = 'custom-toast';
            toast.innerHTML = `
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'info-circle-fill'}" style="color: #10b981; font-size: 1.25rem;"></i>
                    <div>
                        <div style="font-weight: 600; color: #0f172a; font-size: 0.875rem;">${message}</div>
                        <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">Dashboard akan diperbarui...</div>
                    </div>
                </div>
            `;
            container.appendChild(toast);
            
            setTimeout(() => {
                toast.style.animation = 'slideIn 0.3s ease reverse';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Update sync indicator
        function updateSyncIndicator(status, message = 'Data terkini') {
            const indicator = document.getElementById('syncIndicator');
            if (status === 'checking') {
                indicator.className = 'sync-indicator syncing';
                indicator.innerHTML = `
                    <div class="spinner-border spinner-border-sm" role="status"></div>
                    <span>Checking...</span>
                `;
            } else if (status === 'syncing') {
                indicator.className = 'sync-indicator syncing';
                indicator.innerHTML = `
                    <div class="spinner-border spinner-border-sm" role="status"></div>
                    <span>${message}</span>
                `;
            } else if (status === 'success') {
                indicator.className = 'sync-indicator';
                indicator.innerHTML = `
                    <i class="bi bi-check-circle-fill"></i>
                    <span>${message}</span>
                `;
            } else if (status === 'live') {
                indicator.className = 'sync-indicator';
                indicator.innerHTML = `
                    <i class="bi bi-broadcast"></i>
                    <span>Live (5s)</span>
                `;
            }
        }

        // Check for data updates
        async function checkForUpdates() {
            if (isChecking) return;
            isChecking = true;

            try {
                const currentYear = {{ $year }};
                const url = `/api/sync-status?year=${currentYear}${lastUpdateTimestamp ? '&last_update=' + lastUpdateTimestamp : ''}`;
                
                const response = await fetch(url);
                const data = await response.json();

                if (data.success) {
                    // Update last update timestamp
                    if (!lastUpdateTimestamp) {
                        lastUpdateTimestamp = data.last_update;
                    }

                    // If there are changes, reload the page
                    if (data.has_changes) {
                        consecutiveNoChanges = 0;
                        showToast('🔥 Data baru terdeteksi!', 'success');
                        updateSyncIndicator('syncing', 'Loading...');
                        
                        // Reload page immediately
                        setTimeout(() => {
                            window.location.reload();
                        }, 800);
                    } else {
                        consecutiveNoChanges++;
                        updateSyncIndicator('live', 'Live (5s)');
                    }
                }
            } catch (error) {
                console.error('Error checking for updates:', error);
                updateSyncIndicator('success', 'Offline');
            } finally {
                isChecking = false;
            }
        }

        // Start SUPER FAST auto-checking every 5 seconds!
        setInterval(checkForUpdates, 5000); // Check every 5 seconds (FAST!)
        
        // Initial check after 2 seconds
        setTimeout(() => {
            checkForUpdates();
            updateSyncIndicator('live', 'Live (5s)');
        }, 2000);

        console.log('⚡ SUPER FAST Real-time sync ACTIVE - checking every 5 seconds!');

        // Function to download chart as image
        function downloadChart(chartId, filename) {
            // Get chart instance
            const chartInstance = Chart.getChart(chartId);
            if (!chartInstance) return;
            
            const canvas = document.getElementById(chartId);
            const isPieChart = chartInstance.config.type === 'doughnut';
            
            // Chart titles mapping
            const chartTitles = {
                'customerChart': 'DISTRIBUSI PELANGGAN PER ULP',
                'powerChart': 'DAYA TERSAMBUNG PER ULP (kVA)',
                'kwhChart': 'PENJUALAN ENERGI PER ULP (kWh)',
                'rpChart': 'PENDAPATAN PER ULP (Rp)'
            };
            
            // Create a large temporary canvas for high quality export
            const exportWidth = isPieChart ? 1400 : 1200;
            const exportHeight = isPieChart ? 1400 : 900;
            const padding = isPieChart ? 120 : 100;
            const titleHeight = 100;
            
            const tempCanvas = document.createElement('canvas');
            tempCanvas.width = exportWidth;
            tempCanvas.height = exportHeight;
            const tempCtx = tempCanvas.getContext('2d');
            
            // Fill white background
            tempCtx.fillStyle = '#ffffff';
            tempCtx.fillRect(0, 0, tempCanvas.width, tempCanvas.height);
            
            // Draw title
            tempCtx.fillStyle = '#00897b';
            tempCtx.font = 'bold 28px Arial';
            tempCtx.textAlign = 'center';
            tempCtx.fillText(chartTitles[chartId] || 'CHART', tempCanvas.width / 2, 50);
            
            // Draw subtitle with current month/year
            const monthSelector = document.getElementById('monthSelector');
            const monthText = monthSelector ? monthSelector.options[monthSelector.selectedIndex].text : '';
            const yearSelector = document.getElementById('yearSelector');
            const yearText = yearSelector ? ' ' + yearSelector.value : '';
            tempCtx.fillStyle = '#666666';
            tempCtx.font = '18px Arial';
            tempCtx.fillText(monthText + yearText, tempCanvas.width / 2, 80);
            
            // Calculate chart area dimensions
            const chartWidth = tempCanvas.width - (padding * 2);
            const chartHeight = tempCanvas.height - (padding * 2) - titleHeight;
            
            // Create a temporary hidden canvas for rendering chart at larger size
            const chartCanvas = document.createElement('canvas');
            chartCanvas.width = chartWidth;
            chartCanvas.height = chartHeight;
            chartCanvas.style.display = 'none';
            document.body.appendChild(chartCanvas);
            
            // Clone chart configuration with larger font sizes for export
            const exportConfig = JSON.parse(JSON.stringify(chartInstance.config));
            exportConfig.options.animation = { duration: 0 };
            exportConfig.options.responsive = false;
            exportConfig.options.maintainAspectRatio = false;
            
            // Update font sizes for export
            if (exportConfig.options.plugins.datalabels) {
                exportConfig.options.plugins.datalabels.font = {
                    size: isPieChart ? 14 : 13,
                    weight: 'bold'
                };
                if (isPieChart) {
                    exportConfig.options.plugins.datalabels.offset = 20;
                    exportConfig.options.plugins.datalabels.padding = {
                        top: 10,
                        bottom: 10,
                        left: 14,
                        right: 14
                    };
                }
            }
            
            // Create temporary chart
            const tempChart = new Chart(chartCanvas, exportConfig);
            
            // Wait for chart to render
            setTimeout(() => {
                // Draw the temporary chart onto main canvas
                tempCtx.drawImage(chartCanvas, padding, padding + titleHeight, chartWidth, chartHeight);
                
                // Download
                const url = tempCanvas.toDataURL('image/png');
                const link = document.createElement('a');
                link.download = `${filename}-${new Date().toISOString().split('T')[0]}.png`;
                link.href = url;
                link.click();
                
                // Cleanup
                tempChart.destroy();
                document.body.removeChild(chartCanvas);
            }, 100);
        }

        // Function to download table as Excel
        function downloadTableAsExcel() {
            // Get table data
            const table = document.querySelector('.table');
            let html = '<html><head><meta charset="utf-8"><style>table{border-collapse:collapse;width:100%}th,td{border:1px solid #ddd;padding:8px;text-align:left}th{background-color:#00897b;color:white;font-weight:bold}</style></head><body>';
            html += '<h2>Data Pengusahaan 309 - Ringkasan Data per ULP</h2>';
            html += '<p>Tanggal: ' + new Date().toLocaleDateString('id-ID') + '</p>';
            html += table.outerHTML;
            html += '</body></html>';
            
            const blob = new Blob(['\ufeff' + html], {
                type: 'application/vnd.ms-excel'
            });
            
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `data-pengusahaan-309-${new Date().toISOString().split('T')[0]}.xls`;
            link.click();
            window.URL.revokeObjectURL(url);
        }
    </script>
</body>
</html>

