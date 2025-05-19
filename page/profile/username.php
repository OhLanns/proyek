<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Username Form</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #D3D3D3;
    }

    .form-container-username {
      background-color: #FFA500;
      padding: 20px;
      margin-top: 80px;
      border-radius: 12px;
      min-height: 400px;
    }

    .input-group-username {
      background-color: white;
      display: flex;
      align-items: center;
      padding: 10px;
      border-radius: 5px;
      margin-bottom: 20px;
    }

    .input-group-username input {
      border: none;
      flex: 1;
      padding: 8px;
      font-size: 16px;
    }

    .input-group-username span {
      margin-right: 10px;
    }

    .button-container-username {
      padding: 40px;
      text-align: center;
    }

    .btn-confirm-username {
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
   
  <div class="form-container-username">
    <div class="input-group-username">
      <span>&#128100;</span>
      <input type="text" placeholder="ketik username mu">
    </div>
  </div>

  <div class="button-container-username">
    <button class="btn-confirm-username">Konfirmasi</button>
  </div>

</body>
</html>
