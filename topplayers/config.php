<?php

//
// Configuration file for plugin
//

// Preload config file
pluginsLoadConfig();


// Fill configuration parameters
$cfg = array();
array_push($cfg, array('descr' => 'Плагін дозволяє відображати на сторінках сайта рейтинг гравців та команд, щоб додати або відредагуваити гравця перейдіть по цій ссилці  <i><a href="http://sportanalytic.com/plugin/topplayers/?do=edit" target="_blank">редагування</a><br />.</i>'));

$cfgX = array();
array_push($cfgX, array('name' => 'localsource', 'title' => "Выберите каталог из которого плагин будет брать шаблоны для отображения<br /><small><b>Шаблон сайта</b> - плагин будет пытаться взять шаблоны из общего шаблона сайта; в случае недоступности - шаблоны будут взяты из собственного каталога плагина<br /><b>Плагин</b> - шаблоны будут браться из собственного каталога плагина</small>", 'type' => 'select', 'values' => array ( '0' => 'Шаблон сайта', '1' => 'Плагин'), 'value' => PluginGetVariable($plugin,'localsource')));

array_push($cfgX, array('name' => 'pptop', 'title' => "Количество человек в топ на странице рейтинга", 'type' => 'input', 'value' => intval(PluginGetVariable($plugin,'pptop'))?PluginGetVariable($plugin,'pptop'):'10'));
array_push($cfgX, array('name' => 'ppedittop', 'title' => "Количество человек в топ при редактировании", 'type' => 'input', 'value' => intval(PluginGetVariable($plugin,'ppedittop'))?PluginGetVariable($plugin,'ppedittop'):'25'));
array_push($cfgX, array('name' => 'top', 'title' => "Количество человек в топ", 'type' => 'input', 'value' => intval(PluginGetVariable($plugin,'top'))?PluginGetVariable($plugin,'top'):'10'));
array_push($cfgX, array('name' => 'topua', 'title' => "Количество человек в топ украинцев", 'type' => 'input', 'value' => intval(PluginGetVariable($plugin,'topua'))?PluginGetVariable($plugin,'topua'):'5'));
array_push($cfgX, array('name' => 'ratings', 'title' => 'Количесво рейтингов', 'type' => 'input', 'value' => intval(PluginGetVariable($plugin,'ratings'))));
array_push($cfgX, array('name' => 'editusers', 'title' => 'Пользователи, имеющие возможность редактировать рейтинг','descr' => 'Администраторов тоже нужно включать в этот список','type' => 'text', 'html_flags' => 'rows=8 cols=60', 'value' => extra_get_param($plugin,'editusers')));
array_push($cfgX, array('name' => 'photos', 'title' => 'URL-путь к фотографиям', 'descr' => 'например: http://server.com/uploads/topplayers/photos/', 'type' => 'input', 'html_flags' => 'size=60', 'value' => PluginGetVariable($plugin,'photos')));
array_push($cfgX, array('name' => 'photodir', 'title' => 'Путь к фотографиям на диске', 'descr' => 'например: /home/servercom/public_html/uploads/topplayers/photos/', 'type' => 'input', 'html_flags' => 'size=60', 'value' => PluginGetVariable($plugin,'photodir')));
array_push($cfgX, array('name' => 'flags', 'title' => 'URL-путь к флагам', 'descr' => 'например: http://server.com/uploads/topplayers/flags/', 'type' => 'input', 'html_flags' => 'size=60', 'value' => PluginGetVariable($plugin,'flags')));
array_push($cfgX, array('name' => 'flagdir', 'title' => 'Путь к флагам на диске', 'descr' => 'например: /home/servercom/public_html/uploads/topplayers/flags/', 'type' => 'input', 'html_flags' => 'size=60', 'value' => PluginGetVariable($plugin,'flagdir')));
array_push($cfgX, array('name' => 'flagext', 'title' => 'Расширение изображений с флагами', 'descr' => 'например: gif', 'type' => 'input', 'html_flags' => 'size=60', 'value' => PluginGetVariable($plugin,'flagext')));

array_push($cfg,  array('mode' => 'group', 'title' => '<b>Настройки плагина</b>', 'entries' => $cfgX));

for ($i = 1; $i <= intval(PluginGetVariable($plugin,'ratings')); $i++) {
	$cfgX = array();
	array_push($cfgX, array('name' => 'rating'.$i, 'title' => 'Выводить блок<br /><small><b>Да</b> - выводится<br /><b>Нет</b> - не выводится</small>', 'type' => 'select', 'values' => array ( '1' => 'Да', '0' => 'Нет'), 'value' => intval(PluginGetVariable($plugin,'rating'.$i))));
	array_push($cfgX, array('name' => 'rating'.$i.'name', 'title' => 'Название рейтинга', 'type' => 'input', 'value' => PluginGetVariable($plugin,'rating'.$i.'name')));
	array_push($cfg,  array('mode' => 'group', 'title' => '<b>Настройки рейтинга № <b>'.$i.'</b>', 'entries' => $cfgX));
}

$cfgX = array();
array_push($cfgX, array('name' => 'cache', 'title' => "Использовать кеширование данных<br /><small><b>Да</b> - кеширование используется<br /><b>Нет</b> - кеширование не используется</small>", 'type' => 'select', 'values' => array ( '1' => 'Да', '0' => 'Нет'), 'value' => intval(PluginGetVariable($plugin,'cache'))));
array_push($cfgX, array('name' => 'cacheExpire', 'title' => "Период обновления кеша<br /><small>(через сколько секунд происходит обновление кеша. Значение по умолчанию: <b>60</b>)</small>", 'type' => 'input', 'value' => intval(PluginGetVariable($plugin,'cacheExpire'))?PluginGetVariable($plugin,'cacheExpire'):'60'));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>Настройки кеширования</b>', 'entries' => $cfgX));

// RUN
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}
