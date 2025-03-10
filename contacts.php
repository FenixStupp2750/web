

<?php
session_start();
require 'db.php';

// Подключение PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';

// Обработка отправки письма (только для авторизованных пользователей)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $message = $_POST['message'];
    $user_id = $_SESSION['user_id'];

    // Получаем данные пользователя
    $query = "SELECT email, фамилия, имя, отчество FROM посетители WHERE КодПосетителя = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Данные пользователя
    $user_email = $user['email'];
    $user_lastname = $user['фамилия'];
    $user_firstname = $user['имя'];
    $user_middlename = $user['отчество'];

    // Настройка PHPMailer
    $mail = new PHPMailer(true);

    try {
       $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Хост Gmail
        $mail->SMTPAuth = true;
        $mail->Username = 'hpg.fenixstupp2750@gmail.com'; // Ваш Gmail
        $mail->Password = 'mnpd xumc rsce sblo'; // Пароль от Gmail или пароль приложения
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Шифрование TLS
        $mail->Port = 587; // Порт для Gmail


        // Отправитель и получатель
        $mail->setFrom($user_email); // Email пользователя
        $mail->addAddress('hpg.fenixstupp2750@gmail.com'); // Реальный email получателя

        // Содержание письма
        $mail->isHTML(false); // Отправка в виде plain text
        $mail->Subject = 'message from guest of museum';

        // Формируем тело письма с данными отправителя
        $mail->Body = "Сообщение от: $user_lastname $user_firstname $user_middlename ($user_email)\n\n";
        $mail->Body .= "Текст сообщения:\n$message";

        // Отправка письма
        $mail->send();
        $_SESSION['message'] = "Ваше сообщение успешно отправлено!";
    } catch (Exception $e) {
        $_SESSION['error'] = "Ошибка при отправке сообщения: {$mail->ErrorInfo}";
    }

    // Перенаправление обратно на страницу контактов
    header("Location: contacts.php");
    exit();
}
?>




<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Контакты</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <?php include 'includes/header.php'; ?>

        <main>
            <section class="contacts">
                <h2>Контакты</h2>

                <!-- FAQ -->
                <div class="faq">
                    <h3>FAQ</h3>
                    <p>Перед тем как написать нам, пожалуйста, ознакомьтесь с ответами на часто задаваемые вопросы:</p>

                    <div class="faq-item">
                        <button class="faq-question">Как узнать расписание мероприятий?</button>
                        <div class="faq-answer">
                            <p>Перейдите на страницу "Мероприятия".</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-question">Где найти информацию об экспонатах?</button>
                        <div class="faq-answer">
                            <p>Вся информация доступна на странице "Экспонаты".</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-question">Как связаться с администрацией?</button>
                        <div class="faq-answer">
                            <p>Используйте форму ниже для отправки сообщения.</p>
                        </div>
                    </div>
                </div>

                <!-- Форма отправки сообщения (только для авторизованных пользователей) -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="contact-form">
                        <h3>Написать нам</h3>
                        <?php if (isset($_SESSION['message'])): ?>
                            <p class="success"><?= $_SESSION['message']; unset($_SESSION['message']); ?></p>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['error'])): ?>
                            <p class="error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
                        <?php endif; ?>
                        <form action="contacts.php" method="post">
                            <textarea name="message" placeholder="Ваше сообщение" required></textarea>
                            <button type="submit">Отправить</button>
                        </form>
                    </div>
                <?php else: ?>
                    <p>Чтобы отправить сообщение, пожалуйста, <a href="login.php">авторизуйтесь</a>.</p>
                <?php endif; ?>
            </section>
        </main>

        <?php include 'includes/footer.php'; ?>
    </div>

    <script>
        // JavaScript для раскрывающегося списка FAQ
        document.querySelectorAll('.faq-question').forEach(button => {
            button.addEventListener('click', () => {
                const answer = button.nextElementSibling;
                answer.style.display = answer.style.display === 'block' ? 'none' : 'block';
            });
        });
    </script>
</body>
</html>