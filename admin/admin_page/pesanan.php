<?php
// Pastikan hanya admin yang bisa akses
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php?halaman=login");
    exit();
}

$users = [];
$sql = "SELECT id, nama, username, email, no_telepon, alamat, role, create_akun 
        FROM users 
        where role = 'user'
        ORDER BY create_akun DESC";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $users = $result->fetch_all(MYSQLI_ASSOC);
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Menu - Dapur Aizlan</title>
    <link rel="stylesheet" href="../aset/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../aset/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        .img-thumbnail {
            max-width: 100px;
            height: auto;
        }
        .action-buttons .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            margin-right: 5px;
        }
        .table th {
            white-space: nowrap;
            position: sticky;
            top: 0;
            background-color: #2c3e50;
            z-index: 10;
        }

        .table-responsive {
            max-height: 70vh;
            overflow-y: auto;
        }

        .table {
            margin-bottom: 0;
        }

        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
            font-size: 0.75em;
        }
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
                        <a class="nav-link " href="index.php?page=kelola_user">
                            <i class="bi bi-people"></i> Kelola User
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php?page=pesanan">
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
        
    </div>

    <script src="../aset/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../aset/script.js"></script>
</body>
</html>