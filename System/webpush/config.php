<?php
// Protect against hack attempts
if (!defined('NGCMS')) die('HAL');

pluginsLoadConfig();

$cfg = [];
$grp = [];

// Основные настройки
array_push($grp, [
    'name'   => 'enabled',
    'title'  => 'Включить Web Push уведомления',
    'descr'  => 'Активировать систему push-уведомлений на сайте',
    'type'   => 'select',
    'values' => ['0' => 'Нет', '1' => 'Да'],
    'value'  => extra_get_param($plugin, 'enabled'),
]);

array_push($grp, [
    'name'   => 'show_button',
    'title'  => 'Показывать кнопку подписки',
    'descr'  => 'Автоматически показывать кнопку подписки на уведомления',
    'type'   => 'select',
    'values' => ['0' => 'Нет', '1' => 'Да'],
    'value'  => extra_get_param($plugin, 'show_button'),
]);

array_push($grp, [
    'name'  => 'subscribe_text',
    'title' => 'Текст кнопки подписки',
    'descr' => 'Текст на кнопке подписки на уведомления',
    'type'  => 'input',
    'value' => extra_get_param($plugin, 'subscribe_text'),
]);

array_push($cfg, [
    'mode'    => 'group',
    'title'   => '<b>Основные настройки</b>',
    'entries' => $grp,
]);

// VAPID настройки
$grp = [];

array_push($grp, [
    'name'  => 'vapid_public',
    'title' => 'VAPID Public Key',
    'descr' => 'Публичный ключ VAPID (генерируется через send.php?action=genkeys)',
    'type'  => 'input',
    'value' => extra_get_param($plugin, 'vapid_public'),
]);

array_push($grp, [
    'name'  => 'vapid_private',
    'title' => 'VAPID Private Key',
    'descr' => 'Приватный ключ VAPID (храните в секрете!)',
    'type'  => 'input',
    'value' => extra_get_param($plugin, 'vapid_private'),
]);

array_push($grp, [
    'name'  => 'vapid_subject',
    'title' => 'VAPID Subject',
    'descr' => 'Email или URL сайта (формат: mailto:admin@example.com или https://example.com)',
    'type'  => 'input',
    'value' => extra_get_param($plugin, 'vapid_subject'),
]);

array_push($cfg, [
    'mode'    => 'group',
    'title'   => '<b>VAPID настройки</b>',
    'entries' => $grp,
]);

// Внешний вид уведомлений
$grp = [];

array_push($grp, [
    'name'  => 'default_icon',
    'title' => 'Иконка уведомления',
    'descr' => 'Путь к изображению иконки (рекомендуется 192x192px)',
    'type'  => 'input',
    'value' => extra_get_param($plugin, 'default_icon'),
]);

array_push($grp, [
    'name'  => 'default_badge',
    'title' => 'Badge иконка',
    'descr' => 'Путь к монохромному badge изображению (рекомендуется 96x96px)',
    'type'  => 'input',
    'value' => extra_get_param($plugin, 'default_badge'),
]);

array_push($cfg, [
    'mode'    => 'group',
    'title'   => '<b>Внешний вид уведомлений</b>',
    'entries' => $grp,
]);

// Безопасность
$grp = [];

array_push($grp, [
    'name'  => 'send_secret',
    'title' => 'Секретный ключ для отправки',
    'descr' => 'Токен для защиты send.php (используется при отправке уведомлений)',
    'type'  => 'input',
    'value' => extra_get_param($plugin, 'send_secret'),
]);

array_push($cfg, [
    'mode'    => 'group',
    'title'   => '<b>Безопасность</b>',
    'entries' => $grp,
]);

// Информация
$info = '<div class="alert alert-info">';
$info .= '<h4>Инструкция по настройке:</h4>';
$info .= '<ol>';
$info .= '<li>Установите Composer пакет: <code>composer require minishlink/web-push</code></li>';
$info .= '<li>Сгенерируйте VAPID ключи, выполнив запрос: <br><code>GET /engine/plugins/webpush/send.php?action=genkeys&secret=' . htmlspecialchars(extra_get_param($plugin, 'send_secret')) . '</code></li>';
$info .= '<li>Скопируйте полученные ключи в поля VAPID Public Key и VAPID Private Key</li>';
$info .= '<li>Убедитесь, что файл webpush-sw.js находится в корне сайта</li>';
$info .= '<li>Для отправки уведомлений используйте: <br><code>POST /engine/plugins/webpush/send.php?secret=...<br>Параметры: title, body, url</code></li>';
$info .= '</ol>';
$info .= '<p><strong>Важно:</strong> Web Push работает только по HTTPS (кроме localhost для тестирования)</p>';
$info .= '</div>';

array_push($cfg, [
    'mode'  => 'info',
    'title' => $info,
]);

// Обработка сохранения
if (isset($_REQUEST['action']) && $_REQUEST['action'] === 'commit') {
    commit_plugin_config_changes($plugin, $cfg);
    print_commit_complete($plugin);
} else {
    generate_config_page($plugin, $cfg);
}
