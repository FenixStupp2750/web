<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login'];
    $password = $_POST['password'];

    $query = "SELECT * FROM посетители WHERE Логин = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['Пароль'])) {
            $_SESSION['user_id'] = $user['КодПосетителя'];
            $_SESSION['login'] = $user['Логин'];
            header("Location: profile.php");
        } else {
            $_SESSION['error'] = "Неверный пароль";
        }
    } else {
        $_SESSION['error'] = "Пользователь с таким логином не найден";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
	 <div class="container">

    <?php include 'includes/header.php'; ?>

    <section class="login">
        <h2>Авторизация</h2>
        <?php if (isset($_SESSION['error'])): ?>
            <p class="error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <form action="login.php" method="post">
            <input type="text" name="login" placeholder="Логин" required>
            <input type="password" name="password" placeholder="Пароль" required>
            <button type="submit">Войти</button>
        </form>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>
</html>