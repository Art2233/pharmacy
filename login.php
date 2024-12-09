<?php session_start(); ?>
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

    if (isset($_SESSION["session_user"])) {

        header("Location: main_page.php");
    }

    if (isset($_POST["login"])) {

        if (!empty($_POST['email']) && !empty($_POST['password'])) {

            $email = htmlspecialchars($_POST['email']); 
            $password = htmlspecialchars($_POST['password']);
            $query = mysqli_query($connect, "SELECT * FROM client WHERE email='$email' AND password='$password'");
            
            if (mysqli_num_rows(mysqli_query($connect, "SELECT * FROM client WHERE email='$email' AND password='$password'"))) {

                $query = mysqli_query($connect, "SELECT * FROM client WHERE email='$email' AND password='$password'");
                $_SESSION['session_user_type'] = 'client';
            }
            else if (mysqli_num_rows(mysqli_query($connect, "SELECT * FROM admin WHERE email='$email' AND password='$password'"))) {

                $query = mysqli_query($connect, "SELECT * FROM admin WHERE email='$email' AND password='$password'");
                $_SESSION['session_user_type'] = 'admin';
            }
            else if (mysqli_num_rows(mysqli_query($connect, "SELECT * FROM pharmacist WHERE email='$email' AND password='$password'"))) {

                $query = mysqli_query($connect, "SELECT * FROM pharmacist WHERE email='$email' AND password='$password'");
                $_SESSION['session_user_type'] = 'pharmacist';
            }
            else {

                echo "Помилка в запиті до бази даних!";
            }

            $numrows = mysqli_num_rows($query);

            if ($numrows != 0) {

                while ($row = mysqli_fetch_assoc($query)) {

                    $dbemail = $row['email'];
                    $dbpassword = $row['password'];
                    if ($email == $dbemail && $password == $dbpassword) {
    
                        $_SESSION['session_user'] = $row;
                        header("Location: main_page.php");
                    }
                }

            }
            else {

                echo "Невірне ім'я користувача або пароль!";
            }
        }
        else {
            echo "Всі поля обов'язкові для заповнення!";
        }
    }
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Login</title>
        <link href="styles/login.css" media="screen" rel="stylesheet">
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>
    </head>
    <body>
        <div class="container mlogin">
            <div id="login">
                <h1>Вхід</h1>
                <form action="" id="loginform" method="post" name="loginform">
                    <p>
                        <label for="user_login">Пошта<br>
                            <input class="input" id="email" name="email" size="20" type="text" value="">
                        </label>
                    </p>
                    <p>
                        <label for="user_pass">Пароль<br>
                            <input class="input" id="password" name="password" size="20" type="password" value="">
                        </label>
                    </p>
                    <p class="submit">
                        <input class="button" name="login" type="submit" value="Увійти">
                    </p>
                    <p class="regtext">Ще не зареєстровані?<a href="register.php">Реєстрація</a>!</p>
                </form>
            </div>
        </div>
    </body>
</html>
