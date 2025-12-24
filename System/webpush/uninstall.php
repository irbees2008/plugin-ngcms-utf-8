<?php
// Protect against hack attempts
if (!defined('NGCMS')) die('HAL');

pluginsLoadConfig();

$db_update = [
    [
        'table'  => 'webpush_subscriptions',
        'action' => 'drop',
    ],
];

if (($_REQUEST['action'] ?? '') === 'commit') {
    if (fixdb_plugin_install('webpush', $db_update, 'deinstall')) {
        $root = dirname(__DIR__, 3);

        // Удаляем директорию конфигурации (опционально)
        // @unlink($root . '/engine/conf/extras/webpush/config.php');

        // Service worker в корне удаляется вручную при необходимости
        // @unlink($root . '/webpush-sw.js');

        plugin_mark_deinstalled('webpush');
    }
} else {
    generate_install_page(
        'webpush',
        '<p>Плагин Web Push будет удалён, таблица подписок будет удалена.</p>' .
            '<p><strong>Внимание:</strong> Service worker файл (webpush-sw.js) в корне сайта ' .
            'и директория uploads/webpush останутся. Удалите их вручную при необходимости.</p>',
        'deinstall'
    );
}
