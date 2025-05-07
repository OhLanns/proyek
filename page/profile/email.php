<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pengaturan dan Akun</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #D3D3D3;
    }

    .form-container-email {
      background-color: #FFA500;
      padding: 20px;
      margin-top: 80px;
      border-radius: 12px;
      min-height: 400px;
    }

    .input-group-email {
      background-color: white;
      display: flex;
      align-items: center;
      padding: 10px;
      border-radius: 5px;
      margin-bottom: 20px;
    }

    .input-group-email input {
      border: none;
      flex: 1;
      padding: 8px;
      font-size: 16px;
    }

    .input-group-email span {
      margin-right: 10px;
    }

    .button-container-email {
      background-color: #D3D3D3;
      padding: 40px;
      text-align: center;
    }

    .btn-confirm-email {
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

  <div class="form-container-email">
    <div class="input-group-email">
    <span>&#9993;</span>
      <input type="text" placeholder="ketik emailmu">
    </div>
  </div>

  <div class="button-container-email">
    <button class="btn-confirm-email">Konfirmasi</button>
  </div>

</body>
</html>
