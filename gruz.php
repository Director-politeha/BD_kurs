<?php

require_once("connect.php");

function showNotification($message) {
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                var notification = document.createElement('div');
                notification.innerText = '$message';
                notification.className = 'errorMessageElement';
                document.body.appendChild(notification);
                setTimeout(function() {
                    notification.style.opacity = '0';
                    setTimeout(function() {
                        notification.remove();
                    }, 1000);
                }, 3000);
            });
          </script>";
}

// Обработка данных формы добавления
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add"])) {
    $numgruz = $_POST["numgruz"];
    $price = $_POST["price"];
    $adress = $_POST["adress"];
    
    // При добавлении записи в таблицу gruz
    $insert_sql = "INSERT INTO gruz (numgruz, price, adress) VALUES ('$numgruz', '$price', '$adress')";
    if (mysqli_query($conn, $insert_sql)) {
        $last_id = mysqli_insert_id($conn); // Получаем ID только что добавленной записи
        
        // Добавляем номер груза в таблицу voditel с использованием того же ID
        $insert_voditel_sql = "INSERT INTO voditel (idv, numgruz) VALUES ('$last_id', '$numgruz')";
        mysqli_query($conn, $insert_voditel_sql);
        
        // Добавляем запись в таблицу klient только с адресом
        $insert_klient_sql = "INSERT INTO klient (adressklient) VALUES ('$adress')";
        mysqli_query($conn, $insert_klient_sql);
        
        showNotification("Новая запись успешно добавлена.");
    } else {
        echo "Ошибка: " . $insert_sql . "<br>" . mysqli_error($conn);
    }
}

// При обновлении записи в таблице gruz
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["edit"])) {
    $id = $_POST["id"];
    $numgruz = $_POST["numgruz"];
    $price = $_POST["price"];
    $adress = $_POST["adress"];
    
    $update_sql = "UPDATE gruz SET numgruz='$numgruz', price='$price', adress='$adress' WHERE idg='$id'";
    if (mysqli_query($conn, $update_sql)) {
        // Обновляем номер груза в таблице voditel с использованием того же ID
        $update_voditel_sql = "UPDATE voditel SET numgruz='$numgruz' WHERE idv='$id'";
        mysqli_query($conn, $update_voditel_sql);
        
        // Обновляем адрес в таблице klient
        $update_klient_sql = "UPDATE klient SET adressklient='$adress' WHERE idk='$id'";
        mysqli_query($conn, $update_klient_sql);
        
        showNotification("Запись успешно обновлена.");
    } else {
        echo "Ошибка: " . $update_sql . "<br>" . mysqli_error($conn);
    }
}

// Обработка данных для удаления
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete"])) {
    $id = $_POST["id"];
    // Сначала получим адрес и номер груза, чтобы удалить соответствующие записи в таблицах klient и voditel
    $get_data_sql = "SELECT adress, numgruz FROM gruz WHERE idg='$id'";
    $result = mysqli_query($conn, $get_data_sql);
    $row = mysqli_fetch_array($result);
    $adress = $row["adress"];
    $numgruz = $row["numgruz"];
    
    $delete_sql = "DELETE FROM gruz WHERE idg='$id'";
    if (mysqli_query($conn, $delete_sql)) {
        // Удаляем соответствующую запись из таблицы klient
        $delete_klient_sql = "DELETE FROM klient WHERE adressklient='$adress'";
        mysqli_query($conn, $delete_klient_sql);
        
        // Удаляем номер груза из таблицы voditel
        $delete_voditel_sql = "DELETE FROM voditel WHERE numgruz='$numgruz'";
        mysqli_query($conn, $delete_voditel_sql);
        
        showNotification("Запись успешно удалена.");
    } else {
        echo "Ошибка: " . $delete_sql . "<br>" . mysqli_error($conn);
    }
}

