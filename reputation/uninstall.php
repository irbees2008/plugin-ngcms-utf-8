<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');


$db_update = array(
	array(
		'table'		=>	'reputation',
		'action'	=>	'drop',
	),
	
	array(
		'table'  => 'users',
		'action' => 'modify',
		'fields' => array(
					array('action' => 'drop', 'name' => 'reputation'),
		)
	),
);

if ($_REQUEST['action'] == 'commit') {
	if (fixdb_plugin_install('reputation', $db_update, 'deinstall')) {
		plugin_mark_deinstalled('reputation');
	}
} else {
	generate_install_page('reputation', '<font color=red>Внимание! Удаление плагина приведёт к удалению всех данных из базы данных. Вы уверены?</font>', 'deinstall');
}
?>