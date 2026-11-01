<?php

if (!defined('NGCMS')) {
    die('HAL');
}

pluginsLoadConfig();
LoadPluginLang('posters', 'config', '', '', ':');

$plugin = 'posters';
$dbUpdate = [
    [
        'table' => 'posters',
        'action' => 'drop',
    ],
];

if (isset($_REQUEST['action']) && $_REQUEST['action'] === 'commit') {
    if (fixdb_plugin_install($plugin, $dbUpdate, 'deinstall')) {
        plugin_mark_deinstalled($plugin);
    }
} else {
    generate_install_page($plugin, $lang['posters:desc_deinstall'], 'deinstall');
}
