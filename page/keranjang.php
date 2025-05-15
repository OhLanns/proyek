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
    <title>Keranjang Belanja - Dapur Aizlan</title>
    <link rel="stylesheet" href="../aset/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../aset/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,300;0,400;0,700;1,700&display=swap" rel="stylesheet" />
</head>
<body>
    <!-- Toast Notification Container -->
    <div class="toast-container" id="notification-container"></div>

    <div class="atas" style="height: 60px">

    </div>
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
        // Fungsi untuk menampilkan notifikasi toast
        function showToast(message, type = 'success', duration = 2500) {
            const container = document.getElementById('notification-container');
            const toastId = 'toast-' + Date.now();
            
            const toastHTML = `
                <div id="${toastId}" class="toast custom-toast toast-${type}" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="${duration}">
                    <div class="toast-body d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi ${type === 'success' ? 'bi-check-circle' : 'bi-exclamation-circle'} me-2"></i>
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', toastHTML);
            const toast = new bootstrap.Toast(document.getElementById(toastId));
            toast.show();
            
            // Hapus toast setelah selesai
            document.getElementById(toastId).addEventListener('hidden.bs.toast', function() {
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
                    <div class="cart-item p-3 mb-3 bg-white" data-id="${item.id}">
                        <div class="d-flex align-items-center">
                            <img src="../gambar/menu/${item.img}" 
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
            let ppn = subtotal * 0.11;
            let total = subtotal + ppn;
            
            orderSummaryContainer.innerHTML = `
                <div class="summary-item d-flex justify-content-between mb-2">
                    <span>Subtotal:</span>
                    <span>Rp ${subtotal.toLocaleString('id-ID')}</span>
                </div>
                <div class="summary-item d-flex justify-content-between mb-2">
                    <span>PPN (11%):</span>
                    <span>Rp ${ppn.toLocaleString('id-ID')}</span>
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
                        showToast('Item dihapus dari keranjang');
                    } else {
                        showToast('Gagal menghapus item', 'error');
                    }
                });
            }
        }

        // Fungsi untuk checkout
        async function checkout() {
            const confirmed = await showCustomConfirm('Lanjutkan proses checkout?', 'Konfirmasi Checkout');
            
            if (confirmed) {
                fetch('page/checkout.php', {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Checkout berhasil!', 'success');
                        loadCart();
                        updateCartCount();
                    } else {
                        showToast('Gagal checkout: ' + data.message, 'error');
                    }
                });
            }
        }

        // Inisialisasi saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            loadCart();
            document.getElementById('checkout-btn').addEventListener('click', checkout);
        });
    </script>
</body>
</html>