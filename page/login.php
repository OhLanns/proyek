<div class="login-wrapper">
    <div class="login-card">
        <h4 class="text-center mb-4">Login</h4>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <form action="proses_login.php" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required autofocus>
            </div>
            <div class="mb-3"
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="button" class="btn btn-warning w-100 mb-3" onclick="window.location.href='?halaman=profile'">Login</button>
            <div class="mb-3 d-flex justify-content-between">
                <a href="lupa_password.php" class="text-decoration-none">Lupa kata sandi?</a>
            </div>
        </form>

        <p class="text-center">Belum punya akun? <a href="index.php?halaman=sigin" class="text-decoration-none">Daftar sekarang</a></p>
    </div> 
</div> 