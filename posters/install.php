<?php

if (!defined('NGCMS')) {
    die('HAL');
}

pluginsLoadConfig();

function plugin_posters_install($action)
{
    global $lang;

    if ($action !== 'autoapply') {
        LoadPluginLang('posters', 'config', '', '', ':');
    }

    $dbUpdate = [
        [
            'table' => 'posters',
            'action' => 'cmodify',
            'key' => 'primary key (`id`)',
            'fields' => [
                ['action' => 'cmodify', 'name' => 'id', 'type' => 'int', 'params' => 'not null auto_increment'],
                ['action' => 'cmodify', 'name' => 'date', 'type' => 'varchar(255)', 'params' => 'not null'],
                ['action' => 'cmodify', 'name' => 'title', 'type' => 'varchar(255)', 'params' => 'not null'],
                ['action' => 'cmodify', 'name' => 'text', 'type' => 'text', 'params' => 'not null'],
                ['action' => 'cmodify', 'name' => 'active', 'type' => 'tinyint(1)', 'params' => 'not null default 0'],
                ['action' => 'cmodify', 'name' => 'tpl', 'type' => 'varchar(20)', 'params' => 'not null default "poster_1"'],
            ],
        ],
    ];

    switch ($action) {
        case 'confirm':
            generate_install_page('posters', $lang['posters:desc_install']);
            break;
        case 'autoapply':
        case 'apply':
            if (!fixdb_plugin_install('posters', $dbUpdate, 'install', $action === 'autoapply')) {
                return false;
            }
            plugin_mark_installed('posters');
            break;
    }

    return true;
}
