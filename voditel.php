<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Водители</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;700&display=swap&subset=cyrillic" rel="stylesheet">
    <style>
        #addForm, #editForm {
            display: none;
            margin-top: 20px;
        }
        .successMessageElement, .errorMessageElement {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            border-radius: 20px;
            font-size: 20px;
        }
        .successMessageElement {
            background-color: rgba(75, 202, 129, 0.7); /* Цвет фона успешной операции */
            color: white;
        }
        .errorMessageElement {
            background-color: rgba(241, 118, 158, 0.7); /* Цвет фона ошибки */
            color: white;
        }
    </style>
</head>
<body>
<video autoplay loop muted>
        <source src="1.mp4" type="video/mp4">
        <!-- Добавьте другие форматы видео, если необходимо -->
    </video>
    <button onclick="window.location.href='admin.php'">Назад</button>
    <h3 class="hello-text">Водители</h3>
<?php
require_once("connect.php");

// Функция для показа уведомления об успешной операции
function showSuccess($message) {
    echo "<script>
            var successElement = document.createElement('div');
            successElement.classList.add('successMessageElement');
            successElement.innerText = '$message';
            document.body.appendChild(successElement);
            setTimeout(function() {
                successElement.style.opacity = '0';
                setTimeout(function() {
                    successElement.remove();
                }, 1000);
            }, 3000);
          </script>";
}

// Функция для показа ошибки
function showError($message) {
    echo "<script>
            var errorElement = document.createElement('div');
            errorElement.classList.add('errorMessageElement');
            errorElement.innerText = '$message';
            document.body.appendChild(errorElement);
            setTimeout(function() {
                errorElement.style.opacity = '0';
                setTimeout(function() {
                    errorElement.remove();
                }, 1000);
            }, 3000);
          </script>";
}

// Обработка данных формы добавления
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add"])) {
    $fio = $_POST["fio"];
    $numvod = $_POST["numvod"];
    $numcar = $_POST["numcar"];
    $numgruz = $_POST["numgruz"];
    
    $insert_sql = "INSERT INTO voditel (fio, numvod, numcar, numgruz) VALUES ('$fio', '$numvod', '$numcar', '$numgruz')";
    if (mysqli_query($conn, $insert_sql)) {
        showSuccess("Новая запись успешно добавлена.");
    } else {
        showError("Ошибка: " . $insert_sql . "<br>" . mysqli_error($conn));
    }
}

// Обработка данных для удаления
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete"])) {
    $id = $_POST["id"];
    $delete_sql = "DELETE FROM voditel WHERE idv='$id'";
    if (mysqli_query($conn, $delete_sql)) {
        showSuccess("Запись успешно удалена.");
    } else {
        showError("Ошибка: " . $delete_sql . "<br>" . mysqli_error($conn));
    }
}

// Обработка данных для редактирования
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["edit"])) {
    $id = $_POST["id"];
    $fio = $_POST["fio"];
    $numvod = $_POST["numvod"];
    $numcar = $_POST["numcar"];
    $numgruz = $_POST["numgruz"];
    
    $update_sql = "UPDATE voditel SET fio='$fio', numvod='$numvod', numcar='$numcar', numgruz='$numgruz' WHERE idv='$id'";
    if (mysqli_query($conn, $update_sql)) {
        showSuccess("Запись успешно обновлена.");
    } else {
        showError("Ошибка: " . $update_sql . "<br>" . mysqli_error($conn));
    }
}
?>
<?php
// Вывод данных таблицы
echo '<table border="1">';
echo '<tr><th>№</th><th>Водитель</th><th>Номер Водителя</th><th>Номер ТС</th><th>Номер груза</th><th>Действия</th></tr>';
$sql = "SELECT * FROM voditel";
if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>';
        echo '<td>' . $row["idv"] . '</td>';
        echo '<td>' . $row["fio"] . '</td>';
        echo '<td>' . $row["numvod"] . '</td>';
        echo '<td>' . $row["numcar"] . '</td>';
        echo '<td>' . $row["numgruz"] . '</td>';
        echo '<td>';
        echo '<button onclick="showEditForm(' . $row["idv"] . ', \'' . $row["fio"] . '\', \'' . $row["numvod"] . '\', \'' . $row["numcar"] . '\', \'' . $row["numgruz"] . '\')">Изменить</button>';
        echo ' <form method="post" action="" style="display:inline-block; margin:0;">';
        echo ' <input type="hidden" name="delete" value="true">';
        echo ' <input type="hidden" name="id" value="' . $row["idv"] . '">';
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
        <label for="fio">Водитель:</label>
        <input type="text" id="fio" name="fio" required><br><br>
        <label for="numvod">Номер водителя:</label>
        <input type="text" id="numvod" name="numvod" required><br><br>
        <label for="numcar">Номер ТС:</label>
        <input type="text" id="numcar" name="numcar" required><br><br>
        <label for="numgruz">Номер груза:</label>
        <input type="text" id="numgruz" name="numgruz" required><br><br>
        <input type="submit" value="Добавить">
    </form>
</div>

<!-- Форма для редактирования данных -->
<div id="editForm">
    <form method="post" action="">
        <input type="hidden" name="edit" value="true">
        <input type="hidden" id="editId" name="id">
        <label for="editfio">Водитель:</label>
        <input type="text" id="editfio" name="fio" required><br><br>
        <label for="editnumvod">Номер водителя:</label>
        <input type="text" id="editnumvod" name="numvod" required><br><br>
        <label for="editnumcar">Номер ТС:</label>
        <input type="text" id="editnumcar" name="numcar" required><br><br>
        <label for="editnumgruz">Номер груза:</label>
        <input type="text" id="editnumgruz" name="numgruz" required><br><br>
        <input type="submit" value="Сохранить">
    </form>
</div>

<script>
    function showAddForm() {
        document.getElementById('addForm').style.display = 'block';
        document.getElementById('editForm').style.display = 'none';
    }

    function showEditForm(id, fio, numvod, numcar, numgruz) {
        document.getElementById('editForm').style.display = 'block';
        document.getElementById('addForm').style.display = 'none';
        document.getElementById('editId').value = id;
        document.getElementById('editfio').value = fio;
        document.getElementById('editnumvod').value = numvod;
        document.getElementById('editnumcar').value = numcar;
        document.getElementById('editnumgruz').value = numgruz;
    }
</script>

</body>
</html>