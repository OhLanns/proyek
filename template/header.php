
    <!-- Header -->
    <nav
      class="navbar navbar-expand-lg "
      style="background-color: var(--primary)"
    >
      <div class="container d-flex align-items-center">
        <!-- Logo dan Nama Brand -->
        <a class="navbar-brand d-flex align-items-center" href="index.php?halaman=home">
          <img
            src="../gambar/logo.jpg"
            alt="Logo"
            style="height: 40px; margin-right: 10px; border-radius: 50%"
          />
          <h3 class="mb-0">
            <strong>Dapur<span style="color: var(--bg)">Aizlan</span></strong>
          </h3>
        </a>
        <!-- Ikon Cart dan Profil -->
        <div class="d-flex align-items-center ms-auto order-lg-2">
          <a href="index.php?halaman=keranjang"><i
            class="bi bi-cart-fill me-3"
            style="font-size: 2rem; color: hsl(0, 0%, 10%)"
          ></i></a>
          <i
            class="bi bi-person-circle me-2"
            style="font-size: 2rem; color: hsl(0, 0%, 10%)"
          ></i>
          <!-- Hamburger Menu -->
          <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarNav"
            aria-controls="navbarNav"
            aria-expanded="false"
            aria-label="Toggle navigation"
          >
            <span class="navbar-toggler-icon"></span>
          </button>
        </div>
        <div class="collapse navbar-collapse order-lg-1" id="navbarNav">
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="index.php?halaman=home"
                >Home</a
              >
            </li>
            <li class="nav-item">
              <a class="nav-link" href="index.php?halaman=home#about">About</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="index.php?halaman=menu">Menu</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="index.php?halaman=kontak">Kontak</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
