<?php
if (!isset($_SESSION['logged_in'])) {
    header("Location: index.php?halaman=login");
    exit();
}
$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pembelian - Dapur Aizlan</title>
    <link rel="stylesheet" href="../aset/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../aset/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
</head>
<body>
    <div class="atas" style="height: 60px"></div>

    <section class="riwayat py-5">
        <div class="container">
            <h2 class="text-center mb-4"><b>Riwayat Pembelian</b></h2>
            
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID Pesanan</th>
                                    <th>Tanggal</th>
                                    <th>Total</th>
                                    <th>Metode Pembayaran</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="order-history">
                                <!-- Order history will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
                            <h6>Informasi Pesanan</h6>
                            <p><strong>Tanggal:</strong> <span id="detailOrderDate"></span></p>
                            <p><strong>Status:</strong> <span id="detailOrderStatus" class="badge"></span></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Pembayaran</h6>
                            <p><strong>Metode:</strong> <span id="detailPaymentMethod"></span></p>
                            <p><strong>Total:</strong> <span id="detailOrderTotal"></span></p>
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

    <script>
    // Fungsi untuk memuat riwayat pesanan
    function loadOrderHistory() {
        fetch('page/get_order_history.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderOrderHistory(data.orders);
                } else {
                    document.getElementById('order-history').innerHTML = 
                        '<tr><td colspan="6" class="text-center py-4 text-muted">Belum ada riwayat pesanan</td></tr>';
                }
            });
    }

    // Fungsi untuk merender riwayat pesanan
    function renderOrderHistory(orders) {
        const tbody = document.getElementById('order-history');
        
        if (orders.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">Belum ada riwayat pesanan</td></tr>';
            return;
        }
        
        let html = '';
        
        orders.forEach(order => {
            const orderDate = new Date(order.tanggal);
            const formattedDate = orderDate.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            
            // Determine badge color based on status
            let badgeClass = 'bg-secondary';
            if (order.status === 'completed') badgeClass = 'bg-success';
            if (order.status === 'processing') badgeClass = 'bg-primary';
            if (order.status === 'cancelled') badgeClass = 'bg-danger';
            
            html += `
                <tr>
                    <td>#${order.id}</td>
                    <td>${formattedDate}</td>
                    <td>Rp ${order.total.toLocaleString('id-ID')}</td>
                    <td>${order.payment_method}</td>
                    <td><span class="badge ${badgeClass}">${order.status}</span></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary view-detail" data-id="${order.id}">
                            <i class="bi bi-eye"></i> Detail
                        </button>
                    </td>
                </tr>
            `;
        });
        
        tbody.innerHTML = html;
        
        // Add event listeners to detail buttons
        document.querySelectorAll('.view-detail').forEach(button => {
            button.addEventListener('click', function() {
                const orderId = this.getAttribute('data-id');
                viewOrderDetail(orderId);
            });
        });
    }

    // Fungsi untuk melihat detail pesanan
    function viewOrderDetail(orderId) {
        fetch(`page/get_order_detail.php?order_id=${orderId}`)
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
                    
                    // Set status badge
                    const statusBadge = document.getElementById('detailOrderStatus');
                    statusBadge.textContent = data.order.status;
                    
                    let badgeClass = 'bg-secondary';
                    if (data.order.status === 'completed') badgeClass = 'bg-success';
                    if (data.order.status === 'processing') badgeClass = 'bg-primary';
                    if (data.order.status === 'cancelled') badgeClass = 'bg-danger';
                    statusBadge.className = `badge ${badgeClass}`;
                    
                    document.getElementById('detailPaymentMethod').textContent = data.order.payment_method;
                    document.getElementById('detailOrderTotal').textContent = `Rp ${data.order.total.toLocaleString('id-ID')}`;
                    
                    // Payment proof
                    const proofContainer = document.getElementById('paymentProofImage');
                    if (data.order.payment_proof) {
                        proofContainer.innerHTML = `
                            <a href="../gambar/payment/${data.order.payment_proof}" target="_blank">
                                <img src="../gambar/payment/${data.order.payment_proof}" 
                                     alt="Bukti Pembayaran" 
                                     style="max-width: 200px; max-height: 200px;" 
                                     class="img-thumbnail">
                            </a>
                        `;
                        document.getElementById('paymentProofContainer').style.display = 'block';
                    } else {
                        document.getElementById('paymentProofContainer').style.display = 'none';
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
                    
                    // Show modal
                    const modal = new bootstrap.Modal(document.getElementById('orderDetailModal'));
                    modal.show();
                } else {
                    showToast('Gagal memuat detail pesanan', 'error');
                }
            });
    }

    // Inisialisasi saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        loadOrderHistory();
    });
    </script>
</body>
</html>