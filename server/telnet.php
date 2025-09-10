<?php
// Заголовки для ответа JSON
header('Content-Type: application/json; charset=utf-8');

// Хост и порт можно захардкодить, если ты всегда тестируешь localhost:23
// Получаем host и port из GET-параметров
$host = $_GET['host'] ?? '127.0.0.1';
$port = $_GET['port'] ?? 80;

// Попытка соединения (очень быстрое)
$connection = @fsockopen($host, $port, $errno, $errstr, 1);

if ($connection) {
    fclose($connection);
    echo json_encode([
        'status' => 'success',
        'message' => "✅ Соединение с {$host}:{$port} успешно"
    ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => "❌ Ошибка соединения: {$errstr} (Код: {$errno})"
    ], JSON_UNESCAPED_UNICODE);
}