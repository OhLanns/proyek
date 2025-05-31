<?php
if (!isset($_SESSION['logged_in'])) {
    header("Location: index.php?halaman=login");
    exit();
}
$user_id = $_SESSION['user_id'];

// Get user data directly in the same file
$sql = "SELECT nama, email, no_telepon, alamat FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - Dapur Aizlan</title>
    <link rel="stylesheet" href="../aset/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../aset/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,300;0,400;0,700;1,700&display=swap" rel="stylesheet" />
    <style>
        /* Improved Toast Notification Styles */
        #notification-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1100;
            width: 350px;
        }
        
        .custom-toast {
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            margin-bottom: 15px;
            overflow: hidden;
            border-radius: 8px;
        }
        
        .toast-success {
            background-color: #28a745;
            color: white;
        }
        
        .toast-error {
            background-color: #dc3545;
            color: white;
        }
        
        .toast-warning {
            background-color: #ffc107;
            color: #212529;
        }
        
        .toast-info {
            background-color: #17a2b8;
            color: white;
        }
        
        .toast-body {
            padding: 15px;
        }
        
        .btn-close-white {
            filter: invert(1);
            opacity: 0.8;
        }
        
        .btn-close-white:hover {
            opacity: 1;
        }
        
        .toast-progress {
            width: 100%;
            height: 4px;
            background: rgba(255,255,255,0.3);
        }
        
        .toast-progress-bar {
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.7);
            animation: toastProgress linear forwards;
        }
        
        @keyframes toastProgress {
            from { width: 100%; }
            to { width: 0%; }
        }
        
        /* Cart item styles */
        .cart-item-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }
        
    </style>
