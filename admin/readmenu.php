<?php
include "../config.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Dapur Aizlan</title>
    <link rel="stylesheet" href="../aset/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../aset/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,300;0,400;0,700;1,700&display=swap" rel="stylesheet" />
</head>
<body>
    <!-- Toast Notification Container -->
    <div class="toast-container-menu"></div>

    <section class="menu py-5">
        <div class="lebar"  style="height: 60px;">
        </div>
        <div class="container">
            <h2 class="text-center mb-4 menukami"><b>Menu Kami</b></h2>
            <div class="d-flex align-items-center mb-4">
            <a class="btn btn-primary hidden button-beli" href="createmenu.php">Tambahkan menu</a>
            </div>
            <div class="row">
                <?php               
                $sql = "SELECT id,judul,img,deskripsi,harga  FROM menu";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
                    echo '
                    <div class="col-md-4 mb-4 hidden">
                        <div class="card card-menu">
                            <img src="../gambar/menu/' . $row["img"] . '" class="card-img-top" alt="' . $row["judul"] . '">
                            <div class="card-body">
                                <h5 class="card-title hidden">' . $row["judul"] . '</h5>
                                <p class="card-text hidden">' . $row["deskripsi"] . '</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <p class="card-text harga hidden"><strong>Rp ' . number_format($row["harga"], 0, ',', '.') . '</strong></p> 
                                        <a class="btn btn-primary button-beli add-to-cart-btn hidden" href="updatemenu.php?id=' . $row["id"] . '">
                                            Edit
                                        </a>
                                        <a class="btn btn-primary button-beli add-to-cart-btn hidden" href="deletemenu.php?id=' . $row["id"] . '">
                                            delete
                                        </a>
                                </div>
                            </div>
                        </div>
                    </div>';
                }
                } else {
                echo "0 results";
                }
                $conn->close();                ?>
            </div>
        </div>
    </section>

    <script src="../aset/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../aset/script.js"></script>
</body>
</html>