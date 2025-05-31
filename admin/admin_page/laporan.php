<?php
// Pastikan hanya admin yang bisa akses
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php?halaman=login");
    exit();
}

// Tangkap parameter filter jika ada
$filter = isset($_GET['filter']) ? $_GET['filter'] : '7hari';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - Dapur Aizlan</title>
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
        
        
        .chart-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .filter-container {
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .data-table {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .badge-pending { background-color: #6c757d; }
        .badge-diproses { background-color:rgb(17, 120, 205); }
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
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar (sama dengan home.php) -->
        <div class="admin-sidebar">
            <div>
                <div class="brand">
                    <h4><i class="bi bi-egg-fried"></i> Dapur Aizlan</h4>
                    <small>Admin Panel</small>
                </div>
                <ul class="nav flex-column sidebar-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=home">
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
                        <a class="nav-link active" href="index.php?page=laporan">
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
            <h2 class="mb-4"><i class="bi bi-graph-up"></i> Laporan</h2>
            
            <!-- Filter -->
            <div class="filter-container">
                <form method="get" action="">
                    <input type="hidden" name="page" value="laporan">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="btn-group" role="group">
                                <button type="submit" name="filter" value="7hari" class="btn btn-<?= $filter == '7hari' ? 'primary' : 'outline-primary' ?>">
                                    7 Hari Terakhir
                                </button>
                                <button type="submit" name="filter" value="1bulan" class="btn btn-<?= $filter == '1bulan' ? 'primary' : 'outline-primary' ?>">
                                    1 Bulan Terakhir
                                </button>
                                <button type="submit" name="filter" value="1tahun" class="btn btn-<?= $filter == '1tahun' ? 'primary' : 'outline-primary' ?>">
                                    1 Tahun Terakhir
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="date" class="form-control" name="custom_date" value="<?= isset($_GET['custom_date']) ? $_GET['custom_date'] : '' ?>">
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Statistik -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-white bg-primary mb-3 h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">Pesanan Aktif</h5>
                            <p class="card-text display-6 mb-auto">
                                <?php
                                $sql = "SELECT COUNT(*) as total FROM orders WHERE status IN ('pending', 'diproses')";
                                if ($filter == '7hari') {
                                    $sql .= " AND tanggal >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                                } elseif ($filter == '1bulan') {
                                    $sql .= " AND tanggal >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
                                } elseif ($filter == '1tahun') {
                                    $sql .= " AND tanggal >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
                                } elseif (isset($_GET['custom_date'])) {
                                    $sql .= " AND DATE(tanggal) = '" . $_GET['custom_date'] . "'";
                                }
                                $result = $conn->query($sql);
                                echo ($result && $result->num_rows > 0) ? $result->fetch_assoc()['total'] : "0";
                                ?>
                            </p>
                            <small class="text-white-50">Menunggu & Diproses</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success mb-3 h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">Pesanan Selesai</h5>
                            <p class="card-text display-6 mb-auto">
                                <?php
                                $sql = "SELECT COUNT(*) as total FROM orders WHERE status = 'selesai'";
                                if ($filter == '7hari') {
                                    $sql .= " AND tanggal >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                                } elseif ($filter == '1bulan') {
                                    $sql .= " AND tanggal >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
                                } elseif ($filter == '1tahun') {
                                    $sql .= " AND tanggal >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
                                } elseif (isset($_GET['custom_date'])) {
                                    $sql .= " AND DATE(tanggal) = '" . $_GET['custom_date'] . "'";
                                }
                                $result = $conn->query($sql);
                                echo ($result && $result->num_rows > 0) ? $result->fetch_assoc()['total'] : "0";
                                ?>
                            </p>
                            <small class="text-white-50">Pesanan berhasil</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-info mb-3 h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">Total Pendapatan</h5>
                            <p class="card-text display-6 mb-auto">
                                Rp <?php
                                $sql = "SELECT SUM(total) as total FROM orders WHERE status = 'selesai'";
                                if ($filter == '7hari') {
                                    $sql .= " AND tanggal >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                                } elseif ($filter == '1bulan') {
                                    $sql .= " AND tanggal >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
                                } elseif ($filter == '1tahun') {
                                    $sql .= " AND tanggal >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
                                } elseif (isset($_GET['custom_date'])) {
                                    $sql .= " AND DATE(tanggal) = '" . $_GET['custom_date'] . "'";
                                }
                                $result = $conn->query($sql);
                                $total = ($result && $result->num_rows > 0) ? ($result->fetch_assoc()['total'] ?? 0) : 0;
                                echo number_format((float)$total, 0, ',', '.');
                                ?>
                            </p>
                            <small class="text-white-50">Dari pesanan selesai</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-warning mb-3 h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">Rata-rata Pesanan</h5>
                            <p class="card-text display-6 mb-auto">
                                Rp <?php
                                $sql = "SELECT AVG(total) as rata FROM orders WHERE status = 'selesai'";
                                if ($filter == '7hari') {
                                    $sql .= " AND tanggal >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                                } elseif ($filter == '1bulan') {
                                    $sql .= " AND tanggal >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
                                } elseif ($filter == '1tahun') {
                                    $sql .= " AND tanggal >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
                                } elseif (isset($_GET['custom_date'])) {
                                    $sql .= " AND DATE(tanggal) = '" . $_GET['custom_date'] . "'";
                                }
                                $result = $conn->query($sql);
                                $rata = ($result && $result->num_rows > 0) ? ($result->fetch_assoc()['rata'] ?? 0) : 0;
                                echo number_format((float)$rata, 0, ',', '.');
                                ?>
                            </p>
                            <small class="text-white-50">Per pesanan selesai</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Grafik Pendapatan -->
            <div class="chart-container mb-4">
                <h5><i class="bi bi-graph-up"></i> Grafik Pendapatan</h5>
                <canvas id="revenueChart" height="100"></canvas>
            </div>
            
            <!-- Grafik Status Pesanan -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="chart-container">
                        <h5><i class="bi bi-pie-chart"></i> Status Pesanan</h5>
                        <canvas id="orderStatusChart" height="250"></canvas>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="chart-container">
                        <h5><i class="bi bi-bar-chart"></i> Metode Pembayaran</h5>
                        <canvas id="paymentMethodChart" height="250"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Tabel Data Pesanan -->
            <div class="data-table">
                <h5 class="mb-4"><i class="bi bi-table"></i> Data Pesanan</h5>
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
                            </tr>
                        </thead>
                        <tbody>
                         <?php
                                $sql = "SELECT o.id, u.username, o.tanggal, o.total, o.status, o.payment_method 
                                        FROM orders o
                                        JOIN users u ON o.user_id = u.id";

                                if ($filter == '7hari') {
                                    $sql .= " WHERE o.tanggal >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                                } elseif ($filter == '1bulan') {
                                    $sql .= " WHERE o.tanggal >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
                                } elseif ($filter == '1tahun') {
                                    $sql .= " WHERE o.tanggal >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
                                } elseif (isset($_GET['custom_date'])) {
                                    $sql .= " WHERE DATE(o.tanggal) = '" . $_GET['custom_date'] . "'";
                                }

                                $sql .= " ORDER BY o.tanggal DESC";

                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        // Ubah teks status sebelum ditampilkan
                                        $status_text = $row['status'];
                                        if ($status_text == 'pending') {
                                            $status_text = 'menunggu';
                                        }
                                        
                                        $status_class = 'badge-' . strtolower($row['status']);
                                        echo '<tr>
                                                <td>#' . $row['id'] . '</td>
                                                <td>' . htmlspecialchars($row['username']) . '</td>
                                                <td>' . date('d M Y H:i', strtotime($row['tanggal'])) . '</td>
                                                <td>Rp ' . number_format($row['total'], 0, ',', '.') . '</td>
                                                <td><span class="badge ' . $status_class . '">' . ucfirst($status_text) . '</span></td>
                                                <td>' . htmlspecialchars($row['payment_method']) . '</td>
                                            </tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="6" class="text-center">Tidak ada pesanan</td></tr>';
                                }
                                ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Fungsi untuk menghasilkan label berdasarkan filter
        function generateLabels() {
            let labels = [];
            const now = new Date();
            
            <?php if ($filter == '7hari'): ?>
                for (let i = 6; i >= 0; i--) {
                    const date = new Date();
                    date.setDate(date.getDate() - i);
                    labels.push(date.toLocaleDateString('id-ID', {day: 'numeric', month: 'short'}));
                }
            <?php elseif ($filter == '1bulan'): ?>
                for (let i = 3; i >= 0; i--) {
                    const date = new Date();
                    date.setDate(date.getDate() - (i * 7));
                    labels.push('Minggu ' + (4 - i));
                }
            <?php elseif ($filter == '1tahun'): ?>
                for (let i = 11; i >= 0; i--) {
                    const date = new Date();
                    date.setMonth(date.getMonth() - i);
                    labels.push(date.toLocaleDateString('id-ID', {month: 'long'}));
                }
            <?php else: ?>
                // Untuk custom date, tampilkan jam dalam sehari
                for (let i = 0; i < 24; i++) {
                    labels.push(i + ':00');
                }
            <?php endif; ?>
            
            return labels;
        }
        
        // Grafik Pendapatan
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: generateLabels(),
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: [
                        <?php
                        if ($filter == '7hari') {
                            for ($i = 6; $i >= 0; $i--) {
                                $date = date('Y-m-d', strtotime("-$i days"));
                                $sql = "SELECT COALESCE(SUM(total), 0) as total 
                                        FROM orders 
                                        WHERE DATE(tanggal) = '$date' AND status = 'selesai'";
                                $result = $conn->query($sql);
                                $row = $result ? $result->fetch_assoc() : ['total' => 0];
                                echo $row['total'] . ',';
                            }
                        } elseif ($filter == '1bulan') {
                            for ($i = 3; $i >= 0; $i--) {
                                $start = date('Y-m-d', strtotime("-" . ($i+1) . " weeks +1 day"));
                                $end = date('Y-m-d', strtotime("-$i weeks"));
                                $sql = "SELECT COALESCE(SUM(total), 0) as total 
                                        FROM orders 
                                        WHERE tanggal BETWEEN '$start' AND '$end' AND status = 'selesai'";
                                $result = $conn->query($sql);
                                $row = $result ? $result->fetch_assoc() : ['total' => 0];
                                echo $row['total'] . ',';
                            }
                        } elseif ($filter == '1tahun') {
                            for ($i = 11; $i >= 0; $i--) {
                                $month = date('Y-m', strtotime("-$i months"));
                                $sql = "SELECT COALESCE(SUM(total), 0) as total 
                                        FROM orders 
                                        WHERE DATE_FORMAT(tanggal, '%Y-%m') = '$month' AND status = 'selesai'";
                                $result = $conn->query($sql);
                                $row = $result ? $result->fetch_assoc() : ['total' => 0];
                                echo $row['total'] . ',';
                            }
                        } elseif (isset($_GET['custom_date'])) {
                            for ($i = 0; $i < 24; $i++) {
                                $hour = str_pad($i, 2, '0', STR_PAD_LEFT);
                                $sql = "SELECT COALESCE(SUM(total), 0) as total 
                                        FROM orders 
                                        WHERE DATE(tanggal) = '" . $_GET['custom_date'] . "' 
                                        AND HOUR(tanggal) = $hour AND status = 'selesai'";
                                $result = $conn->query($sql);
                                $row = $result ? $result->fetch_assoc() : ['total' => 0];
                                echo $row['total'] . ',';
                            }
                        }
                        ?>
                    ],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
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
                labels: ['Menunggu', 'Diproses', 'Selesai', 'Dibatalkan'],
                datasets: [{
                    data: [
                        <?php
                        $statuses = ['pending', 'diproses', 'selesai', 'dibatalkan'];
                        foreach ($statuses as $status) {
                            $sql = "SELECT COUNT(*) as total FROM orders WHERE status = '$status'";
                            if ($filter == '7hari') {
                                $sql .= " AND tanggal >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                            } elseif ($filter == '1bulan') {
                                $sql .= " AND tanggal >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
                            } elseif ($filter == '1tahun') {
                                $sql .= " AND tanggal >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
                            } elseif (isset($_GET['custom_date'])) {
                                $sql .= " AND DATE(tanggal) = '" . $_GET['custom_date'] . "'";
                            }
                            $result = $conn->query($sql);
                            $row = $result ? $result->fetch_assoc() : ['total' => 0];
                            echo $row['total'] . ',';
                        }
                        ?>
                    ],
                    backgroundColor: [
                        '#6c757d', // pending
                        'rgb(17, 120, 205)', // diproses
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
        
        // Grafik Metode Pembayaran
        const paymentCtx = document.getElementById('paymentMethodChart').getContext('2d');
        const paymentChart = new Chart(paymentCtx, {
            type: 'bar',
            data: {
                labels: [
                    <?php
                    $sql = "SELECT DISTINCT payment_method FROM orders";
                    $result = $conn->query($sql);
                    while ($row = $result->fetch_assoc()) {
                        echo "'" . htmlspecialchars($row['payment_method']) . "',";
                    }
                    ?>
                ],
                datasets: [{
                    label: 'Jumlah Pesanan',
                    data: [
                        <?php
                        $sql = "SELECT payment_method, COUNT(*) as total FROM orders";
                        if ($filter == '7hari') {
                            $sql .= " WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                        } elseif ($filter == '1bulan') {
                            $sql .= " WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
                        } elseif ($filter == '1tahun') {
                            $sql .= " WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
                        } elseif (isset($_GET['custom_date'])) {
                            $sql .= " WHERE DATE(tanggal) = '" . $_GET['custom_date'] . "'";
                        }
                        $sql .= " GROUP BY payment_method";
                        $result = $conn->query($sql);
                        while ($row = $result->fetch_assoc()) {
                            echo $row['total'] . ',';
                        }
                        ?>
                    ],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
    
    <script src="../aset/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>