</head>
<body>
    <!-- Notification Container -->
    <div id="notification-container"></div>

    <div class="atas" style="height: 60px"></div>
    
    <section class="keranjang py-5">
        <div class="container">
            <h2 class="text-center mb-4"><b>Keranjang Belanja</b></h2>
            
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Daftar Pesanan</h5>
                            <div id="cart-items">
                                <!-- Item keranjang akan dimuat di sini -->
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Ringkasan Pesanan</h5>
                            <div id="order-summary">
                                <!-- Ringkasan pesanan akan dimuat di sini -->
                            </div>
                            <button id="checkout-btn" class="btn btn-primary w-100 mt-3 py-2 fw-bold">
                                <i class="bi bi-credit-card me-2"></i>Checkout
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Custom Modal for Confirmations -->
    <div class="custom-modal" id="customConfirmModal">
        <div class="custom-modal-content">
            <div class="custom-modal-title" id="customConfirmTitle">Konfirmasi</div>
            <div class="custom-modal-body" id="customConfirmMessage"></div>
            <div class="custom-modal-footer">
                <button class="custom-modal-btn custom-modal-btn-secondary" id="customConfirmCancel">Batal</button>
                <button class="custom-modal-btn custom-modal-btn-primary" id="customConfirmOk">OK</button>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Form Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Informasi Pelanggan</h6>
                            <div class="mb-3">
                                <label class="form-label">Nama</label>
                                <input type="text" class="form-control" id="customerName" value="<?php echo htmlspecialchars($user_data['nama'] ?? ''); ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nomor Telepon</label>
                                <input type="text" class="form-control" id="customerPhone" value="<?php echo htmlspecialchars($user_data['no_telepon'] ?? '-'); ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Metode Penerimaan</label>
                                <select class="form-select" id="penerimaanMethod">
                                    <option value="ambil_di_tempat">Ambil di Tempat</option>
                                    <option value="diantar">Diantar</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tanggal Pengambilan/Pengiriman</label>
                                <input type="date" class="form-control" id="deliveryDate" min="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Alamat </label>
                                
                                <div class="d-flex mb-3">
                                    <div class="form-check me-4">
                                        <input class="form-check-input" type="radio" name="addressOption" id="useAccountAddress" checked>
                                        <label class="form-check-label" for="useAccountAddress">
                                            Alamat Akun
                                        </label>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="addressOption" id="useNewAddress">
                                        <label class="form-check-label" for="useNewAddress">
                                            Alamat Baru
                                        </label>
                                    </div>
                                </div>
                                
                                <!-- Container untuk alamat akun -->
                                <div id="accountAddressContainer">
                                    <div class="card card-body bg-light mb-3">
                                        <address class="mb-0">
                                            <?php echo htmlspecialchars($user_data['alamat'] ?? 'Alamat belum diisi'); ?>
                                        </address>
                                    </div>
                                </div>
                                
                                <!-- Container untuk alamat baru (awalnya tersembunyi) -->
                                <div id="newAddressContainer" class="d-none">
                                    <textarea class="form-control" id="newShippingAddress" rows="3" 
                                            placeholder="Masukkan alamat lengkap pengiriman"></textarea>
                                    <small class="text-muted">Contoh: Nama Gedung, Lantai, Nama Penerima, dll.</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>Metode Pembayaran</h6>
                            <div class="mb-3">
                                <label class="form-label">Pilih Metode</label>
                                <select class="form-select" id="paymentMethod">
                                    <option value="Dana" selected>Dana</option>
                                    <option value="Transfer Bank">Transfer Bank</option>
                                    <option value="COD">Cash on Delivery (COD)</option>
                                </select>
                            </div>
                            <div id="danaInfo" class="payment-info">
                                <p class="small text-muted">Silahkan transfer ke nomor Dana berikut:</p>
                                <div class="d-flex align-items-center mb-2">
                                    <img src="gambar/dana.jpg" alt="Dana" style="height: 30px; margin-right: 10px;">
                                    <div>
                                        <h6 class="mb-0">081234567890</h6>
                                        <small class="text-muted">a/n Dapur Aizlan</small>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="paymentProof" class="form-label">Upload Bukti Pembayaran</label>
                                    <input class="form-control" type="file" id="paymentProof" accept="image/*,.pdf">
                                    <small class="text-muted">Format: JPG, PNG, PDF (maks. 2MB)</small>
                                </div>
                            </div>
                            <div id="bankInfo" class="payment-info d-none">
                                <p class="small text-muted">Silahkan transfer ke rekening berikut:</p>
                                <div class="mb-2">
                                    <h6 class="mb-0">Bank BCA</h6>
                                    <small class="text-muted">1234567890 a/n Dapur Aizlan</small>
                                </div>
                                <div class="mb-3">
                                    <label for="bankProof" class="form-label">Upload Bukti Transfer</label>
                                    <input class="form-control" type="file" id="bankProof" accept="image/*,.pdf">
                                </div>
                            </div>
                            <div id="codInfo" class="payment-info d-none">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> Pembayaran dilakukan saat pesanan diterima. Tidak perlu upload bukti pembayaran.
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Catatan (Opsional)</label>
                                <textarea class="form-control" id="orderNotes" rows="2" placeholder="Contoh: Jangan pakai cabe, sama  bumbu di pisah, dll"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-warning small">
                                <i class="bi bi-exclamation-triangle"></i> Pastikan data yang Anda masukkan benar. 
                                Pesanan akan diproses setelah pembayaran diverifikasi.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="confirmPayment">Konfirmasi Pembayaran</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Success Modal -->
    <div class="modal fade" id="orderSuccessModal" tabindex="-1" aria-labelledby="orderSuccessModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="orderSuccessModalLabel">Pesanan Berhasil</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                    <h4 class="mt-3">Terima kasih!</h4>
                    <p>Pesanan Anda telah berhasil dibuat dengan ID: <strong id="orderIdDisplay"></strong></p>
                    <p>Kami akan segera memproses pesanan Anda.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <a href="index.php?halaman=riwayat" class="btn btn-primary">Lihat Riwayat</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Custom confirmation modal
        const customConfirmModal = document.getElementById('customConfirmModal');
        const customConfirmMessage = document.getElementById('customConfirmMessage');
        const customConfirmTitle = document.getElementById('customConfirmTitle');
        const customConfirmOk = document.getElementById('customConfirmOk');
        const customConfirmCancel = document.getElementById('customConfirmCancel');

        // Fungsi untuk menampilkan konfirmasi kustom
        function showCustomConfirm(message, title = 'Konfirmasi') {
            return new Promise((resolve) => {
                customConfirmTitle.textContent = title;
                customConfirmMessage.textContent = message;
                customConfirmModal.style.display = 'flex';
                
                customConfirmOk.onclick = () => {
                    customConfirmModal.style.display = 'none';
                    resolve(true);
                };
                
                customConfirmCancel.onclick = () => {
                    customConfirmModal.style.display = 'none';
                    resolve(false);
                };
            });
        }

        // Fungsi untuk menampilkan alert kustom
        function showCustomAlert(message, title = 'Pemberitahuan') {
            return new Promise((resolve) => {
                customConfirmTitle.textContent = title;
                customConfirmMessage.textContent = message;
                customConfirmModal.style.display = 'flex';
                
                // Hanya tampilkan tombol OK
                customConfirmCancel.style.display = 'none';
                customConfirmOk.textContent = 'OK';
                
                customConfirmOk.onclick = () => {
                    customConfirmModal.style.display = 'none';
                    customConfirmCancel.style.display = '';
                    customConfirmOk.textContent = 'OK';
                    resolve();
                };
            });
        }

        // Improved notification function
        function showToast(message, type = 'success', duration = 3000) {
            const container = document.getElementById('notification-container');
            const toastId = 'toast-' + Date.now();
            
            // Icon mapping
            const icons = {
                success: 'bi-check-circle-fill',
                error: 'bi-exclamation-circle-fill',
                warning: 'bi-exclamation-triangle-fill',
                info: 'bi-info-circle-fill'
            };
            
            const toastHTML = `
                <div id="${toastId}" class="toast custom-toast toast-${type}" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-body d-flex justify-content-between align-items-start" style="background-color:var(--primary);">
                        <div class="d-flex align-items-center">
                            <i class="bi ${icons[type] || 'bi-info-circle'} me-3" style="font-size: 1.5rem;"></i>
                            <div>
                                <strong class="me-auto">${type.charAt(0).toUpperCase() + type.slice(1)}</strong>
                                <div>${message}</div>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white ms-3" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-progress">
                        <div class="toast-progress-bar" style="animation-duration: ${duration}ms;"></div>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', toastHTML);
            const toastElement = document.getElementById(toastId);
            const toast = new bootstrap.Toast(toastElement, {
                autohide: true,
                delay: duration
            });
            toast.show();
            
            // Remove toast after it's hidden
            toastElement.addEventListener('hidden.bs.toast', function() {
                this.remove();
            });
        }

        // Fungsi untuk memuat keranjang
        function loadCart() {
            fetch('page/get_cart_items.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        renderCart(data.items);
                    } else {
                        showEmptyCart();
                    }
                });
        }

        // Fungsi untuk menampilkan keranjang kosong
        function showEmptyCart() {
            document.getElementById('cart-items').innerHTML = 
                '<div class="text-center py-4"><i class="bi bi-cart-x fs-1 text-muted"></i><p class="mt-2 text-muted">Keranjang belanja kosong</p></div>';
            document.getElementById('order-summary').innerHTML = 
                '<p class="text-muted">Belum ada item dalam keranjang</p>';
            document.getElementById('checkout-btn').disabled = true;
        }

        // Fungsi untuk merender item keranjang
        function renderCart(items) {
            const cartItemsContainer = document.getElementById('cart-items');
            const orderSummaryContainer = document.getElementById('order-summary');
            const checkoutBtn = document.getElementById('checkout-btn');
            
            if (items.length === 0) {
                showEmptyCart();
                return;
            }
            
            let cartItemsHTML = '';
            let subtotal = 0;
            
            items.forEach(item => {
                let itemTotal = item.harga * item.quantity;
                subtotal += itemTotal;
                
                cartItemsHTML += `
                    <div class="cart-item p-3 mb-3 bg-white rounded" data-id="${item.id}">
                        <div class="d-flex align-items-center">
                            <img src="gambar/menu/${item.img}" 
                                alt="${item.judul}" 
                                class="cart-item-img me-3">
                            <div class="flex-grow-1">
                                <h6 class="mb-1 fw-bold">${item.judul}</h6>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Rp ${item.harga.toLocaleString('id-ID')} × ${item.quantity}</span>
                                    <span class="fw-bold">Rp ${itemTotal.toLocaleString('id-ID')}</span>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-outline-danger ms-3 remove-item" data-id="${item.id}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            });
            
            cartItemsContainer.innerHTML = cartItemsHTML;
            
            // Render ringkasan pesanan
            let total = subtotal;
            
            orderSummaryContainer.innerHTML = `
                <div class="summary-item d-flex justify-content-between mb-2">
                    <span>Subtotal:</span>
                    <span>Rp ${subtotal.toLocaleString('id-ID')}</span>
                </div>
                
                <hr>
                <div class="summary-total d-flex justify-content-between">
                    <span>Total Pembayaran:</span>
                    <span>Rp ${total.toLocaleString('id-ID')}</span>
                </div>
            `;
            
            checkoutBtn.disabled = false;
            
            // Tambahkan event listener untuk tombol hapus
            document.querySelectorAll('.remove-item').forEach(button => {
                button.addEventListener('click', function() {
                    const itemId = parseInt(this.getAttribute('data-id'));
                    confirmDelete(itemId);
                });
            });
        }

        // Fungsi untuk konfirmasi penghapusan
        async function confirmDelete(itemId) {
            const confirmed = await showCustomConfirm('Yakin ingin menghapus item ini dari keranjang?', 'Hapus Item');
            
            if (confirmed) {
                fetch('page/remove_from_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        menu_id: itemId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadCart();
                        updateCartCount();
                        showToast('Item dihapus dari keranjang', 'success');
                    } else {
                        showToast('Gagal menghapus item', 'error');
                    }
                });
            }
        }

        // Fungsi untuk update jumlah item di cart counter
        function updateCartCount() {
            fetch('page/get_cart_count.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const cartCountElements = document.querySelectorAll('.cart-count');
                        cartCountElements.forEach(el => {
                            el.textContent = data.count;
                            el.style.display = data.count > 0 ? 'inline-block' : 'none';
                        });
                    }
                });
        }

        // Fungsi untuk checkout
        function checkout() {
            // Langsung tampilkan modal pembayaran karena data user sudah di-load di PHP
            const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
            paymentModal.show();
        }

        // Fungsi untuk proses pembayaran
        async function processPayment() {
            const paymentMethod = document.getElementById('paymentMethod').value;
            const paymentProofInput = paymentMethod === 'Dana' ? 
                document.getElementById('paymentProof') : 
                document.getElementById('bankProof');
            const penerimaanMethod = document.getElementById('penerimaanMethod').value;
            const deliveryDate = document.getElementById('deliveryDate').value;
            const orderNotes = document.getElementById('orderNotes').value;
            
            // Validasi alamat
            const useNewAddress = document.getElementById('useNewAddress').checked;
            const shippingAddress = useNewAddress 
                ? document.getElementById('newShippingAddress').value
                : document.querySelector('#accountAddressContainer address').textContent;
            
            // Validasi umum
            if ((paymentMethod === 'Dana' || paymentMethod === 'Transfer Bank') && !paymentProofInput.files[0]) {
                showToast('Harap upload bukti pembayaran', 'error');
                return;
            }
            
            if (penerimaanMethod === 'diantar' && !deliveryDate) {
                showToast('Harap pilih tanggal pengiriman', 'error');
                return;
            }
            
            if (useNewAddress && !shippingAddress.trim()) {
                showToast('Harap masukkan alamat pengiriman', 'error');
                return;
            }
            
            const formData = new FormData();
            formData.append('payment_method', paymentMethod);
            formData.append('penerimaan_method', penerimaanMethod);
            formData.append('delivery_date', deliveryDate);
            formData.append('notes', orderNotes);
            formData.append('shipping_address', shippingAddress);
            formData.append('use_new_address', useNewAddress ? '1' : '0');
            
            if (paymentProofInput.files[0]) {
                formData.append('payment_proof', paymentProofInput.files[0]);
            }
            
            try {
                const response = await fetch('page/checkout.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Hide payment modal
                    const paymentModal = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
                    paymentModal.hide();
                    
                    // Show success modal
                    document.getElementById('orderIdDisplay').textContent = '#' + data.order_id;
                    const successModal = new bootstrap.Modal(document.getElementById('orderSuccessModal'));
                    successModal.show();
                    
                    // Update cart
                    loadCart();
                    updateCartCount();
                } else {
                    showToast('Gagal checkout: ' + data.message, 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('Terjadi kesalahan saat memproses pembayaran', 'error');
            }
        }

        // Inisialisasi saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            loadCart();
            document.getElementById('checkout-btn').addEventListener('click', checkout);
            
            // Payment method change handler
            document.getElementById('paymentMethod').addEventListener('change', function() {
                const method = this.value;
                document.querySelectorAll('.payment-info').forEach(el => el.classList.add('d-none'));
                
                if (method === 'Dana') {
                    document.getElementById('danaInfo').classList.remove('d-none');
                } else if (method === 'Transfer Bank') {
                    document.getElementById('bankInfo').classList.remove('d-none');
                } else if (method === 'COD') {
                    document.getElementById('codInfo').classList.remove('d-none');
                }
            });
            
            // Address selection handler
            document.querySelectorAll('input[name="addressOption"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const accountAddressContainer = document.getElementById('accountAddressContainer');
                    const newAddressContainer = document.getElementById('newAddressContainer');
                    
                    if (this.id === 'useAccountAddress') {
                        accountAddressContainer.classList.remove('d-none');
                        newAddressContainer.classList.add('d-none');
                    } else {
                        accountAddressContainer.classList.add('d-none');
                        newAddressContainer.classList.remove('d-none');
                    }
                });
            });
            
            // Confirm payment button
            document.getElementById('confirmPayment').addEventListener('click', processPayment);
        });
</script>
</body>
</html>