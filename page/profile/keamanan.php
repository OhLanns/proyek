<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Keamanan & Akun</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f5f5f5;
    }

    .pengaturan-container {
      max-width: 500px;
      margin: 30px auto;
      background-color: #fff;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      padding: 20px;
    }

    .section-header {
      background-color: orange;
      padding: 15px;
      border-radius: 8px 8px 0 0;
      font-size: 1.5rem;
      font-weight: bold;
      color: #000;
      text-align: center;
      margin-bottom: 16px;
    }

    .profil-section .section-header {
      background-color: #ff9900; /* contoh warna khusus profil */
    }

    .menu-item {
      background-color: orange;
      color: black;
      margin-bottom: 10px;
      padding: 12px 20px;
      border-radius: 6px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      cursor: pointer;
    }

  </style>
</head>
<body>

  <div class="pengaturan-container">
    <div class="section-header">Keamanan & Akun</div>

    <div class="menu-item" onclick="window.location.href='?halaman=username'">Username </div>
    <div class="menu-item" onclick="window.location.href='?halaman=nomer'">No.Handphone </div>
    <div class="menu-item" onclick="window.location.href='?halaman=email'">Email </div>
    <div class="menu-item"onclick="window.location.href='?halaman=password'">Ganti Password </div>
  </div>

</body>
</html>
