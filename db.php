<?php
$host = 'localhost';  // Хост (обычно localhost)
$dbname = 'museum'; // Имя вашей БД
$user = 'root'; // Имя пользователя MySQL
$password = ''; // Пароль MySQL (по умолчанию пустой для XAMPP)

$conn = mysqli_connect($host, $user, $password, $dbname);

// Проверка соединения
if (!$conn) {
    die("Ошибка подключения к БД: " . mysqli_connect_error());
}

// Установка кодировки UTF-8
mysqli_set_charset($conn, "utf8");
?>