// Обработка данных для вывода результатов поиска
if ($_SERVER["REQUEST_METHOD"] == "GET" && (isset($_GET["numgruz"]) || isset($_GET["adress"]))) {
    // Получаем значения numgruz и adress из GET-запроса
    $numgruz = $_GET["numgruz"];
    $adress = $_GET["adress"];

    // Формируем SQL-запрос для выборки записей из базы данных в зависимости от переданных значений
    $sql = "SELECT * FROM gruz WHERE";
    $conditions = array();
    if (!empty($numgruz)) {
        $conditions[] = " numgruz='$numgruz'";
    }
    if (!empty($adress)) {
        $conditions[] = " adress='$adress'";
    }
    $whereClause = implode(" AND ", $conditions);
    $sql .= $whereClause;
    ?>
    <div id="searchResults">
    <?php
    // Выводим таблицу с результатами запроса
    echo '<h2>Результаты поиска</h2>';
    echo '<table border="1">';
    echo '<tr><th>№</th><th>Номер груза</th><th>Стоимость</th><th>Адрес доставки</th><th>Действия</th></tr>';
    if ($result = mysqli_query($conn, $sql)) {
        while ($row = mysqli_fetch_array($result)) {
            echo '<tr>';
            echo '<td>' . $row["idg"] . '</td>';
            echo '<td>' . $row["numgruz"] . '</td>';
            echo '<td>' . $row["price"] . '</td>';
            echo '<td>' . $row["adress"] . '</td>';
            echo '<td>';
            echo '<button onclick="showEditForm(' . $row["idg"] . ', \'' . $row["numgruz"] . '\', \'' . $row["price"] . '\', \'' . $row["adress"] . '\')">Изменить</button>';
            echo ' <form method="post" action="" style="display:inline-block; margin:0;">';
            echo ' <input type="hidden" name="delete" value="true">';
            echo ' <input type="hidden" name="id" value="' . $row["idg"] . '">';
            echo ' <input type="submit" value="Удалить">';
            echo '</form>';
            echo '</td>';
            echo '</tr>';
        }
    } else {
        echo "Ошибка: " . $sql . "<br>" . mysqli_error($conn);
    }
    echo '</table>';
}
?>
</div>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Грузы</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;700&display=swap&subset=cyrillic" rel="stylesheet">
    <style>
        #addForm, #editForm {
            display: none;
            margin-top: 20px;
        }
        th, td {
            font-size: 26px; /* Увеличиваем размер текста */
            padding: 8px;
            text-align: left;
        }
        .errorMessageElement {
            position: fixed;
            background-color: rgba(241, 118, 158, 0.7); /* Цвет фона ошибки */
            top: 20%; /* Располагаем элемент внизу экрана */
            color: white;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            opacity: 1;
            transition: opacity 1s;
            top: 50%; /* Располагаем по вертикали посередине экрана */
            left: 50%; /* Располагаем по горизонтали посередине экрана */
            transform: translate(-50%, -50%); /* Центрируем элемент относительно его собственных размеров */
        }
        .error-message {
            transition: opacity 2s ease-in-out;
            opacity: 1;
            top: 20%; /* Располагаем элемент внизу экрана */
            background-color: rgba(241, 118, 158, 0.7); /* Цвет фона ошибки */
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
            color: white;
        }
        .error-message.fade-out {
            opacity: 0;
        }
        #searchResults {
            position: fixed;
            bottom: 20px; /* Adjust as needed */
            left: 50%;
            transform: translateX(-50%);
        }
        h2{
            color: rgba(14, 59, 107);
            background-color: rgba(255, 255, 255, 0.5); /* белый цвет с полупрозрачностью */
            border-radius: 15px; /* радиус скругления углов */
            width: 80%;
            position: fixed;
            bottom: 150px; /* Adjust as needed */
            left: 50%;
            transform: translateX(-50%);
        }
        
    </style>
</head>
<body>
<video autoplay loop muted>
        <source src="1.mp4" type="video/mp4">
        <!-- Добавьте другие форматы видео, если необходимо -->
    </video>

<!-- Кнопка для возврата на страницу admin.php -->
<button onclick="window.location.href='admin.php'">Назад</button>
<h3 class="hello-text">Управление грузоперевозками</h3>
<!-- Вывод таблицы -->
<?php

