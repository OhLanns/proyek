<?php
// Pastikan hanya admin yang bisa akses
if (!isset($_SESSION['logged_in'])) {
    header("Location: ../index.php?halaman=login");
    exit();
}

// Get all orders with user information
$sql = "SELECT o.*, u.nama as customer_name, u.no_telepon as customer_phone 
        FROM orders o
        JOIN users u ON o.user_id = u.id
        ORDER BY o.tanggal DESC";
$result = $conn->query($sql);
$orders = [];
if ($result && $result->num_rows > 0) {
    $orders = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesanan - Dapur Aizlan</title>
    <link rel="stylesheet" href="../aset/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../aset/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
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
            color: white;
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
        
        body {
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
        
        /* Status Badges */
        .badge-pending {
            background-color: #6c757d;
        }
        .badge-processing {
            background-color: #0d6efd;
        }
        .badge-completed {
            background-color: #198754;
        }
        .badge-cancelled {
            background-color: #dc3545;
        }
        
        /* Address Box */
        .address-box {
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            margin-top: 0.5rem;
            padding: 0.75rem;
            background-color: #f8f9fa;
        }
        
        /* Toast Notification */
        .toast-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1100;
        }
        
        .toast {
            transition: opacity 0.3s ease;
            margin-bottom: 10px;
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
                        <a class="nav-link" href="index.php?page=kelola_user">
                            <i class="bi bi-people"></i> Kelola User
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php?page=pesanan">
                            <i class="bi bi-receipt"></i> Pesanan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=laporan">
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
        <div class="admin-main w-100">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="bi bi-receipt"></i> Kelola Pesanan</h2>
                </div>
                
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID Pesanan</th>
                                        <th>Tanggal</th>
                                        <th>Pelanggan</th>
                                        <th>Total</th>
                                        <th>Metode Pembayaran</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($orders) > 0): ?>
                                        <?php foreach ($orders as $order): 
                                            $orderDate = new DateTime($order['tanggal']);
                                            $statusClass = '';
                                            $statusText = '';
                                            
                                            switch ($order['status']) {
                                                case 'pending':
                                                    $statusClass = 'badge-pending';
                                                    $statusText = 'Menunggu';
                                                    break;
                                                case 'diproses':
                                                    $statusClass = 'badge-processing';
                                                    $statusText = 'Diproses';
                                                    break;
                                                case 'selesai':
                                                    $statusClass = 'badge-completed';
                                                    $statusText = 'Selesai';
                                                    break;
                                                case 'dibatalkan':
                                                    $statusClass = 'badge-cancelled';
                                                    $statusText = 'Dibatalkan';
                                                    break;
                                            }
                                        ?>
                                            <tr>
                                                <td>#<?= $order['id'] ?></td>
                                                <td><?= $orderDate->format('d M Y H:i') ?></td>
                                                <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                                <td>Rp <?= number_format($order['total'], 0, ',', '.') ?></td>
                                                <td><?= htmlspecialchars($order['payment_method']) ?></td>
                                                <td>
                                                    <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                                                </td>
                                                <td class="action-buttons">
                                                    <button class="btn btn-sm btn-outline-primary view-detail" 
                                                            data-id="<?= $order['id'] ?>"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#orderDetailModal" style="min-width:120px;margin-bottom:5px;">
                                                        <i class="bi bi-eye"></i> <span>Detail</span>
                                                    </button>
                                                    <div class="dropdown d-inline-block">
                                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                                type="button" 
                                                                data-bs-toggle="dropdown" 
                                                                aria-expanded="false"
                                                                style="min-width:120px;">
                                                            <i class="bi bi-gear"></i> <span>Status</span>
                                                        </button>
                                                            <ul class="dropdown-menu">
                                                                <li>
                                                                    <a class="dropdown-item update-status" 
                                                                    href="#" 
                                                                    data-id="<?= $order['id'] ?>" 
                                                                    data-status="pending">
                                                                        <span class="badge bg-secondary me-2">●</span> Menunggu
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item update-status" 
                                                                    href="#" 
                                                                    data-id="<?= $order['id'] ?>" 
                                                                    data-status="diproses">
                                                                        <span class="badge bg-primary me-2">●</span> Diproses
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item update-status" 
                                                                    href="#" 
                                                                    data-id="<?= $order['id'] ?>" 
                                                                    data-status="selesai">
                                                                        <span class="badge bg-success me-2">●</span> Selesai
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">
                                                Belum ada pesanan
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Detail Modal -->
    <div class="modal fade" id="orderDetailModal" tabindex="-1" aria-labelledby="orderDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderDetailModalLabel">Detail Pesanan #<span id="detailOrderId"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Informasi Pelanggan</h6>
                            <p><strong>Nama:</strong> <span id="detailCustomerName"></span></p>
                            <p><strong>Telepon:</strong> <span id="detailCustomerPhone"></span></p>
                            <div id="addressInfo">
                                <!-- Alamat akan dimuat dinamis disini -->
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>Informasi Pesanan</h6>
                            <p><strong>Tanggal:</strong> <span id="detailOrderDate"></span></p>
                            <p><strong>Status:</strong> <span id="detailOrderStatus" class="badge"></span></p>
                            <p><strong>Metode Penerimaan:</strong> <span id="detailPenerimaanMethod"></span></p>
                            <div id="deliveryDateContainer">
                                <p><strong>Tanggal Pengambilan/Pengiriman:</strong> <span id="detailDeliveryDate"></span></p>
                            </div>
                            <p><strong>Catatan:</strong> <span id="detailOrderNotes"></span></p>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Pembayaran</h6>
                            <p><strong>Metode:</strong> <span id="detailPaymentMethod"></span></p>
                            <p><strong>Total:</strong> <span id="detailOrderTotal"></span></p>
                        </div>
                        <div class="col-md-6">
                            <div id="paymentProofContainer" class="mt-2">
                                <strong>Bukti Pembayaran:</strong>
                                <div id="paymentProofImage" class="mt-2"></div>
                            </div>
                        </div>
                    </div>
                    
                    <h6>Item Pesanan</h6>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="orderItemsTable">
                                <!-- Order items will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toastContainer" class="toast-container"></div>

    <script src="../aset/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
    // Function to show toast notification
    function showToast(message, type = 'success') {
        const toastContainer = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        toast.className = `toast show align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;
        
        toastContainer.appendChild(toast);
        
        // Remove toast after 5 seconds
        setTimeout(() => {
            toast.remove();
        }, 5000);
    }

    // Function to load order details
    function loadOrderDetail(orderId) {
        fetch(`admin_page/get_order_detail.php?order_id=${orderId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Populate modal with order details
                    document.getElementById('detailOrderId').textContent = data.order.id;
                    
                    const orderDate = new Date(data.order.tanggal);
                    document.getElementById('detailOrderDate').textContent = orderDate.toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    
                    // Customer info
                    document.getElementById('detailCustomerName').textContent = data.order.customer_name;
                    document.getElementById('detailCustomerPhone').textContent = data.order.customer_phone || '-';
                    
                    // Set status badge
                    const statusBadge = document.getElementById('detailOrderStatus');
                    statusBadge.textContent = data.order.status === 'pending' ? 'Menunggu' : 
                                           data.order.status === 'diproses' ? 'Diproses' :
                                           data.order.status === 'selesai' ? 'Selesai' : 'Dibatalkan';
                    
                    let badgeClass = 'bg-secondary';
                    if (data.order.status === 'selesai') badgeClass = 'bg-success';
                    if (data.order.status === 'diproses') badgeClass = 'bg-primary';
                    if (data.order.status === 'dibatalkan') badgeClass = 'bg-danger';
                    statusBadge.className = `badge ${badgeClass}`;
                    
                    // Payment info
                    document.getElementById('detailPaymentMethod').textContent = data.order.payment_method;
                    document.getElementById('detailOrderTotal').textContent = `Rp ${data.order.total.toLocaleString('id-ID')}`;
                    
                    // Penerimaan method
                    const penerimaanMethodText = data.order.penerimaanMethod === 'ambil_di_tempat' ? 'Ambil di Tempat' : 'Diantar';
                    document.getElementById('detailPenerimaanMethod').textContent = penerimaanMethodText;
                    
                    // Delivery date
                    const deliveryDateContainer = document.getElementById('deliveryDateContainer');
                    const detailDeliveryDate = document.getElementById('detailDeliveryDate');
                    if (data.order.tanggal_diambil_dikirim) {
                        const deliveryDate = new Date(data.order.tanggal_diambil_dikirim);
                        detailDeliveryDate.textContent = deliveryDate.toLocaleDateString('id-ID');
                        deliveryDateContainer.style.display = 'block';
                    } else {
                        deliveryDateContainer.style.display = 'none';
                    }
                    
                    // Order notes
                    const detailOrderNotes = document.getElementById('detailOrderNotes');
                    detailOrderNotes.textContent = data.order.catatan || '-';
                    
                    // Handle alamat
                    const addressInfo = document.getElementById('addressInfo');
                    let addressHtml = '';
                    
                    if (data.order.penerimaanMethod === 'ambil_di_tempat') {
                        addressHtml = `
                            <h6 class="address-title">Alamat</h6>
                            <div class="address-box">
                                Ambil di tempat
                            </div>
                        `;
                    } else {
                        if (data.order.use_new_address) {
                            addressHtml = `
                                <h6 class="address-title">Alamat Pengiriman</h6>
                                <div class="address-box">
                                    ${data.order.shipping_address}
                                </div>
                            `;
                        } else {
                            addressHtml = `
                                <h6 class="address-title">Alamat Pengiriman</h6>
                                <div class="address-box">
                                    ${data.order.user_address}
                                </div>
                            `;
                        }
                    }
                    
                    addressInfo.innerHTML = addressHtml;
                    
                    // Payment proof
                    // Payment proof
                    const proofContainer = document.getElementById('paymentProofImage');
                    if (data.order.payment_proof && data.order.payment_proof_exists) {
                        proofContainer.innerHTML = `
                            <a href="/gambar/payment/${data.order.payment_proof}" target="_blank">
                                <img src="/gambar/payment/${data.order.payment_proof}" 
                                    alt="Bukti Pembayaran" 
                                    style="max-width: 200px; max-height: 200px;" 
                                    class="img-thumbnail">
                            </a>
                        `;
                        document.getElementById('paymentProofContainer').style.display = 'block';
                    } else {
                        proofContainer.innerHTML = '<p class="text-muted">Bukti pembayaran tidak tersedia</p>';
                        document.getElementById('paymentProofContainer').style.display = 'block';
                    }
                    
                    // Order items
                    const itemsTable = document.getElementById('orderItemsTable');
                    let itemsHtml = '';
                    
                    data.items.forEach(item => {
                        itemsHtml += `
                            <tr>
                                <td>${item.judul}</td>
                                <td>Rp ${item.price.toLocaleString('id-ID')}</td>
                                <td>${item.quantity}</td>
                                <td>Rp ${(item.price * item.quantity).toLocaleString('id-ID')}</td>
                            </tr>
                        `;
                    });
                    
                    itemsTable.innerHTML = itemsHtml;
                } else {
                    showToast('Gagal memuat detail pesanan', 'error');
                }
            });
    }

    // Function to update order status
    function updateOrderStatus(orderId, newStatus) {
        fetch('admin_page/update_order_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                order_id: orderId,
                status: newStatus
            })
        })
        .then(response => {
            if (!response.ok) throw new Error('Network error');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showToast('Status updated');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(data.message || 'Error', 'error');
            }
        })
        .catch(error => {
            showToast('Failed to update: ' + error.message, 'error');
        });
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Order detail modal event
        document.querySelectorAll('.view-detail').forEach(button => {
            button.addEventListener('click', function() {
                const orderId = this.getAttribute('data-id');
                loadOrderDetail(orderId);
            });
        });
        
        // Update status dropdown event
        document.querySelectorAll('.update-status').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const orderId = this.getAttribute('data-id');
                const newStatus = this.getAttribute('data-status');
                updateOrderStatus(orderId, newStatus);
            });
        });
    });
    </script>
</body>
</html>