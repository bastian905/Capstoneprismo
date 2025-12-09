<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRISMO - History Transaksi</title>
    <link rel="stylesheet" href="{{ asset('css/history.css') }}">
    <link rel="preload" href="/images/logo.png" as="image">
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="header__content">
                <div class="header__left">
                    <div class="header__brand">
                        <img src="{{ asset('images/logo.png') }}" alt="PRISMO" class="logo" width="120" height="40">
                    </div>
                </div>

                <div class="header__center">
                    <h1 class="header__title">Laporan Keuangan</h1>
                </div>

                <div class="user-menu">
                    <button class="btn btn--back" onclick="window.history.back()">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" style="margin-right: 8px;">
                            <path d="M10.707 2.293a1 1 0 010 1.414L6.414 8l4.293 4.293a1 1 0 01-1.414 1.414l-5-5a1 1 0 010-1.414l5-5a1 1 0 011.414 0z"/>
                        </svg>
                        Kembali
                    </button>
                </div>
            </div>
        </header>

        <main class="main">
            <!-- Konten Laporan Keuangan -->
            <div class="laporan-container">
                
                <div class="laporan-controls">
                    <div class="control-group">
                        <!-- Desktop View Selector -->
                        <div class="view-selector desktop-view">
                            <button class="view-btn active" data-view="daily">Harian</button>
                            <button class="view-btn" data-view="weekly">Mingguan</button>
                            <button class="view-btn" data-view="monthly">Bulanan</button>
                            <button class="view-btn" data-view="yearly">Tahunan</button>
                        </div>
                        
                        <!-- Mobile View Selector -->
                        <div class="view-selector-mobile mobile-view">
                            <select class="view-dropdown" id="viewDropdown">
                                <option value="daily">Harian</option>
                                <option value="weekly">Mingguan</option>
                                <option value="monthly">Bulanan</option>
                                <option value="yearly">Tahunan</option>
                            </select>
                        </div>
                        
                        <!-- Controls Row untuk Desktop: Filter + Export sejajar -->
                        <div class="controls-row" id="controlsRow">
                            <div class="date-filter" id="dateFilter">
                                <input type="date" id="filter-date">
                                <button id="filter-btn">Filter Tanggal</button>
                            </div>
                            
                            <div class="export-container">
                                <button class="export-btn">Export</button>
                                <div class="export-dropdown">
                                    <button id="export-pdf">Export PDF</button>
                                    <button id="export-excel">Export Excel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tampilan Harian -->
                <div class="daily-view laporan-view">
                    <div class="summary">
                        <div class="summary-item">
                            <h3>Total Transaksi</h3>
                            <p id="total-transactions">4</p>
                        </div>
                        <div class="summary-item">
                            <h3>Total Pendapatan</h3>
                            <p class="total-amount" id="total-income">Rp 700.000</p>
                        </div>
                    </div>
                    
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Jenis Layanan</th>
                                    <th>Tipe</th>
                                    <th>Plat Nomor</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody id="transaction-table">
                                <tr>
                                    <td><span class="service-type">Cuci Hidrolik + Vakum</span></td>
                                    <td>Avanza</td>
                                    <td>B 1234 TRE</td>
                                    <td class="amount">Rp 60.000</td>
                                </tr>
                                <tr>
                                    <td><span class="service-type">Cuci Hidrolik</span></td>
                                    <td>Wuling</td>
                                    <td>B 1874 UTZ</td>
                                    <td class="amount">Rp 40.000</td>
                                </tr>
                                <tr>
                                    <td><span class="service-type">Detailing</span></td>
                                    <td>Ayla</td>
                                    <td>B 0212 NRH</td>
                                    <td class="amount">Rp 450.000</td>
                                </tr>
                                <tr>
                                    <td><span class="service-type">Cuci + Fogging</span></td>
                                    <td>Pajero</td>
                                    <td>B 9024 BTR</td>
                                    <td class="amount">Rp 60.000</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Tampilan Mingguan -->
                <div class="weekly-view laporan-view">
                    <div class="summary">
                        <div class="summary-item">
                            <h3>Total Transaksi</h3>
                            <p id="weekly-transactions">16</p>
                        </div>
                        <div class="summary-item">
                            <h3>Total Pendapatan</h3>
                            <p class="total-amount" id="weekly-income">Rp 2.550.000</p>
                        </div>
                    </div>
                    
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Minggu</th>
                                    <th>Total Transaksi</th>
                                    <th>Total Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody id="weekly-table">
                                <tr>
                                    <td>Minggu 1 Feb 2025</td>
                                    <td>4</td>
                                    <td class="amount">Rp 700.000</td>
                                </tr>
                                <tr>
                                    <td>Minggu 4 Jan 2025</td>
                                    <td>5</td>
                                    <td class="amount">Rp 850.000</td>
                                </tr>
                                <tr>
                                    <td>Minggu 3 Jan 2025</td>
                                    <td>7</td>
                                    <td class="amount">Rp 1.000.000</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Tampilan Bulanan -->
                <div class="monthly-view laporan-view">
                    <div class="summary">
                        <div class="summary-item">
                            <h3>Total Transaksi</h3>
                            <p id="monthly-transactions">16</p>
                        </div>
                        <div class="summary-item">
                            <h3>Total Pendapatan</h3>
                            <p class="total-amount" id="monthly-income">Rp 2.550.000</p>
                        </div>
                    </div>
                    
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Bulan</th>
                                    <th>Total Transaksi</th>
                                    <th>Total Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody id="monthly-table">
                                <tr>
                                    <td>Februari 2025</td>
                                    <td>4</td>
                                    <td class="amount">Rp 700.000</td>
                                </tr>
                                <tr>
                                    <td>Januari 2025</td>
                                    <td>12</td>
                                    <td class="amount">Rp 1.850.000</td>
                                </tr>
                                <tr>
                                    <td>Desember 2024</td>
                                    <td>15</td>
                                    <td class="amount">Rp 2.200.000</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Tampilan Tahunan -->
                <div class="yearly-view laporan-view">
                    <div class="summary">
                        <div class="summary-item">
                            <h3>Total Transaksi</h3>
                            <p id="yearly-transactions">16</p>
                        </div>
                        <div class="summary-item">
                            <h3>Total Pendapatan</h3>
                            <p class="total-amount" id="yearly-income">Rp 2.550.000</p>
                        </div>
                    </div>
                    
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Tahun</th>
                                    <th>Total Transaksi</th>
                                    <th>Total Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody id="yearly-table">
                                <tr>
                                    <td>2025</td>
                                    <td>16</td>
                                    <td class="amount">Rp 2.550.000</td>
                                </tr>
                                <tr>
                                    <td>2024</td>
                                    <td>120</td>
                                    <td class="amount">Rp 18.500.000</td>
                                </tr>
                                <tr>
                                    <td>2023</td>
                                    <td>98</td>
                                    <td class="amount">Rp 15.200.000</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <footer class="laporan-footer">
                    <p>Laporan dibuat secara otomatis</p>
                </footer>
            </div>
        </main>
    </div>

    <script>
        // Inject real data from server - NO MORE MOCK DATA
        window.withdrawalHistory = @json($withdrawalHistory);
        window.earningsHistory = @json($earningsHistory);
    </script>
    <script src="{{ asset('js/history.js') }}"></script>
</body>
</html>
