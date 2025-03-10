<?php
session_start();
require 'db.php';

// Получение будущих мероприятий
$query = "SELECT * FROM мероприятия WHERE ДатаПроведения >= CURDATE() ORDER BY ДатаПроведения ASC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мероприятия</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <?php include 'includes/header.php'; ?>

        <main>
            <section class="events">
                <h2>Мероприятия</h2>

                <!-- Список мероприятий -->
                <div class="events-grid">
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <div class="event-card">
                            <h3><?= htmlspecialchars($row['Название']) ?></h3>
                            <p class="event-date">📅 <?= htmlspecialchars($row['ДатаПроведения']) ?></p>

                            <!-- Форма для добавления в корзину (только для авторизованных пользователей) -->
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <form action="add_to_cart.php" method="post" class="event-form">
                                    <input type="hidden" name="event_id" value="<?= $row['КодМероприятия'] ?>">
                                    <label for="exhibit_id">Выберите экспонат:</label>
                                    <select name="exhibit_id" id="exhibit_id" required>
                                        <?php
                                        // Получение списка экспонатов
                                        $exhibits_query = "SELECT * FROM экспонаты";
                                        $exhibits_result = mysqli_query($conn, $exhibits_query);
                                        while ($exhibit = mysqli_fetch_assoc($exhibits_result)) {
                                            echo "<option value='{$exhibit['КодЭкспоната']}'>{$exhibit['Название']}</option>";
                                        }
                                        ?>
                                    </select>
                                    <button type="submit" class="btn-add-to-cart">Добавить в корзину</button>
                                </form>
                            <?php else: ?>
                                <p class="event-login-prompt">Чтобы добавить мероприятие в корзину, <a href="login.php">войдите</a> или <a href="register.php">зарегистрируйтесь</a>.</p>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            </section>
        </main>

        <?php include 'includes/footer.php'; ?>
    </div>
</body>
</html>