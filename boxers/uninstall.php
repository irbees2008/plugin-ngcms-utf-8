<?php

/*
 * Uninstall plugin "boxers" for NextGeneration CMS (http://ngcms.ru/)
 * Copyright (C) 2011 Alexey N. Zhukov (http://digitalplace.ru)
 * http://digitalplace.ru
 */
 
# protect against hack attempts
if (!defined('NGCMS')) die ('Galaxy in danger');

LoadPluginLang('boxers', 'config', '', '', ':');

$db_update = array(
	array(
		'table'		=>	'boxers',
		'action'	=>	'drop',
	),
	array(
		'table'		=>	'boxers_weight_class',
		'action'	=>	'drop',
	),
	
	array(
		'table'		=>	'boxers_ratings_history',
		'action'	=>	'drop',
	),
	
	array(
		'table'		=>	'boxers_fights',
		'action'	=>	'drop',
	)
);

if ($_REQUEST['action'] == 'commit') {
	if (fixdb_plugin_install('boxers', $db_update, 'deinstall')) {
		
		$ULIB = new urlLibrary();
		$ULIB->loadConfig();
		$ULIB->removeCommand('boxers', 'profile');
		
		$ULIB->saveConfig();
		
		file_put_contents(extras_dir.'/boxers/history.txt', '');	

		plugin_mark_deinstalled('boxers');
	}
} else {
	generate_install_page('boxers', $lang['boxers:uninstall_msg'], 'deinstall');
}