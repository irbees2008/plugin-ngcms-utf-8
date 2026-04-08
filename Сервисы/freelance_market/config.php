<?php
// Protect against hack attempts
if (!defined('NGCMS')) die('HAL');

pluginsLoadConfig();
loadPluginLang('freelance_market', 'main', '', '', ':');

$plugin = 'freelance_market';
$cfg = [];
array_push($cfg, array('descr' => $lang['freelance_market:config_desc']));

$cfgX = [];
array_push($cfgX, array('name' => 'price_10', 'title' => 'Цена доступа на 10 дней, ₽', 'type' => 'input', 'value' => pluginGetVariable($plugin, 'price_10')));
array_push($cfgX, array('name' => 'price_30', 'title' => 'Цена доступа на 30 дней, ₽', 'type' => 'input', 'value' => pluginGetVariable($plugin, 'price_30')));
array_push($cfg, array('mode' => 'group', 'title' => 'Тарифы', 'entries' => $cfgX));

$cfgX = [];
array_push($cfgX, array('name' => 'robokassa_login', 'title' => 'Robokassa: Логин', 'type' => 'input', 'value' => pluginGetVariable($plugin, 'robokassa_login')));
array_push($cfgX, array('name' => 'robokassa_pass1', 'title' => 'Robokassa: Пароль #1', 'type' => 'input', 'value' => pluginGetVariable($plugin, 'robokassa_pass1')));
array_push($cfgX, array('name' => 'robokassa_pass2', 'title' => 'Robokassa: Пароль #2', 'type' => 'input', 'value' => pluginGetVariable($plugin, 'robokassa_pass2')));
array_push($cfgX, array('name' => 'robokassa_is_test', 'title' => 'Тестовый режим', 'type' => 'select', 'values' => array('1' => 'Да', '0' => 'Нет'), 'value' => pluginGetVariable($plugin, 'robokassa_is_test')));
array_push($cfg, array('mode' => 'group', 'title' => 'Robokassa', 'entries' => $cfgX));

if ($_REQUEST['action'] == 'commit') {
    commit_plugin_config_changes($plugin, $cfg);
    print_commit_complete($plugin);
} else {
    generate_config_page($plugin, $cfg);
}
