<?php

//
// Configuration file for plugin
//

// Preload config file
pluginsLoadConfig();

// Fill configuration parameters
$cfg = array();
$cfgX = array();
array_push($cfg, array('descr' => 'Плагин Репутация'));
array_push($cfgX, array('name' => 'timeformat', 'title' => 'Формат времени', 'descr' => 'Значение по умолчанию: <b>H:i:s d-m-Y</b>', 'type' => 'input', 'value' => pluginGetVariable('reputation','timeformat')?pluginGetVariable('reputation','timeformat'):'H:i:s d-m-Y'));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>Настройки плагина</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'localsource', 'title' => "Выберите каталог из которого плагин будет брать шаблоны для отображения<br /><small><b>Шаблон сайта</b> - плагин будет пытаться взять шаблоны из общего шаблона сайта; в случае недоступности - шаблоны будут взяты из собственного каталога плагина<br /><b>Плагин</b> - шаблоны будут браться из собственного каталога плагина</small>", 'type' => 'select', 'values' => array ( '0' => 'Шаблон сайта', '1' => 'Плагин'), 'value' => intval(PluginGetVariable('reputation','localsource'))));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>Настройки отображения</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'timelimit', 'title' => "Временное ограничение<br /><small>через какое пользователь сможет другому пользователю изменить репутацию. <b>По умолчанию 1 сутки</b></small>", 'type' => 'input', 'value' => intval(PluginGetVariable('reputation','timelimit'))?PluginGetVariable('reputation','timelimit'):1));
array_push($cfgX, array('name' => 'timetype', 'title' => "Единицы измерения<br /><small>ограничения по времени</small>", 'type' => 'select', 'values' => array ( '1' => 'Секунды', '2' => 'Минуты', '3' => 'Часы', '4' => 'Сутки'), 'value' => intval(PluginGetVariable('reputation','timetype'))?intval(PluginGetVariable('reputation','timetype')):'4'));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>Настройки временного ограничения</b>', 'entries' => $cfgX));


// RUN 
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes('reputation', $cfg);
	print_commit_complete('reputation');
} else {
	generate_config_page('reputation', $cfg);
}


?>
