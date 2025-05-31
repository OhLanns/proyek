<?php
if (!isset($_SESSION['logged_in'])) {
    header("Location: index.php?halaman=login");
    exit();
}
$user_id = $_SESSION['user_id'];

// Get user data
$sql = "SELECT nama, email, no_telepon, alamat FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
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
<style>
    /* Toast Notification */
    .toast-container {
        z-index: 1100;
    }

    .toast {
        transition: opacity 0.3s ease;
        margin-bottom: 10px;
    }

    /* Badge Status */
    .badge.bg-secondary {
        background-color: #6c757d !important;
    }

    .badge.bg-primary {
        background-color: #0d6efd !important;
    }

    .badge.bg-success {
        background-color: #198754 !important;
    }

    .badge.bg-danger {
        background-color: #dc3545 !important;
    }

    .badge.bg-warning {
        background-color: #ffc107 !important;
        color: #000 !important;
    }

    /* Address Box */
    .address-box {
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        margin-top: 0.5rem;
    }

    .address-title {
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    /* Custom Modal Header */
    .modal-header.bg-warning {
        background-color: #ffc107 !important;
    }

    /* Contact Card */
    .contact-card {
        transition: all 0.3s ease;
    }

    .contact-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
</style>
<body>
    <div class="atas" style="height: 60px"></div>

    <section class="riwayat py-5">
        <div class="container" style="min-height:315px;">
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
                            <h6>Informasi Pelanggan</h6>
                            <p><strong>Nama:</strong> <span id="detailCustomerName"><?php echo htmlspecialchars($user_data['nama'] ?? ''); ?></span></p>
                            <p><strong>Telepon:</strong> <span id="detailCustomerPhone"><?php echo htmlspecialchars($user_data['no_telepon'] ?? '-'); ?></span></p>
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

    <!-- Cancel Order Modal -->
    <div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelOrderModalLabel">Batalkan Pesanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin membatalkan pesanan ini?</p>
                    <div class="alert alert-warning">
                        <strong>Perhatian!</strong> Jika pembayaran sudah dilakukan, harap hubungi admin di WhatsApp: 
                        <a href="https://wa.me/6281234567890" target="_blank">081234567890</a> untuk proses pengembalian dana.
                    </div>
                    <input type="hidden" id="cancelOrderId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-danger" id="confirmCancel">Batalkan Pesanan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Admin Modal (Will be dynamically inserted) -->
    <div id="dynamicModalContainer"></div>

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
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('order-history').innerHTML = 
                    '<tr><td colspan="6" class="text-center py-4 text-muted">Gagal memuat riwayat pesanan</td></tr>';
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
            if (order.status === 'selesai') badgeClass = 'bg-success';
            if (order.status === 'diproses') badgeClass = 'bg-primary';
            if (order.status === 'dibatalkan') badgeClass = 'bg-danger';
            if (order.status === 'pending') badgeClass = 'bg-warning';
            
            // Terjemahkan status ke bahasa Indonesia
            let statusText = order.status;
            if (order.status === 'pending') statusText = 'Menunggu';
            if (order.status === 'diproses') statusText = 'Diproses';
            if (order.status === 'selesai') statusText = 'Selesai';
            if (order.status === 'dibatalkan') statusText = 'Dibatalkan';
            
            html += `
                <tr>
                    <td>#${order.id}</td>
                    <td>${formattedDate}</td>
                    <td>Rp ${order.total.toLocaleString('id-ID')}</td>
                    <td>${order.payment_method}</td>
                    <td><span class="badge ${badgeClass}">${statusText}</span></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary view-detail d-inline-flex align-items-center gap-1" data-id="${order.id}">
                            <i class="bi bi-eye"></i>
                            <span>Detail</span>
                        </button>

                        ${(order.status === 'pending') ? 
                        `<button class="btn btn-sm btn-outline-danger cancel-order d-inline-flex align-items-center gap-1 ms-2" data-id="${order.id}">
                            <i class="bi bi-x-circle"></i>
                            <span>Batalkan</span>
                        </button>` : 
                        (order.status === 'diproses') ?
                        `<button class="btn btn-sm btn-outline-warning cancel-order d-inline-flex align-items-center gap-1 ms-2" data-id="${order.id}">
                            <i class="bi bi-telephone"></i>
                            <span>Hubungi Admin</span>
                        </button>` : ''}

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
        
        // Add event listeners to cancel buttons
        document.querySelectorAll('.cancel-order').forEach(button => {
            button.addEventListener('click', function() {
                const orderId = this.getAttribute('data-id');
                const status = this.closest('tr').querySelector('.badge').textContent.toLowerCase();
                
                if (status === 'diproses') {
                    showContactAdminModal(orderId);
                } else {
                    document.getElementById('cancelOrderId').value = orderId;
                    const modal = new bootstrap.Modal(document.getElementById('cancelOrderModal'));
                    modal.show();
                }
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
                    statusBadge.textContent = data.order.status === 'pending' ? 'Menunggu' : 
                                           data.order.status === 'diproses' ? 'Diproses' :
                                           data.order.status === 'selesai' ? 'Selesai' : 'Dibatalkan';
                    
                    let badgeClass = 'bg-secondary';
                    if (data.order.status === 'selesai') badgeClass = 'bg-success';
                    if (data.order.status === 'diproses') badgeClass = 'bg-primary';
                    if (data.order.status === 'dibatalkan') badgeClass = 'bg-danger';
                    if (data.order.status === 'pending') badgeClass = 'bg-warning';
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
                            <div class="address-box bg-light p-3 rounded">
                                Ambil di tempat
                            </div>
                        `;
                    } else {
                        if (data.order.use_new_address) {
                            addressHtml = `
                                <h6 class="address-title">Alamat Pengiriman</h6>
                                <div class="address-box bg-light p-3 rounded">
                                    ${data.order.shipping_address}
                                </div>
                            `;
                        } else {
                            addressHtml = `
                                <h6 class="address-title">Alamat Pengiriman</h6>
                                <div class="address-box bg-light p-3 rounded">
                                    ${data.order.user_address}
                                </div>
                            `;
                        }
                    }
                    
                    addressInfo.innerHTML = addressHtml;
                    
                    // Payment proof
                    const proofContainer = document.getElementById('paymentProofImage');
                    if (data.order.payment_proof) {
                        proofContainer.innerHTML = `
                            <a href="gambar/payment/${data.order.payment_proof}" target="_blank">
                                <img src="gambar/payment/${data.order.payment_proof}" 
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
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Terjadi kesalahan saat memuat detail pesanan', 'error');
            });
    }

    // Fungsi untuk membatalkan pesanan
    function cancelOrder(orderId) {
        fetch('page/cancel_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `order_id=${orderId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Pesanan berhasil dibatalkan', 'success');
                loadOrderHistory(); // Refresh riwayat
                
                // Tutup modal menggunakan Bootstrap JavaScript
                const modal = bootstrap.Modal.getInstance(document.getElementById('cancelOrderModal'));
                if (modal) {
                    modal.hide();
                }
            } else {
                if (data.message.includes('Hubungi admin')) {
                    showContactAdminModal(orderId);
                } else {
                    showToast('Gagal membatalkan pesanan: ' + data.message, 'error');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Terjadi kesalahan saat membatalkan pesanan', 'error');
        });
    }

    // Fungsi untuk menampilkan modal kontak admin
    function showContactAdminModal(orderId) {
        const modalHtml = `
            <div class="modal fade" id="contactAdminModal" tabindex="-1" aria-labelledby="contactAdminModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-warning text-white">
                            <h5 class="modal-title" id="contactAdminModalLabel">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                Pesanan #${orderId} Sedang Diproses
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-warning">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                Untuk membatalkan pesanan yang sedang diproses, Anda perlu menghubungi admin secara langsung.
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100 contact-card">
                                        <div class="card-body text-center">
                                            <div class="bg-success bg-opacity-10 p-3 rounded-circle d-inline-block mb-3">
                                                <i class="bi bi-whatsapp fs-1 text-success"></i>
                                            </div>
                                            <h5>WhatsApp</h5>
                                            <p class="text-muted">Hubungi admin via WhatsApp untuk pembatalan cepat</p>
                                                <a href="https://wa.me/6281234567890?text=Halo%20Admin,%20saya%20ingin%20membatalkan%20pesanan%20%23${orderId}" 
                                                class="btn btn-success w-100 d-inline-flex align-items-center justify-content-center gap-2" 
                                                target="_blank">
                                                    <i class="bi bi-whatsapp"></i>
                                                    <span>081234567890</span>
                                                </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100 contact-card">
                                        <div class="card-body text-center">
                                            <div class="bg-primary bg-opacity-10 p-3 rounded-circle d-inline-block mb-3">
                                                <i class="bi bi-envelope fs-1 text-primary"></i>
                                            </div>
                                            <h5>Email</h5>
                                            <p class="text-muted">Kirim email untuk pembatalan dengan lampiran bukti pembayaran</p>
                                            <a href="mailto:admin@dapuraizlan.com?subject=Pembatalan Pesanan #${orderId}&body=Halo Admin,%0D%0A%0D%0ASaya ingin membatalkan pesanan #${orderId}.%0D%0A%0D%0ATerima kasih." 
                                                class="btn btn-primary w-100 d-inline-flex align-items-center justify-content-center gap-2">
                                                <i class="bi bi-envelope"></i>
                                                <span>admin@dapuraizlan.com</span>
                                            </a>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card border-warning">
                                <div class="card-header bg-warning bg-opacity-10">
                                    <h6 class="mb-0"><i class="bi bi-lightbulb-fill text-warning me-2"></i>Informasi Penting</h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Sertakan nomor pesanan (#${orderId}) saat menghubungi admin</li>
                                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Siapkan bukti pembayaran (jika sudah melakukan pembayaran)</li>
                                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Berikan alasan pembatalan yang jelas</li>
                                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Proses pembatalan membutuhkan waktu 1-2 hari kerja</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Insert modal HTML
        document.getElementById('dynamicModalContainer').innerHTML = modalHtml;
        
        // Tampilkan modal
        const modal = new bootstrap.Modal(document.getElementById('contactAdminModal'));
        modal.show();
    }

    // Fungsi untuk menyalin nomor pesanan
    function copyOrderNumber(orderId) {
        navigator.clipboard.writeText(`#${orderId}`).then(() => {
            showToast('Nomor pesanan #' + orderId + ' berhasil disalin', 'success');
        }).catch(err => {
            showToast('Gagal menyalin nomor pesanan', 'error');
        });
    }

    // Fungsi untuk menampilkan notifikasi toast
    function showToast(message, type = 'success') {
        // Create toast container if not exists
        let toastContainer = document.getElementById('toastContainer');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toastContainer';
            toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            document.body.appendChild(toastContainer);
        }
        
        // Create toast
        const toast = document.createElement('div');
        toast.className = `toast show align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi ${type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;
        
        toastContainer.appendChild(toast);
        
        // Auto remove toast after 5 seconds
        setTimeout(() => {
            toast.remove();
            // Remove container if empty
            if (toastContainer.children.length === 0) {
                toastContainer.remove();
            }
        }, 5000);
    }

    // Inisialisasi saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        loadOrderHistory();
        
        // Event listener untuk tombol konfirmasi pembatalan
        document.getElementById('confirmCancel').addEventListener('click', function() {
            const orderId = document.getElementById('cancelOrderId').value;
            cancelOrder(orderId);
        });
    });
    </script>
</body>
</html>