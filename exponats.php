<?php
session_start();
require 'db.php';

// Получаем список всех экспонатов
$query = "SELECT экспонаты.КодЭкспоната, экспонаты.Название, экспонаты.СтранаПроисхождения, эпохи.ВременнойПромежуток 
          FROM экспонаты 
          JOIN эпохи ON экспонаты.КодЭпохи = эпохи.КодЭпохи";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Экспонаты</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <?php include 'includes/header.php'; ?>

        <main>
            <section class="exponats">
                <h2>Экспонаты</h2>

                <!-- Форма для фильтрации -->
                <form action="exponats.php" method="get" class="filter-form">
                    <input type="text" name="search" placeholder="Поиск по названию" value="<?= $_GET['search'] ?? '' ?>">
                    <input type="text" name="country" placeholder="Страна происхождения" value="<?= $_GET['country'] ?? '' ?>">
                    <select name="epoch">
                        <option value="">Все эпохи</option>
                        <?php
                        $epochs_query = "SELECT * FROM эпохи";
                        $epochs_result = mysqli_query($conn, $epochs_query);
                        while ($epoch = mysqli_fetch_assoc($epochs_result)) {
                            $selected = ($_GET['epoch'] == $epoch['КодЭпохи']) ? 'selected' : '';
                            echo "<option value='{$epoch['КодЭпохи']}' $selected>{$epoch['ВременнойПромежуток']}</option>";
                        }
                        ?>
                    </select>
                    <button type="submit">Фильтровать</button>
                </form>

                <!-- Список экспонатов -->
                <div class="exponats-list">
                    <?php
                    // Фильтрация экспонатов
                    $search = $_GET['search'] ?? '';
                    $country = $_GET['country'] ?? '';
                    $epoch = $_GET['epoch'] ?? '';

                    $query = "SELECT экспонаты.КодЭкспоната, экспонаты.Название, экспонаты.СтранаПроисхождения, эпохи.ВременнойПромежуток 
                              FROM экспонаты 
                              JOIN эпохи ON экспонаты.КодЭпохи = эпохи.КодЭпохи 
                              WHERE экспонаты.Название LIKE ? 
                              AND экспонаты.СтранаПроисхождения LIKE ? 
                              AND (? = '' OR экспонаты.КодЭпохи = ?)";
                    $stmt = $conn->prepare($query);
                    $search_param = "%$search%";
                    $country_param = "%$country%";
                    $stmt->bind_param("ssss", $search_param, $country_param, $epoch, $epoch);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<div class='exponat-item'>
                                    <h3>{$row['Название']}</h3>
                                    <p>Страна происхождения: {$row['СтранаПроисхождения']}</p>
                                    <p>Эпоха: {$row['ВременнойПромежуток']}</p>
                                    <a href='exponat_details.php?id={$row['КодЭкспоната']}'>Подробнее</a>
                                  </div>";
                        }
                    } else {
                        echo "<p>Экспонаты не найдены.</p>";
                    }
                    ?>
                </div>
            </section>
        </main>

        <?php include 'includes/footer.php'; ?>
    </div>
</body>
</html>