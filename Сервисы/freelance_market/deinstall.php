<?php
// Protect against hack attempts
if (!defined('NGCMS')) die('HAL');

pluginsLoadConfig();

$db_update = [
    ['table' => 'freelance_jobs', 'action' => 'drop'],
    ['table' => 'freelance_bids', 'action' => 'drop'],
    ['table' => 'freelance_user_access', 'action' => 'drop'],
    ['table' => 'freelance_pay_order', 'action' => 'drop'],
    ['table' => 'freelance_rating', 'action' => 'drop'],
];

if ($_REQUEST['action'] == 'commit') {
    if (fixdb_plugin_install('freelance_market', $db_update, 'deinstall')) {
        plugin_mark_deinstalled('freelance_market');
    }

    $url = home . "/engine/admin.php?mod=extras";
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: {$url}");
    exit();
} else {
    $text = 'Плагин будет удалён вместе с таблицами.';
    generate_install_page('freelance_market', $text, 'deinstall');
}
