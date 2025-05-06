<div class="login-wrapper">
    <div class="login-card">
        <h4 class="text-center mb-4">MASUK</h4>

        <?php if (isset($_SESSION['login_error'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($_SESSION['login_error']) ?>
                <?php unset($_SESSION['login_error']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['redirect_url']) && strpos($_SESSION['redirect_url'], 'keranjang') !== false): ?>
            <div class="alert alert-info">Silakan login terlebih dahulu untuk mengakses keranjang belanja</div>
        <?php endif; ?>

        <form action="page/proses_login.php" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required autofocus>
            </div>
            <div class="mb-3"
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
<<<<<<< HEAD
            <button type="button" class="btn btn-warning w-100 mb-3" onclick="window.location.href='?halaman=profile'">Login</button>
=======
            <button type="submit" class="btn btn-warning w-100 mb-3">Masuk</button>
>>>>>>> 241b444d7d2e8b72609f509d4dce404a311a5d2c
            <div class="mb-3 d-flex justify-content-between">
                <a href="lupa_password.php" class="text-decoration-none">Lupa kata sandi?</a>
            </div>
        </form>

        <p class="text-center">Belum punya akun? <a href="index.php?halaman=signup" class="text-decoration-none">Daftar sekarang</a></p>
    </div> 
</div>