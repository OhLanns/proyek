<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pengaturan dan akun</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #D3D3D3;;
    }

    .form-container-pw {
      background-color: #FFA500;
      padding: 20px;
      margin-top: 80px;
      border-radius: 12px;
      min-height: 400px;
    }

    .input-group-pw {
      background-color: white;
      display: flex;
      align-items: center;
      padding: 10px;
      border-radius: 5px;
      margin-bottom: 20px;
    }

    .input-group-pw input {
      border: none;
      flex: 1;
      padding: 8px;
      font-size: 16px;
    }

    .input-group-pw span {
      margin-right: 10px;
    }

    .button-container-pw {
      background-color: #D3D3D3;;
      padding: 40px;
      text-align: center;
    }

    .btn-confirm-pw {
      background-color: white;
      color: black;
      padding: 10px 24px;
      border-radius: 20px;
      font-weight: bold;
      border: none;
      cursor: pointer;
    }
  </style>
</head>
<body>

  <div class="form-container-pw">
    <div class="input-group-pw">
      <input type="text" placeholder="Password Baru">
    </div>

    <div class="input-group-pw">
      <input type="text" placeholder="Konfirmasi Password">
    </div>
  </div>

  <div class="button-container-pw">
    <button class="btn-confirm-pw">Konfirmasi</button>
  </div>

</body>
</html>
