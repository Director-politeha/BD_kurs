<?php
require_once("connect.php");

$login = $_POST['login'];
$password = $_POST['password'];

$query = mysqli_query($conn, "SELECT id, password FROM accounts WHERE login='" . mysqli_real_escape_string($conn, $login) . "'");

if(mysqli_num_rows($query) > 0){
    $user_data = mysqli_fetch_assoc($query);
    if ($user_data['password'] === $password) {
        $response = array('status' => 'success');
    } else {
        $response = array('status' => 'invalid_password', 'message' => 'Неверный пароль');
    }
} else {
    $response = array('status' => 'invalid_login', 'message' => 'Пользователя с таким логином не существует');
}

header('Content-Type: application/json');
echo json_encode($response);

mysqli_close($conn);
?>
