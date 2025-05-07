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

    .pengaturan-container-bank {
      max-width: 500px;
      margin: 30px auto;
      background-color: #fff;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      padding: 20px;
      min-height: 400px;
    }

    .section-header-bank {
      background-color: orange;
      padding: 15px;
      border-radius: 8px 8px 0 0;
      font-size: 1.5rem;
      font-weight: bold;
      color: #000;
      text-align: center;
      margin-bottom: 16px;
    }

    .tambah-bank-btn {
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

    .tambah-bank-btn:hover {
      background-color: #eee;
    }

    .tambah-bank-btn i {
      font-style: normal;
      font-weight: bold;
      margin-right: 8px;
    }

  </style>
</head>
<body>
  <div class="pengaturan-container-bank">
    <div class="section-header-bank">Rekening Bank</div>

    <div style="text-align: center;">
      <a href="?halaman=tambah_alamat" class="tambah-bank-btn">
        <i>+</i> Tambah No Rekening (Sertakan Nama Banknya)
      </a>
    </div>
</div>
</body>
</html>
