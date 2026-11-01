<?php

/*
 * Plugin "football_stats" for NextGeneration CMS (http://ngcms.ru/)
 * Copyright (C) 2011 Alexey N. Zhukov (http://digitalplace.ru)
 * web:    http://digitalplace.ru 
 * e-mail: zhukov.alexei@gmail.com
 */
 
# protect against hack attempts
if (!defined('NGCMS'))die ('Galaxy in danger');

register_plugin_page('football_stats', 'football',    'showCountryStat');
register_plugin_page('football_stats', 'team',        'showTeam');
register_plugin_page('football_stats', 'player',      'showPlayer');
register_plugin_page('football_stats', 'match',       'showGame');
register_plugin_page('football_stats', 'add-fine',    'addFine');
//register_plugin_page('football_stats', 'add-fine1',    'addFine1');
register_plugin_page('football_stats', 'get-players', 'getPlayers');
register_plugin_page('football_stats', 'league',      'showLeague');
register_plugin_page('football_stats', 'uefa_team',   'showUEFAteam');
register_plugin_page('football_stats', 'uefa_country','showUEFAcountry');
register_plugin_page('football_stats', 'fifa_country','showFIFAcountry');
//register_plugin_page('football_stats', 'today',      'showtoday');
register_plugin_page('football_stats', 'matches',      'showMatches');
register_plugin_page('football_stats', 'scorers_page',      'showScorers');

add_act('index', 'showTablesMain');
add_act('index', 'showtoday');
add_act('index', 'showSelectedMatches');

add_act('index', 'goalScorersUkraine');
add_act('index', 'goalScorersChampionatLigue');
add_act('index', 'goalScorersEuropaLeague');
add_act('index', 'goalScorersRussia');
add_act('index', 'goalScorersEngland');
add_act('index', 'goalScorersGermany');
add_act('index', 'goalScorersSpain');
add_act('index', 'goalScorersFrance');
add_act('index', 'goalScorersItaly');

LoadPluginLang('football_stats', 'main', '', 'fs', ':');

function compare_scores($a, $b){
	if ($a['scores'] == $b['scores']) {
		if($a['in'] - $a['out'] < $b['in'] - $b['out'])
			return 1;
		elseif($a['in'] - $a['out'] == $b['in'] - $b['out'] && 
		       $a['team_'.$b['id_team']] <  $b['team_'.$a['id_team']])
			return 1;
		elseif ($a['in'] - $a['out'] == $b['in'] - $b['out'] && 
		        $a['team_'.$b['id_team']] == $b['team_'.$a['id_team']] &&
		        $a['wins-out'] < $b['wins-out'])
			return 1;
		else
			return 0;
	}
	return ($a['scores'] < $b['scores']) ? 1 : -1;
}

function clear_cache($plugin){
	if (($dir = get_plugcache_dir($plugin))) {
		if ($handle = opendir($dir)) {
			while (false !== ($filename = readdir($handle))) { 
				if ($filename == "." || $filename == "..")
					continue;
				unlink ($dir.$filename);
			}
			closedir($handle); 
		}
	}
}

function gameFillVariables($game, $pageOfMatch){
	
	global $lang;
	
	$tvars = array();
	$tvars['vars']['game_started'] = !empty($game['status']);
	
	switch($game['status']){
			case 0: $tvars['vars']['status'] = $lang['fs:status-0']; break;
			case 1: $tvars['vars']['status'] = $lang['fs:status-1']; break;
			case 2: $tvars['vars']['status'] = $lang['fs:status-2']; break;
            case 3: $tvars['vars']['status'] = $lang['fs:status-3']; break;
            case 4: $tvars['vars']['status'] = $lang['fs:status-4']; break;
	}
    
    //print date("Y.m.d")." ".date('Y.m.d', strtotime($game['date']))."<br/>";
    if(date("Y.m.d") > date('Y.m.d', strtotime($game['date'])) || date("Y.m.d") < date('Y.m.d', strtotime($game['date']))){
        $tvars['vars']['date_mini'] = date('d.m', strtotime($game['date']));
        $tvars['vars']['date'] = date('d.m, H:i', strtotime($game['date']));
        //print date('d.m', strtotime($game['date']))."<br/>";
    }
    else{
        $tvars['vars']['date_mini'] = date('H:i', strtotime($game['date']));
        $tvars['vars']['date'] = date('d.m, H:i', strtotime($game['date']));
    }
    		
	$tvars['vars']['team-owner']        = $game['team_owner'];
	$tvars['vars']['team-guest']        = $game['team_guest'];
	$tvars['vars']['link-team-owner']   = generatePluginLink('football_stats', 'team', array('name' => $game['alias_owner']), array(), true, true);
	$tvars['vars']['link-team-guest']   = generatePluginLink('football_stats', 'team', array('name' => $game['alias_guest']), array(), true, true);
	$tvars['vars']['link-game']         = generatePluginLink('football_stats', 'match', array('id' => $game['id'], array(), true, true));
	
	$tvars['vars']['reds_owner'] = $tvars['vars']['reds_guest'] = '';

    $photo_link_owner = getSomeDataFootballStats("photo", prefix."_football_teams", " WHERE alias=\"".$game['alias_owner']."\"");
    $photo_link_guest = getSomeDataFootballStats("photo", prefix."_football_teams", " WHERE alias=\"".$game['alias_guest']."\"");

    $tvars['vars']['photo_link_owner'] = ($photo_link_owner != "") ? pluginGetVariable('football_stats', 'photos.url').$photo_link_owner : pluginGetVariable('football_stats', 'photos.url')."default.png";
    $tvars['vars']['photo_link_guest'] = ($photo_link_guest != "") ? pluginGetVariable('football_stats', 'photos.url').$photo_link_guest : pluginGetVariable('football_stats', 'photos.url')."default.png";
		
	if($game['status'] != 0){
		$tvars['vars']['goals-owner'] = $game['goals_owner'];
		$tvars['vars']['goals-guest'] = $game['goals_guest'];

		if($pageOfMatch != "match" && $game['status'] != 4){
			if($game['status'] == 2 && (showPenalti($game['id'], $game['id_team_guest']) != 0 || showPenalti($game['id'], $game['id_team_owner'] != 0))){
				$tvars['vars']['goals-guest'] = intval(showPenalti($game['id'], $game['id_team_guest'])) + intval($game['goals_guest']);
				$tvars['vars']['goals-owner'] = intval(showPenalti($game['id'], $game['id_team_owner'])) + intval($game['goals_owner']);
			}
		}
        else if($game['status'] == 4){
            if(intval($game['goals_owner']) > intval($game['goals_guest'])){
                $tvars['vars']['goals-owner'] = '+';
                $tvars['vars']['goals-guest'] = '-';
            }
            else if(intval($game['goals_guest']) > intval($game['goals_owner'])){
                $tvars['vars']['goals-owner'] = '-';
                $tvars['vars']['goals-guest'] = '+';
            }
        }
        
        if (strlen($game['photo'])>0)
        {
         $tvars['vars']['photo'] = "<a href=".$game['photo']." style='text-decoration: underline;'>фото</a>";   
        }
		else{
		$tvars['vars']['photo'] = "<span style='color: #BBBBBB; font-size: 9px; line-height: 16px; text-decoration: none;'>фото<span>";
		}
         if (strlen($game['video'])>0)
        {
         $tvars['vars']['video'] = "<a href=".$game['video']." style='text-decoration: underline;'>відео</a>";   
        }
		else{
		$tvars['vars']['video'] = "<span style='color: #BBBBBB; font-size: 9px; line-height: 16px; text-decoration: none;'>відео<span>";
		}
	//	$tvars['vars']['video']       = $game['video'];
        
	//	$tvars['vars']['stats']       = $game['stats'];
		 if (strlen($game['stats'])>0)
        {
         $tvars['vars']['stats'] = "<a href=".$game['stats']." style='text-decoration: underline;'>статистика</a>";   
        }
		else{
		$tvars['vars']['stats'] = "<span style='color: #BBBBBB; font-size: 9px; line-height: 16px; text-decoration: none;'>статистика<span>";
		}

		for($i = 0; $i < $game['reds_owner']; $i++)
			$tvars['vars']['reds_owner'] .= $lang['fs:reds'];
					
		for($i = 0; $i < $game['reds_guest']; $i++)
			$tvars['vars']['reds_guest'] .= $lang['fs:reds'];
			
		$tvars['regx']['/\[not-complete\](.*?)\[\/not-complete\]/si'] = '$1';
	}
	else {
		$tvars['vars']['goals-owner'] = '?';
		$tvars['vars']['goals-guest'] = '?';
		$tvars['regx']['/\[not-complete\](.*?)\[\/not-complete\]/si'] = '';
        
        if (strlen($game['photo'])>0)
        {
         $tvars['vars']['photo'] = "<a href=".$game['photo']." style='text-decoration: underline;'>фото</a>";   
        }
		else{
		$tvars['vars']['photo'] = "<span style='color: #BBBBBB; font-size: 9px; line-height: 16px; text-decoration: none;'>фото<span>";
		}
       if (strlen($game['video'])>0)
        {
         $tvars['vars']['video'] = "<a href=".$game['video']." style='text-decoration: underline;'>відео</a>";   
        }
		else{
		$tvars['vars']['video'] = "<span style='color: #BBBBBB; font-size: 9px; line-height: 16px; text-decoration: none;'>відео<span>";
		}
        if (strlen($game['stats'])>0)
        {
         $tvars['vars']['stats'] = "<a href=".$game['stats']." style='text-decoration: underline;'>статистика</a>";   
        }
		else{
		$tvars['vars']['stats'] = "<span style='color: #BBBBBB; font-size: 9px; line-height: 16px; text-decoration: none;'>статистика<span>";
		}
	}	
	
	return $tvars;
}

