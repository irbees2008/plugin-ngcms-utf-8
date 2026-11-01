<?php
	// Protect against hack attempts
	if (!defined('NGCMS')) die ('HAL');
	
	//
	// Configuration file for plugin
	//
	pluginsLoadConfig();
	function plugin_topplayers_install($action) {
	global $lang;

	if ($action != 'autoapply')
		loadPluginLang('topplayers', 'config', '', '', ':');

	// Fill DB_UPDATE configuration scheme
	$db_update = array(
	 array(
	  'table'  => 'topplayers',
	  'action' => 'cmodify',
	  'key'    => 'primary key(id)',
	  'fields' => array(
	    array('action' => 'cmodify', 'name' => 'id', 'type' => 'int', 'params' => 'not null auto_increment'),
	    array('action' => 'cmodify', 'name' => 'scores', 'type' => 'int', 'params' => 'default 0'),
	    array('action' => 'cmodify', 'name' => 'flag', 'type' => 'char(20)'),
	    array('action' => 'cmodify', 'name' => 'name', 'type' => 'char(40)'),
	    array('action' => 'cmodify', 'name' => 'pos', 'type' => 'int', 'params' => 'default 0'),
		array('action' => 'cmodify', 'name' => 'photo', 'type' => 'char(40)'),
		array('action' => 'cmodify', 'name' => 'biography', 'type' => 'text'),
		array('action' => 'cmodify', 'name' => 'rating', 'type' => 'int(2)'),
		array('action' => 'cmodify', 'name' => 'country', 'type' => 'char(40)'),
		array('action' => 'cmodify', 'name' => 'hand', 'type' => 'char(20)'),
		array('action' => 'cmodify', 'name' => 'birthdate', 'type' => 'char(20)'),
		array('action' => 'cmodify', 'name' => 'birthplace', 'type' => 'char(20)'),
	   )
	 ),
	);

	// Apply requested action
	switch ($action) {
		case 'confirm':
			generate_install_page('topplayers', $lang['topplayers:text.install']);
			break;
		case 'autoapply':
		case 'apply':
			if (fixdb_plugin_install('topplayers', $db_update, 'install', ($action=='autoapply')?true:false)) {
				plugin_mark_installed('topplayers');
			} else {
				return false;
			}
			break;
	}
	return true;
}
?>