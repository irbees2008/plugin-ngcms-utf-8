<?php

# protect against hack attempts
if (!defined('NGCMS')) die ('Galaxy in danger');

LoadPluginLang('football_stats', 'config', '', 'fs', ':');

$db_update = array(
	array(
		'table'		=>	'football_countries',
		'action'	=>	'drop',
	),
	array(
		'table'		=>	'football_teams',
		'action'	=>	'drop',
	),
	
	array(
		'table'		=>	'football_championats',
		'action'	=>	'drop',
	),
	
	array(
		'table'		=>	'football_teams_championats',
		'action'	=>	'drop',
	),
	
	array(
		'table'		=>	'football_games',
		'action'	=>	'drop',
	),
	
	array(
		'table'		=>	'football_players',
		'action'	=>	'drop',
	),
	
	array(
		'table'		=>	'football_games_goals',
		'action'	=>	'drop',
	),
	
	array(
		'table'		=>	'football_leagues',
		'action'	=>	'drop',
	),
);

if ($_REQUEST['action'] == 'commit') {
	if (fixdb_plugin_install('football_stats', $db_update, 'deinstall')) {
		
		$ULIB = new urlLibrary();
		$ULIB->loadConfig();
		$ULIB->removeCommand('football_stats', 'football');
		$ULIB->removeCommand('football_stats', 'team');
		$ULIB->removeCommand('football_stats', 'player');
		$ULIB->removeCommand('football_stats', 'match');
		$ULIB->removeCommand('football_stats', 'league');
		$ULIB->removeCommand('football_stats', 'uefa_team');
		$ULIB->removeCommand('football_stats', 'uefa_country');
		$ULIB->removeCommand('football_stats', 'fifa_country');
        
		$ULIB->saveConfig();
		
		plugin_mark_deinstalled('football_stats');
	}
} else {
	generate_install_page('football_stats', $lang['fs:uninstall_msg'], 'deinstall');
}