# execute SQL queries and save data into a cache
function saveTableAndGameData($country_alias, $id_ch = 0){
	global $mysql;
	
	/*if(!is_file(get_plugcache_dir('football_stats').'countries')){
		$countries = $mysql->select('SELECT * FROM '.prefix.'_football_countries', 1);
		foreach($countries as $row)
			$countries_cache[$row['alias']] = $row; 
		cacheStoreFile('countries', serialize($countries_cache), 'football_stats');
		unset($countries);
	}
	else
		$countries_cache = unserialize(file_get_contents(get_plugcache_dir('football_stats').'countries'));
	*/
	
	$cacheFileName = md5('football_stats_countries').'.txt';

    if(pluginGetVariable('football_stats', 'cache')){
        $cacheData = cacheRetrieveFile($cacheFileName, pluginGetVariable('football_stats','cacheExpire'), 'football_stats');
        if ($cacheData != false) {
            $countries_cache = unserialize($cacheData);
        }
		else{
			$countries = $mysql->select('SELECT * FROM '.prefix.'_football_countries', 1);
			foreach($countries as $row)
				$countries_cache[$row['alias']] = $row; 
			cacheStoreFile($cacheFileName, serialize($countries_cache), 'football_stats');
			unset($countries);
		}
    }
	else{
		$countries = $mysql->select('SELECT * FROM '.prefix.'_football_countries', 1);
		foreach($countries as $row)
			$countries_cache[$row['alias']] = $row;
		unset($countries);
	}
	
	if(!is_numeric($country_alias))
		# $country = $mysql->record("SELECT * FROM ".prefix."_football_countries WHERE `alias` = ".db_squote($country_alias));
		$country = $countries_cache[$country_alias];
	else { 
		# $country = $mysql->record("SELECT * FROM ".prefix."_football_countries WHERE `id` = ".intval($country_alias));
		foreach($countries_cache as $key => $row)
			if($row['id'] == $country_alias){
				$country = $row;
				$country_alias = $key;
				break;
			}
	}	
		
	if(!$country)
		return -1;
		
	$cache_champ = 'champ_'.$country_alias;
	
	if(is_file(get_plugcache_dir('football_stats').$cache_champ))
		$championats = unserialize(file_get_contents(get_plugcache_dir('football_stats').$cache_champ));
	else
		$championats = $mysql->select("SELECT * FROM ".prefix."_football_championats 
		                               WHERE id_country = {$country['id']} 
		                               ORDER BY start DESC", 1);
	
	$true_championat = false;
	$last_championat = false;
	
	if(!$id_ch){
		$id_championat = $championats[0]['id'];
		$name_championat = $championats[0]['name'];
		$view_tournament_table = $championats[0]['view_tournament_table'];
		$id_country = $championats[0]['id_country'];
		if($id_championat) {
			$true_championat = true;
			$last_championat = true;
		}	
	}	
	else {
		foreach($championats as $championat)
			if($championat['id'] == $id_ch){
				$id_championat = $id_ch;
				$name_championat = $championat['name'];
				$view_tournament_table = $championat['view_tournament_table'];
				$id_country = $championat['id_country'];
				$true_championat = true;
				if($championats[0]['id'] == $id_championat)
					$last_championat = true;
				break;	
			}	
	}

	if(!$true_championat)
		return -2;
	
	$cache_teams = $last_championat ? "teams_{$country_alias}_last" : "teams_{$country_alias}_{$id_championat}";
	$cache_games = $last_championat ? "games_{$country_alias}_last" : "games_{$country_alias}_{$id_championat}";
	
	if(is_file(get_plugcache_dir('football_stats').$cache_teams))
		return array(
				'id_championat'   => $id_championat,
				'view_tournament_table' => $view_tournament_table,
				'id_country' => $id_country,
				'last_championat' => $last_championat, 
				'country'         => $country['name'], 
				'alias'           => $country['alias'], 
				'name_championat' => $name_championat);
			
	$teams_r = $mysql->select('SELECT tc.*, t.name, t.alias, t.groupped_teams
	                           FROM '.prefix."_football_teams_championats tc 
	                           LEFT JOIN ".prefix."_football_teams t 
	                            ON tc.id_team = t.id 
	                           WHERE id_championat = {$id_championat}", 1);

	$games = $mysql->select('SELECT g.*, t1.name as team_guest, t1.alias as alias_guest, t2.name as team_owner, t2.alias as alias_owner 
	                         FROM '.prefix.'_football_games g 
	                         LEFT JOIN '.prefix.'_football_teams t1 
	                           ON g.id_team_guest = t1.id 
	                         LEFT JOIN '.prefix."_football_teams t2 
	                           ON g.id_team_owner = t2.id 
	                         WHERE id_championat = {$id_championat}
	                         ORDER BY g.round DESC, g.date ASC", 1);
	
	$teams = array();
	
	foreach($teams_r as $team)
		$teams[$team['id_team']] = array(
				'id_team' => $team['id_team'], 
				'name' => $team['name'], 
				'alias' => $team['alias'],
				'groupped_teams' => $team['groupped_teams'],
				'games' => 0, 
				'wins' => 0,
				'wins-out' => 0,
				'defeats' => 0, 
				'draws' => 0, 
				'scores' => - $team['fine'],
				'in' => 0, 
				'out' => 0
				);	

	foreach($games as $game){
	
		if($game['status'] == 4){
			if($game['goals_guest'] > $game['goals_owner']){
				$teams[$game['id_team_guest']]['wins']++;
				$teams[$game['id_team_guest']]['wins-out']++;
				$teams[$game['id_team_owner']]['defeats']++;
				$teams[$game['id_team_guest']]['scores'] += 3;
				$teams[$game['id_team_guest']]["team_{$game['id_team_owner']}"]++;
			}
			elseif ($game['goals_owner'] > $game['goals_guest']){
				$teams[$game['id_team_owner']]['wins']++;
				$teams[$game['id_team_guest']]['defeats']++;
				$teams[$game['id_team_owner']]['scores'] += 3;
				$teams[$game['id_team_owner']]["team_{$game['id_team_guest']}"]++;
			}
		}
	
		if($game['status'] != 2) continue;
		
		if($game['goals_guest'] > $game['goals_owner']){
			$teams[$game['id_team_guest']]['wins']++;
			$teams[$game['id_team_guest']]['wins-out']++;
			$teams[$game['id_team_owner']]['defeats']++;
			$teams[$game['id_team_guest']]['scores'] += 3;
			$teams[$game['id_team_guest']]["team_{$game['id_team_owner']}"]++;
		}
		elseif ($game['goals_owner'] > $game['goals_guest']){
			$teams[$game['id_team_owner']]['wins']++;
			$teams[$game['id_team_guest']]['defeats']++;
			$teams[$game['id_team_owner']]['scores'] += 3;
			$teams[$game['id_team_owner']]["team_{$game['id_team_guest']}"]++;
		}
		else {
			$teams[$game['id_team_guest']]['draws']++;
			$teams[$game['id_team_owner']]['draws']++;
			$teams[$game['id_team_owner']]['scores'] += 1;
			$teams[$game['id_team_guest']]['scores'] += 1;
		}	
		
		$teams[$game['id_team_guest']]['games']++;
		$teams[$game['id_team_owner']]['games']++;
		$teams[$game['id_team_guest']]['in'] += $game['goals_guest'];
		$teams[$game['id_team_owner']]['in'] += $game['goals_owner'];
		$teams[$game['id_team_guest']]['out'] += $game['goals_owner'];
		$teams[$game['id_team_owner']]['out'] += $game['goals_guest'];	
	}		
	
	usort($teams, 'compare_scores');
	
	cacheStoreFile($cache_teams, serialize($teams), 'football_stats');
	cacheStoreFile($cache_games, serialize($games), 'football_stats');
	cacheStoreFile($cache_champ, serialize($championats), 'football_stats');
	
	return array(
		'id_championat'   => $id_championat,
		'view_tournament_table' => $view_tournament_table,
		'id_country' => $id_country,
		'last_championat' => $last_championat, 
		'country'         => $country['name'], 
		'alias'           => $country['alias'], 
		'name_championat' => $name_championat);
}

# /football/country/COUNTRY_NAME
function showCountryStat($params){
	global $mysql, $template, $tpl, $lang, $SYSTEM_FLAGS;

	$chid = isset($_GET['chid']) ? intval($_GET['chid']) : 0;
	
	$country_alias = rtrim($params['name'], '/');
	
	$status_save = saveTableAndGameData($country_alias, $chid);
	
	# country doesn't exist
	if ($status_save == -1){
		echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL=/">';
		exit();
	}
	
	# championat doesn't exist
	if ($status_save == -2){
		echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL=/">';	
		exit();
	}
	$SYSTEM_FLAGS['info']['title']['group'] = $lang['fs:tournament_table']." ".str_replace('{country}', $status_save['country'], $lang['fs:title-football']);

    $SYSTEM_FLAGS['meta']['description'] = $lang['fs:tournament_table']." ".str_replace('{country}', $status_save['country'], $lang['fs:title-football']).", ".
    $lang['fs:result_calendar_and_matches_of']." ".str_replace('{country}', $status_save['country'], $lang['fs:title-football']);
    $SYSTEM_FLAGS['meta']['keywords'] = str_replace('{country}', $status_save['country'], $lang['fs:title-football'])." ".
    $lang['fs:tournament_table'].", ".str_replace('{country}', $status_save['country'], $lang['fs:title-football'])." ".
    $lang['fs:results'].", ".$lang['fs:table']." ".str_replace('{country}', $status_save['country'], $lang['fs:title-football']).", ".
    str_replace('{country}', $status_save['country'], $lang['fs:title-football'])." ".$lang['fs:groups'].", ".
    str_replace('{country}', $status_save['country'], $lang['fs:title-football'])." ".$lang['fs:calendar'];
	
	$cache_all = $status_save['last_championat'] ? "ppage_{$country_alias}_last" : "ppage_{$country_alias}_{$status_save['id_championat']}";

    $cacheFileName = md5('football_'.$cache_all).'.txt';

    if(pluginGetVariable('football_stats', 'cache')){
        $cacheData = cacheRetrieveFile($cacheFileName, pluginGetVariable('football_stats','cacheExpire'), 'football_stats');
        if ($cacheData != false) {
            $template['vars']['mainblock'] = $cacheData;
            return;
        }
    }
    /*
	if(is_file(get_plugcache_dir('football_stats').$cache_all)){
		$template['vars']['mainblock'] = file_get_contents(get_plugcache_dir('football_stats').$cache_all);
		return;
	}*/
		
	$tpath = locatePluginTemplates(array(
		'plugin.country.table', 'plugin.country.table.row', 'plugin.show.games', 'plugin.country.table.group',
		'plugin.show.games.row', 'plugin.team.goals-scorers.row','plugin.logo_img'), 'football_stats', 1, '', 'main');
	
	$cache_teams = $status_save['last_championat'] ? "teams_{$country_alias}_last" : "teams_{$country_alias}_{$status_save['id_championat']}";
	$cache_games = $status_save['last_championat'] ? "games_{$country_alias}_last" : "games_{$country_alias}_{$status_save['id_championat']}";
	$cache_champ = 'champ_'.$country_alias;
	
	$teams = unserialize(file_get_contents(get_plugcache_dir('football_stats').$cache_teams));
	$games = unserialize(file_get_contents(get_plugcache_dir('football_stats').$cache_games));
	$champ = unserialize(file_get_contents(get_plugcache_dir('football_stats').$cache_champ));
	
	$tvars['vars']['entries'] = '';
	$first_six = $second_six = 0;
	$countryId = intval($status_save['id_country']);
	$champId = intval($status_save['id_championat']);
	
	foreach($teams as $key => $team){
		if ($status_save['view_tournament_table']) {			
			$groupped_teams = unserialize($team['groupped_teams']);
			
			if (isset($groupped_teams[$countryId][$champId])) {
				if($groupped_teams[$countryId][$champId] == 1) {
					$first_six++;
					$pvars['vars']['position'] = $first_six;
				} elseif($groupped_teams[$countryId][$champId] == 2) {
					$second_six++;
					$pvars['vars']['position'] = $second_six;
				} else {
					$pvars['vars']['position'] = $key + 1;
				}
			} else {
				$pvars['vars']['position'] = $key + 1;
			}
		} else {
			$pvars['vars']['position'] = $key + 1;
		}
	
		$pvars['vars']['name']          = $team['name'];
		$pvars['vars']['link']          = generatePluginLink('football_stats', 'team', array('name' => $team['alias']), array(), true, true);
		$pvars['vars']['games-all']     = $team['games'];
		$pvars['vars']['games-wins']    = $team['wins'];
		$pvars['vars']['games-draws']   = $team['draws'];
		$pvars['vars']['games-defeats'] = $team['defeats'];
		$pvars['vars']['scores']        = round($team['scores']);
		$pvars['vars']['in']            = $team['in'];
		$pvars['vars']['out']           = $team['out'];
        $photo_link = getSomeDataFootballStats("photo", prefix."_football_teams", " WHERE alias=\"".$team['alias']."\"");
        $pvars['vars']['photo_link'] = ($photo_link != "") ? pluginGetVariable('football_stats', 'photos.url').$photo_link : pluginGetVariable('football_stats', 'photos.url')."default.png";
		
		$tpl->template('plugin.country.table.row', $tpath['plugin.country.table.row']);
		$tpl->vars('plugin.country.table.row', $pvars);
		
		if ($status_save['view_tournament_table']) {
			$groupped_teams = unserialize($team['groupped_teams']);
			
			if (isset($groupped_teams[$countryId][$champId])) {
				if($groupped_teams[$countryId][$champId] == 1) {
					$tvars['vars']['entries_first'] .= $tpl->show('plugin.country.table.row');
				} elseif($groupped_teams[$countryId][$champId] == 2) {
					$tvars['vars']['entries_second'] .= $tpl->show('plugin.country.table.row');
				}
			}
		} else {
			$tvars['vars']['entries'] .= $tpl->show('plugin.country.table.row');
		}
	}
	
	$round = 0;
	$games_row = '';
	unset($pvars);
	
	foreach($games as $game){
		
		$pvars = gameFillVariables($game, "");
			/*=================*/
                    $channel_logo = include(extras_dir.'/football_stats/inc/channel.php');
				
				foreach($channel_logo as $key => $value){
					if($key == $game['channel']) {
					       $tlogo['vars']['logo'] = $value;
					       $tpl->template('plugin.logo_img', $tpath['plugin.logo_img']);
	                       $tpl->vars('plugin.logo_img', $tlogo);
	                        
						$pvars['vars']['channel'] = $tpl->show('plugin.logo_img');
						break;
					}	
				}
                
     if(strlen($pvars['vars']['channel'])<2)  
     {
       $pvars['vars']['channel']=''; 
     } 
            
            
 		/*=================*/
		if($round != $game['round']){
			$round = $game['round'];
			$pvars['vars']['round'] = str_replace('{round}', $round, $lang['fs:round']);
		}
		else
			$pvars['vars']['round'] = '';
		
		$tpl->template('plugin.show.games.row', $tpath['plugin.show.games.row']);
		$tpl->vars('plugin.show.games.row', $pvars);
		$games_row .= $tpl->show('plugin.show.games.row');	
	}
	
	$tpl->template('plugin.show.games', $tpath['plugin.show.games']);
	$tpl->vars('plugin.show.games', array('vars' => array('entries' => $games_row, 'id' => $status_save['id_championat'])));
	$tvars['vars']['games'] = $tpl->show('plugin.show.games');

    $scorers_on_site = (pluginGetVariable('football_stats', 'scorers_on_site')) ?
    " LIMIT ".pluginGetVariable('football_stats', 'scorers_on_site') : "";
    $count_scorers_in_admin = (pluginGetVariable('football_stats', 'scorers_in_admin_panel')) ?
    pluginGetVariable('football_stats', 'scorers_in_admin_panel') : 20;

	foreach($champ as $ch)
		$tvars['vars']['other-championats'] .= str_replace(
		                                        array('{name}', '{start}', '{finish}', '{link}', '{selected}'),
		                                        array($ch['name'], $ch['start'], $ch['finish'], 
		                                          generatePluginLink('football_stats', 'football', array('name' => $country_alias), array('chid' => $ch['id']), true, true), 
		                                        $status_save['id_championat'] == $ch['id'] ? 'selected' : ''), $lang['fs:other-championats']);
	
	
    $goal_scorers = $mysql->select("SELECT COUNT(fg.id_player) as `goals`, `id_player`
											FROM ".prefix."_football_games_goals fg
											LEFT JOIN ".prefix."_football_players p 
											  ON fg.id_player = p.id
											WHERE fg.id_championat = ".$status_save['id_championat']." AND fg.id_player <> -1 AND fg.auto = 0
											GROUP BY fg.id_player 
											ORDER BY goals DESC LIMIT ".$count_scorers_in_admin, 1);
    
    foreach($goal_scorers as $goal){
		if(!mysql_query("UPDATE ".prefix."_football_scorers SET goals=".$goal['goals']." WHERE id_player=".$goal['id_player']." AND id_championat=".$status_save['id_championat'])){
			print mysql_error();
		}
	}
    
    $get_scorers = $mysql->select("SELECT s.*, SUM(s.goals) as count_goals, p.name as player, c.name as championat
									FROM ".prefix."_football_scorers s
									JOIN ".prefix."_football_players p ON s.id_player = p.id
									JOIN ".prefix."_football_championats c ON s.id_championat = c.id
									WHERE s.id_championat=".$status_save['id_championat']." GROUP BY s.id_player
									ORDER BY count_goals DESC, number_id".$scorers_on_site);

	$tvars['vars']['goal-scorers'] = '';
	
	foreach($get_scorers as $row){
		$pvars['vars']['name']  = $row['player'];
		$pvars['vars']['link']  = generatePluginLink('football_stats', 'player', array('id' => $row['id_player'], array(), true, true));
		$pvars['vars']['goals'] = $row['count_goals'];
		
		$tpl->template('plugin.team.goals-scorers.row', $tpath['plugin.team.goals-scorers.row']);
		$tpl->vars('plugin.team.goals-scorers.row', $pvars);

		$tvars['vars']['goal-scorers'] .= $tpl->show('plugin.team.goals-scorers.row');
	}
	if($tvars['vars']['goal-scorers'] != ""){
		$tvars['vars']['goal-scorers'] = "<table border='0'>
			<tbody>
				<tr>
					<td style='width: 600px; text-align: right;'><b>".$lang['fs:goal_scorers'].":</b><br></td>
				</tr>
				<tr>
					<td style='text-align: right;'>".$tvars['vars']['goal-scorers']."</td>
				</tr>
			</tbody>
			</table>";
	}
	
	if ($status_save['view_tournament_table']) {
		$tpl->template('plugin.country.table.group', $tpath['plugin.country.table.group']);
		$tpl->vars('plugin.country.table.group', $tvars);
		$template['vars']['mainblock'] = $tpl->show('plugin.country.table.group');
	} else {	
		$tpl->template('plugin.country.table', $tpath['plugin.country.table']);
		$tpl->vars('plugin.country.table', $tvars);
		$template['vars']['mainblock'] = $tpl->show('plugin.country.table');
	}
	
    # create cache
	if (pluginGetVariable('football_stats', 'cache')) {
		cacheStoreFile($cacheFileName, $template['vars']['mainblock'], 'football_stats');
	}

	//cacheStoreFile($cache_all, $template['vars']['mainblock'], 'football_stats');
}
# blocks in main.tpl {COUNTRY_NAME}
function showTablesMain(){
	global $mysql, $lang, $config, $template, $tpl, $CurrentHandler;
	
	$championats = $rounds = array();
	$championats = pluginGetVariable('football_stats', 'championats') ? explode(', ', pluginGetVariable('football_stats', 'championats')) : '';
	
	$country_views = unserialize(pluginGetVariable('football_stats', 'country_views'));
	$routeCacheSuffix = md5(
		(isset($CurrentHandler['pluginName']) ? $CurrentHandler['pluginName'] : '') . "\0" .
		(isset($CurrentHandler['handlerName']) ? $CurrentHandler['handlerName'] : '')
	);
	# $rounds      = pluginGetVariable('football_stats', 'rounds') ? explode(', ', pluginGetVariable('football_stats', 'rounds')) : '';

	$tpath = locatePluginTemplates(array('plugin.main.country.table', 'plugin.main.country.table.group', 'plugin.main.country.table.row', 'plugin.main.show.games', 
	                                     'plugin.main.show.games.row', 'plugin.league.group.table.row', 'plugin.league.group.table', 'plugin.logo_img'), 'football_stats', 1, '', 'main');
	
	if($championats){
		foreach($championats as $country_alias){
			
			$status_save = saveTableAndGameData($country_alias);

			$cache_all = 'main_'.$country_alias.'_last_'.$routeCacheSuffix;
			if(is_file(get_plugcache_dir('football_stats').$cache_all)){
				$template['vars']['championat_'.$country_alias] = file_get_contents(get_plugcache_dir('football_stats').$cache_all);
				continue;
			}
		
			$cache_teams = 'teams_'.$country_alias.'_last';
			
			$teams = unserialize(file_get_contents(get_plugcache_dir('football_stats').$cache_teams));
			
			$tvars['vars']['entries'] = '';
			$first_six = $second_six = 0;
			
			$countryId = intval($status_save['id_country']);
			$chid = intval($status_save['id_championat']);
			
			foreach($teams as $key => $team){			
				if ($status_save['view_tournament_table']) {
					$groupped_teams = unserialize($team['groupped_teams']);
			
					if (isset($groupped_teams[$countryId][$chid])) {
						if($groupped_teams[$countryId][$chid] == 1) {
							$first_six++;
							$pvars['vars']['position'] = $first_six;
						} elseif($groupped_teams[$countryId][$chid] == 2) {
							$second_six++;
							$pvars['vars']['position'] = $second_six;
						}
					} else {
						$pvars['vars']['position']  = $key + 1;
					}
				} else {
					$pvars['vars']['position']  = $key + 1;
				}
			
				$pvars['vars']['name']      = $team['name'];
				$pvars['vars']['games-all'] = $team['games'];
				$pvars['vars']['scores']    = round($team['scores']);
				$pvars['vars']['link']      = generatePluginLink('football_stats', 'team', array('name' => $team['alias']), array(), true, true);

                $photo_link = getSomeDataFootballStats("photo", prefix."_football_teams", " WHERE alias=\"".$team['alias']."\"");
                $pvars['vars']['photo_link'] = ($photo_link != "") ? pluginGetVariable('football_stats', 'photos.url').$photo_link : pluginGetVariable('football_stats', 'photos.url')."default.png";
				
				$tpl->template('plugin.main.country.table.row', $tpath['plugin.main.country.table.row']);
				$tpl->vars('plugin.main.country.table.row', $pvars);
				
				if ($status_save['view_tournament_table']) {
					$groupped_teams = unserialize($team['groupped_teams']);
					
					if (isset($groupped_teams[$countryId][$chid])) {
						if($groupped_teams[$countryId][$chid] == 1) {
							$tvars['vars']['entries_first'] .= $tpl->show('plugin.main.country.table.row');
						} elseif($groupped_teams[$countryId][$chid] == 2) {
							$tvars['vars']['entries_second'] .= $tpl->show('plugin.main.country.table.row');
						}
					} else {
						$tvars['vars']['entries'] .= $tpl->show('plugin.main.country.table.row');
					}
				} else {
					$tvars['vars']['entries'] .= $tpl->show('plugin.main.country.table.row');
				}
			}
			
			$tvars['vars']['championat-link'] = generatePluginLink('football_stats', 'football', array('name' => $country_alias), array('chid' => $status_save['id_championat']), true, true);
			$tvars['vars']['championat-name'] = $status_save['name_championat'];
			$tvars['regx']['/\[not-country\](.*?)\[\/not-country\]/si'] = '';
			$tvars['regx']['/\[country\](.*?)\[\/country\]/si'] = '$1';			
			
			
			if ($status_save['view_tournament_table']) {
				$tpl->template('plugin.main.country.table.group', $tpath['plugin.main.country.table.group']);
				$tpl->vars('plugin.main.country.table.group', $tvars);
				$template['vars']['championat_'.$country_alias] = $tpl->show('plugin.main.country.table.group');
			} else {
				$tpl->template('plugin.main.country.table', $tpath['plugin.main.country.table']);
				$tpl->vars('plugin.main.country.table', $tvars);
				$template['vars']['championat_'.$country_alias] = $tpl->show('plugin.main.country.table');
			}
			
			cacheStoreFile($cache_all, $template['vars']['championat_'.$country_alias], 'football_stats');
			
			unset($tvars, $pvars);
		}
	}
	
	$rounds = unserialize(pluginGetVariable('football_stats', 'country_views'));
	
	if($rounds){
		foreach($rounds as $country_alias => $info){
			
			if(!$info['v']){
				$template['vars']['round_'.$country_alias] = '';
				continue;
			}	
			
			$status_save = saveTableAndGameData($country_alias);

			$cache_all = 'main_round_'.$country_alias."_{$info['n']}_".$routeCacheSuffix;
			if(is_file(get_plugcache_dir('football_stats').$cache_all)){
				$template['vars']['round_'.$country_alias] = file_get_contents(get_plugcache_dir('football_stats').$cache_all);
				continue;
			}
		
			$cache_games = 'games_'.$country_alias."_last";
			
			$games = unserialize(file_get_contents(get_plugcache_dir('football_stats').$cache_games));
			
			$tvars['vars']['entries'] = '';
			
			foreach($games as $game){
				if($info['n'] != $game['round']){
					continue;
				}

				$pvars = gameFillVariables($game, "");
					
                			/*=================*/
                    $channel_logo = include(extras_dir.'/football_stats/inc/channel.php');
				
				foreach($channel_logo as $key => $value){
					if($key == $game['channel']) {
					       $tlogo['vars']['logo'] = $value;
					       $tpl->template('plugin.logo_img', $tpath['plugin.logo_img']);
	                       $tpl->vars('plugin.logo_img', $tlogo);
	                        
						$pvars['vars']['channel'] = $tpl->show('plugin.logo_img');
						break;
					}	
				}
                
     if(strlen($pvars['vars']['channel'])<2)  
     {
       $pvars['vars']['channel']=''; 
     } 
            
            
 		/*=================*/
                
				$tpl->template('plugin.main.show.games.row', $tpath['plugin.main.show.games.row']);
				$tpl->vars('plugin.main.show.games.row', $pvars);
				$tvars['vars']['entries'].= $tpl->show('plugin.main.show.games.row');	
			}
			
			
			$tvars['vars']['championat-link']  = generatePluginLink('football_stats', 'football', array('name' => $country_alias), array('chid' => $status_save['id_championat']), true, true);
			$tvars['vars']['championat-name']  = $status_save['name_championat'];
			$tvars['vars']['championat-round'] = $info['n'];
			$tvars['regx']['/\[country\](.*?)\[\/country\]/si'] = '$1';
			$tvars['regx']['/\[not-country\](.*?)\[\/not-country\]/si'] = '';
			
			$tpl->template('plugin.main.show.games', $tpath['plugin.main.show.games']);
			$tpl->vars('plugin.main.show.games', $tvars);
			$template['vars']['round_'.$country_alias] = $tpl->show('plugin.main.show.games');

			cacheStoreFile($cache_all, $template['vars']['round_'.$country_alias], 'football_stats');
			
			unset($tvars, $pvars);
		}
	}
	
	if($countries_cache = @file_get_contents(get_plugcache_dir('football_stats').'countries'))
		$countries_cache = unserialize($countries_cache);
	if($countries_cache){
	$r = $championats ? array_diff(array_keys($countries_cache), $championats) : array_keys($countries_cache);
	if($r)
	foreach($r as $k => $v)
		$template['vars']['championat_'.$v] = '';		
	}	
	
	$league_views = unserialize(pluginGetVariable('football_stats', 'league_views'));
	
	foreach($league_views as $league){
		if(!$league['v']){
				$template['vars'][$league['name']] = '';
				continue;
			}
			
		$cache_all = "league_{$league['name']}";
		
		if(is_file(get_plugcache_dir('football_stats').$cache_all)){
				$template['vars'][$league['name']] = file_get_contents(get_plugcache_dir('football_stats').$cache_all);
				continue;
		}
		
		$games = saveLeagueGames($league['id']);
		
		if($league['t'] == 0){
		
			$tvars['vars']['entries'] = '';
		
			foreach($games as $game){

					$pvars = gameFillVariables($game, "");
					
								/*=================*/
                    $channel_logo = include(extras_dir.'/football_stats/inc/channel.php');
				
				foreach($channel_logo as $key => $value){
					if($key == $game['channel']) {
					       $tlogo['vars']['logo'] = $value;
					       $tpl->template('plugin.logo_img', $tpath['plugin.logo_img']);
	                       $tpl->vars('plugin.logo_img', $tlogo);
	                        
						$pvars['vars']['channel'] = $tpl->show('plugin.logo_img');
						break;
					}	
				}
                
     if(strlen($pvars['vars']['channel'])<2)  
     {
       $pvars['vars']['channel']=''; 
     } 
            
            
 		/*=================*/
					$tpl->template('plugin.main.show.games.row', $tpath['plugin.main.show.games.row']);
					$tpl->vars('plugin.main.show.games.row', $pvars);
					$tvars['vars']['entries'] .= $tpl->show('plugin.main.show.games.row');	
			}
			
			if($league['champt_start'] != $league['champt_end'])
				$cnf = array('type' => $league['champt_type'], 'year_start' => $league['champt_start'], 'year_end' => $league['champt_end']);
			else
				$cnf = array('type' => $league['champt_type'], 'year_start' => $league['champt_start']);
			
			$tvars['regx']['/\[country\](.*?)\[\/country\]/si'] = '';		
			$tvars['regx']['/\[not-country\](.*?)\[\/not-country\]/si'] = '$1';
			$tvars['vars']['championat-link']  = generatePluginLink('football_stats', 'league', $cnf, array(), true, true);
			$tvars['vars']['championat-name']  = $league['league_name'];
			$tvars['vars']['championat-round'] = $league['champt_name'];			
			
			$tpl->template('plugin.main.show.games', $tpath['plugin.main.show.games']);
			$tpl->vars('plugin.main.show.games', $tvars);
			$template['vars'][$league['name']] = $tpl->show('plugin.main.show.games');
		} else {
			$teams = saveLeagueTable($league['id'], $games);
			
			$tvars['vars']['entries'] = '';
			
				foreach($teams as $key => $team){
					$xvars['vars']['position']      = $key + 1;
					$xvars['vars']['name']          = $team['name'];
					$xvars['vars']['link']          = generatePluginLink('football_stats', 'team', array('name' => $team['alias']), array(), true, true);
					$xvars['vars']['games-all']     = $team['games'];
					$xvars['vars']['games-wins']    = $team['wins'];
					$xvars['vars']['games-draws']   = $team['draws'];
					$xvars['vars']['games-defeats'] = $team['defeats'];
					$xvars['vars']['scores']        = round($team['scores']);
					$xvars['vars']['in']            = $team['in'];
					$xvars['vars']['out']           = $team['out'];

                    $photo_link = getSomeDataFootballStats("photo", prefix."_football_teams", " WHERE alias=\"".$team['alias']."\"");
                    $xvars['vars']['photo_link'] = ($photo_link != "") ? pluginGetVariable('football_stats', 'photos.url').$photo_link : pluginGetVariable('football_stats', 'photos.url')."default.png";
					
					$tpl->template('plugin.main.country.table.row', $tpath['plugin.main.country.table.row']);
					$tpl->vars('plugin.main.country.table.row', $xvars);
					$tvars['vars']['entries'] .= $tpl->show('plugin.main.country.table.row');
				}
				
				if($league['champt_start'] != $league['champt_end'])
				$cnf = array('type' => $league['champt_type'], 'year_start' => $league['champt_start'], 'year_end' => $league['champt_end']);
					else
				$cnf = array('type' => $league['champt_type'], 'year_start' => $league['champt_start']);
				
				$tvars['regx']['/\[country\](.*?)\[\/country\]/si'] = '';
				$tvars['regx']['/\[not-country\](.*?)\[\/not-country\]/si'] = '$1';						
				$tvars['vars']['championat-link']  = generatePluginLink('football_stats', 'league', $cnf, array(), true, true);
				$tvars['vars']['championat-name']  = $league['league_name'];
				$tvars['vars']['championat-round'] = $league['champt_name'];			
			
				
				$tpl->template('plugin.main.country.table', $tpath['plugin.main.country.table']);
				$tpl->vars('plugin.main.country.table', $tvars);
				$template['vars'][$league['name']] =  $tpl->show('plugin.main.country.table');
		}		
		
		cacheStoreFile($cache_all, $template['vars'][$league['name']], 'football_stats');
		
		unset($xvars, $pvars, $tvars);
	}
	
	unset($tpath, $tvars, $xvars, $pvars);
	
	if(is_file(get_plugcache_dir('football_stats').'uefa_teams_main')){
		$template['vars']['uefa_teams'] = file_get_contents(get_plugcache_dir('football_stats').'uefa_teams_main');
	} else {	
	
		$tpath = locatePluginTemplates(array('plugin.main.team.uefa', 'plugin.main.team.uefa.row'), 'football_stats', 1, '', 'main');
		
		$teams = saveUEFAcache('team');
		
		if(pluginGetVariable('football_stats', 'uefa_main'))
			$teams = array_slice($teams, 0, pluginGetVariable('football_stats', 'uefa_main'));
		
		foreach($teams as $team){
			$pvars['vars']['name']          = $team['name'];
			$pvars['vars']['uefa_rating']   = $team['uefa_rating'];
			$pvars['vars']['uefa_position'] = $team['uefa_position'];
			$pvars['vars']['link']          = generatePluginLink('football_stats', 'team', array('name' => $team['alias'], array(), true, true));

            $photo_link = getSomeDataFootballStats("photo", prefix."_football_teams", " WHERE alias=\"".$team['alias']."\"");

            $pvars['vars']['photo_link'] = ($photo_link != "") ? pluginGetVariable('football_stats', 'photos.url').$photo_link : pluginGetVariable('football_stats', 'photos.url')."default.png";
			
			$tpl->template('plugin.main.team.uefa.row', $tpath['plugin.main.team.uefa.row']);
			$tpl->vars('plugin.main.team.uefa.row', $pvars);
			$tvars['vars']['entries'] .= $tpl->show('plugin.main.team.uefa.row');
		}
		
		$tpl->template('plugin.main.team.uefa', $tpath['plugin.main.team.uefa']);
		$tpl->vars('plugin.main.team.uefa', $tvars);
			
		$template['vars']['uefa_teams'] = $tpl->show('plugin.main.team.uefa');
		cacheStoreFile('uefa_teams_main', $template['vars']['uefa_teams'], 'football_stats');
	}
	#
	
	unset($tpath, $tvars, $xvars, $pvars);
	
	if(is_file(get_plugcache_dir('football_stats').'uefa_countries_main')){
		$template['vars']['uefa_countries'] = file_get_contents(get_plugcache_dir('football_stats').'uefa_countries_main');
	} else {
	
		$tpath = locatePluginTemplates(array('plugin.main.country.uefa', 'plugin.main.country.uefa.row'), 'football_stats', 1, '', 'main');
		
		$teams = saveUEFAcache('country');
		
		if(pluginGetVariable('football_stats', 'uefa_main'))
			$teams = array_slice($teams, 0, pluginGetVariable('football_stats', 'uefa_main'));
			
		foreach($teams as $team){
			$pvars['vars']['name']          = $team['name'];
			$pvars['vars']['uefa_rating']   = $team['uefa_rating'];
			$pvars['vars']['uefa_position'] = $team['uefa_position'];
			$pvars['vars']['link']          = generatePluginLink('football_stats', 'team', array('name' => $team['alias'], array(), true, true));
			
            $photo_link = getSomeDataFootballStats("photo", prefix."_football_teams", " WHERE alias=\"".$team['alias']."\"");

            $pvars['vars']['photo_link'] = ($photo_link != "") ? pluginGetVariable('football_stats', 'photos.url').$photo_link : pluginGetVariable('football_stats', 'photos.url')."default.png";

			$tpl->template('plugin.main.country.uefa.row', $tpath['plugin.main.country.uefa.row']);
			$tpl->vars('plugin.main.country.uefa.row', $pvars);
			$tvars['vars']['entries'] .= $tpl->show('plugin.main.country.uefa.row');
		}
		
		$tpl->template('plugin.main.country.uefa', $tpath['plugin.main.country.uefa']);
		$tpl->vars('plugin.main.country.uefa', $tvars);
			
		$template['vars']['uefa_countries'] = $tpl->show('plugin.main.country.uefa');
		cacheStoreFile('uefa_countries_main', $template['vars']['uefa_countries'], 'football_stats');
	}

    unset($tpath, $tvars, $xvars, $pvars);
	
	if(is_file(get_plugcache_dir('football_stats').'fifa_countries_main')){
		$template['vars']['fifa_countries'] = file_get_contents(get_plugcache_dir('football_stats').'fifa_countries_main');
	} else {
	
		$tpath = locatePluginTemplates(array('plugin.main.country.uefa', 'plugin.main.country.uefa.row'), 'football_stats', 1, '', 'main');
		
        $limit_fifa = (pluginGetVariable('football_stats', 'uefa_main')) ?
        ' LIMIT '.pluginGetVariable('football_stats', 'uefa_main') : "";

        $teams = $mysql->select('SELECT * FROM '.prefix.'_football_teams WHERE fifa_rating IS NOT NULL AND fifa_rating <> 0
        ORDER BY fifa_position ASC'.$limit_fifa, 1);
		/*$teams = saveUEFAcache('country');
		
		if(pluginGetVariable('football_stats', 'uefa_main'))
			$teams = array_slice($teams, 0, pluginGetVariable('football_stats', 'uefa_main'));*/
			
		foreach($teams as $team){
			$pvars['vars']['name']          = $team['name'];
			$pvars['vars']['uefa_rating']   = $team['fifa_rating'];
			$pvars['vars']['uefa_position'] = $team['fifa_position'];
			$pvars['vars']['link']          = generatePluginLink('football_stats', 'team', array('name' => $team['alias'], array(), true, true));
			
            $photo_link = getSomeDataFootballStats("photo", prefix."_football_teams", " WHERE alias=\"".$team['alias']."\"");

            $pvars['vars']['photo_link'] = ($photo_link != "") ? pluginGetVariable('football_stats', 'photos.url').$photo_link : pluginGetVariable('football_stats', 'photos.url')."default.png";

			$tpl->template('plugin.main.country.uefa.row', $tpath['plugin.main.country.uefa.row']);
			$tpl->vars('plugin.main.country.uefa.row', $pvars);
			$tvars['vars']['entries'] .= $tpl->show('plugin.main.country.uefa.row');
		}
		
		$tpl->template('plugin.main.country.uefa', $tpath['plugin.main.country.uefa']);
		$tpl->vars('plugin.main.country.uefa', $tvars);
			
		$template['vars']['fifa_countries'] = $tpl->show('plugin.main.country.uefa');
		cacheStoreFile('fifa_countries_main', $template['vars']['fifa_countries'], 'football_stats');
	}
}

# team/TEAM_NAME
function showTeam($params){
	global $mysql, $config, $tpl, $template, $SYSTEM_FLAGS, $lang;
	
	$alias = db_squote($params['name']);
	$year = (isset($_GET['chid'])) ? htmlspecialchars($_GET['chid']) :'';// date('Y');
    
	$cache = "team_{$params['name']}_{$year}";
    
	//echo $year."<br/>";
    
    
	if(is_file(get_plugcache_dir('football_stats').$cache)){
		$info = explode('<-->', file_get_contents(get_plugcache_dir('football_stats').$cache));
		
		$SYSTEM_FLAGS['info']['title']['group'] = str_replace('{team}', $info[0], $lang['fs:title-team']);
        $SYSTEM_FLAGS['meta']['description'] = $lang['fs:All_result_about_club']." ".str_replace('{team}', $info[0], $lang['fs:title-team']);
        $SYSTEM_FLAGS['meta']['keywords'] = str_replace('{team}', $info[0], $lang['fs:title-team']).", ".$lang['fs:club']." ".
        str_replace('{team}', $info[0], $lang['fs:title-team']).", ".$lang['fs:result']." ".
        str_replace('{team}', $info[0], $lang['fs:title-team']).", ".$lang['fs:news']." ".
        str_replace('{team}', $info[0], $lang['fs:title-team']);
		$template['vars']['mainblock'] = $info[1];
		return;
	}
			
	$team = $mysql->record('SELECT * FROM '.prefix."_football_teams WHERE `alias` = {$alias} LIMIT 1", 1);
	
	if(!$team){
		header('Location: '.$config['home_url']);
		exit();
	}
	
	$SYSTEM_FLAGS['info']['title']['group'] = str_replace('{team}', $team['name'], $lang['fs:title-team']);
	
	$tpath = locatePluginTemplates(array('plugin.team', 'plugin.team.players.row', 'plugin.team.games.row', 'plugin.team.games.round', 'plugin.logo_img'), 'football_stats', 1, '', 'main');
	
	$tvars['vars']['name']        = $team['name'];
	$tvars['vars']['description'] = $team['description'];

    $tvars['vars']['photo'] = ($team['photo'] != NULL && trim($team['photo']) != "") ? pluginGetVariable('football_stats', 'photos.url').$team['photo'] : pluginGetVariable('football_stats', 'photos.url')."default.png";

	if($team['type'] == 0){
		$type = 'id_team';
        $get_rating_uefa = $mysql->select("SELECT uefa_rating, uefa_position FROM ".prefix."_football_teams WHERE alias=".$alias);
        foreach($get_rating_uefa as $uefa){
            $uefa_pos = ($uefa['uefa_position'] != 0) ? "<span class='cell12'><b>Розташування в <a href='".
            generatePluginLink('football_stats', 'uefa_team', array(), array(), true, true)
            ."' target='_blank' style='text-decoration: underline;'>рейтингу УЄФА</a>: </b>".$uefa['uefa_position']." місце.</span><br/>" : "";
            if($uefa['uefa_rating'] != 0){
                $uefa_rat = "<span class='cell12'><b><a href='".
            generatePluginLink('football_stats', 'uefa_team', array(), array(), true, true)
            ."' target='_blank' style='text-decoration: underline;'>Коефіцієнт УЄФА</a>: </b>".$uefa['uefa_rating'].".</span><br/>";
            }
            else{
                $uefa_rat = "";
            }
            if($uefa_pos != "" || $uefa_rat != ""){
                $tvars['vars']['rating_team'] = $uefa_pos.$uefa_rat."<br/>";
            }
            else{
                $tvars['vars']['rating_team'] = "";
            }
        }
    }
	else{
		$type = 'id_team_country';
        $get_rating_fifa = $mysql->select("SELECT fifa_rating, fifa_position, uefa_rating, uefa_position FROM ".prefix."_football_teams WHERE alias=".$alias);
        foreach($get_rating_fifa as $fifa){
            $fifa_pos_rat = ($fifa['fifa_position'] != 0 && $fifa['fifa_rating'] != 0) ? 
            "<span class='cell12'><b>Розташування в <a href='".
            generatePluginLink('football_stats', 'fifa_country', array(), array(), true, true)
            ."' target='_blank' style='text-decoration: underline;'>рейтингу ФІФА</a>: </b>".$fifa['fifa_position']." місце (".$fifa['fifa_rating'].
            " оч.).</span><br/>" : "";
            $uefa_pos_country = ($fifa['uefa_position'] != 0) ? "<span class='cell12'><b>Розташування в <a href='".
            generatePluginLink('football_stats', 'uefa_country', array(), array(), true, true)
            ."' target='_blank' style='text-decoration: underline;'>рейтингу УЄФА</a>: </b>".
            $fifa['uefa_position']." місце.</span><br/>" : "";
            $uefa_rat_country = ($fifa['uefa_rating'] != 0) ? "<span class='cell12'><b><a href='".
            generatePluginLink('football_stats', 'uefa_country', array(), array(), true, true)
            ."' target='_blank' style='text-decoration: underline;'>Коефіцієнт УЄФА</a>: </b>".$fifa['uefa_rating'].".</span><br/>" : "";

            if($fifa_pos_rat != "" || $uefa_pos_country != "" || $uefa_rat_country != ""){
                $tvars['vars']['rating_team'] = $fifa_pos_rat.$uefa_pos_country.$uefa_rat_country."<br/>";
            }
            else{
                $tvars['vars']['rating_team'] = "";
            }
        }
    }

    $query_players = "SELECT * FROM ".prefix."_football_players WHERE ".$type."=".$team['id']." AND status=1";
    $current_year = date('Y');
    if(mysql_num_rows(mysql_query($query_players)) >= 11){
	    $players = $mysql->select($query_players, 1);
	    $rows = '';
	
	    if($players){
		    foreach($players as $player){
			    $pvars['vars']['name'] = $player['name'];
			    $pvars['vars']['link'] = generatePluginLink('football_stats', 'player', array('id' => $player['id'], array(), true, true));
			
			    $tpl->template('plugin.team.players.row', $tpath['plugin.team.players.row']);
			    $tpl->vars('plugin.team.players.row', $pvars);
			    $rows .= $tpl->show('plugin.team.players.row');
		    }
	    }	

	    $tvars['vars']['players'] = "<span class='cell12'><b>Гравці у складі: </b><br><br>".mb_substr($rows, 0, -2).".</span><br/>";
    }
    else{
        $tvars['vars']['players'] = "";
    }
    $tvars['vars']['link_to_edit'] = "<a href='{admin_url}/admin.php?mod=football_stats&amp;action=edit-team&amp;id=".$team['id']
    ."' target='_blank'>".$lang['fs:edit']."</a>";
	
	$tvars['vars']['games'] = '';
	unset($pvars, $xvars);	
        $country_alias = rtrim($params['name'], '/');
        $cache_champ = 'champ_'.$country_alias;
    //echo $cache_champ."<br/>";
    $champ = unserialize(file_get_contents(get_plugcache_dir('football_stats').$cache_champ));
    if(!$champ)
    {
        $query = 'SELECT DISTINCT c.start, c.finish, g.id_championat, c.name as name_championat  
	                         FROM '.prefix."_football_games g
	                         LEFT JOIN ".prefix."_football_championats c
	                           ON g.id_championat = c.id
	                         WHERE (g.id_team_owner = {$team['id']} OR g.id_team_guest = {$team['id']})
                             group by c.finish
	                         ORDER BY c.finish DESC";
        //echo $query."<br/>";
       $champ = $mysql->select($query, 1);
	 
    }
    //print "<pre>";
    //print_r($champ);
    //print "</pre>";
    foreach($champ as $ch)
    {
        //echo $ch['finish']." ".$year."<br>"; 
        if (strlen($year)==0)
        {
            $year = $ch['finish'];
        }
        
		$tvars['vars']['other-championats'] .= str_replace(
		                                        array('{name}', '{start}', '{finish}', '{link}', '{selected}'),
		                                        array($ch['name'], $ch['start'], $ch['finish'], 
		                                        generatePluginLink('football_stats', 'team', array('name' => $country_alias), array('chid' => $ch['finish']), true, true), 
		                                        $year == $ch['finish'] ? 'selected="selected"' : ''), $lang['fs:other-championats']);
    }

    //print $year."<br>";

    if($year > date("Y")){
        $ifIsGames = $mysql->select("SELECT g.id
                                FROM ".prefix."_football_games g
                                JOIN ".prefix."_football_championats c ON g.id_championat=c.id
                                LEFT JOIN ".prefix."_football_teams t1 ON g.id_team_owner = t1.id
                                LEFT JOIN ".prefix."_football_teams t2 ON g.id_team_guest = t2.id
                                WHERE (g.id_team_owner=".$team['id']." OR g.id_team_guest=".$team['id'].") AND c.finish=".$year);

        if(count($ifIsGames) == 0){
            $year--;
        }
    }

    //print $year."<br>";

	# games in leauges (2 years) start
	$cy = $current_year - ($team['type'] == 0 ? 2 : 10);
     $query = "SELECT g.*, c.name as name_championat, c.type, c.start, c.finish, t1.alias as alias_owner, t2.alias as alias_guest, t1.name as team_owner, t2.name as team_guest
	                         FROM ".prefix."_football_games g
	                         LEFT JOIN ".prefix."_football_championats c
	                           ON g.id_championat = c.id
	                         LEFT JOIN ".prefix."_football_teams t1
                                   ON g.id_team_owner = t1.id
	                         LEFT JOIN ".prefix."_football_teams t2
                                   ON g.id_team_guest = t2.id  
	                         WHERE (g.id_team_owner = '{$team['id']}' OR g.id_team_guest = '{$team['id']}') AND(c.finish='{$year}')
	                         ORDER BY  c.type, g.date DESC, c.id, c.finish DESC";
    //echo $query."<br/>"; //AND (g.round = -1)
	$games = $mysql->select($query, 1);
	
	$i = 0;
    "INSERT INTO #__fb_whoisonline (`userid` , `time`, `what`, `task`, `do`, `func`,`link`, `userip`, `user`) "
            . "\n VALUES ('{$my->id}', '{$now}', '{$what}','{$task}','{$do}','{$func}','{$link}', '{$myip}', '{$user}')";
    
       


/*====*/
	foreach($games as $game){
		
		++$i;
	

		$pvars = gameFillVariables($game, "");
	
		$pvars['vars']['round'] = '';
		
                               $channel_logo = include(extras_dir.'/football_stats/inc/channel.php');
				
				foreach($channel_logo as $key => $value){
					if($key == $game['channel']) {
					       $tlogo['vars']['logo'] = $value;
					       $tpl->template('plugin.logo_img', $tpath['plugin.logo_img']);
	                       $tpl->vars('plugin.logo_img', $tlogo);
	                        
						$pvars['vars']['channel'] = $tpl->show('plugin.logo_img');
						break;
					}	
				}
                
     if(strlen($pvars['vars']['channel'])<2)  
     {
       $pvars['vars']['channel']='';
     } 
    
        		
		$tpl->template('plugin.team.games.row', $tpath['plugin.team.games.row']);
		$tpl->vars('plugin.team.games.row', $pvars);
		$xvars['vars']['all-games']  .= $tpl->show('plugin.team.games.row'); 
		
        			/*=================*/
     
            
            
 		/*=================*/
        	
		if($game['id_championat'] != $games[$i]['id_championat'] || $game['type'] != $games[$i]['type']){//|| $game['channel']!= $xvars['vars']['channel']){
			$xvars['vars']['name'] = $game['name_championat'];
			$tpl->template('plugin.team.games.round', $tpath['plugin.team.games.round']);
			$tpl->vars('plugin.team.games.round', $xvars);
			$tvars['vars']['games'] .= $tpl->show('plugin.team.games.round'); 
			$xvars['vars']['all-games'] = '';
		}
	}
	# games in leauges (2 years) end
	/*
	if(getPluginStatusActive('tags')){
		include_once extras_dir.'/tags/tags.php';
		$tvars['vars']['tags'] = plugin_tags_tag(array('tag' => $team['alias']));
	}	
	*/
	
	$tpl->template('plugin.team', $tpath['plugin.team']);
	$tpl->vars('plugin.team', $tvars);
	$template['vars']['mainblock'] = $tpl->show('plugin.team');

     //--------------------------------------------------

    // Determine TAG that will be used for output

    $correctTag = headersTags(trim($params['name']), $team['name']);
    
	$template['vars']['mainblock'] .= showTagsTeam($correctTag, $team['name']);
	
	cacheStoreFile($cache, "{$team['name']}<-->".$template['vars']['mainblock'], 'football_stats');
}

# /player/PLAYER_NAME
function showPlayer($params){
	global $mysql, $config, $tpl, $template, $SYSTEM_FLAGS, $lang;
	$tpath = locatePluginTemplates(array('plugin.player'), 'football_stats', 1, '', 'main');
	
	$params['id'] = intval($params['id']);
	
	$player = $mysql->record('SELECT p.*, t.alias, t.name as team_name 
	                          FROM '.prefix.'_football_players p 
	                          LEFT JOIN '.prefix."_football_teams t 
	                            ON p.id_team = t.id
	                          WHERE p.id = {$params['id']}
	                          LIMIT 1");
	
	if(!$player){
		header('Location: '.$config['home_url']);
		exit();
	}
	
	$SYSTEM_FLAGS['info']['title']['group'] = str_replace('{player}', $player['name'], $lang['fs:title-player']);
    $SYSTEM_FLAGS['meta']['description'] = $lang['fs:All_news_and_statistic_about']." ".str_replace('{player}', $player['name'], $lang['fs:title-player']);
    $SYSTEM_FLAGS['meta']['keywords'] = str_replace('{player}', $player['name'], $lang['fs:title-player']).", ".
    $lang['fs:club']." ".str_replace('{player}', $player['name'], $lang['fs:title-player']).", ".
    $lang['fs:information']." ".str_replace('{player}', $player['name'], $lang['fs:title-player']).", ".
    $lang['fs:news']." ".str_replace('{player}', $player['name'], $lang['fs:title-player']);

	//$template['vars']['mainblock'] = $info[1];

	$tvars['vars']['name'] = $player['name'];
    if(trim($player['bio']) != "" && $player['bio'] != NULL){
	    $tvars['vars']['bio']  = "<br><p style='padding-left: 10px;'>".$player['bio']."</p><br>";
    }
    else{
        $tvars['vars']['bio']  = "";
    }
	//$tvars['vars']['team'] = $player['team_name'];

    switch($player['status']){
        case 1:
            $tvars['vars']['status'] = "Грає в команді <a href='".
            generatePluginLink('football_stats', 'team', array('name' => $player['alias']), array(), true, true)
            ."' class='link_on_players_page' style='display: inline-block; font-size: 15px;'>".$player['team_name']."</a>.";
            break;
        case 2:
            $tvars['vars']['status'] = "Закінчив кар'єру.";
            break;
        case 3:
            $tvars['vars']['status'] = "Вільний агент.";
            break;
        case 4:
            $tvars['vars']['status'] = "Помер (загинув).";
            break;
        default:
            $tvars['vars']['status'] = "";
            break;
    }

    $tvars['vars']['admin_link_player'] = "<a href='{admin_url}/admin.php?mod=football_stats&action=edit-player&id=".$params['id']."'><small>Редагувати</small></a>";

    $query_team = "SELECT t.name as team_name, t.id, t.alias, g.id_team
    FROM ".prefix."_football_games_goals g
    JOIN ".prefix."_football_teams t ON g.id_team=t.id
    WHERE g.id_player=".$params['id']." AND g.auto<>1 GROUP BY g.id_team";
    $get_teams = $mysql->select($query_team);
    $tvars['vars']['history_goals'] = "Історія:";
    foreach($get_teams as $team){
        $query_goals = "SELECT id FROM ".prefix."_football_games_goals WHERE id_player=".$params['id']." AND id_team=".$team['id_team']." AND auto<>1";
        $count_goals = mysql_num_rows(mysql_query($query_goals));
        $tvars['vars']['history_goals'] .= "<a href='".
        generatePluginLink('football_stats', 'team', array('name' => $team['alias']), array(), true, true)
        ."' class='link_on_players_page'  style='font-size: 15px; display: inline-block;'>".$team['team_name']."(".$count_goals.")</a>, ";
        //$get_goals = $mysql->select($query_goals);
    }
    $tvars['vars']['history_goals'] = mb_substr($tvars['vars']['history_goals'], 0, -2).".";
	
    if(mysql_num_rows(mysql_query($query_team)) == 0){
        $tvars['vars']['history_goals'] = "";
    }

	$tpl->template('plugin.player', $tpath['plugin.player']);
	$tpl->vars('plugin.player', $tvars);
	$template['vars']['mainblock'] = $tpl->show('plugin.player');
    $template['vars']['mainblock'] .= showTags($player['name']);
}

# /game/ID_GAME
function showGame($params){
	global $mysql, $config, $tpl, $template, $SYSTEM_FLAGS, $lang;
	
	$params['id'] = intval($params['id']);

    $auto_in_out = getSomeDataFootballStats("auto_in_out", prefix."_football_games", " WHERE id=".$params['id']);

    if($auto_in_out){
        $status_game = $mysql->select("SELECT status, date FROM ".prefix."_football_games WHERE id=".$params['id']);
        foreach($status_game as $status){
            $status_now = $status['status'];
            $date_of_game = $status['date'];
        }

        if($status_now == 0 && date("Y-m-d H:i:s") > $date_of_game){
            if(!mysql_query("UPDATE ".prefix."_football_games SET status=1 WHERE id=".$params['id'])){
                print mysql_error();
            }
        }
        /*else if($status_now == 1 && date("Y-m-d H:i:s") > date("Y-m-d H:i:s", strtotime("+115 minute",strtotime($date_of_game)))){
            if(!mysql_query("UPDATE ".prefix."_football_games SET status=2 WHERE id=".$params['id'])){
                print mysql_error();
            }
        }*/
    }

	
	$game = $mysql->record('SELECT g.*, t1.name as team_guest, t1.photo as photo_guest, t1.alias as alias_guest, t2.name as team_owner, t2.photo as photo_owner, t2.alias as alias_owner, c.name as name_championat, c.start, c.finish, c.alias as champo_alias, c.id as id_champo
	                        FROM '.prefix.'_football_games g 
	                        LEFT JOIN '.prefix.'_football_teams t1 
	                          ON g.id_team_guest = t1.id 
	                        LEFT JOIN '.prefix."_football_teams t2 
	                          ON g.id_team_owner = t2.id
	                        LEFT JOIN ".prefix."_football_championats c
	                          ON g.id_championat = c.id
	                        WHERE g.id = {$params['id']}
	                        LIMIT 1", 1);
	
	if(!$game){
		header('Location: '.$config['home_url']);
		exit();
	}
	
	$SYSTEM_FLAGS['info']['title']['group'] = str_replace(array('{owner}', '{guest}'), array($game['team_owner'], $game['team_guest']), $lang['fs:title-game']);
    $SYSTEM_FLAGS['meta']['description'] = $lang['fs:show_online_match']." ".str_replace(array('{owner}', '{guest}'), array($game['team_owner'], $game['team_guest']), $lang['fs:title-game']);    
    $SYSTEM_FLAGS['meta']['keywords'] = $lang['fs:show_online']." ".
    str_replace(array('{owner}', '{guest}'), array($game['team_owner'], $game['team_guest']), $lang['fs:title-game']).", ".
    $lang['fs:rus_to_see']." ".str_replace(array('{owner}', '{guest}'), array($game['team_owner'], $game['team_guest']), $lang['fs:title-game']).", ".
    $lang['fs:to_see']." ".str_replace(array('{owner}', '{guest}'), array($game['team_owner'], $game['team_guest']), $lang['fs:title-game']).", ".
    $lang['fs:show_text']." ".str_replace(array('{owner}', '{guest}'), array($game['team_owner'], $game['team_guest']), $lang['fs:title-game']);
	
	$tpath = locatePluginTemplates(array('plugin.game', 'plugin.logo_img', 'plugin.games.selected', 'plugin.translation.online'), 'football_stats', 1, '', 'main');
	
	$tvars = gameFillVariables($game, "match");
	$views = $game['views']+1;
    $id_game = $game['id'];
    $tvars['vars']['views']               = $views;
    $tvars['vars']['round']               = $game['round'];
    if($game['a_trans'] != NULL && trim($game['a_trans']) != ""){
    $online_translation = "
                <div class='blockbuts'>
                <div class='bsl'>
                </div>
                <div class='bsr'>
                </div>
                <div class='bsc'><a href='".$game['a_trans']."' style='text-decoration: underline;'>Онлайн</a></div>
                <div class='clr'>
                </div>
            </div>";
    }
    else{
        $online_translation = "";
    }

    switch($game['status']){
            case 0:
                $tvars['vars']['status'] = "<img src='/engine/plugins/football_stats/tpl/images/gray-status.gif' align='absmiddle' title='".$lang['fs:match_not_started']."' />";
                $tvars['vars']['a_trans'] = $online_translation;
                break;
            case 1:
                $tvars['vars']['status'] = "<img src='/engine/plugins/football_stats/tpl/images/green-status.gif' align='absmiddle' title='".$lang['fs:plaing_match']."' />";
                $tvars['vars']['a_trans'] = $online_translation;
                break;
            case 2:
                $tvars['vars']['status'] = "<img src='/engine/plugins/football_stats/tpl/images/red-status.gif' align='absmiddle' title='".$lang['fs:match_is_over']."' />";
                $tvars['vars']['a_trans'] = "";
                break;
            case 3:
                $tvars['vars']['status'] = "<img src='/engine/plugins/football_stats/tpl/images/black-status.gif' align='absmiddle' title='".$lang['fs:match_stopped']."' />";
                $tvars['vars']['a_trans'] = "";
                break;
            case 4:
                $tvars['vars']['status'] = "<img src='/engine/plugins/football_stats/tpl/images/black-status.gif' align='absmiddle' title='".$lang['fs:match_did_not_take_place']."' />";
                $tvars['vars']['a_trans'] = "";
                break;
            default:
                $tvars['vars']['status'] = "Не определен";
        }
	//$tvars['vars']['channel']             = $game['channel'];

    $query_countries = "SELECT * FROM `".prefix."_football_countries` WHERE name LIKE '%".$game['name_championat']."%'";
    if(mysql_num_rows(mysql_query($query_countries)) > 0){
        $get_contries = $mysql->select($query_countries);
        foreach($get_contries as $country){
            $tvars['vars']['name-championat'] = "<a href='".
            generatePluginLink('football_stats', 'football', array('name' => $country['alias']), array('chid' => $game['id_champo']), true, true)
            ."' target='_blank' class='link_championat_on_match'>".$game['name_championat']."</a>";
            $tvars['vars']['name-championat-title'] = "<a href='".
            generatePluginLink('football_stats', 'football', array('name' => $country['alias']), array('chid' => $game['id_champo']), true, true)
            ."' target='_blank' class='link_championat_on_match_title'>".$game['name_championat']."</a>";
            break;
        }
    }
    else{
        $get_championats = $mysql->select("SELECT start, finish, alias FROM `".prefix."_football_championats` WHERE id=".$game['id_champo']);
        foreach($get_championats as $champo){
            $tvars['vars']['name-championat'] = "<a href='".
            generatePluginLink('football_stats', 'league', array('type' => $champo['alias'], 'year_start' => $champo['start'], 'year_end' => $champo['finish']), array(), true, true)
            ."' target='_blank' class='link_championat_on_match'>".$game['name_championat']."</a>";
            $tvars['vars']['name-championat-title'] = "<a href='".
            generatePluginLink('football_stats', 'league', array('type' => $champo['alias'], 'year_start' => $champo['start'], 'year_end' => $champo['finish']), array(), true, true)
            ."' target='_blank' class='link_championat_on_match_title'>".$game['name_championat']."</a>";
        }
    }




	//$tvars['vars']['name-championat']     = $game['name_championat'];
	$tvars['vars']['start-championat']    = $game['start'];
	$tvars['vars']['finish-championat']   = $game['finish'];

    $tvars['vars']['photo-guest'] = ($game['photo_guest'] != NULL && trim($game['photo_guest']) != "") ? pluginGetVariable('football_stats', 'photos.url').$game['photo_guest'] : pluginGetVariable('football_stats', 'photos.url')."default.png";
    $tvars['vars']['photo-owner'] = ($game['photo_owner'] != NULL && trim($game['photo_owner']) != "") ? pluginGetVariable('football_stats', 'photos.url').$game['photo_owner'] : pluginGetVariable('football_stats', 'photos.url')."default.png";

    $cacheFileName = md5('game_translation_'.$params['id']).'.txt';

    $cacheData = cacheRetrieveFile($cacheFileName, 60, 'football_stats');
    if ($cacheData != false) {
        $tvars['vars']['translation'] = $cacheData;
    }
    else{
        $query_translation = "SELECT * FROM ".prefix."_football_translation WHERE id_game=".$params['id']." ORDER BY minute DESC, minute_add DESC, id DESC";
        if(mysql_num_rows(mysql_query($query_translation)) > 0){
            $get_translation = $mysql->select($query_translation);
            foreach($get_translation as $translation){
                if($translation['minute'] == 0){
                    $minute = "";
                }
                else{
                    if($translation['minute_add'] != 0 && $translation['minute_add'] != NULL){
                        $minute = $translation['minute']."+".$translation['minute_add']." хв.";
                    }
                    else{
                        $minute = $translation['minute']." хв.";
                    }
                }

                $translation_online .= "<tr><td style='width: 20px;'>".$translation['mini_photo']."</td><td style='width: 70px;'><b>".$minute.
                "</b></td><td>".$translation['text_translation']."</td></tr>";
                if($translation['photo'] != "" && $translation['photo'] != NULL){
                    $translation_online .= "<tr><td></td><td></td><td>".$translation['photo']."</td></tr>";
                }
                if($translation['video'] != "" && $translation['video'] != NULL){
                    $translation_online .= "<tr><td></td><td></td><td>".$translation['video']."</td></tr>";
                }
                $translation_online .= "<tr><td colspan='3' style='padding: 0; border-bottom: 1px dotted #A8A8A8;'></td></tr>";
            }

            $trans['vars']['translation'] = $translation_online;
            $trans['vars']['team-owner'] = $game['team_owner'];
            $trans['vars']['team-guest'] = $game['team_guest'];
            $trans['vars']['translation_online'] = $lang['fs:translation_online'];

            $tpl->template('plugin.translation.online', $tpath['plugin.translation.online']);
	        $tpl->vars('plugin.translation.online', $trans);
	        $tvars['vars']['translation'] = $tpl->show('plugin.translation.online');

        }
        else{
            $tvars['vars']['translation'] = "";
        }

        # create cache
	    cacheStoreFile($cacheFileName, $tvars['vars']['translation'], 'football_stats');
    }

     
    $channel_logo = include(extras_dir.'/football_stats/inc/channel.php');
				
				foreach($channel_logo as $key => $value){
					if($key == $game['channel']) {
					       $tlogo['vars']['logo'] = $value;
					       $tpl->template('plugin.logo_img', $tpath['plugin.logo_img']);
	                       $tpl->vars('plugin.logo_img', $tlogo);
	                        
						$tvars['vars']['channel'] = $tpl->show('plugin.logo_img');
						break;
					}	
				}
                
     if(strlen($tvars['vars']['channel'])<2)  
     {
       $tvars['vars']['channel']=''; 
     }         

	$tvars['regx']['/\[if-championat\](.*?)\[\/if-championat\]/si'] = $game['round'] == -1 ? '' : '$1';

    $mysql->query('UPDATE '.prefix."_football_games SET `views` = {$views} WHERE `id` = {$id_game}");

    $tvars['vars']['players-goals-guest'] = $tvars['vars']['players-goals-owner'] = '';

    $query_goals_owner = "SELECT * FROM ".prefix."_football_games_goals WHERE id_game=".$id_game." AND id_team=".$game['id_team_owner'];
    $query_cards_owner = "SELECT * FROM ".prefix."_football_cards WHERE id_game=".$id_game." AND team=".$game['id_team_owner'];
    $query_goals_guest = "SELECT * FROM ".prefix."_football_games_goals WHERE id_game=".$id_game." AND id_team=".$game['id_team_guest'];
    $query_cards_guest = "SELECT * FROM ".prefix."_football_cards WHERE id_game=".$id_game." AND team=".$game['id_team_guest'];

    $tvars['vars']['players-goals-owner'] = goalsAndCards($query_goals_owner, $query_cards_owner, 'owner');
    $tvars['vars']['players-goals-guest'] = goalsAndCards($query_goals_guest, $query_cards_guest, 'guest');

    $tvars['vars']['players-penalti-owner'] = showPenaltiPlayers($id_game, $game['id_team_owner'], "owner");
    $tvars['vars']['players-penalti-guest'] = showPenaltiPlayers($id_game, $game['id_team_guest'], "guest");

    $query_players_owner = "SELECT p.*, gp.* FROM ".prefix."_games_players gp JOIN ".prefix."_football_players p
    ON p.id = gp.id_player AND gp.id_game = ".$params['id']." WHERE p.id_team = ".$game['id_team_owner']." OR p.id_team_country = ".$game['id_team_owner'];
    $players_owner = $mysql->select($query_players_owner);
                                 
    $get_coach = mysql_query("SELECT coach_name FROM ".prefix."_coaches WHERE id_team=".$game['id_team_owner']." AND id_game=".$params['id']);
    if(mysql_num_rows($get_coach) > 0){
        $res_coach = mysql_fetch_array($get_coach);
        do{
            $coach_owner = $res_coach['coach_name'];
            break;
        }
        while($res_coach = mysql_fetch_array());
        $tvars['vars']['coach-owner'] = "<br/><b>Тренер: </b><br/>".$coach_owner."<br/><br/>";
    }
    else{
        $tvars['vars']['coach-owner'] = "";
    }
    
    $tvars['vars']['players-owner'] = "";
    //$tvars['vars']['players-owner-zap'] = "";
    $tvars['vars']['players-guest'] = "";
    //$tvars['vars']['players-guest-zap'] = "";
    if(mysql_num_rows(mysql_query($query_players_owner)) == 11){
        $tvars['vars']['players-owner'] = "<b>Стартовий склад:</b><br/>";
        foreach($players_owner as $row){	
            switch($row['in_game'])
            {
                case 1: $tvars['vars']['players-owner'] .= "<a href='".
                generatePluginLink('football_stats', 'player', array('id' => $row['id_player'], array(), true, true))
                ."'>".$row['name']."</a><br/> ";  break;
                //case 2: $tvars['vars']['players-owner-zap'] .= $row['name'].", "; break;
            }
        }
        $tvars['vars']['players-owner'] = mb_substr($tvars['vars']['players-owner'], 0, -2);
        $tvars['vars']['players-owner'] .= ".<br/>";
    }
    else{
        $tvars['vars']['players-owner'] = "";
    }


    $cacheFileRepOwn = md5('game_replacements_owner_'.$params['id']).'.txt';

    $cacheDataRepOwn = cacheRetrieveFile($cacheFileRepOwn, 120, 'football_stats');
    if ($cacheDataRepOwn != false) {
        $tvars['vars']['replasements-owner'] = $cacheDataRepOwn;
    }
    else{
        //----------------------
        $query_owner_out = "SELECT p.*, r.* FROM ".prefix."_replacements r JOIN ".prefix."_football_players p
        ON p.id=r.player_out AND r.id_game=".$params['id']." WHERE p.id_team=".$game['id_team_owner']." OR p.id_team_country=".$game['id_team_owner']." ORDER BY minute";
        $replacements_owner_out = $mysql->select($query_owner_out);
        //----------------------
        $query_owner_in = "SELECT p.*, r.* FROM ".prefix."_replacements r JOIN ".prefix."_football_players p
        ON p.id=r.player_in AND r.id_game=".$params['id']." WHERE p.id_team=".$game['id_team_owner']." OR p.id_team_country=".$game['id_team_owner']." ORDER BY minute";
        $replacements_owner_in = $mysql->select($query_owner_in);

         if(mysql_num_rows(mysql_query($query_owner_out)) == 0 && mysql_num_rows(mysql_query($query_owner_in)) == 0){
             $tvars['vars']['replasements-owner'] = "";
         }
         else{
             $tvars['vars']['replasements-owner'] = show_replacements($replacements_owner_in, $replacements_owner_out);
         }
         # create cache
	     cacheStoreFile($cacheFileRepOwn, $tvars['vars']['replasements-owner'], 'football_stats');
    }

     if($tvars['vars']['coach-owner'] != "" || $tvars['vars']['players-owner'] != "" || $tvars['vars']['replasements-owner'] != ""){
         $tvars['vars']['team-owner-header'] = "<b>Команда ".$game['team_owner']."</b><br/><br/>";
     }
     else{
         $tvars['vars']['team-owner-header'] = "";
     }


    $query_players_guest = "SELECT p.*, gp.* FROM ".prefix."_games_players gp JOIN ".prefix."_football_players p
    ON p.id = gp.id_player AND gp.id_game = ".$params['id']." WHERE p.id_team = ".$game['id_team_guest']." OR p.id_team_country = ".$game['id_team_guest'];
     $players_guest = $mysql->select($query_players_guest);
        $get_coach = mysql_query("SELECT coach_name FROM ".prefix."_coaches WHERE id_team=".$game['id_team_guest']." AND id_game=".$params['id']);
        if(mysql_num_rows($get_coach) > 0){
            $res_coach = mysql_fetch_array($get_coach);
            do{
                $coach_guest = $res_coach['coach_name'];
                break;
            }
            while($res_coach = mysql_fetch_array());
            $tvars['vars']['coach-guest'] = "<br/><b>Тренер: </b><br/>".$coach_guest."<br/><br/>";
        }
        else{
            $tvars['vars']['coach-guest'] = "";
        }
     if(mysql_num_rows(mysql_query($query_players_guest)) == 11){
         $tvars['vars']['players-guest'] = "<b>Стартовий склад:</b><br/>";
         foreach($players_guest as $row){
            switch($row['in_game'])
            {
            case 1: $tvars['vars']['players-guest'] .= "<a href='".
            generatePluginLink('football_stats', 'player', array('id' => $row['id_player'], array(), true, true))
            ."'>".$row['name']."</a><br/>";  break;
            //case 2: $tvars['vars']['players-guest-zap'] .= $row['name'].", "; break;
            }
        }
        $tvars['vars']['players-guest'] = mb_substr($tvars['vars']['players-guest'], 0, -2);
        $tvars['vars']['players-guest'] .= ".<br/>";
     }
     else{
         $tvars['vars']['players-guest'] = "";
     }

    $get_id_championats = mysql_query("SELECT id, id_country FROM ".prefix."_football_championats WHERE name='".
    $game['name_championat']."' AND start=".$game['start']." AND finish=".$game['finish']);
    print mysql_error();
    $res_id_championats = mysql_fetch_array($get_id_championats);
    do{
        $cid = $res_id_championats['id'];
        $id_country = $res_id_championats['id_country'];
    }
    while($res_id_championats = mysql_fetch_array($get_id_championats));

    if($id_country == 0){
        $tvars['vars']['admin_link_match'] = "<a href='{admin_url}/admin.php?mod=football_stats&action=edit-league-game&id=".$params['id']."&cid=".$cid."' target='_blank'>".$lang['fs:match_in_admin']."</a>";
    }
    else{
        $tvars['vars']['admin_link_match'] = "<a href='{admin_url}/admin.php?mod=football_stats&action=edit-game&id=".$params['id']."&cid=".$cid."' target='_blank'>".$lang['fs:match_in_admin']."</a>";
    }

    $cacheFileRepGuest = md5('game_replacements_guest_'.$params['id']).'.txt';

    $cacheDataRepGuest = cacheRetrieveFile($cacheFileRepGuest, 120, 'football_stats');
    if ($cacheDataRepGuest != false) {
        $tvars['vars']['replasements-guest'] = $cacheDataRepGuest;
    }
    else{
        //----------------------
        $query_guest_out = "SELECT p.*, r.* FROM ".prefix."_replacements r JOIN ".prefix."_football_players p
        ON p.id=r.player_out AND r.id_game=".$params['id']." WHERE p.id_team=".$game['id_team_guest']." OR p.id_team_country=".$game['id_team_guest']." ORDER BY minute";
        $replacements_guest_out = $mysql->select($query_guest_out);
        //----------------------
        $query_guest_in = "SELECT p.*, r.* FROM ".prefix."_replacements r JOIN ".prefix."_football_players p
        ON p.id=r.player_in AND r.id_game=".$params['id']." WHERE p.id_team=".$game['id_team_guest']." OR p.id_team_country=".$game['id_team_guest']." ORDER BY minute";
        $replacements_guest_in = $mysql->select($query_guest_in);

        if(mysql_num_rows(mysql_query($query_guest_out)) == 0 && mysql_num_rows(mysql_query($query_guest_in)) == 0){
             $tvars['vars']['replasements-guest'] = "";
         }
         else{
             $tvars['vars']['replasements-guest'] = show_replacements($replacements_guest_in, $replacements_guest_out);
         }
         # create cache
	     cacheStoreFile($cacheFileRepGuest, $tvars['vars']['replasements-guest'], 'football_stats');
    }
     if($tvars['vars']['coach-guest'] != "" || $tvars['vars']['players-guest'] != "" || $tvars['vars']['replasements-guest'] != ""){
         $tvars['vars']['team-guest-header'] = "<b>Команда ".$game['team_guest']."</b><br/><br/>";
     }
     else{
         $tvars['vars']['team-guest-header'] = "";
     }

     if(showPenalti($params['id'], $game['id_team_owner']) == 0 && showPenalti($params['id'], $game['id_team_guest']) == 0){
         $tvars['vars']['penalti'] = "";
         $tvars['vars']['line_height'] = "";
     }
     else{
         $tvars['vars']['penalti'] .= "<span style='font-size: 15px;'>(".showPenalti($params['id'], $game['id_team_owner'])." : "
         .showPenalti($params['id'], $game['id_team_guest']).")</span><br/>";
         $tvars['vars']['line_height'] = "style='line-height: 26px;'";
     }
            
	$tpl->template('plugin.game', $tpath['plugin.game']);
	$tpl->vars('plugin.game', $tvars);
	$template['vars']['mainblock'] = $tpl->show('plugin.game');
}

function showPenalti($id_game, $id_team){
    global $mysql;
    $get_penalties = mysql_num_rows(mysql_query("SELECT id FROM ".prefix."_football_penalti_players WHERE id_game=".$id_game." AND id_team=".$id_team));
    
    if($get_penalties > 0){
        return $get_penalties;
    }
    else{
        $get_penalties_mini = $mysql->result("SELECT count FROM ".prefix."_football_penalti_mini WHERE id_game=".$id_game." AND id_team=".$id_team);
        if($get_penalties_mini != "" && $get_penalties_mini != NULL){
            return $get_penalties_mini;
        }
        else{
            return 0;
        }
    }
}

function goalsAndCards($query_goals, $query_cards, $who){
    global $mysql, $lang;
    $cards_and_goals = array();
    if(mysql_num_rows(mysql_query($query_goals)) > 0){
        $goals = $mysql->select($query_goals);
        foreach($goals as $goal){
            $player_name_goal = getSomeDataFootballStats("name", prefix."_football_players", " WHERE id=".$goal['id_player']);
            if($goal['minute'] != 0 && $player_name_goal != ""){
			    $ball = ($goal['auto'] == 1) ? $lang['fs:auto'] : "<img src='http://sportanalytic.com/engine/plugins/football_stats/tpl/images/miach.png' height='15'>";
                $player_name_a = ($goal['id_player'] != -1) ? "<a href='".
		        generatePluginLink('football_stats', 'player', array('id' => $goal['id_player'], array(), true, true))
		        ."' style='vertical-align: bottom;'>".getSomeDataFootballStats("name", prefix."_football_players", " WHERE id=".$goal['id_player'])
		        ."</a>, " : "";
			    if($who == 'owner'){
					$add_min_own = (intval($goal['minute_add']) != 0) ? "+".$goal['minute_add'] : "";
				    $cards_and_goals[$goal['minute'] + $goal['minute_add']] = $player_name_a.$goal['minute'].$add_min_own." авт. ".$ball."<br/>";
			    }
			    else{
					$add_min_gue = (intval($goal['minute_add']) != 0) ? "+".$goal['minute_add'] : "";
				    $cards_and_goals[$goal['minute'] + $goal['minute_add']] = $ball." ".$player_name_a.$goal['minute'].$add_min_gue." авт.<br/>";
			    }
            }
        }
    }
    if(mysql_num_rows(mysql_query($query_cards)) > 0){
        $cards = $mysql->select($query_cards);
        foreach($cards as $card){
			$what_card = ($card['type'] == 1) ? "<img src='http://sportanalytic.com/engine/skins/default/tags/online/mico4.gif' height='15' />" : "<img src='http://sportanalytic.com/engine/skins/default/tags/online/mico5.gif' height='15' />";
			if($who == 'owner'){
				$cards_and_goals[$card['minute']] = "<a href='".
				generatePluginLink('football_stats', 'player', array('id' => $card['id_player'], array(), true, true))
				."' style='vertical-align: bottom;'>".getSomeDataFootballStats("name", prefix."_football_players", " WHERE id=".$card['id_player'])
				."</a>, ".$card['minute']." хв. ".$what_card."<br/>";
			}
			else{
				$cards_and_goals[$card['minute']] = $what_card." <a href='".
                    generatePluginLink('football_stats', 'player', array('id' => $card['id_player'], array(), true, true))
                    ."' style='vertical-align: bottom;'>".getSomeDataFootballStats("name", prefix."_football_players", " WHERE id=".$card['id_player'])
                    ."</a>, ".$card['minute']." хв. <br/>";
			}
        }
    }
    if(!empty($cards_and_goals)){
        ksort($cards_and_goals);
        foreach($cards_and_goals as $key => $value){
			$result .= $value;
        }
        return $result;
    }
    else{
        return "";
    }
}

function showPenaltiPlayers($id_game, $id_team, $who){
    global $mysql;
    $players = "";
    $get_player = $mysql->select("SELECT pp.*, fp.name as player_name
                                FROM ".prefix."_football_penalti_players pp
                                LEFT JOIN ".prefix."_football_players fp ON pp.id_player = fp.id
                                WHERE pp.id_game=".$id_game." AND pp.id_team=".$id_team." ORDER BY id");
    
    $ball = "<img src='http://sportanalytic.com/engine/plugins/football_stats/tpl/images/miach.png' height='15'>";
    foreach($get_player as $player){
        if($who == 'owner'){
            $players .= "<a href='".
			generatePluginLink('football_stats', 'player', array('id' => $player['id_player']), array(), true, true)
			."' style='vertical-align: bottom;'>".$player['player_name']."</a> (пен.) ".$ball."<br/>";
        }
        else{
            $players .= $ball." <a href='".
			generatePluginLink('football_stats', 'player', array('id' => $player['id_player']), array(), true, true)
			."' style='vertical-align: bottom;'>".$player['player_name']."</a> (пен.)<br/>";
        }
    }
    return $players;
}

function show_replacements($replacements_in, $replacements_out){
    $array_in = array();
     $array_out = array();
     $result = "";
    foreach($replacements_in as $in){
        $array_in[] = "<a href='".
        generatePluginLink('football_stats', 'player', array('id' => $in['player_in'], array(), true, true))
        ."'>".$in['name']."</a><br/>";
    }
    foreach($replacements_out as $out){
        $array_out[] = "<img src='http://sportanalytic.com/engine/skins/default/tags/online/mico3.gif' height='20'> ".$out['minute']." хв. &nbsp;<a href='".
        generatePluginLink('football_stats', 'player', array('id' => $out['player_out'], array(), true, true))
        ."'>".$out['name']."</a> &rarr; ";
    }
    for($i=0; $i<count($array_in); $i++){
        $result .= $array_out[$i]." ".$array_in[$i];
    }

    if($result != ""){
        $result = "<b>Заміни:</b><br/>".$result;
    }

    return $result;
}

function saveLeagueGames($id_championat){
	global $mysql;
	
	$cache_games = "league_games_{$id_championat}";
	
	if(!is_file(get_plugcache_dir('football_stats').$cache_games)){
		$games = $mysql->select('SELECT g.*, t1.name as team_guest, t1.alias as alias_guest, t2.name as team_owner, t2.alias as alias_owner 
		                         FROM '.prefix.'_football_games g 
		                         LEFT JOIN '.prefix.'_football_teams t1 
		                           ON g.id_team_guest = t1.id 
		                         LEFT JOIN '.prefix."_football_teams t2 
		                           ON g.id_team_owner = t2.id 
		                         WHERE id_championat = {$id_championat}
		                         ORDER BY g.round DESC, g.date ASC", 1);
		cacheStoreFile($cache_games, serialize($games), 'football_stats');
	}
	else
		$games = unserialize(file_get_contents(get_plugcache_dir('football_stats').$cache_games));		
		
	return $games;
}

function saveLeagueTable($id_championat, &$games){
	global $mysql, $tpl, $template;
	
	$cache_teams = "league_tables_{$id_championat}";
	
	if(is_file(get_plugcache_dir('football_stats').$cache_teams))
		return unserialize(file_get_contents(get_plugcache_dir('football_stats').$cache_teams));
		
	$teams_r = $mysql->select('SELECT tc.*, t.name, t.alias
	                           FROM '.prefix."_football_teams_championats tc 
	                           LEFT JOIN ".prefix."_football_teams t 
	                             ON tc.id_team = t.id 
	                           WHERE id_championat = {$id_championat}", 1);
	$teams = array();
	
	foreach($teams_r as $team)
		$teams[$team['id_team']] = array(
			'id_team' => $team['id_team'], 
			'name' => $team['name'], 
			'alias' => $team['alias'],
			'games' => 0, 
			'wins' => 0,
			'wins-out' => 0,
			'defeats' => 0, 
			'draws' => 0, 
			'scores' => - $team['fine'], 
			'in' => 0, 
			'out' => 0
			);	
	
	foreach($games as $game){
	
		if($game['status'] == 4){
			if($game['goals_guest'] > $game['goals_owner']){
				$teams[$game['id_team_guest']]['wins']++;
				$teams[$game['id_team_guest']]['wins-out']++;
				$teams[$game['id_team_owner']]['defeats']++;
				$teams[$game['id_team_guest']]['scores'] += 3;
				$teams[$game['id_team_guest']]["team_{$game['id_team_owner']}"]++;
			}
			elseif ($game['goals_owner'] > $game['goals_guest']){
				$teams[$game['id_team_owner']]['wins']++;
				$teams[$game['id_team_guest']]['defeats']++;
				$teams[$game['id_team_owner']]['scores'] += 3;
				$teams[$game['id_team_owner']]["team_{$game['id_team_guest']}"]++;
			}
		}
	
		if($game['status'] != 2) continue;
		
		if($game['goals_guest'] > $game['goals_owner']){
			$teams[$game['id_team_guest']]['wins']++;
			$teams[$game['id_team_guest']]['wins-out']++;
			$teams[$game['id_team_owner']]['defeats']++;
			$teams[$game['id_team_guest']]['scores'] += 3;
			$teams[$game['id_team_guest']]["team_{$game['id_team_owner']}"]++;
		}
		elseif ($game['goals_owner'] > $game['goals_guest']){
			$teams[$game['id_team_owner']]['wins']++;
			$teams[$game['id_team_guest']]['defeats']++;
			$teams[$game['id_team_owner']]['scores'] += 3;
			$teams[$game['id_team_owner']]["team_{$game['id_team_guest']}"]++;
		}
		else {
			$teams[$game['id_team_guest']]['draws']++;
			$teams[$game['id_team_owner']]['draws']++;
			$teams[$game['id_team_owner']]['scores'] += 1;
			$teams[$game['id_team_guest']]['scores'] += 1;
		}	
		
		$teams[$game['id_team_guest']]['games']++;
		$teams[$game['id_team_owner']]['games']++;
		$teams[$game['id_team_guest']]['in'] += $game['goals_guest'];
		$teams[$game['id_team_owner']]['in'] += $game['goals_owner'];
		$teams[$game['id_team_guest']]['out'] += $game['goals_owner'];
		$teams[$game['id_team_owner']]['out'] += $game['goals_guest'];	
	}

	usort($teams, 'compare_scores');

/*        ob_start();
        
        echo count($teams[$game['id_team_owner']])."<br/>";
        print_r($teams);
        $myStr = ob_get_clean();
        
        $fd = fopen("/home/domen/data/www/zetblog.com/engine/plugins/football_stats/1.txt","w");
        fwrite($fd,$myStr);
        fclose($fd);
        //file_put_contents('1.txt', $myStr);
        
    for ($i=0; $i<count($teams[$game['id_team_owner']]); $i++)
    {
       $teams[$game['id_team_guest']]['scores'] =  round($teams[$game['id_team_guest']]['scores']);
       $teams[$game['id_team_owner']]['scores'] =  round($teams[$game['id_team_owner']]['scores']);
    }
    
  */  
	cacheStoreFile($cache_table, serialize($teams), 'football_stats');	
	
	return $teams;
}

# /football/TYPE/YEAR_START-YEAR-FINISH
function showLeague($params){
	global $mysql, $config, $tpl, $template, $SYSTEM_FLAGS, $lang;
	
	$year_start = intval($params['year_start']);
	$year_finish = $params['year_end'] ? intval($params['year_end']) : $year_start;
	$type = $params['type'];
	//echo $year_start." ". $year_finish."<br/>";
	$names = "{$params['type']}_page_{$year_start}_{$year_finish}";
	$cache_main = get_plugcache_dir('football_stats').$names;

	$leagues = getLeagues();
	$league  = array();
	foreach($leagues as $row){
		if($row['alias'] == $type){
			$type = $row['id'];
			$league = $row;
			break;
		}	
	}
	if(!$league){
		header('Location: '.$config['home_url']);
		exit();
	}
	
    $cacheData = cacheRetrieveFile($cacheFileName, 300, 'football_stats');

	if(is_file($cache_main)){
		$template['vars']['mainblock'] = file_get_contents($cache_main);
        $SYSTEM_FLAGS['info']['title']['group'] = $lang['fs:tournament_table']." ".str_replace(array('{name}', '{year_start}', '{year_finish}'), array($league['name'], $year_start, $year_finish), $lang['fs:title-league']);
        $SYSTEM_FLAGS['meta']['description'] = $lang['fs:tournament_table']." ".str_replace(array('{name}', '{year_start}', '{year_finish}'), array($league['name'], $championat['start'], $championat['finish']), $lang['fs:title-league']).", ".
        $lang['fs:result_calendar_and_matches_of']." ".str_replace(array('{name}', '{year_start}', '{year_finish}'), array($league['name'], $championat['start'], $championat['finish']), $lang['fs:title-league']);
        $SYSTEM_FLAGS['meta']['keywords'] = str_replace(array('{name}', '{year_start}', '{year_finish}'), array($league['name'], $championat['start'], $championat['finish']), $lang['fs:title-league'])." ".
        $lang['fs:tournament_table'].", ".str_replace(array('{name}', '{year_start}', '{year_finish}'), array($league['name'], $championat['start'], $championat['finish']), $lang['fs:title-league'])." ".
        $lang['fs:results'].", ".$lang['fs:table']." ".str_replace(array('{name}', '{year_start}', '{year_finish}'), array($league['name'], $championat['start'], $championat['finish']), $lang['fs:title-league']).", ".
        str_replace(array('{name}', '{year_start}', '{year_finish}'), array($league['name'], $championat['start'], $championat['finish']), $lang['fs:title-league'])." ".$lang['fs:groups'].", ".
        str_replace(array('{name}', '{year_start}', '{year_finish}'), array($league['name'], $championat['start'], $championat['finish']), $lang['fs:title-league'])." ".$lang['fs:calendar'];
		return;
	}

	$championats = $mysql->select("SELECT *
	                               FROM ".prefix."_football_championats
	                               WHERE type=".$type." AND start=".$year_start." AND finish=".$year_finish."
	                               ORDER BY add_type DESC, id DESC", 1);

	if(!$championats){
		header('Location: '.$config['home_url']);
		exit();
	}
	
	foreach($championats as $championat)
		$ids_championat[] = $championat['id'];
		
	$tpath = locatePluginTemplates(array('plugin.league', 'plugin.league.all.games', 'plugin.league.all.games.row', 
	                                     'plugin.league.round', 'plugin.league.group.table', 'plugin.league.group.table.row','plugin.logo_img'), 'football_stats', 1, '', 'main');
	
	foreach($championats as $championat){
		$games = saveLeagueGames($championat['id']);
		
		$tvars['vars']['games'] = '';
		//if(is_file(get_plugcache_dir('football_stats').$cache)){
		//	$template['vars']['mainblock'] .= file_get_contents(get_plugcache_dir('football_stats').$cache);
		//	continue;
		//}
		
		foreach($games as $game){
			
			$pvars = gameFillVariables($game, "");
			 /*=================*/
                    $channel_logo = include(extras_dir.'/football_stats/inc/channel.php');
				
				foreach($channel_logo as $key => $value){
					if($key == $game['channel']) {
					       $tlogo['vars']['logo'] = $value;
					       $tpl->template('plugin.logo_img', $tpath['plugin.logo_img']);
	                       $tpl->vars('plugin.logo_img', $tlogo);
	                        
						$pvars['vars']['channel'] = $tpl->show('plugin.logo_img');
						break;
					}	
				}
                
     if(strlen($pvars['vars']['channel'])<2)  
     {
       $pvars['vars']['channel']=''; 
     } 
            
            
 		/*=================*/		
			$tpl->template('plugin.league.all.games.row', $tpath['plugin.league.all.games.row']);
			$tpl->vars('plugin.league.all.games.row', $pvars);
			$tvars['vars']['games']  .= $tpl->show('plugin.league.all.games.row');	
		}
		
		$tpl->template('plugin.league.all.games', $tpath['plugin.league.all.games']);
		$tpl->vars('plugin.league.all.games', $tvars);
		$ttvars['vars']['all-games'] = $tpl->show('plugin.league.all.games');
		
		$ttvars['vars']['name'] = $championat['name'];
		
		if($championat['add_type'] != 1)
			$ttvars['vars']['table'] = '';
		else {
				$teams = saveLeagueTable($championat['id'], $games);
	
				$xxvars['vars']['entries'] = '';
			
				foreach($teams as $key => $team){
					$xvars['vars']['position']      = $key + 1;
					$xvars['vars']['name']          = $team['name'];
					$xvars['vars']['link']          = generatePluginLink('football_stats', 'team', array('name' => $team['alias']), array(), true, true);
					$xvars['vars']['games-all']     = $team['games'];
					$xvars['vars']['games-wins']    = $team['wins'];
					$xvars['vars']['games-draws']   = $team['draws'];
					$xvars['vars']['games-defeats'] = $team['defeats'];
					$xvars['vars']['scores']        = round($team['scores']);
					$xvars['vars']['in']            = $team['in'];
					$xvars['vars']['out']           = $team['out'];

                    $photo_link = getSomeDataFootballStats("photo", prefix."_football_teams", " WHERE alias=\"".$team['alias']."\"");

                    $xvars['vars']['photo_link'] = ($photo_link != "") ? pluginGetVariable('football_stats', 'photos.url').$photo_link : pluginGetVariable('football_stats', 'photos.url')."default.png";
					
					$tpl->template('plugin.league.group.table.row', $tpath['plugin.league.group.table.row']);
					$tpl->vars('plugin.league.group.table.row', $xvars);
					$xxvars['vars']['entries'] .= $tpl->show('plugin.league.group.table.row');
				}
				
				$tpl->template('plugin.league.group.table', $tpath['plugin.league.group.table']);
				$tpl->vars('plugin.league.group.table', $xxvars);
				$ttvars['vars']['table'] =  $tpl->show('plugin.league.group.table');
				
				unset($xvars, $xxvars);
		}	

		$tpl->template('plugin.league.round', $tpath['plugin.league.round']);
		$tpl->vars('plugin.league.round', $ttvars);
		$tttvars['vars']['round+games'] .= $tpl->show('plugin.league.round');
	}
	
	# get other leagues over the past years
	$leagues = $mysql->select("SELECT start, finish FROM ".prefix."_football_championats 
	                           WHERE type = {$league['id']}
	                           GROUP BY start
	                           ORDER BY start DESC", 1);
	
	$others_cl = '';
	foreach($leagues as $cl)
		$others_cl .= str_replace(
				array('{name}', '{start}', '{finish}', '{link}', '{selected}'),
				array($league['name'], $cl['start'], $cl['finish'], 
				generatePluginLink('football_stats', 'league', array('type' => $league['alias'], 'year_start' => $cl['start'], 'year_end' => $cl['finish']), array(), true, true), 
				$championat['start'] == $cl['start'] ? 'selected' : ''), 
				$lang['fs:other-championats']);
		
	$tttvars['vars']['year-start']  = $championat['start'];
	$tttvars['vars']['year-finish'] = $championat['finish'];
	$tttvars['vars']['others'] = $others_cl;
	$tttvars['vars']['name'] = $league['name'];
		
	$tpl->template('plugin.league', $tpath['plugin.league']);
	$tpl->vars('plugin.league', $tttvars);
	$template['vars']['mainblock'] = $tpl->show('plugin.league');
	
	$SYSTEM_FLAGS['info']['title']['group'] = str_replace(array('{name}', '{year_start}', '{year_finish}'), array($league['name'], $championat['start'], $championat['finish']), $lang['fs:title-league']);
    $SYSTEM_FLAGS['meta']['description'] = $lang['fs:tournament_table']." ".str_replace(array('{name}', '{year_start}', '{year_finish}'), array($league['name'], $championat['start'], $championat['finish']), $lang['fs:title-league']).", ".
    $lang['fs:result_calendar_and_matches_of']." ".str_replace(array('{name}', '{year_start}', '{year_finish}'), array($league['name'], $championat['start'], $championat['finish']), $lang['fs:title-league']);
    $SYSTEM_FLAGS['meta']['keywords'] = str_replace(array('{name}', '{year_start}', '{year_finish}'), array($league['name'], $championat['start'], $championat['finish']), $lang['fs:title-league'])." ".
    $lang['fs:tournament_table'].", ".str_replace(array('{name}', '{year_start}', '{year_finish}'), array($league['name'], $championat['start'], $championat['finish']), $lang['fs:title-league'])." ".
    $lang['fs:results'].", ".$lang['fs:table']." ".str_replace(array('{name}', '{year_start}', '{year_finish}'), array($league['name'], $championat['start'], $championat['finish']), $lang['fs:title-league']).", ".
    str_replace(array('{name}', '{year_start}', '{year_finish}'), array($league['name'], $championat['start'], $championat['finish']), $lang['fs:title-league'])." ".$lang['fs:groups'].", ".
    str_replace(array('{name}', '{year_start}', '{year_finish}'), array($league['name'], $championat['start'], $championat['finish']), $lang['fs:title-league'])." ".$lang['fs:calendar'];
	
	cacheStoreFile($names, $template['vars']['mainblock'], 'football_stats');
}

function addFine(){
	global $mysql, $SUPRESS_TEMPLATE_SHOW, $config, $userROW;
	
	if(!$_GET['ajax'] || $userROW['status'] > 3 || !is_array($userROW)){
		header('Location: '.$config['home_url']);
		exit();
	}		
	
	$SUPRESS_TEMPLATE_SHOW = 1;
	
	$id_team       = intval($_GET['id_team']);
	$id_championat = intval($_GET['id_championat']);
	$fine          = intval($_GET['fine']);
	$fine1          = intval($_GET['fine1']);
    
  /*  if ($fine==21)
    {
      $fine = 0.1;  
    }
    */
    echo $fine." ".$fine1."<br>";
    $fine = $fine+$fine1*0.1;
    
	$row = $mysql->record('SELECT * FROM '.prefix."_football_teams_championats 
	                       WHERE `id_championat` = {$id_championat} AND `id_team` = {$id_team} LIMIT 1", 1);
	
	if(!$row)
		die('record not found');
	
	$mysql->query('UPDATE '.prefix."_football_teams_championats SET `fine` = {$fine} WHERE `id_championat` = {$id_championat} AND `id_team` =  {$id_team}");
	
	clear_cache('football_stats');
}

function getPlayers(){
	global $mysql, $SUPRESS_TEMPLATE_SHOW, $config, $userROW;
	
	if(!$_GET['ajax'] || $userROW['status'] > 3 || !is_array($userROW)){
		header('Location: '.$config['home_url']);
		exit();
	}	
	
	$SUPRESS_TEMPLATE_SHOW = 1;
	
	$type = -1;
	
	if(isset($_GET['id_team'])){
		$id_team = intval($_GET['id_team']);
		$type = 'id_team';
	}	
	elseif(isset($_GET['id_team_country'])){
		$id_team = intval($_GET['id_team_country']);
		$type = 'id_team_country';
	}	

	$players = $mysql->select('SELECT id as id_player, name 
                               FROM '.prefix."_football_players 
	                           WHERE {$type} = {$id_team}
                               ORDER BY name
                               ", 1);
	
	echo json_encode(arrayCharsetConvert(0, $players));
}

function getLeagues(){
	global $mysql;
	
	if(is_file(get_plugcache_dir('football_stats').'leagues')){
		return unserialize(file_get_contents(get_plugcache_dir('football_stats').'leagues'));
	} else {
		$leagues = $mysql->select('SELECT * FROM '.prefix."_football_leagues", 1);
		cacheStoreFile('leagues', serialize($leagues), 'football_stats');
		return $leagues;
	}
}

function saveUEFAcache($ret){
	global $mysql;
	
	$cache_countries = 'uefa_countries';
	$cache_teams     = 'uefa_teams';
			
	if(is_file(get_plugcache_dir('football_stats').$cache_countries)){
		if($ret == 'team')
			return unserialize(file_get_contents(get_plugcache_dir('football_stats').$cache_teams));
		else
			return unserialize(file_get_contents(get_plugcache_dir('football_stats').$cache_countries));
	}
			
	$teams = $mysql->select('SELECT * FROM '.prefix.'_football_teams WHERE uefa_rating IS NOT NULL AND uefa_rating <> 0 ORDER BY uefa_position ASC', 1);
	
	$team = $country = array();
	
	foreach($teams as $row)
		if($row['type'] == 0)
			$team[]    = $row;
		else
			$country[] = $row;
	
	cacheStoreFile($cache_countries, serialize($country), 'football_stats');
	cacheStoreFile($cache_teams,     serialize($team),    'football_stats');
	
	if($ret == 'team')
		return $team;
	else
		return $country;
}

function showUEFAteam(){
	global $mysql, $tpl, $template, $SYSTEM_FLAGS, $lang;

    $SYSTEM_FLAGS['info']['title']['group'] =  $lang['fs:rating_clubs_UEFA'];
	$SYSTEM_FLAGS['meta']['description'] = $lang['fs:rating_clubs_UEFA'];
	$SYSTEM_FLAGS['meta']['keywords'] = $lang['fs:keywords_rating_clubs_UEFA'];
	
	if(is_file(get_plugcache_dir('football_stats').'uefa_teams_ppage')){
		$template['vars']['mainblock'] = file_get_contents(get_plugcache_dir('football_stats').'uefa_teams_ppage');
		return;
	}

	$tpath = locatePluginTemplates(array('plugin.team.uefa', 'plugin.team.uefa.row'), 'football_stats', 1, '', 'main');
	
	$teams = saveUEFAcache('team');
	
	foreach($teams as $team){
		$pvars['vars']['name']          = $team['name'];
		$pvars['vars']['uefa_rating']   = $team['uefa_rating'];
		$pvars['vars']['uefa_position'] = $team['uefa_position'];
		$pvars['vars']['link']          = generatePluginLink('football_stats', 'team', array('name' => $team['alias'], array(), true, true));
		
        $photo_link = getSomeDataFootballStats("photo", prefix."_football_teams", " WHERE alias=\"".$team['alias']."\"");

        $pvars['vars']['photo_link'] = ($photo_link != "") ? pluginGetVariable('football_stats', 'photos.url').$photo_link : pluginGetVariable('football_stats', 'photos.url')."default.png";

		$tpl->template('plugin.team.uefa.row', $tpath['plugin.team.uefa.row']);
		$tpl->vars('plugin.team.uefa.row', $pvars);
		$tvars['vars']['entries'] .= $tpl->show('plugin.team.uefa.row');
	}
	
	$tpl->template('plugin.team.uefa', $tpath['plugin.team.uefa']);
	$tpl->vars('plugin.team.uefa', $tvars);
		
	$template['vars']['mainblock'] = $tpl->show('plugin.team.uefa');
	
	cacheStoreFile('uefa_teams_ppage', $template['vars']['mainblock'], 'football_stats');
}

function showUEFAcountry(){
	global $mysql, $tpl, $template, $SYSTEM_FLAGS, $lang;

    $SYSTEM_FLAGS['info']['title']['group'] =  $lang['fs:title-uefa'];
	$SYSTEM_FLAGS['meta']['description'] = $lang['fs:rating_country_UEFA'];
	$SYSTEM_FLAGS['meta']['keywords'] = $lang['fs:keywords_rating_country_UEFA'];

	if(is_file(get_plugcache_dir('football_stats').'uefa_countries_ppage')){
		$template['vars']['mainblock'] = file_get_contents(get_plugcache_dir('football_stats').'uefa_countries_ppage');
		return;
	}
	$tpath = locatePluginTemplates(array('plugin.country.uefa', 'plugin.country.uefa.row'), 'football_stats', 1, '', 'main');
	
	$teams = saveUEFAcache('country');
	
	foreach($teams as $team){
		$pvars['vars']['name']          = $team['name'];
		$pvars['vars']['uefa_rating']   = $team['uefa_rating'];
		$pvars['vars']['uefa_position'] = $team['uefa_position'];
		$pvars['vars']['link']          = generatePluginLink('football_stats', 'team', array('name' => $team['alias'], array(), true, true));
		
        $photo_link = getSomeDataFootballStats("photo", prefix."_football_teams", " WHERE alias=\"".$team['alias']."\"");

        $pvars['vars']['photo_link'] = ($photo_link != "") ? pluginGetVariable('football_stats', 'photos.url').$photo_link : pluginGetVariable('football_stats', 'photos.url')."default.png";

		$tpl->template('plugin.country.uefa.row', $tpath['plugin.country.uefa.row']);
		$tpl->vars('plugin.country.uefa.row', $pvars);
		$tvars['vars']['entries'] .= $tpl->show('plugin.country.uefa.row');
	}
	
    $tvars['vars']['uefa_of_fifa'] = "УЄФА";

	$tpl->template('plugin.country.uefa', $tpath['plugin.country.uefa']);
	$tpl->vars('plugin.country.uefa', $tvars);
		
	$template['vars']['mainblock'] = $tpl->show('plugin.country.uefa');
	
	cacheStoreFile('uefa_countries_ppage', $template['vars']['mainblock'], 'football_stats');
}
function showFIFAcountry(){
	global $mysql, $tpl, $template, $SYSTEM_FLAGS, $lang;

    $SYSTEM_FLAGS['info']['title']['group'] =  $lang['fs:title-fifa'];
	$SYSTEM_FLAGS['meta']['description'] = $lang['fs:rating_country_FIFA'];
	$SYSTEM_FLAGS['meta']['keywords'] = $lang['fs:keywords_rating_country_FIFA'];

	if(is_file(get_plugcache_dir('football_stats').'fifa_countries_ppage')){
		$template['vars']['mainblock'] = file_get_contents(get_plugcache_dir('football_stats').'fifa_countries_ppage');
		return;
	}
	
	$tpath = locatePluginTemplates(array('plugin.country.uefa', 'plugin.country.uefa.row'), 'football_stats', 1, '', 'main');
	
	//$teams = saveUEFAcache('country');

    $teams = $mysql->select('SELECT * FROM '.prefix.'_football_teams WHERE fifa_rating IS NOT NULL AND fifa_rating <> 0 ORDER BY fifa_position ASC', 1);
	
	foreach($teams as $team){
		$pvars['vars']['name']          = $team['name'];
		$pvars['vars']['uefa_rating']   = $team['fifa_rating'];
		$pvars['vars']['uefa_position'] = $team['fifa_position'];
		$pvars['vars']['link']          = generatePluginLink('football_stats', 'team', array('name' => $team['alias'], array(), true, true));
		
        $photo_link = getSomeDataFootballStats("photo", prefix."_football_teams", " WHERE alias=\"".$team['alias']."\"");

        $pvars['vars']['photo_link'] = ($photo_link != "") ? pluginGetVariable('football_stats', 'photos.url').$photo_link : pluginGetVariable('football_stats', 'photos.url')."default.png";

		$tpl->template('plugin.country.uefa.row', $tpath['plugin.country.uefa.row']);
		$tpl->vars('plugin.country.uefa.row', $pvars);
		$tvars['vars']['entries'] .= $tpl->show('plugin.country.uefa.row');
	}
	
    $tvars['vars']['uefa_of_fifa'] = "ФІФА";

	$tpl->template('plugin.country.uefa', $tpath['plugin.country.uefa']);
	$tpl->vars('plugin.country.uefa', $tvars);
	$template['vars']['mainblock'] = $tpl->show('plugin.country.uefa');
	
	cacheStoreFile('fifa_countries_ppage', $template['vars']['mainblock'], 'football_stats');
}
/*============================================================================================================*/
function showScorers($params){
    global $mysql, $config, $tpl, $template, $SYSTEM_FLAGS, $lang;
    $tpath = locatePluginTemplates('plugin.scorers', 'football_stats', 1, '', 'main');

    $count_scorers_in_main_page = (pluginGetVariable('football_stats', 'count_scorers_in_main_page')) ? 
    " LIMIT ".pluginGetVariable('football_stats', 'count_scorers_in_main_page') : "";
    $count_scorers_in_admin = (pluginGetVariable('football_stats', 'scorers_in_admin_panel')) ?
    pluginGetVariable('football_stats', 'scorers_in_admin_panel') : 100;

    $tvars['vars']['action'] = generatePluginLink('football_stats', 'scorers_page', array('champo' => $params['champo']), array(), true, true);

    $start_year_champo = (isset($_POST['year'])) ? $_POST['year'] : getSomeDataFootballStats("MAX(start)", prefix."_football_championats", " WHERE alias=\"".$params['champo']."\"");

    switch($params['champo']){
        case 'europaleague':
            $champo_name = "Ліги Європи";
            $tvars['vars']['championat'] = "<a href='".generatePluginLink('football_stats', 'league', array('type' => $params['champo'], 'year_start' => $start_year_champo, 'year_end' => ($start_year_champo+1)), array(), true, true)."' style='font-size: 15px; color: #fff;'>".
            $champo_name."</a>";
            break;
        case 'championsleague':
            $champo_name = "Ліги Чемпіонів";
            $tvars['vars']['championat'] = "<a href='".generatePluginLink('football_stats', 'league', array('type' => $params['champo'], 'year_start' => $start_year_champo, 'year_end' => ($start_year_champo+1)), array(), true, true)."' style='font-size: 15px; color: #fff;'>".
            $champo_name."</a>";
            break;
        case 'russia':
            $champo_name = "РПЛ";
            $id_champo_sc = getSomeDataFootballStats("id", prefix."_football_championats", " WHERE alias=\"".$params['champo']."\" AND start=".$start_year_champo);
            $tvars['vars']['championat'] = "<a href='".generatePluginLink('football_stats', 'football',
            array('name' => $params['champo']), array('chid' => $id_champo_sc), true, true)."' style='font-size: 15px; color: #fff;'>".
            $champo_name."</a>";
            break;
        case 'england':
            $champo_name = "АПЛ";
            $id_champo_sc = getSomeDataFootballStats("id", prefix."_football_championats", " WHERE alias=\"".$params['champo']."\" AND start=".$start_year_champo);
            $tvars['vars']['championat'] = "<a href='".generatePluginLink('football_stats', 'football',
            array('name' => $params['champo']), array('chid' => $id_champo_sc), true, true)."' style='font-size: 15px; color: #fff;'>".
            $champo_name."</a>";
            break;
        case 'germany':
            $champo_name = "Бундесліги";
            $id_champo_sc = getSomeDataFootballStats("id", prefix."_football_championats", " WHERE alias=\"".$params['champo']."\" AND start=".$start_year_champo);
            $tvars['vars']['championat'] = "<a href='".generatePluginLink('football_stats', 'football',
            array('name' => $params['champo']), array('chid' => $id_champo_sc), true, true)."' style='font-size: 15px; color: #fff;'>".
            $champo_name."</a>";
            break;
        case 'spain':
            $champo_name = "Прімери";
            $id_champo_sc = getSomeDataFootballStats("id", prefix."_football_championats", " WHERE alias=\"".$params['champo']."\" AND start=".$start_year_champo);
            $tvars['vars']['championat'] = "<a href='".generatePluginLink('football_stats', 'football',
            array('name' => $params['champo']), array('chid' => $id_champo_sc), true, true)."' style='font-size: 15px; color: #fff;'>".
            $champo_name."</a>";
            break;
        case 'france':
            $champo_name = "Ліги 1";
            $id_champo_sc = getSomeDataFootballStats("id", prefix."_football_championats", " WHERE alias=\"".$params['champo']."\" AND start=".$start_year_champo);
            $tvars['vars']['championat'] = "<a href='".generatePluginLink('football_stats', 'football',
            array('name' => $params['champo']), array('chid' => $id_champo_sc), true, true)."' style='font-size: 15px; color: #fff;'>".
            $champo_name."</a>";
            break;
        case 'italy':
            $champo_name = "Серії А";
            $id_champo_sc = getSomeDataFootballStats("id", prefix."_football_championats", " WHERE alias=\"".$params['champo']."\" AND start=".$start_year_champo);
            $tvars['vars']['championat'] = "<a href='".generatePluginLink('football_stats', 'football',
            array('name' => $params['champo']), array('chid' => $id_champo_sc), true, true)."' style='font-size: 15px; color: #fff;'>".
            $champo_name."</a>";
            break;
        default:
            $champo_name = "УПЛ";
            $id_champo_sc = getSomeDataFootballStats("id", prefix."_football_championats", " WHERE alias=\"".$params['champo']."\" AND start=".$start_year_champo);
            $tvars['vars']['championat'] = "<a href='".generatePluginLink('football_stats', 'football',
            array('name' => $params['champo']), array('chid' => $id_champo_sc), true, true)."' style='font-size: 15px; color: #fff;'>".
            $champo_name."</a>";
            break;
    }

    $get_all_years = $mysql->select("SELECT start, finish FROM ".prefix."_football_championats GROUP BY start ORDER BY start");
	foreach($get_all_years as $year){
        if($year['start'] == $start_year_champo){
		    $tvars['vars']['years'] .= "<option value='".$year['start']."' selected>".$year['start']." - ".$year['finish']."</option>";
        }
        else{
            $tvars['vars']['years'] .= "<option value='".$year['start']."'>".$year['start']." - ".$year['finish']."</option>";
        }
	}

    $SYSTEM_FLAGS['info']['title']['group'] =  $lang['fs:scorers']." ".$champo_name;
	$SYSTEM_FLAGS['meta']['description'] = $lang['fs:desc_scorers']." ".$champo_name." ".$start_year_champo;
	$SYSTEM_FLAGS['meta']['keywords'] = "бомбардири ".$champo_name.", список бомбардирів ".$champo_name.", ".$champo_name
	." бомбардири, автори голів ".$champo_name.", бомбардиры ".$champo_name.", лучшие бомбардиры ".$champo_name;

    $id_champo = array();
	
    $add_type = "";	
	if($params['champo'] == "championsleague" || $params['champo'] == "europaleague"){
		$add_type = " AND add_type <> 0";
	}

	$get_max_year_start = $mysql->select("SELECT id FROM ".prefix."_football_championats WHERE alias=\"".$params['champo']."\" AND start=".$start_year_champo.$add_type);
	foreach($get_max_year_start as $year){
		$id_champo[] = $year['id'];
	}

    for($i=0; $i<count($id_champo); $i++){
        $goal_scorers = $mysql->select("SELECT COUNT(fg.id_player) as `goals`, `id_player`
											FROM ".prefix."_football_games_goals fg
											LEFT JOIN ".prefix."_football_players p 
											  ON fg.id_player = p.id
											WHERE fg.id_championat = ".$id_champo[$i]." AND fg.id_player <> -1 AND fg.auto = 0
											GROUP BY fg.id_player 
											ORDER BY goals DESC", 1);
		foreach($goal_scorers as $goal){
			if(!mysql_query("UPDATE ".prefix."_football_scorers SET goals=".$goal['goals']." WHERE id_player=".$goal['id_player']." AND id_championat=".$id_champo[$i])){
				print mysql_error();
				exit();
			}
		}
    }

    $position_id = 0;

    $get_scorers = $mysql->select("SELECT s.*, SUM(s.goals) as count_goals, p.name as player, c.name as championat, p.id_team as team
									FROM ".prefix."_football_scorers s
									JOIN ".prefix."_football_players p ON s.id_player = p.id
									JOIN ".prefix."_football_championats c ON s.id_championat = c.id
									WHERE s.id_championat IN (".implode(',',$id_champo).") GROUP BY s.id_player
									ORDER BY count_goals DESC, number_id".$count_scorers_in_main_page);

    if(count($get_scorers) > 0){
        $tvars['vars']['scorers'] = "<table border='0' cellpadding='0' cellspacing='0'width='100%'>
            <tr>
	            <td class='cells' style='text-align: center; width: 5%;'><b>".$lang['fs:number_sc']."</b></td>
	            <td class='cells' style='text-align: center; width: 6%;'><b>".$lang['fs:Club']."</b></td>
	            <td class='cells'><b>".$lang['fs:player_sc']."</b></td>
                <td class='cells' style='text-align: center; width: 5%;'><b>".$lang['fs:golas']."</b></td>
            </tr>";
        foreach($get_scorers as $scorer){
            $position_id++;
            $club_cs = getSomeDataFootballStats("photo", prefix."_football_teams", " WHERE id=".$scorer['team']);
            $scorer['team'] = ($club_cs != "") ? pluginGetVariable('football_stats', 'photos.url').$club_cs : pluginGetVariable('football_stats', 'photos.url')."default.png";
            $tvars['vars']['scorers'] .= "<tr><td class='cell' style='text-align: center;'>".$position_id
            ."</td><td class='cell' style='text-align: center;'><img src='".$scorer['team']
            ."' width='20' /></td><td class='cell'><a href='".
            generatePluginLink('football_stats', 'player', array('id' => $scorer['id_player'], array(), true, true))
            ."'>".$scorer['player']."</a></td><td class='cell' style='text-align: center;'>"
            .$scorer['count_goals']."</td></tr>";
        }
        $tvars['vars']['scorers'] .= "</table>";
    }
    else{
        $tvars['vars']['scorers'] = "";
    }
    
    $tpl->template('plugin.scorers', $tpath['plugin.scorers']);
	$tpl->vars('plugin.scorers', $tvars);
	$template['vars']['mainblock'] = $tpl->show('plugin.scorers');
}

function showtoday($params){
	global $mysql, $config, $tpl, $template, $SYSTEM_FLAGS, $lang;
    $tpath = locatePluginTemplates(array('plugin.games.today','plugin.games.today.row','plugin.logo_img'), 'football_stats', 1, '', 'main');  
  
    $cacheFileName = md5('showtoday').'.txt';

    if(pluginGetVariable('football_stats', 'cache')){
        $cacheData = cacheRetrieveFile($cacheFileName, pluginGetVariable('football_stats','cacheExpire'), 'football_stats');
        if ($cacheData != false) {
            $template['vars']['today'] = $cacheData;
            return;
        }
    }

    if(pluginGetVariable('football_stats', 'begin_day')){
        $dd = pluginGetVariable('football_stats', 'begin_day');
    }
    else{
        $dd = date('Y-m-d');
    }

    if(pluginGetVariable('football_stats', 'end_day')){
        $tomorrow = pluginGetVariable('football_stats', 'end_day');
    }
    else{
        $tomorrow = next_day($dd);
    }

    $time_now = date('H-m-s');
    $hour = explode('-', $time_now);
    $hour_now = $hour[0];

    if(pluginGetVariable('football_stats', 'begin_hour')){
        $arr_hour_begin = explode(":", pluginGetVariable('football_stats', 'begin_hour'));
        $hour_begin = $arr_hour_begin[0];
    }
    else{
        $hour_begin = "11";
    }

    if(pluginGetVariable('football_stats', 'end_hour')){
        $arr_hour_end = explode(":", pluginGetVariable('football_stats', 'end_hour'));
        $hour_end = $arr_hour_end[0];
    }
    else{
        $hour_end = "11";
    }

    $d = new DateTime($dd);
    $d->modify("-7 day");
    $end_search_day = $d->format("Y-m-d");

    if(!pluginGetVariable('football_stats', 'begin_hour') && !pluginGetVariable('football_stats', 'end_hour')){
        if($hour_now < 11){
            $dd = prev_day($dd);
            $tomorrow = next_day($dd);
        }
    }

   mysql_query("SET NAMES 'utf-8';");

   $query = sql_query($dd, $tomorrow, $hour_begin, $hour_end);

   if(!pluginGetVariable('football_stats', 'begin_day') && !pluginGetVariable('football_stats', 'end_day') &&
   !pluginGetVariable('football_stats', 'begin_hour') && !pluginGetVariable('football_stats', 'end_hour')){
       while(TRUE){
           if($dd == $end_search_day){
               break;
           }
           else if(test_query($query) == FALSE){
               $dd = prev_day($dd);
               $tomorrow = prev_day($tomorrow);
               $query = sql_query($dd, $tomorrow, $hour_begin, $hour_end);
           }
           else{
               break;
           }
       }
   }

    //$ppvars['vars']['title_today_matches'] = "<tr><td><b style='text-aling: center;'>".$lang['fs:today_matches']."</b></td></tr>";
    $day_play = new DateTime($dd);
    $dd = $day_play->format("Y-m-d");

    $array_days_ua = array("Понеділок", "Вівторок", "Середа", "Четвер", "П'ятниця", "Субота", "Неділя");
    $array_days_en = array("Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun");

    $show_date = strftime("%a, %d/%m/%Y", strtotime($dd));
    for($i=0; $i<count($array_days_ua); $i++){
        if($array_days_en[$i] == substr($show_date, 0, 3)){
            $show_date = mb_substr($show_date, 3);
            $show_date = $array_days_ua[$i].$show_date;
        }
    }
    if(pluginGetVariable('football_stats', 'begin_day') && pluginGetVariable('football_stats', 'end_day') &&
        pluginGetVariable('football_stats', 'begin_hour') && pluginGetVariable('football_stats', 'end_hour')){
        if($dd < $tomorrow){
            $after_tomorrow = next_day($tomorrow);
            if(test_query(sql_query($tomorrow, $after_tomorrow, "11", "11")) == TRUE){
                 $second_data = strftime("%a, %d/%m/%Y", strtotime($tomorrow));
                 for($i=0; $i<count($array_days_ua); $i++){
                    if($array_days_en[$i] == substr($second_data, 0, 3)){
                        $second_data = mb_substr($second_data, 3);
                        $second_data = $array_days_ua[$i].$second_data;
                    }
                }
                $show_date .= " - ".$second_data;
            }
        }
    }

    $ppvars['vars']['date'] = $show_date;

    $template['vars']['today']=$query."<br/>";

	$matches = $mysql->select($query, 1);
/*
	if(!$matches){
		header('Location: '.$config['home_url']);
		exit();
	}
*/	

    if (!function_exists('mb_ucfirst') && extension_loaded('mbstring'))
    {
        /**
         * mb_ucfirst converts the first character to uppercase.
         * @param string $str Input string.
         * @param string $encoding Character encoding, UTF-8 by default.
         * @return string
         */
        function mb_ucfirst($str, $encoding='UTF-8')
        {
            $str = mb_ereg_replace('^[\ ]+', '', $str);
            $str = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding).
                   mb_substr($str, 1, mb_strlen($str), $encoding);
            return $str;
        }
    }

	foreach($matches as $match)
    {
        $status_game = $mysql->select("SELECT status, date FROM ".prefix."_football_games WHERE id=".$match['id']);
        foreach($status_game as $status){
            $status_now = $status['status'];
            $date_of_game = $status['date'];
        }

        if($status_now == 0 && date("Y-m-d H:i:s") > $date_of_game){
            if(!mysql_query("UPDATE ".prefix."_football_games SET status=1 WHERE id=".$match['id'])){
                print mysql_error();
            }
        }
        /*else if($status_now == 1 && date("Y-m-d H:i:s") > date("Y-m-d H:i:s", strtotime("+115 minute",strtotime($date_of_game)))){
            if(!mysql_query("UPDATE ".prefix."_football_games SET status=2 WHERE id=".$match['id'])){
                print mysql_error();
            }
        }*/

        switch($match['status']){
            case 0:
                $pvars['vars']['status'] = "<img src='/engine/plugins/football_stats/tpl/images/gray-status.gif' align='absmiddle' title='".$lang['fs:match_not_started']."' />";
                break;
            case 1:
                $pvars['vars']['status'] = "<img src='/engine/plugins/football_stats/tpl/images/green-status.gif' align='absmiddle' title='".$lang['fs:plaing_match']."' />";
                break;
            case 2:
                $pvars['vars']['status'] = "<img src='/engine/plugins/football_stats/tpl/images/red-status.gif' align='absmiddle' title='".$lang['fs:match_is_over']."' />";
                break;
            case 3:
                $pvars['vars']['status'] = "<img src='/engine/plugins/football_stats/tpl/images/black-status.gif' align='absmiddle' title='".$lang['fs:match_stopped']."' />";
                break;
            case 4:
                $pvars['vars']['status'] = "<img src='/engine/plugins/football_stats/tpl/images/black-status.gif' align='absmiddle' title='".$lang['fs:match_did_not_take_place']."' />";
                break;
            default:
                $pvars['vars']['status'] = "Не определен";
        }
      //$mmm.=$match['ligue'].' '.$match['round'].' '.$match['team_owner'].' '.$match['team_guest'].' '.$match['date'].'<br/>';

      $ligue_short = explode(" ", $match['ligue']);
      if(isset($ligue_short[1])){
        $pvars['vars']['ligue'] = $ligue_short[0]." ".$ligue_short[1];
      }
      else{
          $pvars['vars']['ligue'] = $ligue_short[0];
      }
      $pvars['vars']['title_ligue'] = $match['ligue'];

		if(date('m') > 7){
			$past_year = date('Y');
		}
		else{
			$past_year = date('Y') - 1;
		}
        $get_ligue = mysql_query("SELECT * FROM `".prefix."_football_championats` WHERE id=".$match['id_ligue']." AND start >= ".$past_year);
        //print mysql_error();
        $res_ligue = mysql_fetch_array($get_ligue);
        do{
            $get_contries = mysql_query("SELECT * FROM `".prefix."_football_countries` WHERE name LIKE '%".$match['ligue']."%'");
            //print mysql_error();
            if(mysql_num_rows($get_contries) > 0){
                $res_contries = mysql_fetch_array($get_contries);
                do{
                    $pvars['vars']['ligue_link'] = generatePluginLink('football_stats', 'football', array('name' => $res_contries['alias']), array('chid' => $res_ligue['id']), true, true);
                    break;
                }
                while($res_contries = mysql_fetch_array($get_contries));
            }
            else{
                $get_championats = mysql_query("SELECT start, finish, alias FROM `".prefix."_football_championats` WHERE id=".$match['id_ligue']);
                $res_championats = mysql_fetch_array($get_championats);
                do{
                    $pvars['vars']['ligue_link'] = generatePluginLink('football_stats', 'league', array('type' => $res_championats['alias'], 'year_start' => $res_championats['start'], 'year_end' => $res_championats['finish']), array(), true, true);
                }
                while($res_championats = mysql_fetch_array($get_championats));
            }
            break;
        }
        while($res_ligue = mysql_fetch_array($get_ligue));


      $pvars['vars']['round'] =   $match['round'];
      $pvars['vars']['link-owner'] = generatePluginLink('football_stats', 'team', array('name' => $match['alias_owner'], array(), true, true));
      $pvars['vars']['link-guest'] = generatePluginLink('football_stats', 'team', array('name' => $match['alias_guest'], array(), true, true));
      $pvars['vars']['team-guest'] = $match['team_guest'];
      $pvars['vars']['team-owner'] = $match['team_owner'];
      $pvars['vars']['date'] = date('G:i', strtotime($match['date']));
      if($match['status'] == 0){
          $pvars['vars']['goals-guest'] = "?";
          $pvars['vars']['goals-owner'] = "?";
      }
      else if($match['status'] == 4){
          if(intval($match['goals_owner']) > intval($match['goals_guest'])){
              $pvars['vars']['goals-guest'] = '-';
              $pvars['vars']['goals-owner'] = '+';
          }
          else if(intval($match['goals_guest']) > intval($match['goals_owner'])){
              $pvars['vars']['goals-guest'] = '+';
              $pvars['vars']['goals-owner'] = '-';
          }
          else{
              $pvars['vars']['goals-guest'] = $match['goals_guest'];
              $pvars['vars']['goals-owner'] = $match['goals_owner'];
          }
      }
      else{
          $pvars['vars']['goals-guest'] = $match['goals_guest'];
          $pvars['vars']['goals-owner'] = $match['goals_owner'];
      }

      if($match['status'] == 2 && (showPenalti($match['id'], $match['id_team_guest']) != 0 || showPenalti($match['id'], $match['id_team_owner'] != 0))){
          $pvars['vars']['goals-guest'] = intval(showPenalti($match['id'], $match['id_team_guest'])) + intval($match['goals_guest']);
          $pvars['vars']['goals-owner'] = intval(showPenalti($match['id'], $match['id_team_owner'])) + intval($match['goals_owner']);
      }

      $pvars['vars']['link-game']  = generatePluginLink('football_stats', 'match', array('id' => $match['id'], array(), true, true));

      $pvars['vars']['reds_owner'] = $pvars['vars']['reds_guest'] = '';	
	    for($i = 0; $i < $match['reds_owner']; $i++)
		$pvars['vars']['reds_owner'] .= $lang['fs:reds'];
					
	    for($i = 0; $i < $match['reds_guest']; $i++)
		$pvars['vars']['reds_guest'] .= $lang['fs:reds'];


        if (strlen($match['photo'])>0)
        {
         $pvars['vars']['photo'] = "<a href=".$match['photo'].">фото</a>";   
        }
		else{
		$pvars['vars']['photo'] = 'фото';  
		}
         if (strlen($match['video'])>0)
        {
         $pvars['vars']['video'] = "<a href=".$match['video'].">відео</a>";   
        }
		else{
		$pvars['vars']['video'] = 'відео';  
		}
	//	$tvars['vars']['video']       = $game['video'];
        
	//	$tvars['vars']['stats']       = $game['stats'];
		 if (strlen($match['stats'])>0)
        {
         $pvars['vars']['stats'] = "<a href=".$match['stats'].">статистика</a>";   
        }
		else{
		$pvars['vars']['stats'] = 'статистика';  
		}
      /*=================*/
                $channel_logo = include(extras_dir.'/football_stats/inc/channel.php');
				$pvars['vars']['channel'] = "";
				foreach($channel_logo as $key => $value){
					if($key == $match['channel']) {
					       $tlogo['vars']['logo'] = $value;
					       $tpl->template('plugin.logo_img', $tpath['plugin.logo_img']);
	                       $tpl->vars('plugin.logo_img', $tlogo);
	                        
						$pvars['vars']['channel'] = $tpl->show('plugin.logo_img');
						break;
					}	
				}
 		/*=================*/
      
    $tpl->template('plugin.games.today', $tpath['plugin.games.today']);//'/home/domen/data/www/zetblog.com/engine/plugins/football_stats/tpl/main/');
	$tpl->vars('plugin.games.today', $pvars);
    $mmm .= $tpl->show('plugin.games.today');
    
    }
   $ppvars['vars']['games'] = $mmm;
   $tpl->template('plugin.games.today.row', $tpath['plugin.games.today.row']);
   $tpl->vars('plugin.games.today.row', $ppvars);
   $kkk  = $tpl->show('plugin.games.today.row');
   $template['vars']['today'] = $kkk;

   # create cache
   if (pluginGetVariable('football_stats', 'cache')) {
	   cacheStoreFile($cacheFileName, $template['vars']['today'], 'football_stats');
   }
}

function showSelectedMatches(){
    global $mysql, $config, $tpl, $template, $SYSTEM_FLAGS, $lang;
    $tpath = locatePluginTemplates(array('plugin.logo_img', 'plugin.games.selected'), 'football_stats', 1, '', 'main');  

    $cacheFileName = md5('showSelectedMatches').'.txt';

    if(pluginGetVariable('football_stats', 'cache')){
        $cacheData = cacheRetrieveFile($cacheFileName, pluginGetVariable('football_stats','cacheExpire'), 'football_stats');
        if ($cacheData != false) {
            $template['vars']['selected_matches'] = $cacheData;
            return;
        }
    }
	
	$day_minus = (pluginGetVariable('football_stats','day_minus')) ? pluginGetVariable('football_stats','day_minus') : 7;

    $date_now = date("Y-m-d");
	
	$day_later = new DateTime($date_now);
    $day_later->modify("-".$day_minus." day");
    $date_now = $day_later->format("Y-m-d");

    $get_matches = mysql_query("SELECT g.*, t1.name as team_guest, t1.alias as alias_guest, t2.name as team_owner, t2.alias as alias_owner, l1.name as ligue, l1.id as id_ligue
		      FROM ".prefix."_football_games g 
		      LEFT JOIN ".prefix."_football_teams t1 
		      ON g.id_team_guest = t1.id 
		      LEFT JOIN ".prefix."_football_teams t2 
		      ON g.id_team_owner = t2.id 
              LEFT JOIN `".prefix."_football_championats` l1
              ON g.id_championat = l1.id
              WHERE show_match=1 AND date > '".$date_now."' ORDER BY date LIMIT 10");

    $count_selected_matches = mysql_num_rows($get_matches);
    if($count_selected_matches > 0){
        $res_matches = mysql_fetch_array($get_matches);
        do{
            switch($res_matches['status']){
                case 0:
                    $pvars['vars']['status'] = "<img src='/engine/plugins/football_stats/tpl/images/gray-status.gif' style='border-radius: 10px; vertical-align: baseline;' align='absmiddle' title='".$lang['fs:match_not_started']."' />";
                    break;
                case 1:
                    $pvars['vars']['status'] = "<img src='/engine/plugins/football_stats/tpl/images/green-status.gif' style='border-radius: 10px; vertical-align: baseline;' align='absmiddle' title='".$lang['fs:plaing_match']."' />";
                    break;
                case 2:
                    $pvars['vars']['status'] = "<img src='/engine/plugins/football_stats/tpl/images/red-status.gif' style='border-radius: 10px; vertical-align: baseline;' align='absmiddle' title='".$lang['fs:match_is_over']."' />";
                    break;
                case 3:
                    $tvars['vars']['status'] = "<img src='/engine/plugins/football_stats/tpl/images/black-status.gif' style='border-radius: 10px; vertical-align: baseline;' align='absmiddle' title='".$lang['fs:match_stopped']."' />";
                    break;
                default:
                    $pvars['vars']['status'] = "Не определен";
            }
            if($res_matches['channel'] != "0"){
                $pvars['vars']['channel'] = $res_matches['channel'];
            }
            else{
                $pvars['vars']['channel'] = "";
            }
            /*
            $channel_logo = include(extras_dir.'/football_stats/inc/channel.php');
		    foreach($channel_logo as $key => $value){
			    if($key == $res_matches['channel']) {
					    $tlogo['vars']['logo'] = $value;
					    $tpl->template('plugin.logo_img', $tpath['plugin.logo_img']);
	                    $tpl->vars('plugin.logo_img', $tlogo);
	                        
				    $pvars['vars']['channel'] = $tpl->show('plugin.logo_img');
				    break;
			    }	
		    }*/

            if (strlen($res_matches['photo'])>0)
            {
             $pvars['vars']['photo'] = "<a href=".$res_matches['photo'].">фото</a>";   
            }
		    else{
		    $pvars['vars']['photo'] = 'фото';  
		    }
             if (strlen($res_matches['video'])>0)
            {
             $pvars['vars']['video'] = "<a href=".$res_matches['video'].">відео</a>";   
            }
		    else{
		    $pvars['vars']['video'] = 'відео';  
		    }
	    //	$tvars['vars']['video']       = $game['video'];
        
	    //	$tvars['vars']['stats']       = $game['stats'];
		     if (strlen($res_matches['stats'])>0)
            {
             $pvars['vars']['stats'] = "<a href=".$res_matches['stats'].">статистика</a>";   
            }
		    else{
		    $pvars['vars']['stats'] = 'статистика';
		    }


            //$pvars['vars']['date'] = date('G:i', strtotime($res_matches['date']));
            $pvars['vars']['date'] = date('d.m, H:i', strtotime($res_matches['date']));
            $pvars['vars']['ligue'] =   $res_matches['ligue'];

            $past_year = date('Y') - 1;
            $get_ligue = mysql_query("SELECT * FROM `".prefix."_football_championats` WHERE id=".$res_matches['id_ligue']." AND start >= ".$past_year);
            //print mysql_error();
            $res_ligue = mysql_fetch_array($get_ligue);
            do{
                //----------------football_contries---------------------
                $get_contries = mysql_query("SELECT * FROM `".prefix."_football_countries` WHERE name LIKE '%".$res_matches['ligue']."%'");
                if(mysql_num_rows($get_contries) > 0){
                    //print mysql_error();
                    $res_contries = mysql_fetch_array($get_contries);
                    do{
                        $pvars['vars']['ligue_link'] = generatePluginLink('football_stats', 'football', array('name' => $res_contries['alias']), array('chid' => $res_ligue['id']), true, true);
                        break;
                    }
                    while($res_contries = mysql_fetch_array($get_contries));
                    //--------------------------------------------------------
                }
                else{
                    $get_championats = mysql_query("SELECT start, finish, alias FROM `".prefix."_football_championats` WHERE id=".$res_matches['id_ligue']);
                    $res_championats = mysql_fetch_array($get_championats);
                    do{
                        $pvars['vars']['ligue_link'] = generatePluginLink('football_stats', 'league', array('type' => $res_championats['alias'], 'year_start' => $res_championats['start'], 'year_end' => $res_championats['finish']), array(), true, true);
                    }
                    while($res_championats = mysql_fetch_array($get_championats));
                }
                break;
            }
            while($res_ligue = mysql_fetch_array($get_ligue));
            $pvars['vars']['round'] =   $res_matches['round'];
            $pvars['vars']['link-owner'] = generatePluginLink('football_stats', 'team', array('name' => $res_matches['alias_owner'], array(), true, true));
            $pvars['vars']['link-guest'] = generatePluginLink('football_stats', 'team', array('name' => $res_matches['alias_guest'], array(), true, true));
            $pvars['vars']['team-guest'] = $res_matches['team_guest'];
            $pvars['vars']['team-owner'] = $res_matches['team_owner'];

            if($res_matches['status'] == 0){
                  $pvars['vars']['goals-guest'] = "?";
                  $pvars['vars']['goals-owner'] = "?";
              }
              else{
                  $pvars['vars']['goals-guest'] = $res_matches['goals_guest'];
                  $pvars['vars']['goals-owner'] = $res_matches['goals_owner'];
              }

              if($res_matches['status'] == 2 && (showPenalti($res_matches['id'], $res_matches['id_team_guest']) != 0 || showPenalti($res_matches['id'], $res_matches['id_team_owner'] != 0))){
                  $pvars['vars']['goals-guest'] = intval(showPenalti($res_matches['id'], $res_matches['id_team_guest'])) + intval($res_matches['goals_guest']);
                  $pvars['vars']['goals-owner'] = intval(showPenalti($res_matches['id'], $res_matches['id_team_owner'])) + intval($res_matches['goals_owner']);
              }

            //$pvars['vars']['goals-guest'] = $res_matches['goals_guest'];
            //$pvars['vars']['goals-owner'] = $res_matches['goals_owner'];
            $pvars['vars']['link-game']  = generatePluginLink('football_stats', 'match', array('id' => $res_matches['id'], array(), true, true));

            $tpl->template('plugin.games.selected', $tpath['plugin.games.selected']);
	        $tpl->vars('plugin.games.selected', $pvars);
            $selected_matches .= $tpl->show('plugin.games.selected');
        }
        while($res_matches = mysql_fetch_array($get_matches));

        $template['vars']['selected_matches'] = "<table>".$selected_matches."</table>";
    }
    else{
        $template['vars']['selected_matches'] = "";
    }
    # create cache
   if (pluginGetVariable('football_stats', 'cache')) {
	   cacheStoreFile($cacheFileName, $template['vars']['selected_matches'], 'football_stats');
   }
}

function showMatches(){
    global $mysql, $config, $tpl, $template, $SYSTEM_FLAGS, $lang;
    $tpath = locatePluginTemplates(array('plugin.matches','plugin.logo_img'), 'football_stats', 1, '', 'main');
	
	$SYSTEM_FLAGS['info']['title']['group'] =  "Футбол онлайн - LiveScore";
	$SYSTEM_FLAGS['meta']['description'] = $lang['fs:desc_matches'];
	$SYSTEM_FLAGS['meta']['keywords'] = $lang['fs:keyw_matches'];

    mysql_query("SET NAMES 'utf-8';");

    $pvars['vars']['none'] = "none";

    if(empty($_POST)){

        $dd = date('Y-m-d');
        $tomorrow = next_day($dd);

        $query = queryShowMatches($dd, $tomorrow, "11", "11");

        $d = new DateTime($dd);
        $d->modify("-7 day");
        $end_search_day = $d->format("Y-m-d");

        while(TRUE){
            if($dd == $end_search_day){
                break;
            }
            else if(test_query($query) == FALSE){
                $dd = prev_day($dd);
                $tomorrow = prev_day($tomorrow);
                $query = queryShowMatches($dd, $tomorrow, "11", "11");
            }
            else{
                break;
            }
        }
    }
    else{
        if(isset($_POST['prev_date']) && isset($_POST['date'])){
            $dd = prev_day($_POST['date']);
            $tomorrow = next_day($dd);
            $query = queryShowMatches($dd, $tomorrow, "11", "11");
            while(TRUE){
                if(test_query($query) == FALSE){
                    $dd = prev_day($dd);
                    $tomorrow = next_day($dd);
                    $query = queryShowMatches($dd, $tomorrow, "11", "11");
                }
                else{
                    break;
                }
            }
        }
        else if(isset($_POST['next_date']) && isset($_POST['date'])){
            $dd = next_day($_POST['date']);
            $tomorrow = next_day($dd);

            $query = queryShowMatches($dd, $tomorrow, "11", "11");
            while(TRUE){
                if(test_query($query) == FALSE){
                    $dd = next_day($dd);
                    $tomorrow = next_day($dd);
                    $query = queryShowMatches($dd, $tomorrow, "11", "11");
                }
                else{
                    break;
                }
            }
        }
        else if(isset($_POST['interval_start']) && isset($_POST['interval_end'])){
            $dd = $_POST['interval_start'];
            $tomorrow = $_POST['interval_end'];
            $query = queryShowMatches($dd, $tomorrow, "11", "11");
            $pvars['vars']['none'] = "grid";
        }
        else{
            $dd = $_POST['date'];
            $tomorrow = next_day($dd);

            $query = queryShowMatches($dd, $tomorrow, "11", "11");
        }
    }	

	$queryUPL = queryShowMatchesUPL($dd, $tomorrow, "11", "11");
	
   if(test_query($query) != FALSE){   
       $matches = $mysql->select($query);
	   
	   if(test_query($queryUPL) != FALSE){
			$matchesUPL = $mysql->select($queryUPL);
			foreach($matchesUPL as $upl){
				array_unshift($matches, $upl);
			}
	   }
	   
       $ligue_name = "";
       foreach($matches as $match){

            if($match['channel'] != "0"){
                $channel = $match['channel'];
            }
            else{
                $channel = "";
            }
       
            $channel_logo = include(extras_dir.'/football_stats/inc/channel.php');
		    foreach($channel_logo as $key => $value){
			    if($key == $match['channel']) {
					    $tlogo['vars']['logo'] = $value;
					    $tpl->template('plugin.logo_img', $tpath['plugin.logo_img']);
	                    $tpl->vars('plugin.logo_img', $tlogo);
	                        
				    $channel = $tpl->show('plugin.logo_img');
				    break;
			    }	
		    }

           switch($match['status']){
                case 0:
                    $img_status = "<img src='/engine/plugins/football_stats/tpl/images/gray-status.gif' style='border-radius: 10px; vertical-align: baseline;' align='absmiddle' title='".$lang['fs:match_not_started']."' />";
                    break;
                case 1:
                    $img_status = "<img src='/engine/plugins/football_stats/tpl/images/green-status.gif' style='border-radius: 10px; vertical-align: baseline;' align='absmiddle' title='".$lang['fs:plaing_match']."' />";
                    break;
                case 2:
                    $img_status = "<img src='/engine/plugins/football_stats/tpl/images/red-status.gif' style='border-radius: 10px; vertical-align: baseline;' align='absmiddle' title='".$lang['fs:match_is_over']."' />";
                    break;
                case 3:
                    $img_status = "<img src='/engine/plugins/football_stats/tpl/images/black-status.gif' style='border-radius: 10px; vertical-align: baseline;' align='absmiddle' title='".$lang['fs:match_stopped']."' />";
                    break;
                case 4:
                    $img_status = "<img src='/engine/plugins/football_stats/tpl/images/black-status.gif' style='border-radius: 10px; vertical-align: baseline;' align='absmiddle' title='".$lang['fs:match_did_not_take_place']."' />";
                    break;
                default:
                    $img_status = "Не определен";
            }

            $photo = ($match['photo'] != "" && $match['photo'] != NULL) ? "<a href='".$match['photo']."'>фото</a>" :
            "<span style='color: #BBBBBB; font-size: 9px; line-height: 16px; text-decoration: none;'>фото</span>";
            $video = ($match['video'] != "" && $match['video'] != NULL) ? "<a href='".$match['video']."'>відео</a>" :
            "<span style='color: #BBBBBB; font-size: 9px; line-height: 16px; text-decoration: none;'>відео</span>";
            $stats = ($match['stats'] != "" && $match['stats'] != NULL) ? "<a href='".$match['stats']."'>статистика</a>" :
            "<span style='color: #BBBBBB; font-size: 9px; line-height: 16px; text-decoration: none;'>статистика</span>";
			
			$ligue = "<tr class='pluginEntryUninstalled' width='100'><td>".$match['ligue']."</td><td></td><td></td><td></td><td></td><td></td></tr>";
			$query_ligue = "SELECT id FROM ".prefix."_football_leagues WHERE alias='".$match['c_alias']."'";
			$query_countries = "SELECT id FROM ".prefix."_football_countries WHERE alias='".$match['c_alias']."'";
			if(mysql_num_rows(mysql_query($query_ligue)) > 0){
				$ligue = "<tr class='pluginEntryUninstalled' width='100'><td colspan='3' style='padding: 5px 0px;'><a href='".
				generatePluginLink('football_stats', 'league', array('type' => $match['c_alias'], 'year_start' => $match['c_start'], 'year_end' => $match['c_finish']), array(), true, true)
				."'>".$match['ligue']."</a></td><td></td><td></td><td></td><td></td><td></td></tr>";
			}
			else if(mysql_num_rows(mysql_query($query_countries)) > 0){
				$ligue = "<tr class='pluginEntryUninstalled' width='100'><td colspan='3' style='padding: 5px 0px;'><a href='".
				generatePluginLink('football_stats', 'football', array('name' => $match['c_alias']), array('chid' => $match['id_ligue']), true, true)
				."'>".$match['ligue']."</a></td><td></td><td></td><td></td><td></td><td></td></tr>";
			}
            if($ligue_name == $match['ligue']){
                $ligue = "";
            }

            $photo_club_own = getSomeDataFootballStats("photo", prefix."_football_teams", " WHERE id=".$match['id_team_owner']);
            $photo_club_own = ($photo_club_own != "") ? pluginGetVariable('football_stats', 'photos.url').$photo_club_own : pluginGetVariable('football_stats', 'photos.url')."default.png";
            $photo_club_gue = getSomeDataFootballStats("photo", prefix."_football_teams", " WHERE id=".$match['id_team_guest']);
		    $photo_club_gue = ($photo_club_gue != "") ? pluginGetVariable('football_stats', 'photos.url').$photo_club_gue : pluginGetVariable('football_stats', 'photos.url')."default.png";

            //$goals_owner = ($match['goals_owner'])
            $goals_owner = ($match['status'] == 0) ? "?" : $match['goals_owner'];
            $goals_guest = ($match['status'] == 0) ? "?" : $match['goals_guest'];

            if($match['status'] == 2 && (showPenalti($match['id'], $match['id_team_guest']) != 0 || showPenalti($match['id'], $match['id_team_owner'] != 0))){
                $goals_guest = intval(showPenalti($match['id'], $match['id_team_guest'])) + intval($goals_guest);
                $goals_owner = intval(showPenalti($match['id'], $match['id_team_owner'])) + intval($goals_owner);
            }
            else if($match['status'] == 4){
                if(intval($match['goals_owner']) > intval($match['goals_guest'])){
                    $goals_owner = '+';
                    $goals_guest = '-';
                }
                else if(intval($match['goals_guest']) > intval($match['goals_owner'])){
                    $goals_owner = '-';
                    $goals_guest = '+';
                }
                else{
                    $goals_owner = ($match['status'] == 0) ? "?" : $match['goals_owner'];
                    $goals_guest = ($match['status'] == 0) ? "?" : $match['goals_guest'];
                }
            }
			
			$redsCardOwner = $redsCardGuest = "";
			for($i = 0; $i < $match['reds_owner']; $i++){
				$redsCardOwner .= $lang['fs:reds'];
			}
			for($i = 0; $i < $match['reds_guest']; $i++){
				$redsCardGuest .= $lang['fs:reds'];
			}

           $pvars['vars']['matches'] .= $ligue."<tr>
               <td class='cell'>".$img_status." ".date("d.m, H:i", strtotime($match['date']))."</td>
               <td class='cell'>".$channel."</td>
               <td class='cell' style='text-align: right;'>".$redsCardOwner."<a href='".
               generatePluginLink('football_stats', 'team', array('name' => $match['alias_owner']), array(), true, true)
               ."' style='vertical-align: super;'>".$match['team_owner']."</a> <img src='".$photo_club_own."' width='15' /></td>
               <td class='cell' style='text-align: center;'><a href='".
               generatePluginLink('football_stats', 'match', array('id' => $match['id']), array(), true, true)
               ."'>".$goals_owner." : ".$goals_guest."</a></td>
               <td class='cell'><img src='".$photo_club_gue."' width='15' /> <a href='".
               generatePluginLink('football_stats', 'team', array('name' => $match['alias_guest']), array(), true, true)
               ."' style='vertical-align: super;'>".$match['team_guest']."</a>".$redsCardGuest."</td>
               <td class='cell' style='text-align: center;'><a href='".
               generatePluginLink('football_stats', 'match', array('id' => $match['id']), array(), true, true)
               ."'>онлайн</a></td>
               <td class='cell' style='text-align: center;'>".$photo."</td>
               <td class='cell' style='text-align: center;'>".$video."</td>
               <td class='cell' style='text-align: center;'>".$stats."</td>
           </tr>";

           $ligue_name = $match['ligue'];
       }
       $pvars['vars']['comments_left'] = $pvars['vars']['comments_right'] = $pvars['vars']['no_matches'] = "";
    }
    else{
        $pvars['vars']['matches'] = "";
        $pvars['vars']['comments_left'] = "<!--";
        $pvars['vars']['comments_right'] = "-->";
        $pvars['vars']['no_matches'] = "У цей день матчів не заплановано...";
    }

    $pvars['vars']['date'] = $dd;

    $pvars['vars']['interval_start'] = (isset($_POST['interval_start'])) ? $_POST['interval_start'] : "";
    $pvars['vars']['interval_end'] = (isset($_POST['interval_end'])) ? $_POST['interval_end'] : "";

    $pvars['vars']['action'] = generatePluginLink('football_stats', 'matches', array(), array(), true, true);

    $tpl->template('plugin.matches', $tpath['plugin.matches']);
    $tpl->vars('plugin.matches', $pvars);
    $template['vars']['mainblock'] = $tpl->show('plugin.matches');
}

function queryShowMatches($day_begin, $day_end, $hour_begin, $hour_end){
    $query = "SELECT g.*, t1.name as team_guest, t1.alias as alias_guest, t2.name as team_owner, t2.alias as alias_owner, l1.name as ligue, l1.id as id_ligue, l1.alias as c_alias, l1.start as c_start, l1.finish as c_finish
       FROM ".prefix."_football_games g
       LEFT JOIN ".prefix."_football_teams t1
       ON g.id_team_guest = t1.id
       LEFT JOIN ".prefix."_football_teams t2
       ON g.id_team_owner = t2.id
       LEFT JOIN ".prefix."_football_championats l1
       ON g.id_championat = l1.id
       WHERE date BETWEEN '".$day_begin." ".$hour_begin.":00:00' AND '".$day_end." ".$hour_end.":00:00' AND l1.alias <> 'ukraine' ORDER BY id_ligue, date";

    return $query;
}

function queryShowMatchesUPL($day_begin, $day_end, $hour_begin, $hour_end){
    $query = "SELECT g.*, t1.name as team_guest, t1.alias as alias_guest, t2.name as team_owner, t2.alias as alias_owner, l1.name as ligue, l1.id as id_ligue, l1.alias as c_alias, l1.start as c_start, l1.finish as c_finish
       FROM ".prefix."_football_games g
       LEFT JOIN ".prefix."_football_teams t1
       ON g.id_team_guest = t1.id
       LEFT JOIN ".prefix."_football_teams t2
       ON g.id_team_owner = t2.id
       LEFT JOIN ".prefix."_football_championats l1
       ON g.id_championat = l1.id
       WHERE date BETWEEN '".$day_begin." ".$hour_begin.":00:00' AND '".$day_end." ".$hour_end.":00:00' AND l1.alias='ukraine' ORDER BY date DESC";

    return $query;
}

function prev_day($day){
    $d_yesterday = new DateTime($day);
    $d_yesterday->modify("-1 day");
    $yesterday = $d_yesterday->format("Y-m-d");
    return $yesterday;
}
function next_day($day){
    $d_tomorrow = new DateTime($day);
    $d_tomorrow->modify("+1 day");
    $tomorrow = $d_tomorrow->format("Y-m-d");
    return $tomorrow;
}

function sql_query($day_begin, $day_end, $hour_begin, $hour_end){
    $query = "SELECT g.*, t1.name as team_guest, t1.alias as alias_guest, t2.name as team_owner, t2.alias as alias_owner, l1.name as ligue, l1.id as id_ligue, l1.alias as c_alias, l1.start as c_start, l1.finish as c_finish
       FROM ".prefix."_football_games g
       LEFT JOIN ".prefix."_football_teams t1
       ON g.id_team_guest = t1.id
       LEFT JOIN ".prefix."_football_teams t2
       ON g.id_team_owner = t2.id
       LEFT JOIN ".prefix."_football_championats l1
       ON g.id_championat = l1.id
       WHERE date BETWEEN '".$day_begin." ".$hour_begin.":00:00' AND '".$day_end." ".$hour_end.":00:00' ORDER BY date";

    return $query;
}

function test_query($query){
    if(mysql_num_rows(mysql_query($query)) == 0){
        return FALSE;
    }
    else{
        return TRUE;
    }
}

function showTags($tag_name){
    global $tpl, $template, $mysql, $lang, $SYSTEM_FLAGS, $CurrentHandler, $TemplateCache;
    return include(extras_dir.'/football_stats/include/tags.php');
}

function goalScorersUkraine(){
    goalScorers('goalScorersUkraine', 'goal_scorers_ukraine', "ukraine");
}
function goalScorersChampionatLigue(){
    goalScorers('goalScorersChampionatLigue', 'goal_scorers_championsleague', "championsleague");
}
function goalScorersFrance(){
    goalScorers('goalScorersFrance', 'goal_scorers_france', "france");
}
function goalScorersItaly(){
    goalScorers('goalScorersItaly', 'goal_scorers_italy', "italy");
}
function goalScorersEuropaLeague(){
    goalScorers('goalScorersEuropaLeague', 'goal_scorers_europaleague', "europaleague");
}
function goalScorersRussia(){
    goalScorers('goalScorersRussia', 'goal_scorers_russia', "russia");
}
function goalScorersEngland(){
    goalScorers('goalScorersEngland', 'goal_scorers_england', "england");
}
function goalScorersGermany(){
    goalScorers('goalScorersGermany', 'goal_scorers_germany', "germany");
}
function goalScorersSpain(){
    goalScorers('goalScorersSpain', 'goal_scorers_spain', "spain");
}

# goal_scorers block
function goalScorers($filename, $templateName, $alias){
    global $mysql, $config, $tpl, $template, $SYSTEM_FLAGS, $lang;
    $tpath = locatePluginTemplates('plugin.goal.scorers', 'football_stats', 1, '', 'main');
	
	$cacheFileName = md5($filename).'.txt';

    if(pluginGetVariable('football_stats', 'cache')){
        $cacheData = cacheRetrieveFile($cacheFileName, pluginGetVariable('football_stats','cacheExpire'), 'football_stats');
        if ($cacheData != false) {
            $template['vars'][$templateName] = $cacheData;
            return;
        }
    }

    //print $filename." ";

    $svars['vars']['header_scorers'] = $lang['fs:scorers']." ".$lang['fs:'.$alias.'_champo'];
    $svars['vars']['alias'] = $alias;
	
    $id_champo = array();

    $add_type = "";
    if($alias == "championsleague" || $alias == "europaleague"){
        $add_type = " AND add_type <> 0";
    }

	$get_max_year_start = $mysql->select("SELECT id FROM ".prefix."_football_championats WHERE alias=\"".$alias."\" AND start=
	(SELECT MAX(start) FROM ".prefix."_football_championats WHERE alias=\"".$alias."\")".$add_type);

    foreach($get_max_year_start as $year){
		$id_champo[] = $year['id'];
	}

    $scorers_on_site = (pluginGetVariable('football_stats', 'scorers_on_site')) ?
    " LIMIT ".pluginGetVariable('football_stats', 'scorers_on_site') : "";
    $count_scorers_in_admin = (pluginGetVariable('football_stats', 'scorers_in_admin_panel')) ?
    pluginGetVariable('football_stats', 'scorers_in_admin_panel') : 20;

    for($i=0; $i<count($id_champo); $i++){
		$goal_scorers = $mysql->select("SELECT COUNT(fg.id_player) as `goals`, `id_player`
										FROM ".prefix."_football_games_goals fg
										LEFT JOIN ".prefix."_football_players p 
											ON fg.id_player = p.id
										WHERE fg.id_championat = ".$id_champo[$i]." AND fg.id_player <> -1 AND fg.auto = 0
										GROUP BY fg.id_player 
										ORDER BY goals DESC", 1);
		foreach($goal_scorers as $goal){
			if(!mysql_query("UPDATE ".prefix."_football_scorers SET goals=".$goal['goals']." WHERE id_player=".$goal['id_player']." AND id_championat=".$id_champo[$i])){
				print mysql_error();
				exit();
			}
		}
	}
	
	$goal_scorers = $mysql->select("SELECT s.*, SUM(s.goals) as count_goals, p.name as player, c.name as championat, p.id_team as team
									FROM ".prefix."_football_scorers s
									JOIN ".prefix."_football_players p ON s.id_player = p.id
									JOIN ".prefix."_football_championats c ON s.id_championat = c.id
									WHERE s.id_championat IN (".implode(',',$id_champo).") GROUP BY s.id_player
									ORDER BY count_goals DESC, number_id".$scorers_on_site);
									
	$index_goal = 0;
	foreach($goal_scorers as $goal){
		$photo_link = getSomeDataFootballStats("photo", prefix."_football_teams", " WHERE id=".$goal['team']);
		$photo_link = ($photo_link != "") ? pluginGetVariable('football_stats', 'photos.url').$photo_link : pluginGetVariable('football_stats', 'photos.url')."default.png";
		$index_goal++;
		$player_link = generatePluginLink('football_stats', 'player', array('id' => $goal['id_player'], array(), true, true));
		$svars['vars']['goal_scorers'] .= "<tr><td class='cell_goals'>".$index_goal."</td><td class='cell_goals'><img src='".$photo_link."' width='15' /></td>
		<td class='cell_goals'><a href='".$player_link."'>".$goal['player']."</a></td><td class='cell_goals'>".$goal['count_goals']."</td></tr>";
	}
    $tpl->template('plugin.goal.scorers', $tpath['plugin.goal.scorers']);
	$tpl->vars('plugin.goal.scorers', $svars);
	$template['vars'][$templateName] = $tpl->show('plugin.goal.scorers');

    if(count($goal_scorers) == 0){
        $template['vars'][$templateName] = " ";
    }


	# create cache
    if (pluginGetVariable('football_stats', 'cache')) {
	   cacheStoreFile($cacheFileName, $template['vars'][$templateName], 'football_stats');
    }
}

function getSomeDataFootballStats($field, $table, $where){
    return include(extras_dir.'/football_stats/include/get_data.php');
}

function headersTags($tag, $name){
    $headersArray = array('ukraine' => 'України',
                        'spain' => 'Іспанії');
    if(isset($headersArray[$tag])){
        return "Збірна ".$headersArray[$tag];
    }
    else{
        return $name;
    }
}


function showTagsTeam($tag_header, $tag_name){
    global $tpl, $template, $mysql, $lang, $SYSTEM_FLAGS, $CurrentHandler, $TemplateCache;
    $params['tag'] = $tag_name;
	if (isset($params['tag'])) {
		$tag = $params['tag'];
	} else {
		if (($CurrentHandler['pluginName'] == 'tags')&&
			($CurrentHandler['handlerName'] == 'tag') &&
		isset($CurrentHandler['params']['tag'])) {
			$tag = $CurrentHandler['params']['tag'];
		} else {
			$tag = $_REQUEST['tag'];
		}
	}

	$tag = str_replace(array('&', '<'), array('&amp;','&lt;'), $tag);

	/* IF no tag is specified - show cloud
	if (!$tag) {
		if (!isset($params['tag']))
			plugin_tags_cloud();
		return;
	}*/


	LoadPluginLang('tags', 'main', '', '', ':');

	//$SYSTEM_FLAGS['info']['title']['group']		= $lang['tags:header.tag.title'];
	$tpath = locatePluginTemplates(array('cloud', 'cloud.tag', 'pages', 'cloud.tag.entry'), 'tags', pluginGetVariable('tags', 'localsource'), pluginGetVariable('tags', 'skin')?pluginGetVariable('tags', 'skin'):'default');

	include_once root.'includes/news.php';
	// Search for tag in tags table
	if (!($rec = $mysql->record("select * from ".prefix."_tags where tag=".db_squote($tag)))) {
		// Unknown tag
		$entries = $lang['tags:nonews'];
	} else {
		//$SYSTEM_FLAGS['info']['title']['secure_html']	= secure_html($tag);

		// Set page display limit
		$perPage = intval(pluginGetVariable('tags', 'tpage_limit'));
		if (($perPage < 1) || ($perPage > 1000))
			$perPage = 1000;

		// Manage pagination
		if (pluginGetVariable('tags', 'tpage_paginator')) {
			$tagCount = $mysql->result("select count(*) as cnt from ".prefix."_tags_index where tagID = ".db_squote($rec['id']));
			$pagesCount = ceil($tagCount / $perPage);
			$pageNo = intval($_REQUEST['page']);
			if ($pageNo < 1)
				$pageNo = 1;

			$limit = 'limit '.(intval($pageNo - 1)*$perPage).", ".$perPage;

			// If we have more than 1 page or current page != 1, we should generate paginator
			// Load navigation bar
			templateLoadVariables(true);
			$navigations = $TemplateCache['site']['#variables']['navigation'];

			$paginationParams = array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'tags', 'handler' => 'tag'), 'xparams' => array('tag' => $tag), 'paginator' => array('page', 1, false));
			//
			$tvars = array();
			$tvars['regx']["'\[prev-link\](.*?)\[/prev-link\]'si"] = ($pageNo > 1)?str_replace('%page%',"$1",str_replace('%link%',generatePageLink($paginationParams, $pageNo - 1), $navigations['prevlink'])):'';
			$tvars['regx']["'\[next-link\](.*?)\[/next-link\]'si"] = ($pageNo < $pagesCount)?str_replace('%page%',"$1",str_replace('%link%',generatePageLink($paginationParams, $pageNo + 1), $navigations['nextlink'])):'';
			$tvars['vars']['pages'] = generatePagination($pageNo, 1, $pagesCount, 10, $paginationParams, $navigations);

			$tpl -> template('pages', $tpath['pages']);
			$tpl -> vars('pages', $tvars);
			$pages = $tpl -> show('pages');

		} else {
			$limit = 'limit '.$perPage;
			$pagesCount = 1;
		}

		foreach ($mysql->select("select n.* from ".prefix."_tags_index i left join ".prefix."_news n on n.id = i.newsID where i.tagID =".db_squote($rec['id'])." order by n.postdate desc ".$limit) as $row) {
			$entries .= news_showone(0, '', array('overrideTemplateName' => 'cloud.tag.entry', 'overrideTemplatePath' => $tpath['cloud.tag.entry'], 'emulate' => $row, 'style' => 'export', 'plugin' => 'tags'));
		}
	}
	$tvars = array ( 'vars' => array ( 'entries' => $entries, 'tag' => $tag_header, 'pages' => $pages));
	$tvars['regx']['#\[paginator\](.*?)\[\/paginator\]#is'] = ($pagesCount > 1)?'$1':'';

	// Check if we have template `tag`
	$tplName = isset($tpath['cloud.tag'])?'cloud.tag':'cloud';
	$tpl -> template($tplName, $tpath[$tplName]);
	$tpl -> vars($tplName, $tvars);
	$result = $tpl -> show($tplName);
    return $result;
}
