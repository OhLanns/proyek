<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Dashboard Penjualan</title>
    <style>
        
        :root {
            --tokoku-orange: #ee4d2d;
            --tokoku-light-orange: #fff8e6;
            --tokoku-blue: #f0f7ff;
            --tokoku-gray: #f5f5f5;
            --tokoku-dark-gray: #666;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f9f9f9;
            color: #333;
        }
        
        .dashboard-container {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        /* Sidebar Styles */
        .sidebar {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
        }
        
        .sidebar-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
            color: var(--tokoku-orange);
        }
        
        .nav-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .nav-item {
            padding: 12px 15px;
            margin-bottom: 5px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .nav-item:hover {
            background-color: var(--tokoku-gray);
        }
        
        .nav-item.active {
            background-color: var(--tokoku-light-orange);
            color: var(--tokoku-orange);
            font-weight: bold;
        }
        
        /* Main Content Styles */
        .main-content {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .header {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
        }
        
        .page-title {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }
        
        /* Stats Section */
        .stats-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .stats-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
        }
        
        .stats-title {
            font-size: 18px;
            font-weight: bold;
            margin-top: 0;
            margin-bottom: 15px;
            color: var(--tokoku-dark-gray);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        
        .stat-item {
            text-align: center;
            padding: 15px;
            background-color: var(--tokoku-gray);
            border-radius: 8px;
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: var(--tokoku-orange);
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 14px;
            color: var(--tokoku-dark-gray);
        }
        
        /* Revenue Card */
        .revenue-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            text-align: center;
            margin-bottom: -260px;
        }
        
        .revenue-amount {
            font-size: 28px;
            font-weight: bold;
            color: var(--tokoku-orange);
            margin: 10px 0;
        }
        
        .revenue-change {
            font-size: 16px;
            color: var(--tokoku-dark-gray);
        }
        
        /* Products & Promotions Section */
        .content-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .content-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            min-height: 200px;
        }
        
        .content-title {
            font-size: 18px;
            font-weight: bold;
            margin-top: 0;
            margin-bottom: 15px;
            color: var(--tokoku-orange);
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-title">Dashboard Penjual</div>
            <ul class="nav-menu">
                <li class="nav-item active">Kunjungi Menu</li>
                <li class="nav-item">Dashboard</li>
                <li class="nav-item">Produk</li>
                <li class="nav-item">Keuangan</li>
                <li class="nav-item">Performa Toko</li>
                <li class="nav-item">Pusat Bantuan</li>
                <li class="nav-item">Pengaturan</li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1 class="page-title">Dashboard Penjual</h1>
            </div>
            
            <div class="stats-section">
                <div class="stats-card">
                    <h2 class="stats-title">Statistik Toko</h2>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-number">0</div>
                            <div class="stat-label">Pesanan Masuk</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">0</div>
                            <div class="stat-label">Perlu Diproses</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">0</div>
                            <div class="stat-label">Perlu Konfirmasi Pembayaran</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">0</div>
                            <div class="stat-label">Penilaian Perlu Dibalas</div>
                        </div>
                    </div>
                </div>
                
                <div class="revenue-card">
                    <h2 class="stats-title">Pendapatan</h2>
                    <div class="revenue-amount">Rp 1.250.000</div>
                    <div class="revenue-change">+15% dari kemarin</div>
                <div style="height: 300px;">
                    <canvas id="revenueChart"></canvas>
                </div>
                </div>
                
            </div>
            
            <div class="content-section">
                <div class="content-card">
                    <h2 class="content-title">Produk</h2>
                    <!-- Konten produk akan ditampilkan di sini -->
                </div>
                
               
            </div>
        </div>
    </div>
    <script>
        // Inisialisasi grafik setelah halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('revenueChart').getContext('2d');
            
            const revenueChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                    datasets: [{
                        label: 'Pendapatan Harian (Rp)',
                        data: [800000, 950000, 1100000, 1050000, 1200000, 1250000, 1300000],
                        backgroundColor: 'rgba(238, 77, 45, 0.2)',
                        borderColor: 'rgba(238, 77, 45, 1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Rp ' + context.raw.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>