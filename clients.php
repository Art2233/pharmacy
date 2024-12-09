<?php

    session_start();

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

    if (!isset($_SESSION['session_user'])) {

        header("Location: login.php");
    }

    if (isset($_GET['edit_role_id'])) {

        $edit_role_id = intval($_GET['edit_role_id']);
        $editQuery = "SELECT * FROM client WHERE id = '$edit_role_id'";
        $editResult = mysqli_query($connect, $editQuery);
        $clientToEdit = $editResult->fetch_assoc();

        if (!$clientToEdit) {

            header("Location: clients.php");
        }
    }
?>

<?php
    function getPostsPharmacist() {

        $posts = array();
        $posts[0] = $_POST['id'];
        $posts[1] = $_POST['name'];
        $posts[2] = $_POST['phone_number'];
        $posts[3] = $_POST['job_status'];
        $posts[4] = $_POST['product_id'];
        $posts[5] = $_POST['pharmacy_id'];
        $posts[6] = $_POST['admin_id'];
        $posts[7] = $_POST['password'];
        $posts[8] = $_POST['email'];
        return $posts;
    }

    function getPostsAdmin() {

        $posts = array();
        $posts[0] = $_POST['id'];
        $posts[1] = $_POST['name'];
        $posts[2] = $_POST['phone_number'];
        $posts[3] = $_POST['product_id'];
        $posts[4] = $_POST['pharmacy_id'];
        $posts[5] = $_POST['password'];
        $posts[6] = $_POST['email'];
        return $posts;
    }

    if (isset($_POST['insert_pharmacy'])) {

        $dataPharmacist = getPostsPharmacist();

        $insert_Query = "INSERT INTO pharmacist (name, phone_number, job_status, product_id, pharmacy_id, admin_id, password, email)
            VALUES ('$dataPharmacist[1]', '$dataPharmacist[2]', '$dataPharmacist[3]', " . ($dataPharmacist[4] ? "'$dataPharmacist[4]'" : "NULL") . ", '$dataPharmacist[5]', '$dataPharmacist[6]', $dataPharmacist[7], $dataPharmacist[8]');";
        $delete_Query = "DELETE FROM `client` WHERE `id` = $dataPharmacist[0]";

        try {

            $insert_Result = mysqli_query($connect, $insert_Query);
            $delete_Result = mysqli_query($connect, $delete_Query);

            header("Location: pharmacists.php");
            exit;
        } catch (mysqli_sql_exception $ex) {

            echo "Помилка при додаванні продукту";
        }
    }

    if (isset($_POST['insert_admin'])) {

        $dataAdmin = getPostsAdmin();

        $insert_Query = "INSERT INTO admin (name, phone_number, product_id, pharmacy_id, password, email)
            VALUES ('$dataAdmin[1]', '$dataAdmin[2]', " . ($dataAdmin[3] ? "'$dataAdmin[3]'" : "NULL") . ", '$dataAdmin[4]', '$dataAdmin[5]', '$dataAdmin[6]');";
        $delete_Query = "DELETE FROM `client` WHERE `id` = $dataAdmin[0]";

        try {

            $insert_Result = mysqli_query($connect, $insert_Query);
            $delete_Result = mysqli_query($connect, $delete_Query);

            header("Location: admins.php");
            exit;
        } catch (mysqli_sql_exception $ex) {

            echo "Помилка при додаванні продукту";
        }
    }

    if (isset($_POST['insert_client'])) {

        $name = $_POST['name'];
        $phone_number = $_POST['phone_number'];

        $insert_Query = "INSERT INTO client (name, phone_number)
            VALUES ('$name', '$phone_number');";

        try {

            $insert_Result = mysqli_query($connect, $insert_Query);

            header("Location: clients.php");
            exit;
        } catch (mysqli_sql_exception $ex) {

            echo "Помилка при додаванні продукту";
        }
    }

    if (isset($_POST['delete'])) {

        $id = $_POST['id'];

        $delete_Query = "DELETE FROM `client` WHERE `id` = $id";

        try {

            $delete_Result = mysqli_query($connect, $delete_Query);

            header("Location: clients.php");
            exit;
        } catch (mysqli_sql_exception $ex) {

            echo "Помилка при видаленні продукту";
        }
    }

    if (isset($_POST['update'])) {

        $id = $_POST['id'];
        $name = $_POST['name'];
        $phone_number = $_POST['phone_number'];

        $update_Query = "UPDATE `client` SET `name` = '$name', `phone_number` = '$phone_number' WHERE `id` = $id";

        try {

            $update_Result = mysqli_query($connect, $update_Query);

            header("Location: clients.php");
            exit;
        } catch (mysqli_sql_exception $ex) {

            echo "Помилка при оновленні продукту";
        }
    }

    $sqlClient = "SELECT * FROM client";
    $resultClient = mysqli_query($connect, $sqlClient);

    $sqlPharmacy = "SELECT * FROM pharmacy ORDER BY name ASC";
    $resultPharmacy = mysqli_query($connect, $sqlPharmacy);

    $sqlAdmin = "SELECT * FROM admin ORDER BY name ASC";
    $resultAdmin = mysqli_query($connect, $sqlAdmin);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta charset="UTF-8">
        <title>Clients</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        
        <div class="container-form">

            <?php include 'header.php'; ?>

            <div class="body">

                <div>Клієнти:</div>

                <?php 
                    echo "<div class='product-grid'>";
                    while ($client = $resultClient->fetch_assoc()) {
                        echo "<div class='product'>";
                        echo ($client['name']);

                        echo "<div>";
                        if ($_SESSION['session_user_type'] == 'admin') {

                            echo "<a href='clients.php?edit_role_id=" . $client['id'] . "&role=client'>Змінити роль</a>"
                            . " / "
                            . "<a href='clients.php?edit_id=" . $client['id'] . "'>Змінити</a>"
                            . " / "
                            . "<a href='clients.php?delete_id=" . $client['id'] . "'>Видалити</a>";
                        }

                        if ($_SESSION['session_user_type'] == 'client' && $client['id'] == $_SESSION['session_user']['id']) {

                            echo "<a href='clients.php?edit_id=" . $client['id'] . "'>Змінити</a>";
                        }
                        
                        if (!!isset($_GET['edit_role_id'])) {

                            $edit_role_id = intval($_GET['edit_role_id']);
    
                            if ($edit_role_id == $client['id']) {
                                echo "<br>";
                                echo "<a href='clients.php?edit_role_id=" . $client['id'] . "&role=pharmacist'>Фармацевт</a>";
                                echo "<br>";
                                echo "<a href='clients.php?edit_role_id=" . $client['id'] . "&role=admin'>Адміністратор</a>";
                                echo "<br>";
                                echo "<a href='clients.php'>Скасуквати</a>";
                            }
                        }

                        echo "</div>";
                        echo "</div>";
                    }
                    echo "</div>";
                ?>

                <form action="clients.php" method="post">
                    <div class="form-view">
                        <div class="row">

                            <?php

                                if (isset($_GET['edit_role_id'])) {

                                    $edit_role_id = intval($_GET['edit_role_id']);
                                    $editQuery = "SELECT * FROM client WHERE id = '$edit_role_id'";
                                    $editResult = mysqli_query($connect, $editQuery);
                                    $clientToEdit = $editResult->fetch_assoc();
                                    
                                    if (isset($_GET['role'])) {
                                        $role = $_GET['role'];
                                        echo "<div class='col-12'>Редагування клієнта на " . $role . "</div>";
                                        echo "<input type='hidden' name='id' value='" . $clientToEdit['id'] . "'>";
                                        echo "<input type='hidden' name='password' value='" . $clientToEdit['password'] . "'>";
                                        echo "<input type='hidden' name='email' value='" . $clientToEdit['email'] . "'>";
                                        
                                        if ($role == 'client') {

                                        }
                                        else if ($role == 'pharmacist') {

                                            echo "<input type='hidden' name='product_id' value=''>";
                                            echo "<label class='panel-label col-3'>Ім'я</label><input type='text' name='name' class='panel-input col-9' placeholder='Name' value='" . $clientToEdit['name'] . "' required>";
                                            echo "<label class='panel-label col-3'>Ном. тел.</label><input type='text' name='phone_number' class='panel-input col-9' placeholder='Phone number' value='" . $clientToEdit['phone_number'] . "' required>";
                                            echo "<label class='panel-label col-3'>Чи працює</label><input type='text' name='job_status' class='panel-input col-9' placeholder='Job Status' value='0' required>";

                                            echo "<label class='panel-label col-3'>Адмін</label><select name='admin_id' class='panel-select col-9' required>";
                                            while ($admin = $resultAdmin->fetch_assoc()) {

                                                echo "<option value='" . $admin['id'] . "'>"
                                                . $admin['name'] . ' (Admin)'
                                                . "</option>";
                                            }
                                            echo "</select>";

                                            
                                            echo "<label class='panel-label col-3'>Аптека</label><select name='pharmacy_id' class='panel-select col-9' required>";
                                            while ($pharmacy = $resultPharmacy->fetch_assoc()) {

                                                echo "<option value='" . $pharmacy['id'] . "'>"
                                                . $pharmacy['name']
                                                . "</option>";
                                            }
                                            echo "</select>";


                                            echo "<input class='btn-submit green' type='submit' name='insert_pharmacy' value='Змінити на фармацеста'>";
                                        }
                                        else if ($role == 'admin') {
                                            
                                            echo "<label class='panel-label col-3'>Назва</label><input type='text' name='name' class='panel-input col-9' placeholder='Name' value='" . $clientToEdit['name'] . "'required>";
                                            echo "<label class='panel-label col-3'>Ном. тел.</label><input type='text' name='phone_number' class='panel-input col-9' placeholder='Phone number' value='" . $clientToEdit['phone_number'] . "' required>";
                                            
                                            echo "<label class='panel-label col-3'>Аптека</label><select name='pharmacy_id' class='panel-select col-9' required>";
                                            
                                            while ($pharmacy = $resultPharmacy->fetch_assoc()) {

                                                echo "<option value='" . $pharmacy['id'] . "'>"
                                                . $pharmacy['name']
                                                . "</option>";
                                            }
                                            
                                            echo "</select>";
                                            echo "<input class='btn-submit green' type='submit' name='insert_admin' value='Замінити на адміна'>";
                                        }
                                    }
                                }
                                else if (isset($_GET['edit_id'])) {

                                    $edit_id = intval($_GET['edit_id']);
                                    $editQuery = "SELECT * FROM client WHERE id = '$edit_id'";
                                    $editResult = mysqli_query($connect, $editQuery);
                                    $clientToEdit = $editResult->fetch_assoc();

                                    echo "<input type='hidden' name='id' value='" . $edit_id . "'>";

                                    echo "<label class='panel-label col-3'>Ім'я</label><input type='text' name='name' class='panel-input col-9' placeholder='Name' value='" . $clientToEdit['name'] . "' required>";
                                    echo "<label class='panel-label col-3'>Ном. тел.</label><input type='text' name='phone_number' class='panel-input col-9' placeholder='Phone number' value='" . $clientToEdit['phone_number'] . "' required>";
                                    echo "<input class='btn-submit green' type='submit' name='update' value='Змінити клієнта'>";

                                }
                                else if (isset($_GET['delete_id'])) {

                                    echo "Ви впевнені, що хочете видалити цього клієнта?";
    
                                    echo "<input type='hidden' name='id' value='" . intval($_GET['delete_id']) . "'>";
        
                                    echo "<div class='row'>"
                                    . "<input class='btn-submit col' type='submit' name='delete' value='Так'>"
                                    . "<input class='btn-submit white col' type='submit' name='none' value='Ні'>"
                                    . "</div>";
                                    }
                                else {
                                    echo "<label class='panel-label col-3'>Ім'я</label><input type='text' name='name' class='panel-input col-9' placeholder='Name' required>";
                                    echo "<label class='panel-label col-3'>Ном. тел.</label><input type='text' name='phone_number' class='panel-input col-9' placeholder='Phone number' required>";
                                    echo "<input class='btn-submit green' type='submit' name='insert_client' value='Додати клієнта'>";
                                }

                            ?>

                        </div>
                    </div>
                </form>

            </div>
        </div>
    </body>
</html>