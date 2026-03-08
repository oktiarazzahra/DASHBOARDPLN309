<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Data - Dashboard PLN 309</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .status-ok { color: green; font-weight: bold; }
        .status-error { color: red; font-weight: bold; }
        .count-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 15px;
            border-left: 4px solid #007bff;
        }
        .count-number {
            font-size: 2em;
            font-weight: bold;
            color: #007bff;
        }
        .table-preview {
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <h1>🔍 Debug Data - Dashboard PLN 309</h1>
                <p class="text-muted">Halaman ini menampilkan data di database untuk troubleshooting tanpa Shell access</p>
                <a href="{{ route('dashboard.index') }}" class="btn btn-secondary mb-3">← Kembali ke Dashboard</a>
                
                <!-- Year Selector -->
                <div class="mb-3">
                    <label>Pilih Tahun:</label>
                    <select class="form-select" onchange="window.location.href='?year='+this.value" style="width: 200px;">
                        @foreach($availableYears as $y)
                            <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>

                <hr>

                <!-- Environment Check -->
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5>📋 Environment Variables</h5>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            @foreach($envCheck as $key => $value)
                            <tr>
                                <td><strong>{{ $key }}</strong></td>
                                <td>{!! $value !!}</td>
                            </tr>
                            @endforeach
                        </table>
                    </div>
                </div>

                <!-- Database File Check -->
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5>💾 Database File Status</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Path:</strong> <code>{{ $dbPath }}</code></p>
                        <p><strong>Exists:</strong> <span class="{{ $dbExists ? 'status-ok' : 'status-error' }}">{{ $dbExists ? 'YES ✅' : 'NO ❌' }}</span></p>
                        <p><strong>Size:</strong> {{ number_format($dbSize / 1024, 2) }} KB</p>
                        <p><strong>Readable:</strong> <span class="{{ $dbReadable ? 'status-ok' : 'status-error' }}">{{ $dbReadable ? 'YES ✅' : 'NO ❌' }}</span></p>
                        <p><strong>Writable:</strong> <span class="{{ $dbWritable ? 'status-ok' : 'status-error' }}">{{ $dbWritable ? 'YES ✅' : 'NO ❌' }}</span></p>
                    </div>
                </div>

                <!-- Data Count Summary -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5>📊 Jumlah Data (Tahun {{ $year }})</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="count-box">
                                    <h6>📈 Customer Data</h6>
                                    <div class="count-number">{{ number_format($customerCount) }}</div>
                                    <small class="text-muted">Total semua tahun: {{ number_format($customerTotal) }}</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="count-box">
                                    <h6>⚡ Power Data</h6>
                                    <div class="count-number">{{ number_format($powerCount) }}</div>
                                    <small class="text-muted">Total semua tahun: {{ number_format($powerTotal) }}</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="count-box">
                                    <h6>💰 Revenue Data</h6>
                                    <div class="count-number">{{ number_format($revenueCount) }}</div>
                                    <small class="text-muted">Total semua tahun: {{ number_format($revenueTotal) }}</small>
                                </div>
                            </div>
                        </div>

                        @if($customerCount == 0 && $powerCount == 0 && $revenueCount == 0)
                        <div class="alert alert-danger mt-3">
                            <strong>⚠️ TIDAK ADA DATA untuk tahun {{ $year }}!</strong><br>
                            Kemungkinan penyebab:<br>
                            1. Google Sheets belum ada data tahun {{ $year }}<br>
                            2. Format Google Sheets salah (harus ada kata "BULANAN" dan tahun "{{ $year }}")<br>
                            3. Sync belum pernah dijalankan<br>
                            4. Sync gagal (cek logs)
                        </div>
                        @else
                        <div class="alert alert-success mt-3">
                            ✅ Data ditemukan untuk tahun {{ $year }}!
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Customer Data by ULP -->
                @if($customerByUlp->count() > 0)
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5>🏢 Data Customer Per ULP (Tahun {{ $year }})</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>ULP Code</th>
                                    <th>ULP Name</th>
                                    <th>Jumlah Record</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customerByUlp as $ulp)
                                <tr>
                                    <td>{{ $ulp->ulp_code }}</td>
                                    <td>{{ $ulp->ulp_name }}</td>
                                    <td><strong>{{ $ulp->count }}</strong> records</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Recent Customer Data Preview -->
                @if($recentCustomers->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h6>👥 10 Customer Data Terbaru (Tahun {{ $year }})</h6>
                    </div>
                    <div class="card-body table-preview">
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
                                @foreach($recentCustomers as $customer)
                                <tr>
                                    <td>{{ $customer->ulp_code }} - {{ $customer->ulp_name }}</td>
                                    <td>{{ $customer->month }}</td>
                                    <td>{{ number_format($customer->total) }}</td>
                                    <td>{{ $customer->updated_at }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Recent Power Data Preview -->
                @if($recentPower->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h6>⚡ 10 Power Data Terbaru (Tahun {{ $year }})</h6>
                    </div>
                    <div class="card-body table-preview">
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
                                @foreach($recentPower as $power)
                                <tr>
                                    <td>{{ $power->ulp_code }} - {{ $power->ulp_name }}</td>
                                    <td>{{ $power->month }}</td>
                                    <td>{{ number_format($power->total) }}</td>
                                    <td>{{ $power->updated_at }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Recent Revenue Data Preview -->
                @if($recentRevenue->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h6>💰 10 Revenue Data Terbaru (Tahun {{ $year }})</h6>
                    </div>
                    <div class="card-body table-preview">
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
                                @foreach($recentRevenue as $revenue)
                                <tr>
                                    <td>{{ $revenue->ulp_code }} - {{ $revenue->ulp_name }}</td>
                                    <td>{{ $revenue->month }}</td>
                                    <td>Rp {{ number_format($revenue->total) }}</td>
                                    <td>{{ $revenue->updated_at }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Troubleshooting Tips -->
                <div class="card mb-4">
                    <div class="card-header bg-dark text-white">
                        <h5>💡 Tips Troubleshooting</h5>
                    </div>
                    <div class="card-body">
                        <h6>Jika data kosong (count = 0):</h6>
                        <ol>
                            <li>Cek Google Sheets harus ada kata <strong>"BULANAN"</strong> di atas tabel</li>
                            <li>Cek ada tahun <strong>"{{ $year }}"</strong> di dekat kata BULANAN</li>
                            <li>Format kolom harus: <code>Kode ULP | Nama ULP | JAN | FEB | MAR ...</code></li>
                            <li>Angka di Google Sheets dalam satuan ribu (50 = 50,000 pelanggan)</li>
                            <li>Pastikan sudah klik tombol Sync di dashboard</li>
                        </ol>

                        <h6 class="mt-3">Jika data ada tapi tidak muncul di grafik:</h6>
                        <ol>
                            <li>Clear browser cache: <kbd>Ctrl</kbd> + <kbd>Shift</kbd> + <kbd>R</kbd></li>
                            <li>Tutup browser sepenuhnya, buka lagi</li>
                            <li>Coba Incognito/Private mode</li>
                            <li>Cek filter tahun di dashboard sudah benar</li>
                        </ol>

                        <div class="alert alert-info mt-3">
                            <strong>✅ Cara refresh halaman ini:</strong> Tekan <kbd>F5</kbd> atau klik tombol refresh browser
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
