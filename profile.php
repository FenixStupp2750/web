<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Получение личных данных пользователя
$user_query = "SELECT Фамилия, Имя, Отчество, email, НомерТелефона FROM посетители WHERE КодПосетителя = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user_data = $user_result->fetch_assoc();

// Получение билетов пользователя
$tickets_query = "SELECT экспозиции.*, мероприятия.Название AS мероприятие, экспонаты.Название AS экспонат 
                  FROM экспозиции 
                  JOIN мероприятия ON экспозиции.КодМероприятия = мероприятия.КодМероприятия 
                  JOIN экспонаты ON экспозиции.КодЭкспоната = экспонаты.КодЭкспоната 
                  WHERE экспозиции.КодПосетителя = ?";
$stmt = $conn->prepare($tickets_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$tickets_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <?php include 'includes/header.php'; ?>

        <main>
            <section class="profile">
                <h2>Личный кабинет</h2>

                <!-- Личные данные пользователя -->
                <div class="personal-info">
                    <h3>Личные данные</h3>
                    <p><strong>Фамилия:</strong> <?= htmlspecialchars($user_data['Фамилия']) ?></p>
                    <p><strong>Имя:</strong> <?= htmlspecialchars($user_data['Имя']) ?></p>
                    <p><strong>Отчество:</strong> <?= htmlspecialchars($user_data['Отчество']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($user_data['email']) ?></p>
                    <p><strong>Номер телефона:</strong> <?= htmlspecialchars($user_data['НомерТелефона']) ?></p>
                    <a href="edit_profile.php" class="btn">Редактировать данные</a>
                </div>

                <!-- Список билетов -->
                <div class="tickets">
                    <h3>Ваши билеты</h3>
                    <?php if ($tickets_result->num_rows > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($tickets_result)): ?>
                            <div class="ticket">
                                <h4><?= htmlspecialchars($row['мероприятие']) ?></h4>
                                <p><strong>Экспонат:</strong> <?= htmlspecialchars($row['экспонат']) ?></p>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>У вас пока нет купленных билетов.</p>
                    <?php endif; ?>
                </div>
            </section>
        </main>

        <?php include 'includes/footer.php'; ?>
    </div>
</body>
</html>