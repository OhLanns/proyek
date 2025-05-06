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
      background-color: #F5F5F5;
    }

    .header {
      background-color: #FFA500;
      color: black;
      padding: 16px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-weight: bold;
      font-size: 20px;
    }

    .header .back {
      font-size: 24px;
      cursor: pointer;
    }

    .header .check {
      font-size: 24px;
      cursor: pointer;
    }

    .form-container {
      background-color: #FFA500;
      padding: 20px;
      margin-top: 20px;
    }

    .input-group {
      background-color: white;
      display: flex;
      align-items: center;
      padding: 10px;
      border-radius: 5px;
      margin-bottom: 20px;
    }

    .input-group input {
      border: none;
      flex: 1;
      padding: 8px;
      font-size: 16px;
    }

    .input-group span {
      margin-right: 10px;
    }

    .button-container {
      background-color: #D3D3D3;
      padding: 40px;
      text-align: center;
    }

    .btn-confirm {
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

  <div class="header">
    <div class="back">&#8592;</div>
    <div>Username</div>
    <div class="check">&#10004;</div>
  </div>

  <div class="form-container">
    <div class="input-group">
      <span>&#128100;</span>
      <input type="text" placeholder="ketik username mu">
    </div>
  </div>

  <div class="button-container">
    <button class="btn-confirm">Konfirmasi</button>
  </div>

</body>
</html>
