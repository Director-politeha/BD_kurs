<?php

require_once("connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST["login"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Проверяем, существует ли пользователь с таким же логином или email
    $query = mysqli_query($conn, "SELECT id FROM accounts WHERE login='" . mysqli_real_escape_string($conn, $login) . "' OR email='" . mysqli_real_escape_string($conn, $email) . "'");

    if (mysqli_num_rows($query) > 0) {
        $error_message = "Пользователь с таким логином или email уже существует!";
    } else {
        // Вставляем данные нового пользователя в базу данных
        $insert_query = "INSERT INTO accounts (login, email, password) VALUES ('$login', '$email', '$password')";
        $result = mysqli_query($conn, $insert_query);

        if (!$result) {
            $error_message = "Не удалось зарегистрироваться!";
        } else {
            // Регистрация прошла успешно, перенаправляем на страницу admin.php
            header("Location: admin.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .error-message {
            transition: opacity 2s ease-in-out;
            opacity: 1;
            background-color: rgba(241, 118, 158, 0.7); /* Цвет фона ошибки */
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
            color: white;
        }
        .error-message.fade-out {
            opacity: 0;
        }
    </style>
</head>
<body>
    <video autoplay muted loop class="video-bg">
        <source src="1.mp4" type="video/mp4">
        Ваш браузер не поддерживает тег видео.
    </video>

    <div class="content">
        <h3 class="hello-text">Регистрация</h3>
        <form id="registration-form" method="POST" action="reg.php">
            <b>Логин</b><br>
            <input type="text" name="login"><br>
            <b>Email</b><br>
            <input type="text" name="email"><br>
            <b>Пароль</b><br>
            <input type="password" name="password"><br><br>
            <input type="submit" value="Отправить">
        </form>
        <div id="error-message" class="error-message" style="display: none;"></div>
    </div>
    <script>
    document.getElementById("registration-form").addEventListener("submit", function(event) {
        event.preventDefault(); // Предотвращаем отправку формы

        // Получаем значения полей формы
        var login = this.elements["login"].value;
        var email = this.elements["email"].value;
        var password = this.elements["password"].value;

        // Регулярное выражение для проверки корректности email
        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        // Регулярное выражение для проверки пароля
        var passwordPattern = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/;

        // Проверка на заполнение всех полей формы
        var errorMessage = "";
        if (!login || !email || !password) {
            errorMessage = "Пожалуйста, заполните все поля формы";
        } else if (login.length < 5 || login.length > 90) {
            errorMessage = "Логин должен иметь более 5 символов";            
        } else if (!emailPattern.test(email)) {
            errorMessage = "Неверный адрес электронной почты";
        } else if (password.length < 8 || !passwordPattern.test(password)) {
            errorMessage = "Пароль должен быть длиннее 8 символов и содержать как минимум одну цифру и одну букву";
        }

        // Отображение сообщения об ошибке
        var errorMessageElement = document.getElementById("error-message");
        if (errorMessage) {
            errorMessageElement.textContent = errorMessage;
            errorMessageElement.style.display = "block";
            errorMessageElement.classList.remove("fade-out");

            // Добавляем затухание через 3 секунды
            setTimeout(function() {
                errorMessageElement.classList.add("fade-out");
                setTimeout(function() {
                    errorMessageElement.style.display = "none";
                }, 2000); // Ждем время затухания (2 секунды), чтобы скрыть элемент
            }, 3000);
        } else {
            // Проверка на существование пользователя с таким логином или email
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "check_user.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    if (xhr.responseText === "login_exists") {
                        errorMessageElement.textContent = "Пользователь с таким логином уже существует";
                        errorMessageElement.style.display = "block";
                        errorMessageElement.classList.remove("fade-out");

                        // Добавляем затухание через 3 секунды
                        setTimeout(function() {
                            errorMessageElement.classList.add("fade-out");
                            setTimeout(function() {
                                errorMessageElement.style.display = "none";
                            }, 2000); // Ждем время затухания (2 секунды), чтобы скрыть элемент
                        }, 3000);
                    } else if (xhr.responseText === "email_exists") {
                        errorMessageElement.textContent = "Пользователь с таким email уже существует";
                        errorMessageElement.style.display = "block";
                        errorMessageElement.classList.remove("fade-out");

                        // Добавляем затухание через 3 секунды
                        setTimeout(function() {
                            errorMessageElement.classList.add("fade-out");
                            setTimeout(function() {
                                errorMessageElement.style.display = "none";
                            }, 2000); // Ждем время затухания (2 секунды), чтобы скрыть элемент
                        }, 3000);
                    } else {
                        document.getElementById("registration-form").submit(); // Отправка формы, если нет ошибок
                    }
                }
            };
            xhr.send("login=" + encodeURIComponent(login) + "&email=" + encodeURIComponent(email));
        }
    });
    </script>

</body>
</html>
