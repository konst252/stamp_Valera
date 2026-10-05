<?php
// send.php — обработчик отправки формы на почту

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Метод не поддерживается']);
    exit;
}

// Получаем и очищаем входные данные
$name  = isset($_POST['name']) ? trim(htmlspecialchars($_POST['name'])) : '';
$phone = isset($_POST['phone']) ? trim(htmlspecialchars($_POST['phone'])) : '';
$email = isset($_POST['email']) ? trim(htmlspecialchars($_POST['email'])) : '';

if (empty($name) || empty($phone)) {
    echo json_encode(['success' => false, 'error' => 'Заполните обязательные поля']);
    exit;
}

// Адрес получателя
$to = 'pechati-73@mail.ru';

$subject = '=?UTF-8?B?' . base64_encode('Новая оптовая заявка с сайта StampPro') . '?=';

$message = "Поступила новая заявка с сайта:\n\n";
$message .= "Имя / Компания: " . $name . "\n";
$message .= "Телефон: " . $phone . "\n";
$message .= "Email: " . ($email ? $email : 'не указан') . "\n";
$message .= "Дата: " . date('d.m.Y H:i:s') . "\n";
$message .= "IP отправителя: " . $_SERVER['REMOTE_ADDR'] . "\n";

// Служебные заголовки
$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "From: StampPro Bot <noreply@" . $_SERVER['SERVER_NAME'] . ">\r\n";
if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $headers .= "Reply-To: " . $email . "\r\n";
}

// Отправка через функцию mail хостинга
$sent = @mail($to, $subject, $message, $headers);

if ($sent) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Ошибка отправки почтовым сервером']);
}