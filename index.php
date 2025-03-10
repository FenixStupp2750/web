<?php
session_start();
require 'db.php'; // Подключение к БД
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Музей - Главная</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?> 

<section class="hero">
    <h1>Добро пожаловать в музей!</h1>
    <p>Уникальные экспозиции и исторические артефакты.</p>
    <a href="exponats.php" class="btn">Посмотреть экспонаты</a>
</section>

<section class="events">
    <h2>Ближайшие мероприятия</h2>
    <ul>
        <?php
        require 'db.php'; // Подключение к БД
        
        $today = date("Y-m-d"); // Получаем текущую дату
        $query = "SELECT Название, ДатаПроведения FROM мероприятия WHERE ДатаПроведения >= '$today' ORDER BY ДатаПроведения ASC LIMIT 3";
        $result = mysqli_query($conn, $query);

        if (!$result) {
            die("Ошибка запроса: " . mysqli_error($conn)); // Выводим ошибку SQL-запроса
        }

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<li><strong>{$row['Название']}</strong> - {$row['ДатаПроведения']}</li>";
            }
        } else {
            echo "<p>Мероприятий пока нет</p>";
        }
        ?>
    </ul>
</section>


<?php include 'includes/footer.php'; ?> 

</body>
</html>