echo '<table border="1">';
echo '<tr><th>№</th><th>Номер груза</th><th>Стоимость</th><th>Адрес доставки</th><th>Действия</th></tr>';
$sql = "SELECT * FROM gruz";
if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td>' . $row["idg"] . '</td>';
        echo '<td>' . $row["numgruz"] . '</td>';
        echo '<td>' . $row["price"] . '</td>';
        echo '<td>' . $row["adress"] . '</td>';
        echo '<td>';
        echo '<button onclick="showEditForm(' . $row["idg"] . ', \'' . $row["numgruz"] . '\', \'' . $row["price"] . '\', \'' . $row["adress"] . '\')">Изменить</button>';
        echo ' <form method="post" action="" style="display:inline-block; margin:0;">';
        echo ' <input type="hidden" name="delete" value="true">';
        echo ' <input type="hidden" name="id" value="' . $row["idg"] . '">';
        echo ' <input type="submit" value="Удалить">';
        echo '</form>';
        echo '</td>';
        echo '</tr>';
    }
} else {
    echo "Ошибка: " . $sql . "<br>" . mysqli_error($conn);
}
echo '</table>';
?>

<form method="get" action="" style="text-align: center;">
    <div style="display: inline-block; margin-right: 20px;">
        <label for="searchNumgruz">Выберите номер груза:</label>
        <?php
        // Получаем уникальные номера груза из базы данных
        $sql = "SELECT DISTINCT numgruz FROM gruz";
        $result = mysqli_query($conn, $sql);
        
        // Создаем выпадающий список для выбора номера груза
        echo '<select id="searchNumgruz" name="numgruz">';
        echo '<option value="">-- Выберите --</option>';
        while ($row = mysqli_fetch_array($result)) {
            echo '<option value="' . $row["numgruz"] . '">' . $row["numgruz"] . '</option>';
        }
        echo '</select>';
        ?>
    </div>
    <div style="display: inline-block;">
        <label for="searchAdress">Выберите адрес доставки:</label>
        <?php
        // Получаем уникальные адреса доставки из базы данных
        $sql = "SELECT DISTINCT adress FROM gruz";
        $result = mysqli_query($conn, $sql);
        
        // Создаем выпадающий список для выбора адреса доставки
        echo '<select id="searchAdress" name="adress">';
        echo '<option value="">-- Выберите --</option>';
        while ($row = mysqli_fetch_array($result)) {
            echo '<option value="' . $row["adress"] . '">' . $row["adress"] . '</option>';
        }
        echo '</select>';

        mysqli_close($conn);
        ?>
    </div>
    <input type="submit" value="Искать" style="margin-top: 10px;">
</form>

<!-- Кнопка для показа формы добавления -->
<button onclick="showAddForm()">Добавить</button>

<!-- Форма для добавления данных -->
<div id="addForm" style="display: none;">
    <form method="post" action="">
        <input type="hidden" name="add" value="true">
        <label for="numgruz">Номер груза:</label>
        <input type="text" id="numgruz" name="numgruz" required><br><br>
        <label for="price">Стоимость:</label>
        <input type="text" id="price" name="price" required><br><br>
        <label for="adress">Адрес доставки:</label>
        <input type="text" id="adress" name="adress" required><br><br>
        <input type="submit" value="Добавить">
    </form>
</div>

<!-- Форма для редактирования данных -->
<div id="editForm" style="display: none;">
    <form method="post" action="">
        <input type="hidden" name="edit" value="true">
        <input type="hidden" id="editId" name="id">
        <label for="editNumgruz">Номер груза:</label>
        <input type="text" id="editNumgruz" name="numgruz" required><br><br>
        <label for="editPrice">Стоимость:</label>
        <input type="text" id="editPrice" name="price" required><br><br>
        <label for="editAdress">Адрес доставки:</label>
        <input type="text" id="editAdress" name="adress" required><br><br>
        <input type="submit" value="Сохранить">
    </form>
</div>

<script>
    function showAddForm() {
        document.getElementById('addForm').style.display = 'block';
        document.getElementById('editForm').style.display = 'none';
    }

    function showEditForm(id, numgruz, price, adress) {
        document.getElementById('editForm').style.display = 'block';
        document.getElementById('addForm').style.display = 'none';
        document.getElementById('editId').value = id;
        document.getElementById('editNumgruz').value = numgruz;
        document.getElementById('editPrice').value = price;
        document.getElementById('editAdress').value = adress;
    }
</script>
