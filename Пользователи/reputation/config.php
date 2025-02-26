<?php

declare(strict_types=1);

// Configuration file for plugin

// Preload config file
pluginsLoadConfig();

// Fill configuration parameters
$cfg = [];
$cfgX = [];

$cfg[] = ['descr' => 'Плагин Htgenfwbz'];

$timeformat = pluginGetVariable('reputation', 'timeformat') ?? 'H:i:s d-m-Y';
$cfgX[] = [
	'name'  => 'timeformat',
	'title' => 'Формат времени',
	'descr' => 'Значение по умолчанию: <b>H:i:s d-m-Y</b>',
	'type'  => 'input',
	'value' => $timeformat,
];

$cfg[] = [
	'mode'    => 'group',
	'title'   => '<b>Настройки плагина</b>',
	'entries' => $cfgX,
];

$cfgX = [];
$localsource = (int)(pluginGetVariable('reputation', 'localsource') ?? 0);
$cfgX[] = [
	'name'   => 'localsource',
	'title'  => "Выберите каталог из которого плагин будет брать шаблоны для отображения<br /><small><b>Шаблон сайта</b> - плагин будет пытаться взять шаблоны из общего шаблона сайта; в случае недоступности - шаблоны будут взяты из собственного каталога плагина<br /><b>Плагин</b> - шаблоны будут браться из собственного каталога плагина</small>",
	'type'   => 'select',
	'values' => ['0' => 'Шаблон сайта', '1' => 'Плагин'],
	'value'  => $localsource,
];

$cfg[] = [
	'mode'    => 'group',
	'title'   => '<b>Настройки отображения</b>',
	'entries' => $cfgX,
];

$cfgX = [];
$timelimit = (int)(pluginGetVariable('reputation', 'timelimit') ?? 1);
$cfgX[] = [
	'name'  => 'timelimit',
	'title' => "Временное ограничение<br /><small>через какое пользователь сможет другому пользователю изменить репутацию. <b>По умолчанию 1 сутки</b></small>",
	'type'  => 'input',
	'value' => $timelimit,
];

$timetype = (int)(pluginGetVariable('reputation', 'timetype') ?? 4);
$cfgX[] = [
	'name'   => 'timetype',
	'title'  => "Единицы измерения<br /><small>ограничения по времени</small>",
	'type'   => 'select',
	'values' => ['1' => 'Секунды', '2' => 'Минуты', '3' => 'Часы', '4' => 'Сутки'],
	'value'  => $timetype,
];

$cfg[] = [
	'mode'    => 'group',
	'title'   => '<b>Настройки временного ограничения</b>',
	'entries' => $cfgX,
];

// RUN
if ($_REQUEST['action'] === 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes('reputation', $cfg);
	print_commit_complete('reputation');
} else {
	generate_config_page('reputation', $cfg);
}