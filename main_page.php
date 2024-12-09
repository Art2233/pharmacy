<?php
    session_start();
    if (!isset($_SESSION['session_user'])) {
        header("Location: login.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta charset="UTF-8">
        <title>Main Page</title>
        <link rel="stylesheet" href="styles/general-styles.css">
        <link rel="icon" href="madia\pharmacy.png" type="image/x-icon">
        <style>
            .form-view {
                width: 400px;
                height: 600px;
                border: 1px solid #dadada;
                border-radius: 10px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                background-color: #fff;
                padding: 15px;

                margin-left: 50%;
            }

            .btn-submit {
                background-color: #007bff;
                color: #fff;
                border: none;
                padding: 10px;
                border-radius: 10px;
                cursor: pointer;
                text-align: center;
                margin-bottom: 10px;
            }

            a {
                text-decoration: none;
                color: #000;
            }
        </style>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>

    <div class="container-form">

        <div class="header">
            <div class="row">
                <div class="col-9"></div>

                <?php
                    echo "<div class='col-2'> Ім'я користувача: " . $_SESSION['session_user']['name'] . "</div>";
                    echo '<div class="col-1"><a href="logout.php">Вийти</a></div>';
                ?>
            </div>
        </div>

        <div class="body">
            <div class="form-view">
                <div class="row">

                    <?php

                        if ($_SESSION['session_user_type'] == 'admin' || $_SESSION['session_user_type'] == 'pharmacist') {
                            echo '<a style="text-decoration: none;" href="products.php" class="btn-submit col-12">Інформація про ліки</a>';
                            if ($_SESSION['session_user_type'] == 'admin') {
                                echo '<a style="text-decoration: none;" href="clients.php" class="btn-submit col-12">Інформація про клієнтів</a>';
                            }
                            echo '<a style="text-decoration: none;" href="pharmacists.php" class="btn-submit col-12">Інформація про фармацевтів</a>';
                            echo '<a style="text-decoration: none;" href="admins.php" class="btn-submit col-12">Інформація про адміністраторів</a>';
                        }
                        else {
                            echo '<a style="text-decoration: none;" href="products.php" class="btn-submit col-12">Інформація про ліки</a>';
                            echo '<a style="text-decoration: none;" href="clients.php" class="btn-submit col-12">Інформація про клієнтів</a>';
                            echo '<a style="text-decoration: none;" href="pharmacists.php" class="btn-submit col-12">Інформація про фармацевтів</a>';
                        }
                    
                    ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>