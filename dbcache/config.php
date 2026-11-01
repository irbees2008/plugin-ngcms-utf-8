<?php

if (!defined('NGCMS')) {
    exit('HAL');
}

pluginsLoadConfig();
LoadPluginLang('dbcache', 'config', '', '', ':');

$plugin = 'dbcache';
$timeout = pluginGetVariable($plugin, 'timeout');
if ($timeout === false || $timeout === null || $timeout === '') {
    $timeout = '10';
}

$cfg = [
    ['descr' => $lang['dbcache:description']],
    [
        'name' => 'timeout',
        'title' => $lang['dbcache:timeout_title'],
        'descr' => $lang['dbcache:timeout_descr'],
        'error' => '',
        'html_flags' => 'min="0" max="1440" step="1" size="5"',
        'type' => 'input',
        'value' => (string) $timeout,
    ],
];

if (isset($_REQUEST['action']) && $_REQUEST['action'] === 'commit') {
    commit_plugin_config_changes($plugin, $cfg);
    print_commit_complete($plugin);
} else {
    generate_config_page($plugin, $cfg);
}
