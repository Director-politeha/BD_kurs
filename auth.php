<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;700&display=swap&subset=cyrillic" rel="stylesheet">
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
        <h3 class="hello-text">Авторизация</h3>
        <form id="login-form" method="POST" action="authr.php">
            <b>Логин</b><br>
            <input type="text" name="login"><br>
            <b>Пароль</b><br>
            <input type="password" name="password"><br><br>
            <input type="submit" value="Отправить">
        </form>
        <div id="error-message" class="error-message"></div>
    </div>

<script>
    const form = document.getElementById('login-form');
    const errorMessage = document.getElementById('error-message');

    form.addEventListener('submit', function(event) {
        event.preventDefault(); // Отменяем стандартное поведение формы

        const login = form.elements['login'].value;
        const password = form.elements['password'].value;

        if (!login || !password) {
            errorMessage.textContent = 'Заполните все поля!';
            errorMessage.style.display = 'block';
        } else {
            // Отправляем запрос на сервер
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'authr.php');
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.status === 'success') {
                        // Если авторизация прошла успешно, перенаправляем на admin.php
                        window.location.href = 'admin.php';
                    } else if (response.status === 'invalid_login') {
                        // Показываем окно с сообщением о неверном логине
                        errorMessage.textContent = response.message;
                        errorMessage.style.display = 'block';
                    } else if (response.status === 'invalid_password') {
                        // Показываем окно с сообщением о неверном пароле
                        errorMessage.textContent = response.message;
                        errorMessage.style.display = 'block';
                    }
                }
            };
            const data = 'login=' + encodeURIComponent(login) + '&password=' + encodeURIComponent(password);
            xhr.send(data);
        }
    });
</script>



</body>
</html>
