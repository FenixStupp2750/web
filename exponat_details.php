<?php
session_start();
require 'db.php';

if (!isset($_GET['id'])) {
    header("Location: exponats.php");
    exit();
}

$exponat_id = $_GET['id'];

// Получаем информацию о выбранном экспонате
$query = "SELECT экспонаты.*, эпохи.ВременнойПромежуток 
          FROM экспонаты 
          JOIN эпохи ON экспонаты.КодЭпохи = эпохи.КодЭпохи 
          WHERE экспонаты.КодЭкспоната = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $exponat_id);
$stmt->execute();
$result = $stmt->get_result();
$exponat = $result->fetch_assoc();

if (!$exponat) {
    header("Location: exponats.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $exponat['Название'] ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <?php include 'includes/header.php'; ?>

        <main>
            <section class="exponat-details">
                <h2><?= $exponat['Название'] ?></h2>
                <p><strong>Страна происхождения:</strong> <?= $exponat['СтранаПроисхождения'] ?></p>
                <p><strong>Эпоха:</strong> <?= $exponat['ВременнойПромежуток'] ?></p>
                <p><strong>Вес:</strong> <?= $exponat['Вес'] ?> кг</p>
                <a href="exponats.php" class="btn">Назад к списку экспонатов</a>
            </section>
        </main>

        <?php include 'includes/footer.php'; ?>
    </div>
</body>
</html>