<?php

/**
 * Web Push Endpoint - публичный API для подписок
 * Отдаёт публичный ключ и принимает подписки/отписки
 */

// Отключаем отображение ошибок
ini_set('display_errors', '0');
error_reporting(0);

// Устанавливаем заголовок JSON сразу
header('Content-Type: application/json; charset=utf-8');

// Минимальная инициализация без core.php
$root = dirname(__DIR__, 3);

// Подключаем только конфигурацию
require_once $root . '/engine/conf/config.php';

// Устанавливаем константы
if (!defined('prefix')) {
    define('prefix', $config['prefix'] ?? 'ng');
}

// Загружаем конфигурацию плагинов из сериализованного файла
$pluginConfigFile = $root . '/engine/conf/plugdata.php';
$pluginConfig = [];

if (file_exists($pluginConfigFile) && filesize($pluginConfigFile) > 0) {
    $content = file_get_contents($pluginConfigFile);
    $pluginConfig = unserialize($content);
}

// Проверяем, включен ли плагин
if (empty($pluginConfig['webpush']['enabled'])) {
    http_response_code(503);
    echo json_encode(['ok' => false, 'error' => 'WebPush disabled'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Создаём прямое PDO подключение
try {
    $dsn = "mysql:host={$config['dbhost']};dbname={$config['dbname']};charset=utf8mb4";
    $pdo = new PDO($dsn, $config['dbuser'], $config['dbpasswd'] ?? '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Database connection failed'], JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Загрузка подписок из БД
 */
function loadSubscriptions(PDO $pdo): array
{
    $stmt = $pdo->query("SELECT endpoint, p256dh, auth FROM " . prefix . "_webpush_subscriptions ORDER BY id ASC");
    return $stmt->fetchAll();
}

/**
 * Сохранение подписки в БД
 */
function saveSubscription(PDO $pdo, array $data): bool
{
    $hash = hash('sha256', $data['endpoint']);
    $time = time();

    // Проверяем, существует ли подписка
    $stmt = $pdo->prepare("SELECT id FROM " . prefix . "_webpush_subscriptions WHERE hash = ?");
    $stmt->execute([$hash]);
    $existing = $stmt->fetch();

    if ($existing) {
        // Обновляем существующую
        $stmt = $pdo->prepare(
            "UPDATE " . prefix . "_webpush_subscriptions SET " .
                "endpoint = ?, p256dh = ?, auth = ?, user_agent = ?, ip = ?, updated = ? " .
                "WHERE hash = ?"
        );
        $stmt->execute([
            $data['endpoint'],
            $data['p256dh'] ?? '',
            $data['auth'] ?? '',
            substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 250),
            $_SERVER['REMOTE_ADDR'] ?? '',
            $time,
            $hash
        ]);
    } else {
        // Создаём новую
        $stmt = $pdo->prepare(
            "INSERT INTO " . prefix . "_webpush_subscriptions " .
                "(hash, endpoint, p256dh, auth, user_agent, ip, created, updated) VALUES " .
                "(?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $hash,
            $data['endpoint'],
            $data['p256dh'] ?? '',
            $data['auth'] ?? '',
            substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 250),
            $_SERVER['REMOTE_ADDR'] ?? '',
            $time,
            $time
        ]);
    }

    return true;
}

/**
 * Удаление подписки из БД
 */
function removeSubscription(PDO $pdo, string $endpoint): bool
{
    $hash = hash('sha256', $endpoint);
    $stmt = $pdo->prepare("DELETE FROM " . prefix . "_webpush_subscriptions WHERE hash = ?");
    $stmt->execute([$hash]);

    return true;
}

// Определяем действие
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Отдаём публичный ключ
if ($action === 'key') {
    $publicKey = $pluginConfig['webpush']['vapid_public'] ?? '';
    echo json_encode([
        'ok' => true,
        'publicKey' => $publicKey,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Читаем тело запроса
$body = file_get_contents('php://input');
$payload = json_decode($body ?: '{}', true);

if (!is_array($payload)) {
    $payload = [];
}

$endpoint = $payload['endpoint'] ?? '';
$keys = $payload['keys'] ?? [];
$p256dh = $keys['p256dh'] ?? '';
$auth = $keys['auth'] ?? '';

// Проверяем наличие endpoint
if (!$endpoint) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'No endpoint'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Отписка
if ($action === 'unsubscribe') {
    removeSubscription($pdo, $endpoint);
    echo json_encode(['ok' => true], JSON_UNESCAPED_UNICODE);
    exit;
}

// Подписка / обновление
saveSubscription($pdo, [
    'endpoint' => $endpoint,
    'p256dh' => $p256dh,
    'auth' => $auth,
]);

echo json_encode(['ok' => true], JSON_UNESCAPED_UNICODE);
