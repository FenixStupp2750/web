<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

// Добавление каждого билета в таблицу экспозиции
foreach ($_SESSION['cart'] as $item) {
    $user_id = $_SESSION['user_id'];
    $event_id = $item['event_id'];
    $exhibit_id = $item['exhibit_id'];

    $query = "INSERT INTO экспозиции (КодПосетителя, КодМероприятия, КодЭкспоната) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $user_id, $event_id, $exhibit_id);
    $stmt->execute();
}

// Очистка корзины
$_SESSION['cart'] = [];

header("Location: profile.php");
exit();
?>