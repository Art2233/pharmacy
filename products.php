<?php
    session_start();
    if (!isset($_SESSION['session_user'])) {
        header("Location: login.php");
    }
?>

<?php
    $host = "localhost";
    $user = "root";
    $password = "";
    $database = "pharmacy";

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {

        $connect = mysqli_connect($host, $user, $password, $database);
    } catch (mysqli_sql_exception $ex) {

        echo 'Помилка при підключенні до бази даних';
        exit;
    }

    function getPosts() {

        $posts = array();
        $posts[0] = $_POST['id'];
        $posts[1] = $_POST['name'];
        $posts[2] = $_POST['price'];
        $posts[3] = $_POST['quantity'];
        $posts[4] = $_POST['pharmacy_id'];
        return $posts;
    }

    if (isset($_POST['insert'])) {

        $data = getPosts();

        $insert_Query = "INSERT INTO product (name, price, quantity, pharmacy_id) VALUES ( '$data[1]', '$data[2]', '$data[3]', '$data[4]');";

        try {

            $insert_Result = mysqli_query($connect, $insert_Query);

            header("Location: products.php");
            exit;
        } catch (mysqli_sql_exception $ex) {

            echo "Помилка при додаванні продукту";
        }
    }

    if (isset($_POST['update'])) {
    
        $data = getPosts();

        $update_Query = "UPDATE `product` SET `name`='$data[1]',`price`='$data[2]',`quantity`='$data[3]',`pharmacy_id`='$data[4]' WHERE `id` = $data[0]";

            try {

                $update_Result = mysqli_query($connect, $update_Query);

                header("Location: products.php");
                exit;
            } catch (Exception $ex) {
                echo 'Error Update: ' . $ex->getMessage();
            }
    }

    if (isset($_POST['delete'])) {

        $data = getPosts();
        
        $delete_Query = "DELETE FROM `product` WHERE `id` = $data[0]";
        
        try {

            $delete_Result = mysqli_query($connect, $delete_Query);

            header("Location: products.php");
            exit;
        } catch (Exception $ex) {
            echo 'Error Delete: ' . $ex->getMessage();
        }
    }

    if (isset($_POST['none'])) {

        header("Location: products.php");
    }

    $sqlProduct = "SELECT * FROM product ORDER BY name ASC";
    $resultProduct = mysqli_query($connect, $sqlProduct);

    $sqlPharmacy = "SELECT * FROM pharmacy ORDER BY name ASC";
    $resultPharmacy = mysqli_query($connect, $sqlPharmacy);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Products Page</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="styles/general-styles.css">
        <link rel="icon" href="media/pharmacy.png" type="image/x-icon">
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

            .panel-input {
                border: 1px solid #dadada;
                padding: 5px;
                border-top-right-radius: 10px;
                border-bottom-right-radius: 10px;
                margin-bottom: 5px;
            }

            .panel-select {
                border: 1px solid #dadada;
                padding: 5px;
                border-top-right-radius: 10px;
                border-bottom-right-radius: 10px;
                margin-bottom: 5px;
            }

            .with-border {
                border: 1px solid #dadada !important;
            }

            .btn-submit {
                background-color: #007bff;
                color: #fff;
                border: none;
                padding: 10px;
                border-radius: 10px;
                cursor: pointer;
                text-align: center;
            }

            .panel-label {
                border: 1px solid #dadada;
                padding: 5px;
                border-top-left-radius: 10px;
                border-bottom-left-radius: 10px;
                margin-bottom: 5px;
                border-right: none;
            }

            .green {
                background-color: #28a745;
                color: #fff;
            }

            .white {
                background-color: #fff;
                color: #000;
                border: 1px solid #dadada;
            }
        </style>
    </head>
    <body>
        <div class="container-form">
            
            <?php include 'header.php'; ?>

            <div class="body">

                <h3>Продукти:</h3>

                <?php 
                    echo "<div class='product-grid'>";
                    while ($products = $resultProduct->fetch_assoc()) {
                        
                        echo "<div class='product'>";
                        echo ($products['name']) . " — " . $products['price'] . "₴";
                        if ($_SESSION['session_user_type'] != 'client') {

                            echo "<div>"
                            . "<a href='products.php?edit_id=" . $products['id'] . "'>Редагувати</a>"
                            . " / "
                            . "<a href='products.php?delete_id=" . $products['id'] . "'>Видалити</a>"
                            . "</div>";
                        }
                        else {
                            echo "<br>";
                            // echo "<a href='products.php?order_product=" . $products['id'] . "'>Замовити</a>";
                            echo "<a>Замовити</a>";
                        }
                        echo "</div>";
                    }
                    echo "</div>";
                ?>

                <form action="products.php" method="post">
                    <div class="form-view">
                        <div style="height: 100%">
                            <div class="row">
                                <?php

                                    if ($_SESSION['session_user_type'] != 'client') {
                                        if (isset($_GET['edit_id'])) {
        
                                            $edit_id = intval($_GET['edit_id']);
                                            $editQuery = "SELECT * FROM product WHERE id = '$edit_id'";
                                            $editResult = mysqli_query($connect, $editQuery);
                                            $productToEdit = $editResult->fetch_assoc();
        
                                            echo "<input type='hidden' name='id' value='" . $productToEdit['id'] . "'>";
                                            echo "<label class='panel-label col-3'>Ліки</label><input type='text' name='name' class='panel-input col-9' placeholder='Name' value='" . $productToEdit['name'] . "' required>";
                                            echo "<label class='panel-label col-3'>Ціна</label><input type='text' name='price' class='panel-input col-9' placeholder='Price' value='" . $productToEdit['price'] . "' required>";
                                            echo "<label class='with-border panel-label col-3'>Кількість</label><input type='range' name='quantity' class='col-9' value='" . $productToEdit['quantity'] . "' required min='0' max='2'>";
                                            
                                            echo "<label class='panel-label col-3'>Аптека</label><select name='pharmacy_id' class='panel-select col-9' required>";
        
                                            while ($pharmacy = $resultPharmacy->fetch_assoc()) {
                                                $selected = $pharmacy['id'] == $productToEdit['pharmacy_id'] ? 'selected' : '';
                                                echo "<option value='" . $pharmacy['id'] . "' $selected>"
                                                . $pharmacy['name']
                                                . "</option>";
                                            }
        
                                            echo "</select>";
                                            echo "<input class='btn-submit' type='submit' name='update' value='Змінити'>";
                                            echo "<a style='margin-top: 10px; text-align: center;' class='btn-submit white' href='products.php'>Скасувати</a>";
                                        }
                                        else if (isset($_GET['delete_id'])) {
        
                                            echo "Ви впевнені, що хочете видалити цей продукт?";
        
                                            echo "<input type='hidden' name='id' value='" . intval($_GET['delete_id']) . "'>";
        
                                            echo "<div class='row'>"
                                            . "<input class='btn-submit col' type='submit' name='delete' value='Так'>"
                                            . "<input class='btn-submit white col' type='submit' name='none' value='Ні'>"
                                            . "</div>";
                                        }
                                        else {
                                            echo "<label class='panel-label col-3'>Назва</label><input type='text' name='name' class='panel-input col-9' placeholder='Name' required>";
                                            echo "<label class='panel-label col-3'>Ціна</label><input type='text' name='price' class='panel-input col-9' placeholder='Price' required>";
                                            echo "<label class='with-border panel-label col-3'>Кількість</label><input type='range' name='quantity' class='col-9' required min='0' max='2'>";
                                            
                                            echo "<label class='panel-label col-3'>Аптека</label><select name='pharmacy_id' class='panel-select col-9' required>";
                                            
                                            while ($pharmacy = $resultPharmacy->fetch_assoc()) {
        
                                                echo "<option value='" . $pharmacy['id'] . "'>"
                                                . $pharmacy['name']
                                                . "</option>";
                                            }
                                            
                                            echo "</select>";
                                            echo "<input class='btn-submit green' type='submit' name='insert' value='Додати'>";
                                        }
                                    }
                                    // else {

                                    //     if (isset($_GET['order_product'])) {

                                    //         echo "Ви впевнені, що замовити продукт?";
    
                                    //         echo "<input type='hidden' name='id' value='" . $_SESSION['session_user']['id'] . "'>";
                                    //         echo "<input type='hidden' name='product_id' value='" . intval($_GET['order_product']) . "'>";
                    
                                    //         echo "<div class='row'>"
                                    //         . "<input class='btn-submit col' type='submit' name='order_product' value='Так'>"
                                    //         . "<input class='btn-submit white col' type='submit' name='none' value='Ні'>"
                                    //         . "</div>";
                                    //     }

                                    // }
                                ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
