<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Получаем текущие данные пользователя
$query = "SELECT * FROM посетители WHERE КодПосетителя = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Обработка формы редактирования
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $surname = $_POST['surname'];
    $name = $_POST['name'];
    $patronymic = $_POST['patronymic'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $new_login = $_POST['new_login'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Обновление основных данных
    $query = "UPDATE посетители SET Фамилия = ?, Имя = ?, Отчество = ?, email = ?, НомерТелефона = ?";
    $params = [$surname, $name, $patronymic, $email, $phone];

    // Обновление логина, если он изменен
    if (!empty($new_login)) {
        // Проверка уникальности логина
        $check_query = "SELECT КодПосетителя FROM посетители WHERE Логин = ? AND КодПосетителя != ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("si", $new_login, $user_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $_SESSION['error'] = "Логин уже занят!";
            header("Location: edit_profile.php");
            exit();
        }

        $query .= ", Логин = ?";
        $params[] = $new_login;
    }

    // Обновление пароля, если он изменен
    if (!empty($new_password)) {
        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            $query .= ", Пароль = ?";
            $params[] = $hashed_password;
        } else {
            $_SESSION['error'] = "Пароли не совпадают!";
            header("Location: edit_profile.php");
            exit();
        }
    }

    $query .= " WHERE КодПосетителя = ?";
    $params[] = $user_id;

    $stmt = $conn->prepare($query);
    $types = str_repeat('s', count($params)); // Определяем типы параметров
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Данные успешно обновлены!";
        header("Location: profile.php");
        exit();
    } else {
        $_SESSION['error'] = "Ошибка при обновлении данных: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование профиля</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <?php include 'includes/header.php'; ?>

        <main>
            <section class="edit-profile">
                <h2>Редактирование профиля</h2>
                <?php if (isset($_SESSION['error'])): ?>
                    <p class="error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
                <?php endif; ?>
                <form action="edit_profile.php" method="post">
                    <label for="surname">Фамилия:</label>
                    <input type="text" name="surname" value="<?= $user['Фамилия'] ?>" required>

                    <label for="name">Имя:</label>
                    <input type="text" name="name" value="<?= $user['Имя'] ?>" required>

                    <label for="patronymic">Отчество:</label>
                    <input type="text" name="patronymic" value="<?= $user['Отчество'] ?>">

                    <label for="email">Email:</label>
                    <input type="email" name="email" value="<?= $user['email'] ?>" required>

                    <label for="phone">Номер телефона:</label>
                    <input type="text" name="phone" value="<?= $user['НомерТелефона'] ?>" required>

                    <label for="new_login">Новый логин:</label>
                    <input type="text" name="new_login" placeholder="Введите новый логин">

                    <label for="new_password">Новый пароль:</label>
                    <input type="password" name="new_password" placeholder="Введите новый пароль">

                    <label for="confirm_password">Подтвердите пароль:</label>
                    <input type="password" name="confirm_password" placeholder="Подтвердите новый пароль">

                    <button type="submit">Сохранить изменения</button>
                </form>
            </section>
        </main>

        <?php include 'includes/footer.php'; ?>
    </div>
</body>
</html>