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

    $isEdit = false;

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {

        $connect = mysqli_connect($host, $user, $password, $database);
    } catch (mysqli_sql_exception $ex) {

        echo 'Помилка при підключенні до бази даних';
        exit;
    }

    function getPosts() {

        $posts = array();
        $posts[0] = isset($_POST['id']) ? $_POST['id'] : '';
        $posts[1] = isset($_POST['name']) ? $_POST['name'] : '';
        $posts[2] = isset($_POST['phone_number']) ? $_POST['phone_number'] : '';
        $posts[3] = isset($_POST['job_status']) ? $_POST['job_status'] : '';
        $posts[4] = isset($_POST['product_id']) ? $_POST['product_id'] : '';
        $posts[5] = isset($_POST['pharmacy_id']) ? $_POST['pharmacy_id'] : '';
        $posts[6] = isset($_POST['admin_id']) ? $_POST['admin_id'] : '';
        $posts[7] = isset($_POST['pharmacy_id']) ? $_POST['pharmacy_id'] : '';
        return $posts;
    }

    if (isset($_POST['insert'])) {

        $data = getPosts();

        $insert_Query = "INSERT INTO pharmacist (name, phone_number, job_status, product_id, pharmacy_id, admin_id) 
            VALUES ('$data[1]', '$data[2]', '$data[3]', " . ($data[4] ? "'$data[4]'" : "NULL") . ", '$data[5]', '$data[6]');";

        try {

            $insert_Result = mysqli_query($connect, $insert_Query);

            header("Location: pharmacists.php");
            exit;
        } catch (mysqli_sql_exception $ex) {

            echo "Помилка при додаванні продукту";
        }
    }

    if (isset($_POST['update'])) {
    
        $data = getPosts();

        $update_Query = "UPDATE `pharmacist` 
            SET `name`='$data[1]',`phone_number`='$data[2]', `job_status`='$data[3]', `pharmacy_id`='$data[5]', `admin_id`='$data[6]'
            WHERE `id` = $data[0]";

            try {

                $update_Result = mysqli_query($connect, $update_Query);

                header("Location: pharmacists.php");
                exit;
            } catch (Exception $ex) {
                echo 'Error Update: ' . $ex->getMessage();
            }
    }

    if (isset($_POST['delete'])) {

        $data = getPosts();

        $delete_Query = "DELETE FROM `pharmacist` WHERE `id` = $data[0]";

        try {

            $delete_Result = mysqli_query($connect, $delete_Query);

            header("Location: pharmacists.php");
            exit;
        } catch (Exception $ex) {
            echo 'Error Delete: ' . $ex->getMessage();
        }
    }

    if (isset($_POST['none'])) {

        header("Location: products.php");
    }

    if (isset($_POST['select_pharmacist'])) {

        $data = getPosts();
        $updateQuery = "UPDATE `client`
            SET `pharmacist_id` = $data[7] 
            WHERE `id` = $data[0]";

        try {

            $updateResult = mysqli_query($connect, $updateQuery);
            $_SESSION['session_user']['pharmacist_id'] = $data[7];

            header("Location: pharmacists.php");
            exit;
        } catch (Exception $ex) {
            echo 'Error select_pharmacist: ' . $ex->getMessage();
        }
    }

    if (isset($_POST['remove_selected_pharmacist'])) {

        $data = getPosts();
        $updateQuery = "UPDATE `client`
            SET `pharmacist_id` = NULL 
            WHERE `id` = $data[0]";

        try {

            $updateResult = mysqli_query($connect, $updateQuery);
            $_SESSION['session_user']['pharmacist_id'] = NULL;

            header("Location: pharmacists.php");
            exit;
        } catch (Exception $ex) {
            echo 'Error remove_selected_pharmacist: ' . $ex->getMessage();
        }
    }

    $sqlPharmacist = "SELECT * FROM pharmacist ORDER BY name ASC";
    $resultPharmacist = mysqli_query($connect, $sqlPharmacist);

    $sqlPharmacy = "SELECT * FROM pharmacy ORDER BY name ASC";
    $resultPharmacy = mysqli_query($connect, $sqlPharmacy);

    $sqlAdmin = "SELECT * FROM admin ORDER BY name ASC";
    $resultAdmin = mysqli_query($connect, $sqlAdmin);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="madia\pharmacy.png" type="image/x-icon">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="styles/general-styles.css">
        <title>Pharmacists</title>
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
                <h3>Фармацевти:</h3>

                <?php 
                    echo "<div class='product-grid'>";

                    while ($pharmacists = $resultPharmacist->fetch_assoc()) {

                        $jobStatus = $pharmacists['job_status'] ? 'Працює' : 'На Вихідному';

                        echo "<div class='product'>";
                        echo $pharmacists['name'] 
                            . ' (' . $jobStatus . ')'
                            .'<br>'
                            . $pharmacists['phone_number'];

                        if ($_SESSION['session_user_type'] == 'admin') {

                            echo "<div>"
                            . "<a href='pharmacists.php?edit_id=" . $pharmacists['id'] . "'>Редагувати</a>"
                            . " / "
                            . "<a href='pharmacists.php?delete_id=" . $pharmacists['id'] . "'>Видалити</a>"
                            . "</div>";
                        }
                        else if ($_SESSION['session_user_type'] == 'pharmacist' && $_SESSION['session_user']['id'] == $pharmacists['id']) {

                            $isEdit = true;

                            echo "<div>"
                            . "<a href='pharmacists.php?edit_id=" . $pharmacists['id'] . "'>Редагувати свої дані</a>"
                            . "</div>";
                        }
                        else if ($_SESSION['session_user_type'] == 'client' && $_SESSION['session_user']['pharmacist_id'] == $pharmacists['id']) {

                            echo "<div>"
                            . "<a href='pharmacists.php?remove_selected_pharmacist=" . $pharmacists['id'] . "'>Убрати свого фармацевта</a>"
                            . "</div>";
                        }
                        else if ($_SESSION['session_user_type'] == 'client') {

                            echo "<br>";
                            echo "<a href='pharmacists.php?select_pharmacist=" . $pharmacists['id'] ."'>Зробити моїм фармацевтом</a>";
                        }

                        echo "</div>";
                    }
                    echo "</div>";
                ?>
            </div>
        </div>

        <form action="pharmacists.php" method="post">
            <div class="form-view">
                <div class="row">

                    <?php
                        if (isset($_GET['edit_id']) && ($_SESSION['session_user_type'] == 'admin' || $_SESSION['session_user_type'] == 'pharmacist')) {
        
                            $edit_id = intval($_GET['edit_id']);
                            $editQuery = "SELECT * FROM pharmacist WHERE id = '$edit_id'";
                            $editResult = mysqli_query($connect, $editQuery);
                            $pharmacistToEdit = $editResult->fetch_assoc();

                            echo "<input type='hidden' name='id' value='" . $pharmacistToEdit['id'] . "'>";
                            echo "<label class='panel-label col-3'>Ім'я</label><input type='text' name='name' class='panel-input col-9' placeholder='Name' value='" . $pharmacistToEdit['name'] . "' required>";
                            echo "<label class='panel-label col-3'>Ном. тел.</label><input type='text' name='phone_number' class='panel-input col-9' placeholder='Phone number' value='" . $pharmacistToEdit['phone_number'] . "' required>";
                            echo "<label class='panel-label col-3'>Чи працює</label><input type='text' name='job_status' class='panel-input col-9' placeholder='Job Status' value='" . $pharmacistToEdit['job_status'] . "' required>";

                            echo "<label class='panel-label col-3'>Адмін</label><select name='admin_id' class='panel-select col-9' required>";
                            while ($admin = $resultAdmin->fetch_assoc()) {

                                $selected = $admin['id'] == $pharmacistToEdit['admin_id'] ? 'selected' : '';

                                echo "<option value='" . $admin['id'] . "' $selected>"
                                . $admin['name']
                                . "</option>";
                            }
                            echo "</select>";

                            
                            echo "<label class='panel-label col-3'>Аптека</label><select name='pharmacy_id' class='panel-select col-9' required>";
                            while ($pharmacy = $resultPharmacy->fetch_assoc()) {

                                $selected = $pharmacy['id'] == $pharmacistToEdit['pharmacy_id'] ? 'selected' : '';

                                echo "<option value='" . $pharmacy['id'] . "' $selected>"
                                . $pharmacy['name']
                                . "</option>";
                            }
                            echo "</select>";


                            echo "<input class='btn-submit' type='submit' name='update' value='Змінити'>";
                            echo "<a style='margin-top: 10px; text-align: center;' class='btn-submit white' href='pharmacists.php'>Скасувати</a>";
                        }
                        else if ($_SESSION['session_user_type'] == 'admin') {

                            if (isset($_GET['delete_id'])) {
    
                                echo "Ви впевнені, що хочете видалити цей продукт?";
    
                                echo "<input type='hidden' name='id' value='" . intval($_GET['delete_id']) . "'>";
    
                                echo "<div class='row'>"
                                . "<input class='btn-submit col' type='submit' name='delete' value='Так'>"
                                . "<input class='btn-submit white col' type='submit' name='none' value='Ні'>"
                                . "</div>";
                            }
                            else {
                                echo "<label class='panel-label col-3'>Ім'я</label><input type='text' name='name' class='panel-input col-9' placeholder='Name' required>";
                                echo "<label class='panel-label col-3'>Ном. тел.</label><input type='text' name='phone_number' class='panel-input col-9' placeholder='Phone number' required>";
    
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
                                
                                
                                echo "<input class='btn-submit green' type='submit' name='insert' value='Додати'>";
                            }
                        }
                        else if (isset($_GET['select_pharmacist'])) {

                            echo "Ви впевнені, що обрати фармацевта?";

                            echo "<input type='hidden' name='id' value='" . $_SESSION['session_user']['id'] . "'>";
                            echo "<input type='hidden' name='pharmacy_id' value='" . intval($_GET['select_pharmacist']) . "'>";
    
                            echo "<div class='row'>"
                            . "<input class='btn-submit col' type='submit' name='select_pharmacist' value='Так'>"
                            . "<input class='btn-submit white col' type='submit' name='none' value='Ні'>"
                            . "</div>";
                        }
                        else if (isset($_GET['remove_selected_pharmacist'])) {

                            echo "Ви впевнені, що убрати свого фармацевта?";

                            echo "<input type='hidden' name='id' value='" . $_SESSION['session_user']['id'] . "'>";
                            echo "<input type='hidden' name='pharmacy_id' value='" . intval($_GET['remove_selected_pharmacist']) . "'>";
    
                            echo "<div class='row'>"
                            . "<input class='btn-submit col' type='submit' name='remove_selected_pharmacist' value='Так'>"
                            . "<input class='btn-submit white col' type='submit' name='none' value='Ні'>"
                            . "</div>";
                        }
                    ?>
                </div>
            </div>
        </form>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>