<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="../aset/style2.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Dashboard Penjualan</title>
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