<?php

require_once("connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST["login"];
    $email = $_POST["email"];

    // Проверяем, существует ли пользователь с таким же логином
    $query = mysqli_query($conn, "SELECT id FROM accounts WHERE login='" . mysqli_real_escape_string($conn, $login) . "'");
    if (mysqli_num_rows($query) > 0) {
        echo "login_exists";
        exit();
    }

    // Проверяем, существует ли пользователь с таким же email
    $query = mysqli_query($conn, "SELECT id FROM accounts WHERE email='" . mysqli_real_escape_string($conn, $email) . "'");
    if (mysqli_num_rows($query) > 0) {
        echo "email_exists";
        exit();
    }

    // Если пользователя с таким логином или email не существует
    echo "not_exists";
}

?>
