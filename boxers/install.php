<?php

/*
 * Install plugin "boxers" for NextGeneration CMS (http://ngcms.ru/)
 * Copyright (C) 2011 Alexey N. Zhukov (http://digitalplace.ru)
 * http://digitalplace.ru
 */
 
# protect against hack attempts
if (!defined('NGCMS'))die ('Galaxy in danger');

function plugin_boxers_install($action) {
	global $lang, $mysql, $config;
	
	if ($action != 'autoapply')
		LoadPluginLang('boxers', 'config', '', '', ':');
		
	$db_create = array(
		array(
			'table' => 'boxers',
			'action' => 'cmodify',
			'key' => 'primary key (`id`)',
			'fields' => array(
				array('action' => 'cmodify', 'name' => 'id', 'type' => 'int(10)', 'params' => 'UNSIGNED NOT NULL AUTO_INCREMENT'),
				array('action' => 'cmodify', 'name' => 'full_name', 'type' => 'varchar(255)', 'params' => 'NOT NULL'),
				array('action' => 'cmodify', 'name' => 'weight_class', 'type' => 'smallint', 'params' => 'unsigned NOT NULL'),
				array('action' => 'cmodify', 'name' => 'birthday', 'type' => 'date'),
				array('action' => 'cmodify', 'name' => 'photo', 'type' => 'varchar(100)'),
				array('action' => 'cmodify', 'name' => 'country', 'type' => 'varchar(100)'),
				array('action' => 'cmodify', 'name' => 'translit_name', 'type' => 'varchar(255)'),
				array('action' => 'cmodify', 'name' => 'wins', 'type' => 'SMALLINT'),
				array('action' => 'cmodify', 'name' => 'defeats', 'type' => 'SMALLINT'),
				array('action' => 'cmodify', 'name' => 'draws', 'type' => 'SMALLINT'),
				array('action' => 'cmodify', 'name' => 'knockouts', 'type' => 'SMALLINT'),
				array('action' => 'cmodify', 'name' => 'debut', 'type' => 'date'),
				array('action' => 'cmodify', 'name' => 'height', 'type' => 'SMALLINT'),
				array('action' => 'cmodify', 'name' => 'reach', 'type' => 'SMALLINT'),
				array('action' => 'cmodify', 'name' => 'stance', 'type' => 'varchar(50)'),
				array('action' => 'cmodify', 'name' => 'boxrecid', 'type' => 'SMALLINT'),
				array('action' => 'cmodify', 'name' => 'www', 'type' => 'varchar(100)'),
				array('action' => 'cmodify', 'name' => 'nickname', 'type' => 'varchar(255)'),
				array('action' => 'cmodify', 'name' => 'hometown', 'type' => 'varchar(100)'),
				array('action' => 'cmodify', 'name' => 'biography', 'type' => 'text'),
			)
		),
		
		array(
			'table' => 'boxers_weight_class',
			'action' => 'cmodify',
			'key' => 'primary key (`id`)',
			'fields' => array(
				array('action' => 'cmodify', 'name' => 'id', 'type' => 'smallint', 'params' => 'UNSIGNED NOT NULL AUTO_INCREMENT'),
				array('action' => 'cmodify', 'name' => 'name', 'type' => 'varchar(100)', 'params' => 'NOT NULL')
			)
		),
		
		array(
			'table' => 'boxers_ratings_history',
			'action' => 'cmodify',
			'key' => 'primary key (id_boxers, date)',
			'fields' => array(
				array('action' => 'cmodify', 'name' => 'id_boxers', 'type' => 'smallint', 'params' => 'UNSIGNED NOT NULL'),
				array('action' => 'cmodify', 'name' => 'date', 'type' => 'date', 'params' => 'NOT NULL'),
				array('action' => 'cmodify', 'name' => 'weight', 'type' => 'smallint',  'params' => 'UNSIGNED')
			)
		),
		
		array(
			'table' => 'boxers_fights',
			'action' => 'cmodify',
			'key' => 'primary key (id_who, id_with, date)',
			'fields' => array(
				array('action' => 'cmodify', 'name' => 'id_who', 'type' => 'int(10)', 'params' => 'NOT NULL'),
				array('action' => 'cmodify', 'name' => 'id_with', 'type' => 'int(10)', 'params' => 'NOT NULL'),
				array('action' => 'cmodify', 'name' => 'date', 'type' => 'date',  'params' => 'NOT NULL'),
				array('action' => 'cmodify', 'name' => 'news', 'type' => 'varchar(255)'),
				array('action' => 'cmodify', 'name' => 'photo', 'type' => 'varchar(255)'),
				array('action' => 'cmodify', 'name' => 'video', 'type' => 'varchar(255)'),
			)
		)
	);
	
	switch ($action) {
		case 'confirm': 
			 generate_install_page('boxers', $lang['boxers:install_msg']);
			 break;
		case 'autoapply':
		case 'apply':
			if (fixdb_plugin_install('boxers', $db_create, 'install', ($action=='autoapply')?true:false)) {
			
				if (($handle = fopen(extras_dir.'/boxers/install_boxers_weight_class.csv', "r")) !== FALSE) {
					while (($data = fgetcsv($handle, '', ";")) !== FALSE) {
						$mysql->query('INSERT INTO '.prefix.'_boxers_weight_class (name) VALUES ('.db_squote($data[0]).')');
					}
					fclose($handle);
				}
				
				$orgs = array();
				
				if (($handle = fopen(extras_dir.'/boxers/install_boxers_organization.csv', "r")) !== FALSE) {
					while (($data = fgetcsv($handle, '', ";")) !== FALSE) {
						array_push($orgs, array('name' => $data[0], 'db_name' => $data[1]));
						$mysql->query('ALTER TABLE '.prefix.'_boxers ADD rating_'.$data[1].' int(5) DEFAULT NULL');
						$mysql->query('ALTER TABLE '.prefix.'_boxers_ratings_history ADD rating_'.$data[1].' int(5) DEFAULT NULL');
					}
					fclose($handle);
				}
				
				pluginSetVariable('boxers', 'organization', serialize($orgs));
				pluginSetVariable('boxers', 'pagination', 10);
				pluginsSaveConfig();
				
				$ULIB = new urlLibrary();
				$ULIB->loadConfig();
			
				$ULIB->registerCommand('boxers', 'profile',
					array ('vars' => array(
						'name' => array(
							'matchRegex' => '.+?', 
							'descr' => array(
								$config['default_lang'] => $lang['boxers:ULIB_boxer']
								)
							),
					),
					'descr'	=> array ($config['default_lang'] => $lang['boxers:ULIB_boxer_d']),
					)
				);
				$ULIB->saveConfig();
				
				plugin_mark_installed('boxers');
			} else {
				return false;
			}
			break;
	}
	return true;
}
