<?php
session_start();
require 'db.php';

// Обработка удаления элемента из корзины
if (isset($_GET['remove'])) {
    $index = intval($_GET['remove']);
    if (isset($_SESSION['cart'][$index])) {
        unset($_SESSION['cart'][$index]);
        // Переиндексация массива после удаления элемента
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
    // Перенаправление обратно на страницу корзины
    header('Location: cart.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Корзина</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <?php include 'includes/header.php'; ?>

        <main>
            <section class="cart">
                <h2>Корзина</h2>

                <?php if (!isset($_SESSION['user_id'])): ?>
                    <!-- Сообщение для неавторизованных пользователей -->
                    <div class="cart-login-prompt">
                        <p>Чтобы просмотреть корзину, <a href="login.php">войдите</a> или <a href="register.php">зарегистрируйтесь</a>.</p>
                    </div>
                <?php else: ?>
                    <?php if (empty($_SESSION['cart'])): ?>
                        <p>Ваша корзина пуста.</p>
                    <?php else: ?>
                        <div class="cart-items">
                            <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                                <?php
                                // Получение информации о мероприятии и экспонате
                                $event_query = "SELECT * FROM мероприятия WHERE КодМероприятия = ?";
                                $stmt = $conn->prepare($event_query);
                                $stmt->bind_param("i", $item['event_id']);
                                $stmt->execute();
                                $event = $stmt->get_result()->fetch_assoc();

                                $exhibit_query = "SELECT * FROM экспонаты WHERE КодЭкспоната = ?";
                                $stmt = $conn->prepare($exhibit_query);
                                $stmt->bind_param("i", $item['exhibit_id']);
                                $stmt->execute();
                                $exhibit = $stmt->get_result()->fetch_assoc();
                                ?>
                                <div class="cart-item">
                                    <h3><?= htmlspecialchars($event['Название']) ?></h3>
                                    <p><strong>Дата:</strong> <?= htmlspecialchars($event['ДатаПроведения']) ?></p>
                                    <p><strong>Экспонат:</strong> <?= htmlspecialchars($exhibit['Название']) ?></p>
                                    <a href="cart.php?remove=<?= $index ?>">Удалить</a>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Кнопка оплаты -->
                        <form action="checkout.php" method="post">
                            <button type="submit">Оплатить</button>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
            </section>
        </main>

        <?php include 'includes/footer.php'; ?>
    </div>
</body>
</html>