<?php
	// Protect against hack attempts
	if (!defined('NGCMS')) die ('HAL');

//
// Configuration file for plugin
//
pluginsLoadConfig();
	function plugin_reputation_install($action) {
	global $lang;

	if ($action != 'autoapply')
		loadPluginLang('reputation', 'config', '', '', ':');

	// Fill DB_UPDATE configuration scheme
	$db_update = array(
		array(
			'table' => 'reputation',
			'action' => 'cmodify',
			'key' => 'primary key (`id`)',
			'fields' => array(
				array('action' => 'cmodify', 'name' => '`id`', 'type' => 'int(10)', 'params' => 'UNSIGNED NOT NULL AUTO_INCREMENT'),
				array('action' => 'cmodify', 'name' => '`to_id`', 'type' => 'int(10)', 'params' => 'NOT NULL'),
				array('action' => 'cmodify', 'name' => '`from_id`', 'type' => 'int(10)', 'params' => 'NOT NULL'),
				array('action' => 'cmodify', 'name' => '`comment`', 'type' => 'text', 'params' => 'NOT NULL'),
				array('action' => 'cmodify', 'name' => '`url`', 'type' => 'varchar(100)', 'params' => 'NOT NULL'),
				array('action' => 'cmodify', 'name' => '`action`', 'type' => 'varchar(5)', 'params' => 'NOT NULL'),
				array('action' => 'cmodify', 'name' => '`date`', 'type' => 'int(10)', 'params' => 'NOT NULL'),
			)
		),
		
		array(
			 'table'  => 'users',
			 'action' => 'cmodify',
			 'fields' => array(
				array('action' => 'cmodify', 'name' => 'reputation', 'type' => 'smallint(5)', 'params' => "default '0'"),
			)
		),
	);

	// Apply requested action
	switch ($action) {
		case 'confirm':
			generate_install_page('reputation', '”становить плагин –епутаци¤');
			break;
		case 'autoapply':
		case 'apply':
			if (fixdb_plugin_install('reputation', $db_update, 'install', ($action=='autoapply')?true:false)) {
				plugin_mark_installed('reputation');
			} else {
				return false;
			}
			break;
	}
	return true;
}
?>