<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Pengaturan Akun</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f5f5f5;
    }

    .pengaturan-container-alamat {
      max-width: 500px;
      margin: 30px auto;
      background-color: #fff;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      padding: 20px;
      min-height: 400px;
    }

    .section-header-alamat {
      background-color: orange;
      padding: 15px;
      border-radius: 8px 8px 0 0;
      font-size: 1.5rem;
      font-weight: bold;
      color: #000;
      text-align: center;
      margin-bottom: 16px;
    }

    .profil-section .section-header-alamat {
      background-color: #ff9900; /* contoh warna khusus profil */
    }

    .tambah-alamat-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background-color: white;
      color: black;
      border-radius: 30px;
      padding: 10px 20px;
      font-weight: bold;
      text-decoration: none;
      box-shadow: 0 2px 5px rgba(0,0,0,0.2);
      cursor: pointer;
      transition: 0.3s ease;
    }

    .tambah-alamat-btn:hover {
      background-color: #eee;
    }

    .tambah-alamat-btn i {
      font-style: normal;
      font-weight: bold;
      margin-right: 8px;
    }
    

  </style>
</head>
<body>

  <div class="pengaturan-container-alamat">
    <div class="section-header-alamat">Alamat</div>

    <div style="text-align: center;">
      <a href="?halaman=tambah_alamat" class="tambah-alamat-btn">
        <i>+</i> Tambah Alamat
      </a>
    </div>

</div>
</body>
</html>
