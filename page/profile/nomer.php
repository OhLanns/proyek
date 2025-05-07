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

    .form-container-nomer {
      background-color: #FFA500;
      padding: 20px;
      margin-top: 80px;
      border-radius: 12px;
      min-height: 400px;
    }

    .input-group-nomer {
      background-color: white;
      display: flex;
      align-items: center;
      padding: 10px;
      border-radius: 5px;
      margin-bottom: 20px;
    }

    .input-group-nomer input {
      border: none;
      flex: 1;
      padding: 8px;
      font-size: 16px;
    }

    .input-group-nomer span {
      margin-right: 10px;
    }

    .button-container-nomer {
      background-color: #D3D3D3;
      padding: 40px;
      text-align: center;
    }

    .btn-confirm-nomer {
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

  <div class="form-container-nomer">
    <div class="input-group-nomer">
    <span>&#9742;</span>
      <input type="text" placeholder="ketik no handphonemu">
    </div>
  </div>

  <div class="button-container-nomer">
    <button class="btn-confirm-nomer">Konfirmasi</button>
  </div>

</body>
</html>
