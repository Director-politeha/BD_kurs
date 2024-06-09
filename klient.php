<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Клиенты</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;700&display=swap&subset=cyrillic" rel="stylesheet">
    <style>
        #addForm, #editForm {
            display: none;
            margin-top: 20px;
        }
        .errorMessageElement {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    padding: 20px;
    background-color: rgba(241, 118, 158, 0.7); /* Цвет фона ошибки */
    color: white;
    font-size: 20px;
    border-radius: 20px;
    animation: fadeIn 0.5s ease-in-out, fadeOut 0.5s ease-in-out 2.5s forwards;
}

@keyframes fadeIn {
    0% { opacity: 0; }
    100% { opacity: 1; }
}

@keyframes fadeOut {
    0% { opacity: 1; }
    100% { opacity: 0; }
}
    </style>
</head>
<body>
<video autoplay loop muted>
    <source src="1.mp4" type="video/mp4">
    <!-- Добавьте другие форматы видео, если необходимо -->
</video>
<button onclick="window.location.href='admin.php'">Назад</button>
<h3 class="hello-text">Клиенты</h3>
<div class="errorMessageElement" id="notification"></div> <!-- Всплывающее уведомление -->
<?php
require_once("connect.php");

// Функция для показа уведомления
function showNotification($message) {
    echo "<script>
            var notification = document.getElementById('notification');
            notification.innerText = '$message';
            notification.style.display = 'block';
            setTimeout(function() {
                notification.style.opacity = '0';
                setTimeout(function() {
                    notification.style.display = 'none';
                    notification.style.opacity = '1';
                }, 1000);
            }, 3000);
          </script>";
}

// Обработка данных формы добавления
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add"])) {
    $nameklient = $_POST["nameklient"];
    $numklient = $_POST["numklient"];
    $adressklient = $_POST["adressklient"];
    
    $insert_sql = "INSERT INTO klient (nameklient, numklient, adressklient) VALUES ('$nameklient', '$numklient', '$adressklient')";
    if (mysqli_query($conn, $insert_sql)) {
        showNotification("Новая запись успешно добавлена.");
    } else {
        echo "Ошибка: " . $insert_sql . "<br>" . mysqli_error($conn);
    }
}

// Обработка данных для удаления
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete"])) {
    $id = $_POST["id"];
    $delete_sql = "DELETE FROM klient WHERE idk='$id'";
    if (mysqli_query($conn, $delete_sql)) {
        showNotification("Запись успешно удалена.");
    } else {
        echo "Ошибка: " . $delete_sql . "<br>" . mysqli_error($conn);
    }
}

// Обработка данных для редактирования
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["edit"])) {
    $id = $_POST["id"];
    $nameklient = $_POST["nameklient"];
    $numklient = $_POST["numklient"];
    $adressklient = $_POST["adressklient"];
    
    $update_sql = "UPDATE klient SET nameklient='$nameklient', numklient='$numklient', adressklient='$adressklient' WHERE idk='$id'";
    if (mysqli_query($conn, $update_sql)) {
        showNotification("Запись успешно обновлена.");
    } else {
        echo "Ошибка: " . $update_sql . "<br>" . mysqli_error($conn);
    }
}
?>


<?php
// Вывод данных таблицы
echo '<table border="1">';
echo '<tr><th>№</th><th>Клиент</th><th>Номер клиента</th><th>Адрес доставки</th><th>Действия</th></tr>';
$sql = "SELECT * FROM klient";
if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td>' . $row["idk"] . '</td>';
        echo '<td>' . $row["nameklient"] . '</td>';
        echo '<td>' . $row["numklient"] . '</td>';
        echo '<td>' . $row["adressklient"] . '</td>';
        echo '<td>';
        echo '<button onclick="showEditForm(' . $row["idk"] . ', \'' . $row["nameklient"] . '\', \'' . $row["numklient"] . '\', \'' . $row["adressklient"] . '\')">Изменить</button>';
        echo ' <form method="post" action="" style="display:inline-block; margin:0;">';
        echo ' <input type="hidden" name="delete" value="true">';
        echo ' <input type="hidden" name="id" value="' . $row["idk"] . '">';
        echo ' <input type="submit" value="Удалить">';
        echo '</form>';
        echo '</td>';
        echo '</tr>';
    }
} 
echo '</table>';
mysqli_close($conn);
?>

<!-- Кнопка для показа формы добавления -->
<button onclick="showAddForm()">Добавить</button>

<!-- Форма для добавления данных -->
<div id="addForm">
    <form method="post" action="">
        <input type="hidden" name="add" value="true">
        <label for="nameklient">Клиент:</label>
        <input type="text" id="nameklient" name="nameklient" required><br><br>
        <label for="numklient">Номер клиента:</label>
        <input type="text" id="numklient" name="numklient" required><br><br>
        <label for="adressklient">Адрес доставки:</label>
        <input type="text" id="adressklient" name="adressklient" required><br><br>
        <input type="submit" value="Добавить">
    </form>
</div>

<!-- Форма для редактирования данных -->
<div id="editForm">
    <form method="post" action="">
        <input type="hidden" name="edit" value="true">
        <input type="hidden" id="editId" name="id">
        <label for="editnameklient">Клиент:</label>
        <input type="text" id="editnameklient" name="nameklient" required><br><br>
        <label for="editnumklient">Номер клиента:</label>
        <input type="text" id="editnumklient" name="numklient" required><br><br>
        <label for="editadressklient">Адрес доставки:</label>
        <input type="text" id="editadressklient" name="adressklient" required><br><br>
        <input type="submit" value="Сохранить">
    </form>
</div>

<script>
    function showAddForm() {
        document.getElementById('addForm').style.display = 'block';
        document.getElementById('editForm').style.display = 'none';
    }

    function showEditForm(id, nameklient, numklient, adressklient) {
        document.getElementById('editForm').style.display = 'block';
        document.getElementById('addForm').style.display = 'none';
        document.getElementById('editId').value = id;
        document.getElementById('editnameklient').value = nameklient;
        document.getElementById('editnumklient').value = numklient;
        document.getElementById('editadressklient').value = adressklient;
    }

    // Функция для показа уведомления
function showNotification(message) {
    var notification = document.getElementById('notification');
    notification.innerText = message;
    notification.style.display = 'block';

    // Скрытие уведомления через 3 секунды
    setTimeout(function() {
        notification.style.display = 'none';
    }, 3000); // 3000 миллисекунд (3 секунды)
}



</script>

</body>
</html>
