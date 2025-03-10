<?php
session_start();

// Путь к файлу с новостями
$news_file = 'data/news.json';

// Чтение новостей из файла
if (file_exists($news_file)) {
    $news = json_decode(file_get_contents($news_file), true);
} else {
    $news = [];
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новости</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <?php include 'includes/header.php'; ?>

        <main>
            <section class="news">
                <h2>Новости</h2>

                <!-- Список новостей -->
                <div class="news-list">
                    <?php if (!empty($news)): ?>
                        <?php foreach ($news as $item): ?>
                            <div class="news-item">
                                <h3><?= htmlspecialchars($item['title']) ?></h3>
                                <p><?= nl2br(htmlspecialchars($item['content'])) ?></p>
                                <p><em><?= htmlspecialchars($item['date']) ?></em></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Новостей пока нет.</p>
                    <?php endif; ?>
                </div>
            </section>
        </main>

        <?php include 'includes/footer.php'; ?>
    </div>
</body>
</html>