<header>
    <nav>
        <a href="index.php">Главная</a>
        <a href="exponats.php">Экспонаты</a>
        <a href="events.php">Мероприятия</a>
        <a href="news.php">Новости</a>
        <a href="contacts.php">Контакты</a>
	<a href="cart.php">Корзина</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="profile.php">Личный кабинет</a>
            <a href="logout.php">Выход</a>
        <?php else: ?>
            <a href="login.php">Вход</a>
            <a href="register.php">Регистрация</a>
        <?php endif; ?>
    </nav>
</header>