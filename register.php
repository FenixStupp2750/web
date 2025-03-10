<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $email = $_POST['email'];
    $surname = $_POST['surname'];
    $name = $_POST['name'];
    $patronymic = $_POST['patronymic'];
    $phone = $_POST['phone'];

    $query = "INSERT INTO посетители (Логин, Пароль, email, Фамилия, Имя, Отчество, НомерТелефона) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssss", $login, $password, $email, $surname, $name, $patronymic, $phone);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Регистрация прошла успешно!";
        header("Location: login.php");
    } else {
        $_SESSION['error'] = "Ошибка при регистрации: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section class="registration">
        <h2>Регистрация</h2>
        <?php if (isset($_SESSION['error'])): ?>
            <p class="error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <form action="register.php" method="post">
            <input type="text" name="surname" placeholder="Фамилия" required>
            <input type="text" name="name" placeholder="Имя" required>
            <input type="text" name="patronymic" placeholder="Отчество">
            <input type="text" name="phone" placeholder="Номер телефона" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="login" placeholder="Логин" required>
            <input type="password" name="password" placeholder="Пароль" required>
            <button type="submit">Зарегистрироваться</button>
        </form>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>
</html>