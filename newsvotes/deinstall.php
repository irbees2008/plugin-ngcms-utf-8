<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

//
// Configuration file for plugin
//

pluginsLoadConfig();
$plugin = 'newsvotes';
LoadPluginLang($plugin, 'config', '', '', ':');


$db_update = array(
	array(
		'table'		=>	'newsvote',
		'action'	=>	'drop',
	),
	array(
		'table'		=>	'newsanswer',
		'action'	=>	'drop',
	),
    array(
		'table'		=>	'newsvoted',
		'action'	=>	'drop',
	),
	array(
		'table'		=>	'news',
		'action'	=>	'modify',
		'fields'	=>	array(
			array('action' => 'drop', 'name' => 'nvote')
		)
	)
);

if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'commit') {
	if (fixdb_plugin_install($plugin, $db_update, 'deinstall')) {
		plugin_mark_deinstalled($plugin);
	}
} else {
	generate_install_page($plugin, $lang['newsvotes:desc_deinstall'], 'deinstall');
}

?>