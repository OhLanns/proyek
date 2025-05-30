<?php
// Pastikan hanya admin yang bisa akses
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php?halaman=login");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Dapur Aizlan</title>
    <link rel="stylesheet" href="../aset/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../aset/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body{
            background-color: #f8f9fa;
        }
        .admin-sidebar {
            background-color: var(--primary);
            color: white;
            height: 100vh;
            position: fixed;
            width: 250px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .sidebar-menu {
            flex: 1;
            overflow-y: auto;
            padding-top: 20px;
        }
        
        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        
        .admin-sidebar .brand {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }
        
        .admin-sidebar .nav-link {
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            margin: 5px 10px;
            transition: all 0.3s ease;
        }
        
        .admin-sidebar .nav-link:hover, 
        .admin-sidebar .nav-link.active {
            background-color: rgba(255,255,255,0.1);
        }
        
        .admin-sidebar .nav-link i {
            margin-right: 10px;
        }
        
        .admin-main {
            margin-left: 250px;
            padding: 20px;
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            border-radius: 10px;
            padding: 20px;
            color: white;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }
        
        .stat-icon {
            font-size: 2rem;
            margin-bottom: 15px;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 1rem;
            opacity: 0.9;
        }
        
        .card-orders { background: linear-gradient(135deg, #ff7676, #f54ea2); }
        .card-users { background: linear-gradient(135deg, #17ead9, #6078ea); }
        .card-menu { background: linear-gradient(135deg, #fbc531, #e84118); }
        .card-revenue { background: linear-gradient(135deg, #42e695, #3bb2b8); }
        
        .chart-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            height: 100%;
        }
        
        .chart-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .chart-main {
            flex: 2;
            min-width: 300px;
        }
        
        .chart-side {
            flex: 1;
            min-width: 250px;
        }
        
        .recent-orders {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .table th {
            border-top: none;
        }
        
        .badge-pending { background-color: #6c757d; }
        .badge-diproses { background-color: #fd7e14; }
        .badge-selesai { background-color: #28a745; }
        .badge-dibatalkan { background-color: #dc3545; }
        
        @media (max-width: 768px) {
            .admin-sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            
            .admin-main {
                margin-left: 0;
            }
            
            .chart-main, .chart-side {
                flex: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="admin-sidebar">
            <div>
                <div class="brand">
                    <h4><i class="bi bi-egg-fried"></i> Dapur Aizlan</h4>
                    <small>Admin Panel</small>
                </div>
                <ul class="nav flex-column sidebar-menu">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php?page=home">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=menu">
                            <i class="bi bi-book"></i> Kelola Menu
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=kelola_user">
                            <i class="bi bi-people"></i> Kelola User
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=pesanan">
                            <i class="bi bi-receipt"></i> Pesanan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-graph-up"></i> Laporan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php?halaman=logout">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="admin-main">
            <h2 class="mb-4"><i class="bi bi-speedometer2"></i> Dashboard Admin</h2>
            
            <!-- Statistik -->
            <div class="stats-container">
                <div class="stat-card card-orders">
                    <div class="stat-content">
                        <i class="bi bi-cart stat-icon"></i>
                        <div class="stat-number">
                            <?php
                            $sql = "SELECT COUNT(*) as total FROM orders";
                            $result = $conn->query($sql);
                            echo ($result && $result->num_rows > 0) ? $result->fetch_assoc()['total'] : "0";
                            ?>
                        </div>
                        <div class="stat-label">Total Pesanan</div>
                    </div>
                </div>
                
                <div class="stat-card card-users">
                    <div class="stat-content">
                        <i class="bi bi-people stat-icon"></i>
                        <div class="stat-number">
                            <?php
                            $sql = "SELECT COUNT(*) as total FROM users where role= 'user'";
                            $result = $conn->query($sql);
                            echo ($result && $result->num_rows > 0) ? $result->fetch_assoc()['total'] : "0";
                            ?>
                        </div>
                        <div class="stat-label">User Terdaftar</div>
                    </div>
                </div>
                
                <div class="stat-card card-menu">
                    <div class="stat-content">
                        <i class="bi bi-book stat-icon"></i>
                        <div class="stat-number">
                            <?php
                            $sql = "SELECT COUNT(*) as total FROM menu";
                            $result = $conn->query($sql);
                            echo ($result && $result->num_rows > 0) ? $result->fetch_assoc()['total'] : "0";
                            ?>
                        </div>
                        <div class="stat-label">Menu Tersedia</div>
                    </div>
                </div>
                
                <div class="stat-card card-revenue">
                    <div class="stat-content">
                        <i class="bi bi-cash-stack stat-icon"></i>
                        <div class="stat-number">
                            Rp <?php
                            $sql = "SELECT SUM(total) as total FROM orders WHERE status = 'selesai'";
                            $result = $conn->query($sql);
                            $total = ($result && $result->num_rows > 0) ? ($result->fetch_assoc()['total'] ?? 0) : 0;
                            echo number_format((float)$total, 0, ',', '.');
                            ?>
                        </div>
                        <div class="stat-label">Total Pendapatan</div>
                    </div>
                </div>

            </div>

            <!-- Grafik -->
            <div class="chart-row">
                <div class="chart-main">
                    <div class="chart-container">
                        <h5><i class="bi bi-graph-up"></i> Pendapatan 7 Hari Terakhir</h5>
                        <canvas id="revenueChart" height="250"></canvas>
                    </div>
                </div>
                <div class="chart-side">
                    <div class="chart-container">
                        <h5><i class="bi bi-pie-chart"></i> Status Pesanan</h5>
                        <canvas id="orderStatusChart" height="250"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Pesanan Terbaru -->
            <div class="recent-orders">
                <h5 class="mb-4"><i class="bi bi-clock-history"></i> Pesanan Terbaru</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID Pesanan</th>
                                <th>Pelanggan</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Metode Pembayaran</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT o.id, u.username, o.tanggal, o.total, o.status, o.payment_method 
                                    FROM orders o
                                    JOIN users u ON o.user_id = u.id
                                    ORDER BY o.tanggal DESC LIMIT 5";
                            $result = $conn->query($sql);
                            
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    $status_class = 'badge-' . strtolower($row['status']);
                                    echo '<tr>
                                            <td>#' . $row['id'] . '</td>
                                            <td>' . htmlspecialchars($row['username']) . '</td>
                                            <td>' . date('d M Y H:i', strtotime($row['tanggal'])) . '</td>
                                            <td>Rp ' . number_format($row['total'], 0, ',', '.') . '</td>
                                            <td><span class="badge ' . $status_class . '">' . ucfirst($row['status']) . '</span></td>
                                            <td>' . htmlspecialchars($row['payment_method']) . '</td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-outline-primary">Detail</a>
                                            </td>
                                          </tr>';
                                }
                            } else {
                                echo '<tr><td colspan="7" class="text-center">Tidak ada pesanan</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Grafik Pendapatan
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: [
                    <?php
                    for ($i = 6; $i >= 0; $i--) {
                        $date = date('d M', strtotime("-$i days"));
                        echo "'$date',";
                    }
                    ?>
                ],
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: [
                        <?php
                        for ($i = 6; $i >= 0; $i--) {
                            $date = date('Y-m-d', strtotime("-$i days"));
                            $sql = "SELECT COALESCE(SUM(total), 0) as total 
                                    FROM orders 
                                    WHERE DATE(tanggal) = '$date' AND status = 'selesai'";
                            $result = $conn->query($sql);
                            $row = $result ? $result->fetch_assoc() : ['total' => 0];
                            echo $row['total'] . ',';
                        }
                        ?>
                    ],
                    backgroundColor: 'rgba(238, 77, 45, 0.1)',
                    borderColor: 'rgba(238, 77, 45, 1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
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
        
        // Grafik Status Pesanan
        const statusCtx = document.getElementById('orderStatusChart').getContext('2d');
        const statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Diproses', 'Selesai', 'Dibatalkan'],
                datasets: [{
                    data: [
                        <?php
                        $statuses = ['pending', 'diproses', 'selesai', 'dibatalkan'];
                        foreach ($statuses as $status) {
                            $sql = "SELECT COUNT(*) as total FROM orders WHERE status = '$status'";
                            $result = $conn->query($sql);
                            $row = $result ? $result->fetch_assoc() : ['total' => 0];
                            echo $row['total'] . ',';
                        }
                        ?>
                    ],
                    backgroundColor: [
                        '#6c757d', // pending
                        '#fd7e14', // diproses
                        '#28a745', // selesai
                        '#dc3545'  // dibatalkan
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    </script>
    
    <script src="../aset/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>