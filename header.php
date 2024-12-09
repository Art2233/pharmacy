<?php 


    try {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    } catch(Exception $e) {
        echo 'Помилка при спробі створити сесію';
        exit;
    }
?>

<div class="header">
    <div class="row">
        <div class="col-9">
            <a href="main_page.php">Повернутися до головної сторінки</a>
        </div>
        <div class="col-2">
            <?php
                echo "Ім'я користувача: " . $_SESSION['session_user']['name'];
            ?>
        </div>
        <div class="col-1">
            <a href="logout.php">Вийти</a>
        </div>
    </div>
</div>