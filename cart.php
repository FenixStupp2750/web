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

// Обработка очистки корзины
if (isset($_POST['clear_cart'])) {
    unset($_SESSION['cart']);
    header('Location: cart.php');
    exit();
}

// Обработка оплаты выбранных элементов
if (isset($_POST['checkout'])) {
    if (!empty($_POST['selected_items'])) {
        foreach ($_POST['selected_items'] as $index) {
            $item = $_SESSION['cart'][$index];

            // Проверка, существует ли уже такая запись в таблице экспозиции
            $check_query = "SELECT * FROM экспозиции WHERE КодПосетителя = ? AND КодМероприятия = ? AND КодЭкспоната = ?";
            $stmt = $conn->prepare($check_query);
            if (!$stmt) {
                die("Ошибка подготовки запроса: " . $conn->error);
            }
            $stmt->bind_param("iii", $_SESSION['user_id'], $item['event_id'], $item['exhibit_id']);
            if (!$stmt->execute()) {
                die("Ошибка выполнения запроса: " . $stmt->error);
            }
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Если запись уже существует, выводим сообщение
                echo "<script>alert('Вы уже приобрели данный билет');</script>";
            } else {
                // Если записи нет, добавляем её
                $insert_query = "INSERT INTO экспозиции (КодПосетителя, КодМероприятия, КодЭкспоната) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($insert_query);
                if (!$stmt) {
                    die("Ошибка подготовки запроса: " . $conn->error);
                }
                $stmt->bind_param("iii", $_SESSION['user_id'], $item['event_id'], $item['exhibit_id']);
                if (!$stmt->execute()) {
                    die("Ошибка выполнения запроса: " . $stmt->error);
                }
            }
        }

        // Удаление оплаченных элементов из корзины
        $_SESSION['cart'] = array_values(array_diff_key($_SESSION['cart'], array_flip($_POST['selected_items'])));
        header('Location: cart.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Корзина</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .cart-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }

        .cart-item label {
            flex-grow: 1;
            margin-right: 20px;
        }

        .cart-item input[type="checkbox"] {
            margin-left: auto;
            transform: scale(1.5);
        }
    </style>
    <script>
        function showAlert(message) {
            alert(message);
        }
    </script>
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
                        <form action="cart.php" method="post">
                            <div class="cart-items">
                                <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                                    <?php
                                    // Получение информации о мероприятии
                                    $event_query = "SELECT * FROM мероприятия WHERE КодМероприятия = ?";
                                    $stmt = $conn->prepare($event_query);
                                    if (!$stmt) {
                                        die("Ошибка подготовки запроса: " . $conn->error);
                                    }
                                    $stmt->bind_param("i", $item['event_id']);
                                    if (!$stmt->execute()) {
                                        die("Ошибка выполнения запроса: " . $stmt->error);
                                    }
                                    $event = $stmt->get_result()->fetch_assoc();

                                    // Получение информации о экспонате
                                    $exhibit_query = "SELECT * FROM экспонаты WHERE КодЭкспоната = ?";
                                    $stmt = $conn->prepare($exhibit_query);
                                    if (!$stmt) {
                                        die("Ошибка подготовки запроса: " . $conn->error);
                                    }
                                    $stmt->bind_param("i", $item['exhibit_id']);
                                    if (!$stmt->execute()) {
                                        die("Ошибка выполнения запроса: " . $stmt->error);
                                    }
                                    $exhibit = $stmt->get_result()->fetch_assoc();

                                    // Проверка, существует ли запись в таблице экспозиции
                                    $check_query = "SELECT * FROM экспозиции WHERE КодПосетителя = ? AND КодМероприятия = ? AND КодЭкспоната = ?";
                                    $stmt = $conn->prepare($check_query);
                                    if (!$stmt) {
                                        die("Ошибка подготовки запроса: " . $conn->error);
                                    }
                                    $stmt->bind_param("iii", $_SESSION['user_id'], $item['event_id'], $item['exhibit_id']);
                                    if (!$stmt->execute()) {
                                        die("Ошибка выполнения запроса: " . $stmt->error);
                                    }
                                    $result = $stmt->get_result();
                                    $is_purchased = $result->num_rows > 0;

                                    // Форматирование даты
                                    $formatted_date = date('d-m-Y', strtotime($event['ДатаПроведения']));
                                    ?>
                                    <div class="cart-item">
                                        <label for="item_<?= $index ?>">
                                            <h3><?= htmlspecialchars($event['Название']) ?></h3>
                                            <p><strong>Дата:</strong> <?= htmlspecialchars($formatted_date) ?></p>
                                            <p><strong>Экспонат:</strong> <?= htmlspecialchars($exhibit['Название']) ?></p>
                                        </label>
                                        <input type="checkbox" name="selected_items[]" value="<?= $index ?>" id="item_<?= $index ?>" <?= $is_purchased ? 'disabled' : '' ?>>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Кнопка оплаты -->
                            <button type="submit" name="checkout">Оплатить выбранное</button>

                            <!-- Кнопка очистки корзины -->
                            <button type="submit" name="clear_cart">Очистить корзину</button>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
            </section>
        </main>

        <?php include 'includes/footer.php'; ?>
    </div>
</body>
</html>