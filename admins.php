<?php

    // session_start();
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
        $posts[2] = $_POST['phone_number'];
        $posts[3] = $_POST['product_id'];
        $posts[4] = $_POST['pharmacy_id'];
        return $posts;
    }

    if (isset($_POST['insert'])) {

        $data = getPosts();

        $insert_Query = "INSERT INTO admin (name, phone_number, product_id, pharmacy_id) 
            VALUES ('$data[1]', '$data[2]', " . ($data[3] ? "'$data[3]'" : "NULL") . ", '$data[4]');";

        try {

            $insert_Result = mysqli_query($connect, $insert_Query);

            header("Location: admins.php");
            exit;
        } catch (mysqli_sql_exception $ex) {

            echo "Помилка при додаванні продукту";
        }
    }

    if (isset($_POST['update'])) {
    
        $data = getPosts();

        $update_Query = "UPDATE `admin` 
            SET `name`='$data[1]',`phone_number`='$data[2]', `product_id`=" . ($data[3] ? "'$data[3]'" : "NULL") . ", `pharmacy_id`='$data[4]'
            WHERE `id` = $data[0]";

            try {

                $update_Result = mysqli_query($connect, $update_Query);

                header("Location: admins.php");
                exit;
            } catch (Exception $ex) {
                echo 'Error Update: ' . $ex->getMessage();
            }
    }

    if (isset($_POST['delete'])) {

        $data = getPosts();

        $delete_Query = "DELETE FROM `admin` WHERE `id` = $data[0]";

        try {

            $delete_Result = mysqli_query($connect, $delete_Query);

            header("Location: admins.php");
            exit;
        } catch (Exception $ex) {
            echo 'Error Delete: ' . $ex->getMessage();
        }
    }

    if (isset($_POST['none'])) {

        header("Location: products.php");
    }

    $sqlAdmin = "SELECT * FROM admin ORDER BY name ASC";
    $resultAdmin = mysqli_query($connect, $sqlAdmin);

    $sqlPharmacist = "SELECT * FROM pharmacist ORDER BY name ASC";
    $resultPharmacist = mysqli_query($connect, $sqlPharmacist);

    $sqlPharmacy = "SELECT * FROM pharmacy ORDER BY name ASC";
    $resultPharmacy = mysqli_query($connect, $sqlPharmacy);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="madia\pharmacy.png" type="image/x-icon">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="styles/general-styles.css">
        <title>Admins</title>
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
        
            .display-none {
                display: none;
            }
        </style>
    </head>
    <body>
        <div class="container-form">

            <?php include 'header.php'; ?>

            <div class="body">
            
                <h3>Адміни:</h3>

                <?php 
                    echo "<div class='product-grid'>";
                    while ($admins = $resultAdmin->fetch_assoc()) {
                        echo "<div class='product'>"
                        . ($admins['name']) . " (" . $admins['phone_number'] . ")";

                        if ($_SESSION['session_user_type'] == 'admin') {

                            echo "<div>"
                            . "<a href='admins.php?edit_id=" . $admins['id'] . "'>Редагувати</a>"
                            . " / "
                            . "<a href='admins.php?delete_id=" . $admins['id'] . "'>Видалити</a>"
                            . "</div>";
                        }
                        echo "</div>";
                    }
                    echo "</div>";
                ?>

                <form action="admins.php" method="post">
                    <div class="form-view">
                        <div class="row">
                            <?php

                            if ($_SESSION['session_user_type'] == 'admin') {

                                if (isset($_GET['edit_id'])) {
        
                                    $edit_id = intval($_GET['edit_id']);
                                    $editQuery = "SELECT * FROM admin WHERE id = '$edit_id'";
                                    $editResult = mysqli_query($connect, $editQuery);
                                    $adminToEdit = $editResult->fetch_assoc();
    
                                    echo "<input type='hidden' name='id' value='" . $adminToEdit['id'] . "'>";
                                    echo "<label class='panel-label col-3'>Ім'я</label><input type='text' name='name' class='panel-input col-9' placeholder='Name' value='" . $adminToEdit['name'] . "' required>";
                                    echo "<label class='panel-label col-3'>Ном. тел.</label><input type='text' name='phone_number' class='panel-input col-9' placeholder='Phone number' value='" . $adminToEdit['phone_number'] . "' required>";
    
    
                                    echo "<label class='panel-label col-3'>Аптека</label><select name='pharmacy_id' class='panel-select col-9' required>";
                                    while ($pharmacy = $resultPharmacy->fetch_assoc()) {
    
                                        $selected = $pharmacy['id'] == $adminToEdit['pharmacy_id'] ? 'selected' : '';
    
                                        echo "<option value='" . $pharmacy['id'] . "' $selected>"
                                        . $pharmacy['name']
                                        . "</option>";
                                    }
                                    echo "</select>";
                                    echo "<input class='btn-submit' type='submit' name='update' value='Змінити'>";
                                    echo "<a style='margin-top: 10px; text-align: center;' class='btn-submit white' href='admins.php'>Скасувати</a>";
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
                                    echo "<label class='panel-label col-3'>Ном. тел.</label><input type='text' name='phone_number' class='panel-input col-9' placeholder='Phone number' required>";
                                    
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
                            ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>