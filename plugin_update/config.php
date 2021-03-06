<?php

// Protect against hack attempts
if (!defined('NGCMS')) die('HAL');

//
// Configuration file for plugin
//

// Preload config file
pluginsLoadConfig();


// Fill configuration parameters
$cfg = array();
$cfgX = array();
array_push($cfg, array('descr' => 'Плагин наиболее популярные новости. Популярность определяется по кол-ву просмотров новости.'));
array_push($cfgX, array('name' => 'number', 'title' => "Кол-во новостей для отображения<br /><small>(сколько новостей будет отображаться в блоке 'закладки')</small>", 'type' => 'input', 'value' => intval(extra_get_param($plugin, 'number')) ? extra_get_param($plugin, 'number') : '10'));
array_push($cfgX, array('name' => 'maxlength', 'title' => "Ограничение длины названия новости<br /><small>(если название превышает указанные пределы, то оно будет урезано)</small>", 'type' => 'input', 'value' => intval(extra_get_param($plugin, 'maxlength')) ? extra_get_param($plugin, 'maxlength') : '100'));
array_push($cfgX, array('name' => 'counter', 'title' => "Отображать счетчик просмотров<br /><b>Да</b> - счетчик будет отображаться<br /><b>Нет</b> - счетчик не будет отображаться", 'type' => 'select', 'values' => array('0' => 'Нет', '1' => 'Да'), 'value' => intval(extra_get_param($plugin, 'counter'))));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>Настройки плагина</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'localsource', 'title' => "Выберите каталог из которого плагин будет брать шаблоны для отображения<br /><small><b>Шаблон сайта</b> - плагин будет пытаться взять шаблоны из общего шаблона сайта; в случае недоступности - шаблоны будут взяты из собственного каталога плагина<br /><b>Плагин</b> - шаблоны будут браться из собственного каталога плагина</small>", 'type' => 'select', 'values' => array('0' => 'Шаблон сайта', '1' => 'Плагин'), 'value' => intval(extra_get_param($plugin, 'localsource'))));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>Настройки отображения</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'pcall', 'title' => "Интеграция с новостными плагинами<br /><small><b>Да</b> - в плагине появится возможность испольвать переменные других плагинов<br /><b>Нет</b> - переменные других плагинов использовать нельзя</small>", 'type' => 'select', 'values' => array('1' => 'Да', '0' => 'Нет'), 'value' => intval(extra_get_param($plugin, 'pcall'))));
array_push($cfgX, array('name' => 'pcall_mode', 'title' => "Режим вызова", 'descr' => "Вам необходимо выбрать какой из режимов отображения новостей будет эмулироваться<br/><b>экспорт</b> - экспорт данных в другие плагины (<font color=\"red\">рекомендуется</font>)<br /><b>короткая</b> - короткая новость<br><b>полная</b> - полная новость</small>", 'type' => 'select', 'values' => array('0' => 'экспорт', '1' => 'короткая', '2' => 'полная'), 'value' => intval(extra_get_param($plugin, 'pcall_mode'))));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>Интеграция</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'cache', 'title' => "Использовать кеширование данных<br /><small><b>Да</b> - кеширование используется<br /><b>Нет</b> - кеширование не используется</small>", 'type' => 'select', 'values' => array('1' => 'Да', '0' => 'Нет'), 'value' => intval(extra_get_param($plugin, 'cache'))));
array_push($cfgX, array('name' => 'cacheExpire', 'title' => "Период обновления кеша<br /><small>(через сколько секунд происходит обновление кеша. Значение по умолчанию: <b>60</b>)</small>", 'type' => 'input', 'value' => intval(extra_get_param($plugin, 'cacheExpire')) ? extra_get_param($plugin, 'cacheExpire') : '60'));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>Настройки кеширования</b>', 'entries' => $cfgX));


// RUN
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}
