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

    <section class="keranjang py-5">
        <div class="container">
            <h2 class="text-center mb-4">Keranjang Belanja</h2>
            
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
        // Data gambar menu
        const menuImages = {
            1: "1.jpg", 2: "2.jpg", 3: "3.jpg", 4: "4.jpg", 5: "5.jpg",
            6: "6.jpg", 7: "7.jpg", 8: "8.jpg", 9: "9.jpg", 10: "10.jpg",
            11: "11.jpg", 12: "12.jpg"
        };
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
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            let cartItemsContainer = document.getElementById('cart-items');
            let orderSummaryContainer = document.getElementById('order-summary');
            let checkoutBtn = document.getElementById('checkout-btn');
            
            if (cart.length === 0) {
                cartItemsContainer.innerHTML = '<div class="text-center py-4"><i class="bi bi-cart-x fs-1 text-muted"></i><p class="mt-2 text-muted">Keranjang belanja kosong</p></div>';
                orderSummaryContainer.innerHTML = '<p class="text-muted">Belum ada item dalam keranjang</p>';
                checkoutBtn.disabled = true;
                return;
            }
            
            // Render item keranjang
            let cartItemsHTML = '';
            let subtotal = 0;
            
            cart.forEach(item => {
                let itemTotal = item.price * item.quantity;
                subtotal += itemTotal;
                
                cartItemsHTML += `
                    <div class="cart-item p-3 mb-3 bg-white" data-id="${item.id}">
                        <div class="d-flex align-items-center">
                            <img src="../gambar/menu/${menuImages[item.id]}" 
                                 alt="${item.name}" 
                                 class="cart-item-img me-3">
                            <div class="flex-grow-1">
                                <h6 class="mb-1 fw-bold">${item.name}</h6>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Rp ${item.price.toLocaleString('id-ID')} × ${item.quantity}</span>
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

            //  fungsi confirmDelete
    async function confirmDelete(id) {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        let item = cart.find(item => item.id === id);
        
        if (!item) return;
        
        const confirmed = await showCustomConfirm(`Yakin ingin menghapus ${item.name} dari keranjang?`, 'Hapus Item');
        
        if (confirmed) {
            cart = cart.filter(item => item.id !== id);
            localStorage.setItem('cart', JSON.stringify(cart));
            loadCart();
            updateCartCount();
            showToast(`"${item.name}" dihapus dari keranjang`, 'success');
        }
    }

    // fungsi checkout
async function checkout() {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    if (cart.length === 0) return;
    
    let subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    let total = subtotal * 1.11;
    
    const confirmed = await showCustomConfirm(
        `Lanjutkan checkout dengan total Rp ${total.toLocaleString('id-ID')}?`,
        'Konfirmasi Checkout'
    );
    
    if (confirmed) {
        localStorage.removeItem('cart');
        loadCart();
        updateCartCount();
        showToast(
            `Checkout berhasil!<br>Total: Rp ${total.toLocaleString('id-ID')}`,
            'success',
            3000
        );
    }
}
        // Fungsi untuk memperbarui jumlah item di keranjang
        function updateCartCount() {
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            let totalItems = cart.reduce((total, item) => total + item.quantity, 0);
            let cartCountElement = document.getElementById('cart-count');
            if (cartCountElement) {
                cartCountElement.textContent = totalItems;
            }
        }

        // Inisialisasi saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            loadCart();
            
            // Event listener untuk tombol checkout
            document.getElementById('checkout-btn').addEventListener('click', checkout);
        });
    </script>
</body>
</html>