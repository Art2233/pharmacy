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
    die('Помилка при підключенні до бази даних');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone_number = filter_input(INPUT_POST, 'phone_number', FILTER_SANITIZE_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if (!$name || !$email || !$phone_number || !$password) {

        $message = "Всі поля обов'язкові для заповнення!";
    }
    else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Некоректний email!";
    }
    else {

        $query = $connect->prepare("SELECT 'user_type' FROM client WHERE email = ?");
        $query->bind_param("s", $email);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows > 0) {

            $message = "Цей емейл вже існує!";
        }
        else {

            if ($email === false) {

                $message = "Некоректний формат email!";
            }
            else {

                $emailRegex = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';

                if (!preg_match($emailRegex, $email)) {
                    $message = "Email повинен бути у форматі: example@domain.com!";
                }
                else {

                    $domain = substr(strrchr($email, "@"), 1);
                    if (!checkdnsrr($domain, "MX")) {
                        $message = "Домен email не має поштового сервера!";
                    }
                    else {

                            $query = $connect->prepare("INSERT INTO client (name, email, phone_number, password) VALUES (?, ?, ?, ?)");
                            $query->bind_param("ssss", $name, $email, $phone_number, $password);
                            $query->execute();
                
                            $_SESSION['session_user_type'] = 'client';
                            $_SESSION['session_user'] = ['name' => $name, 'email' => $email];
                            header("Location: main_page.php");
                            exit;
                    }
                }
            }

        }
    }
}
?>

<?php if (!empty($message)) { echo "<p class='error'>ПОВІДОМЛЕННЯ: " . $message . "</p>"; } ?>

<!DOCTYPE html>
<html lang="uk">
    <head>
        <meta charset="utf-8">
        <title>Реєстрація</title>
        <link href="styles/login.css" media="screen" rel="stylesheet">
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>
    </head>
    <body>
        <div class="container mregister">
            <div id="login">
                <h1>Реєстрація</h1>
                <form action="register.php" id="registerform" method="post" name="registerform">
                    <p>
                        <label for="name">Повне ім'я<br>
                        <input class="input" id="name" name="name" size="32" type="text" value=""></label>
                    </p>
                    <p>
                        <label for="phone_number">Номер телефону<br>
                        <input class="input" id="phone_number" name="phone_number" size="20" type="text" value=""></label>
                    </p>
                    <p>
                        <label for="email">E-mail<br>
                        <input class="input" id="email" name="email" size="32" type="email" value=""></label>
                    </p>
                    <p>
                        <label for="password">Пароль<br>
                        <input class="input" id="password" name="password" size="32" type="password" value=""></label>
                    </p>
                    <p class="submit">
                        <input class="button" id="register" name="register" type="submit" value="Зареєструватися">
                    </p>
                    <p class="regtext">Вже зареєстровані? <a href="login.php">Введіть ім'я користувача</a>!</p>
                </form>
            </div>
        </div>
        <footer>
        </footer>
    </body>
</html>