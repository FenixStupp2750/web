<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_id = $_POST['event_id'];
    $exhibit_id = $_POST['exhibit_id'];

    // Добавляем билет в корзину (сессию)
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $_SESSION['cart'][] = [
        'event_id' => $event_id,
        'exhibit_id' => $exhibit_id
    ];

    // Остаемся на странице мероприятий
    header("Location: events.php");
    exit();
}
?>