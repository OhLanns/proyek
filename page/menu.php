
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Dapur Aizlan</title>
    <link rel="stylesheet" href="../aset/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../aset/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,300;0,400;0,700;1,700&display=swap" rel="stylesheet" />
    <style>
        
    /* Menghilangkan spinner pada input number */
    .quantity-input::-webkit-outer-spin-button,
    .quantity-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Untuk Firefox */
    .quantity-input {
        -moz-appearance: textfield;
    }
        .quantity-selector {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .quantity-btn {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: var(--primary);
            color: white;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        
        .quantity-input {
            width: 40px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 5px;
            -webkit-appearance: none; /* Menghilangkan spinner di Safari */
            -moz-appearance: textfield; /* Menghilangkan spinner di Firefox */
            appearance: none; /* Menghilangkan spinner di browser modern */
            margin: 0; /* Menghilangkan margin default */
        }
        
        .add-to-cart-btn {
            margin-left: 10px;
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <!-- Toast Notification Container -->
    <div class="toast-container-menu"></div>

    <section class="menu py-5">
        <div class="lebar"  style="height: 60px;">
        </div>
        <div class="container">
            <h2 class="text-center mb-4 menukami"><b>Menu Kami</b></h2>
            <div class="row">
                <?php            
                $sql = "SELECT id,judul,img,deskripsi,harga  FROM menu";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
                    echo  '
                    <div class="col-md-4 mb-4 hidden">
                        <div class="card card-menu">
                            <img src="../gambar/menu/' . $row["img"] . '" class="card-img-top" alt="' . $row["judul"] . '">
                            <div class="card-body">
                                <h5 class="card-title hidden">' . $row["judul"] . '</h5>
                                <p class="card-text hidden">' . $row["deskripsi"] . '</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <p class="card-text harga hidden"><strong>Rp ' . number_format($row["harga"], 0, ',', '.') . '</strong></p>
                                    <div class="d-flex align-items-center">
                                        <div class="quantity-selector hidden">
                                            <button class="quantity-btn minus" onclick="updateQuantity(' . $row["id"] . ', -1)">-</button>
                                            <input type="number" class="quantity-input" id="quantity-' . $row["id"] . '" value="1" min="1" max="99" onchange="validateQuantity(' . $row["id"] . ')">
                                            <button class="quantity-btn plus" onclick="updateQuantity(' . $row["id"] . ', 1)">+</button>
                                        </div>
                                        <button class="btn btn-primary button-beli add-to-cart-btn hidden" 
                                                onclick="addToCart(' . $row["id"] . ', \'' . $row["judul"] . '\', ' . $row["harga"] . ')">
                                            Beli
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>';
                }
                } else {
                echo "0 results";
                }
                $conn->close();
                ?>
            </div>
        </div>
    </section>
    <script>
        // Fungsi untuk menampilkan toast notifikasi
        function showToast(message) {
            // Buat elemen toast
            const toastContainer = document.querySelector('.toast-container-menu');
            const toastId = 'toast-' + Date.now();
            
            const toastHTML = `
                <div id="${toastId}" class="toast custom-toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="2000">
                    <div class="toast-header">
                        <strong class="me-auto">Sukses</strong>
                        <small>Baru saja</small>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        ${message}
                    </div>
                </div>
            `;
            
            toastContainer.insertAdjacentHTML('beforeend', toastHTML);
            
            // Inisialisasi dan tampilkan toast
            const toastElement = document.getElementById(toastId);
            const toast = new bootstrap.Toast(toastElement);
            toast.show();
            
            // Hapus toast dari DOM setelah hilang
            toastElement.addEventListener('hidden.bs.toast', function() {
                toastElement.remove();
            });
        }

        // Fungsi untuk memvalidasi input quantity
        function validateQuantity(id) {
            const input = document.getElementById('quantity-' + id);
            let value = parseInt(input.value);
            
            if (isNaN(value) || value < 1) {
                value = 1;
            } else if (value > 99) {
                value = 99;
            }
            
            input.value = value;
        }

        // Fungsi untuk mengupdate quantity
        function updateQuantity(id, change) {
            const input = document.getElementById('quantity-' + id);
            let value = parseInt(input.value) + change;
            
            if (value < 1) {
                value = 1;
            } else if (value > 99) {
                value = 99;
            }
            
            input.value = value;
        }

        // Fungsi untuk mendapatkan quantity
        function getQuantity(id) {
            const input = document.getElementById('quantity-' + id);
            return parseInt(input.value) || 1;
        }

        // Fungsi untuk menambahkan item ke keranjang
        function addToCart(id, name, price) {
            const quantity = getQuantity(id);
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            let existingItem = cart.find(item => item.id === id);
            
            if (existingItem) {
                existingItem.quantity += quantity;
            } else {
                cart.push({
                    id: id,
                    name: name,
                    price: price,
                    quantity: quantity
                });
            }
            
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartCount();
            
            // Tampilkan toast notifikasi
            showToast(`${name} (${quantity}x) telah ditambahkan ke keranjang`);
            
            // Reset quantity ke 1 setelah menambahkan ke keranjang
            document.getElementById('quantity-' + id).value = 1;
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
    </script>
</body>
</html>