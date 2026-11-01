<?php

# protect against hack attempts
if (!defined('NGCMS'))die ('Galaxy in danger');

register_plugin_page('boxers', 'profile', 'showBoxerProfile');
register_plugin_page('boxers', 'ratings', 'showRatingsPP');
register_plugin_page('boxers', '', 'showAllBoxers');

add_act('index', 'showRatings');

LoadPluginLang('boxers', 'main', '', '', ':');

function MakeWeightList($options, $name, $selected = "FALSE") {
	global $lang;
	
	$output = "<select size=1 onchange='reload()' name='".$name."' id='".$name."'>";
	$output .= "<option value='all'>".$lang['boxers:all']."</option>";
	foreach ($options as $k)
		$output .= "<option value=\"".$k['id']."\"".(($selected==$k['id'])?" selected=\"selected\"":'').">".$k['name']."</option>";
	$output .= "</select>";

	return $output;
}

function MakeOrgList($options, $name, $selected = "FALSE") {
	global $lang;
	
	$output = "<select size=1 onchange='reload()' name='".$name."' id='".$name."'>";
	$output .= "<option value='all'>".$lang['boxers:all']."</option>";
	foreach ($options as $k)
		$output .= "<option value=\"".$k['db_name']."\"".(($selected==$k['db_name'])?" selected=\"selected\"":'').">".$k['name']."</option>";
	$output .= "</select>";

	return $output;
}

function showBoxerProfile($params){
	global $mysql, $template, $tpl, $SYSTEM_FLAGS, $config, $lang;
	
	$name = db_squote($params['name']);
	
	$boxer = $mysql->record('SELECT b.*, w.name as weight FROM '.prefix.'_boxers b LEFT JOIN '.prefix.'_boxers_weight_class w ON b.weight_class = w.id WHERE translit_name = '.$name, 1);
	
	if(!$boxer){
		header('Location: '.$config['home_url']);
		return;
	}
	
	$SYSTEM_FLAGS['info']['title']['group'] = $boxer['full_name'];

	$tpath = locatePluginTemplates(array('profile', 'plugin.fights'), 'boxers', 1);

	$flags = parse_ini_file(extras_dir.'/boxers/flags.ini');
	
	$orgs = array();
	$orgs = unserialize(pluginGetVariable('boxers', 'organization'));
	
	$profile = array(	'full_name', 'biography', 'birthday', 'weight', 'country', 'photo', 'translit_name', 'wins', 'defeats', 'draws', 'debut', 'height', 'reach',
						'stance', 'boxrecid', 'www', 'nickname', 'hometown', 'knockouts');
	foreach($profile as $row)
		$tvars['vars'][$row] = $boxer[$row];
	
	$extra_position = 	array('—' => '—', '-6' => 'Чемпіон', '-5' => 'СуперЧемпіон', '-4' => 'Обєднаний Чемпіон', '-3' => 'Регулярний Чемпіон', '-2' => 'Тимчасовий Чемпіон', '-1' => 'Офіційний претендент');
		
	$tvars['vars']['ratings_position'] = '';	
	foreach($orgs as $org){	
		if($boxer['rating_'.$org['db_name']]){
			if(array_key_exists($boxer['rating_'.$org['db_name']], $extra_position))
				$boxer['rating_'.$org['db_name']] = $extra_position[$boxer['rating_'.$org['db_name']]];
			$tvars['vars']['ratings_position'] .= str_replace(array('{position}', '{link}', '{name}'), array($boxer['rating_'.$org['db_name']], generatePluginLink('boxers', 'ratings', array(), array('weights' => $boxer['weight_class'], 'orgs' => $org['db_name']), false, true), $org['name']), $lang['boxers:ratings_position']);	
		}
	}

	$tvars['vars']['summary'] = $boxer['wins'] + $boxer['defeats'] + $boxer['draws'];	
	$tvars['vars']['flag'] = '/engine/plugins/boxers/flags/'.$flags[$boxer['country']];
	$tvars['vars']['photo'] = pluginGetVariable('boxers', 'photos.url').$boxer['photo'];
	
	$fights = $mysql->select('SELECT f.*, b.translit_name, b.full_name, b.country FROM '.prefix.'_boxers_fights f LEFT JOIN '.prefix.'_boxers b ON f.id_with = b.id WHERE id_who = '.$boxer['id'].' ORDER BY date DESC', 1);

	if($fights){ 
		$tpl->template('plugin.fights', $tpath['plugin.fights']);
	}
	
	$tvars['vars']['fights'] = '';
	$i = count($fights);
	
	foreach($fights as $fight){
		$ttvars['vars']['number'] = $i--;
		$ttvars['vars']['date'] = $fight['date'];
		$ttvars['vars']['boxer_link'] = generatePluginLink('boxers', 'profile', array('name' => $fight['translit_name']), array(), false, true);
		$ttvars['vars']['flag'] = '/engine/plugins/boxers/flags/'.$flags[$fight['country']];
		$ttvars['vars']['boxer_name'] = $fight['full_name'];
		$ttvars['vars']['news'] = $fight['news'] ? '<a  target="_blank" href="'.$fight['news'].'">Детальніше</a>' : '';
		$ttvars['vars']['del_link'] = '/engine/admin.php?mod=extra-config&amp;plugin=boxers&amp;action=delfight&amp;id_who='.$fight['id_who'].'&amp;id_with='.$fight['id_with'].'&amp;date='.$fight['date'];
		$ttvars['vars']['edit_link'] = '/engine/admin.php?mod=extra-config&amp;plugin=boxers&amp;action=editfight&amp;id_who='.$fight['id_who'].'&amp;id_with='.$fight['id_with'].'&amp;date='.$fight['date'];
		$ttvars['vars']['photo'] = $fight['photo'] ? '<a  target="_blank" href="'.$fight['photo'].'">Фото</a>' : '';
		$ttvars['vars']['video'] = $fight['video'] ? '<a  target="_blank" href="'.$fight['video'].'">Відео</a>' : '';
		$tpl->vars('plugin.fights', $ttvars);
		$tvars['vars']['fights'] .= $tpl->show('plugin.fights');
	}
	
	$tpl->template('profile', $tpath['profile']);
	$tpl->vars('profile', $tvars);
	$template['vars']['mainblock'] = $tpl->show('profile');
}

function showRatings(){
	global $mysql, $tpl, $template;
	
	$count = pluginGetVariable('boxers', 'ratings.count');
	
	$flags = parse_ini_file(extras_dir.'/boxers/flags.ini');
	
	$rnd = rand(1, $count);
	
	for($i=1; $i<=$count; $i++) {
		
		$currentVar = 'boxer_rating_'.$i;
		
		$cacheFileName = md5($currentVar).'.txt';
		
		if (pluginGetVariable('boxers', 'cache')) {
			$cacheData = cacheRetrieveFile($cacheFileName, pluginGetVariable('boxers','cacheExpire'), 'boxers');
			if ($cacheData != false) {
				# We got data from cache. Return it and stop
				$template['vars'][$currentVar] = $cacheData;
				if($rnd == $i) $template['vars']['boxer_random'] = $template['vars'][$currentVar];
				continue;
			}
		}
			
		$tpath = locatePluginTemplates(array('entries', 'rating'), 'boxers', 1, '', $currentVar);
		
		$where = 'rating_'.pluginGetVariable('boxers', 'orgs_'.$i).' AND weight_class = '.pluginGetVariable('boxers', 'weight_'.$i);
		$limit = ' LIMIT '.pluginGetVariable('boxers', 'rating.count_'.$i);
		$orderby = ' ORDER BY rating_'.pluginGetVariable('boxers', 'orgs_'.$i);
		
		$boxers = $mysql->select('SELECT b.*, w.name as weight FROM '.prefix.'_boxers b LEFT JOIN '.prefix.'_boxers_weight_class w ON b.weight_class = w.id WHERE '.$where.$orderby.$limit, 1);
		
		$result = ''; $k = 0;
		
		foreach($boxers as $boxer){
			
			$tvars['vars']['position'] = $boxer['rating_'.pluginGetVariable('boxers', 'orgs_'.$i)];
			
			if($tvars['vars']['position'] > 0){
				if($tvars['vars']['position'] - $k > 1)
					for($j=$k+1; $j<$tvars['vars']['position']; $j++)
					$result .= ''.$j.'Вакантно'.'<br>';
				$k = $tvars['vars']['position'];
			}
			
			if($tvars['vars']['position'] == -6) $flag = true;
			
			$extra_position = 	array('—' => '—', '-6' => 'Чемпіон', '-5' => 'СуперЧемпіон', '-4' => 'Обєднаний Чемпіон', '-3' => 'Регулярний Чемпіон', '-2' => 'Тимчасовий Чемпіон', '-1' => 'Офіційний претендент');
				
			if(array_key_exists($tvars['vars']['position'], $extra_position))
				$tvars['vars']['position'] = $extra_position[$tvars['vars']['position']];
			
			$profile = array(	'full_name', 'biography', 'birthday', 'weight', 'country', 'photo', 'translit_name', 'wins', 'defeats', 'draws', 'debut', 'height', 'reach',
						'stance', 'boxrecid', 'www', 'nickname');
			foreach($profile as $row)
				$tvars['vars'][$row] = $boxer[$row];
												
			$tvars['vars']['flag'] = '/engine/plugins/boxers/flags/'.$flags[$boxer['country']];
			$tvars['vars']['photo'] = pluginGetVariable('boxers', 'photos.url').$boxer['photo'];
			$tvars['vars']['profile_link'] = generatePluginLink('boxers', 'profile', array('name' => $boxer['translit_name']), array(), false, true);
	
			$tpl -> template('entries', $tpath['entries']);
			$tpl -> vars('entries', $tvars);
			$result .= $tpl -> show('entries');
		}
		
		if(!$flag) $result = 'Чемпіон вакантно'.$result; 
		
		unset($tvars);
		
		/*$orgs = array();
		$orgs = unserialize(pluginGetVariable('boxers', 'organization'));

		foreach($orgs as $row)
			if($row['db_name'] == pluginGetVariable('boxers', 'orgs_'.$i)) {
				$tvars['vars']['rating_name'] = $row['name'];
				break;
		}*/
	
	    $tvars['vars']['entries'] = $result;

		$tpl -> template('rating', $tpath['rating']);
		$tpl -> vars('rating', $tvars);
		
		$template['vars'][$currentVar] = $tpl->show('rating');	
		
		if($rnd == $i) $template['vars']['boxer_random'] = $template['vars'][$currentVar];
		
		# create cache
		if (pluginGetVariable('boxers','cache')) {
			cacheStoreFile($cacheFileName, $template['vars'][$currentVar], 'boxers');
		}
	}
}

function showRatingsPP(){
	global $tpl, $template, $mysql, $lang;
	
	$tpath = locatePluginTemplates(array('plugin.ratings', 'plugin.rating.entries.all.one', 'plugin.rating.all.one', 'plugin.rating.all.all.entries', 'plugin.rating.all.all'), 'boxers', 1);
	
	$orgs = array();
	$orgs = unserialize(pluginGetVariable('boxers', 'organization'));
	
	$weights = $mysql->select('SELECT * FROM '.prefix.'_boxers_weight_class', 1);
	
	$history = array('-1' => '—'); $h = array();
	if(file_get_contents(extras_dir.'/boxers/history.txt'))
		$h = unserialize(file_get_contents(extras_dir.'/boxers/history.txt')); 	
	$history += $h;
	unset($h);
			
	$org_get = $_GET['orgs'] ? $_GET['orgs'] : 'all';
	$weight_get = $_GET['weights'] ? intval($_GET['weights']) : 'all'; 
	$history_get =  isset($_GET['history']) ? intval($_GET['history']) : -1;
	if(!array_key_exists($history_get, $history))
		$history_get = -1;
	
	$k = 0;
	
	if(($org_get == 'all' && $weight_get == 'all')){
	
		$db_rating = array();
		
		if($history_get == -1){
			foreach($orgs as $org){
				array_push($db_rating, 'rating_'.$org['db_name'].' = -6');
			}
			$db_rating = implode(' OR ', $db_rating);
			$boxers = $mysql->select('SELECT * FROM '.prefix.'_boxers WHERE '.$db_rating.$orderby);
		}
		else {
			foreach($orgs as $org){
				array_push($db_rating, 'h.rating_'.$org['db_name'].' = -6');
			}
			$db_rating = implode(' OR ', $db_rating);
			$profileB = array('b.full_name', 'b.biography', 'b.birthday', 'b.country', 'b.photo', 'b.translit_name', 'b.wins', 'b.defeats', 'b.draws', 'b.debut', 'b.height', 'b.reach',
						'b.stance', 'b.boxrecid', 'b.www', 'b.nickname', 'b.id');
			$boxers = $mysql->select('SELECT h.*, h.weight as weight_class, '.implode(', ', $profileB).' FROM '.prefix.'_boxers_ratings_history h LEFT JOIN '.prefix.'_boxers b ON h.id_boxers = b.id WHERE ('.$db_rating.') AND date = '.db_squote($history[$history_get]));
		} 
		
		foreach($weights as $weight){			
			foreach($orgs as $org){
				$flag_boxer = false;
				$boxxer = array();
				foreach($boxers as $boxer){
					if($boxer['weight_class'] == $weight['id'] && $boxer['rating_'.$org['db_name']] == '-6'){
						$flag_boxer = true;
						$boxxer = $boxer;
						break;
					}
				}
				
				$tvars['vars']['full_name'] = $boxxer['full_name'];
				$tvars['vars']['profile_link'] = generatePluginLink('boxers', 'profile', array('name' => $boxxer['translit_name']), array(), false, true);
				
				$tpl -> template('plugin.rating.all.all.entries', $tpath['plugin.rating.all.all.entries']);
				$tpl -> vars('plugin.rating.all.all.entries', $tvars);
				
				$flag_boxer ? $ttvars['vars'][$org['db_name'].'_'.$weight['id']] =  $tpl->show('plugin.rating.all.all.entries') : $ttvars['vars'][$org['db_name'].'_'.$weight['id']] = $lang['boxers:empty.all.all'];
			}
		}
		
		$tpl -> template('plugin.rating.all.all', $tpath['plugin.rating.all.all']);
		$tpl -> vars('plugin.rating.all.all', $ttvars);
		$ratings = $tpl->show('plugin.rating.all.all');
	}

	if(($org_get == 'all' && $weight_get != 'all')){
		foreach($orgs as $org){
			$orderby = ' ORDER BY rating_'.$org['db_name'];
			if($history_get == -1)
				$boxers = $mysql->select('SELECT * FROM '.prefix.'_boxers WHERE rating_'.$org['db_name'].' AND weight_class = '.$weight_get.$orderby);
			else {
				$profileB = array('b.full_name', 'b.biography', 'b.birthday', 'b.country', 'b.photo', 'b.translit_name', 'b.wins', 'b.defeats', 'b.draws', 'b.debut', 'b.height', 'b.reach',
						'b.stance', 'b.boxrecid', 'b.www', 'b.nickname', 'b.id');
				$boxers = $mysql->select('SELECT h.*, '.implode(', ', $profileB).' FROM '.prefix.'_boxers_ratings_history h LEFT JOIN '.prefix.'_boxers b ON h.id_boxers = b.id WHERE h.rating_'.$org['db_name'].' AND weight = '.$weight_get.' AND date = '.db_squote($history[$history_get]).$orderby);
			}

			$ttvars['vars']['entries'] = ''; 
			
			foreach($boxers as $boxer){
				$flags = parse_ini_file(extras_dir.'/boxers/flags.ini');
	
				$tvars['vars']['position'] = $boxer['rating_'.$org['db_name']];
					
				if($tvars['vars']['position'] > 0){
					if($tvars['vars']['position'] - $k > 1)
						for($j=$k+1; $j<$tvars['vars']['position']; $j++)
						$ttvars['vars']['entries'] .= str_replace('{position}', $j, $lang['boxers:empty']);
					$k = $tvars['vars']['position'];
				}
			
				if($tvars['vars']['position'] == -6) $flag = true;
			
				$profile = array(	'full_name', 'biography', 'birthday', 'weight', 'country', 'photo', 'translit_name', 'wins', 'defeats', 'draws', 'debut', 'height', 'reach',
						'stance', 'boxrecid', 'www', 'nickname');
				foreach($profile as $row)
					$tvars['vars'][$row] = $boxer[$row];
						
				$extra_position = 	array('—' => '—', '-6' => 'Чемпіон', '-5' => 'СуперЧемпіон', '-4' => 'Обєднаний Чемпіон', '-3' => 'Регулярний Чемпіон', '-2' => 'Тимчасовий Чемпіон', '-1' => 'Офіційний претендент');
				
				if(array_key_exists($tvars['vars']['position'], $extra_position))
					$tvars['vars']['position'] = $extra_position[$tvars['vars']['position']];
				
				$tvars['vars']['flag'] = '/engine/plugins/boxers/flags/'.$flags[$boxer['country']];
				$tvars['vars']['photo'] = pluginGetVariable('boxers', 'photos.url').$boxer['photo'];
				$tvars['vars']['profile_link'] = generatePluginLink('boxers', 'profile', array('name' => $boxer['translit_name']), array(), false, true);
				
				$tpl -> template('plugin.rating.entries.all.one', $tpath['plugin.rating.entries.all.one']);
				$tpl -> vars('plugin.rating.entries.all.one', $tvars);
				$ttvars['vars']['entries'] .= $tpl->show('plugin.rating.entries.all.one');
			}
				
			if(!$flag) $ttvars['vars']['entries'] = 'Чемпіон вакантно'.$ttvars['vars']['entries']; 
			
			$ttvars['vars']['name'] = $org['name'];
			$tpl -> template('plugin.rating.all.one', $tpath['plugin.rating.all.one']);
			$tpl -> vars('plugin.rating.all.one', $ttvars);
			$ratings .= $tpl -> show('plugin.rating.all.one');
			
		}
	}
	
	if(($org_get != 'all' && $weight_get == 'all')){
		$orderby = ' ORDER BY rating_'.$org_get;
		foreach($weights as $weight){
			
			if($history_get == -1)
				$boxers = $mysql->select('SELECT * FROM '.prefix.'_boxers WHERE rating_'.$org_get.' AND weight_class = '.$weight['id'].$orderby);
			else {
				$profileB = array('b.full_name', 'b.biography', 'b.birthday', 'b.country', 'b.photo', 'b.translit_name', 'b.wins', 'b.defeats', 'b.draws', 'b.debut', 'b.height', 'b.reach',
						'b.stance', 'b.boxrecid', 'b.www', 'b.nickname', 'b.id');
				$boxers = $mysql->select('SELECT h.*, '.implode(', ', $profileB).' FROM '.prefix.'_boxers_ratings_history h LEFT JOIN '.prefix.'_boxers b ON h.id_boxers = b.id WHERE h.rating_'.$org_get.' AND h.weight = '.$weight['id'].' AND date = '.db_squote($history[$history_get]).$orderby);
			}
			
			$ttvars['vars']['entries'] = '';
			
			foreach($boxers as $boxer){
				$flags = parse_ini_file(extras_dir.'/boxers/flags.ini');
	
				$tvars['vars']['position'] = $boxer['rating_'.$org_get];
					
				if($tvars['vars']['position'] > 0){
					if($tvars['vars']['position'] - $k > 1)
						for($j=$k+1; $j<$tvars['vars']['position']; $j++)
						$ttvars['vars']['entries'] .= str_replace('{position}', $j, $lang['boxers:empty']);
					$k = $tvars['vars']['position'];
				}
			
				if($tvars['vars']['position'] == -6) $flag = true;
				
				$profile = array(	'full_name', 'biography', 'birthday', 'weight', 'country', 'photo', 'translit_name', 'wins', 'defeats', 'draws', 'debut', 'height', 'reach',
						'stance', 'boxrecid', 'www', 'nickname');
				foreach($profile as $row)
					$tvars['vars'][$row] = $boxer[$row];
				
				$extra_position = 	array('—' => '—', '-6' => 'Чемпіон', '-5' => 'СуперЧемпіон', '-4' => 'Обєднаний Чемпіон', '-3' => 'Регулярний Чемпіон', '-2' => 'Тимчасовий Чемпіон', '-1' => 'Офіційний претендент');
				
				if(array_key_exists($tvars['vars']['position'], $extra_position))
					$tvars['vars']['position'] = $extra_position[$tvars['vars']['position']];

				$tvars['vars']['flag'] = '/engine/plugins/boxers/flags/'.$flags[$boxer['country']];
				$tvars['vars']['photo'] = pluginGetVariable('boxers', 'photos.url').$boxer['photo'];
				$tvars['vars']['profile_link'] = generatePluginLink('boxers', 'profile', array('name' => $boxer['translit_name']), array(), false, true);
				
				$tpl -> template('plugin.rating.entries.all.one', $tpath['plugin.rating.entries.all.one']);
				$tpl -> vars('plugin.rating.entries.all.one', $tvars);
				$ttvars['vars']['entries'] .= $tpl->show('plugin.rating.entries.all.one');
			}
			
			if(!$flag) $ttvars['vars']['entries'] = 'Чемпіон вакантно'.$ttvars['vars']['entries']; 
			
			$ttvars['vars']['name'] = $weight['name'];
			$tpl -> template('plugin.rating.all.one', $tpath['plugin.rating.all.one']);
			$tpl -> vars('plugin.rating.all.one', $ttvars);
			$ratings .= $tpl -> show('plugin.rating.all.one');
		}
	}
	
	if(($org_get != 'all' && $weight_get != 'all')){
			$orderby = ' ORDER BY rating_'.$org_get;
						
			if($history_get == -1)
				$boxers = $mysql->select('SELECT * FROM '.prefix.'_boxers WHERE rating_'.$org_get.' AND weight_class = '.$weight_get.$orderby);
			else {
				$profileB = array('b.full_name', 'b.biography', 'b.birthday', 'b.country', 'b.photo', 'b.translit_name', 'b.wins', 'b.defeats', 'b.draws', 'b.debut', 'b.height', 'b.reach',
						'b.stance', 'b.boxrecid', 'b.www', 'b.nickname', 'b.id');
				$boxers = $mysql->select('SELECT h.*, '.implode(', ', $profileB).' FROM '.prefix.'_boxers_ratings_history h LEFT JOIN '.prefix.'_boxers b ON h.id_boxers = b.id WHERE h.rating_'.$org_get.' AND h.weight = '.$weight_get.' AND date = '.db_squote($history[$history_get]).$orderby);
			}
			
			$ttvars['vars']['entries'] = '';
			
			foreach($boxers as $boxer){
			
				$tvars['vars']['position'] = $boxer['rating_'.$org_get];
					
				if($tvars['vars']['position'] > 0){
					if($tvars['vars']['position'] - $k > 1)
						for($j=$k+1; $j<$tvars['vars']['position']; $j++)
						$ttvars['vars']['entries'] .= str_replace('{position}', $j, $lang['boxers:empty']);
					$k = $tvars['vars']['position'];
				}
				
				if($tvars['vars']['position'] == -6) $flag = true;
				
				$flags = parse_ini_file(extras_dir.'/boxers/flags.ini');
	
				$profile = array(	'full_name', 'biography', 'birthday', 'weight', 'country', 'photo', 'translit_name', 'wins', 'defeats', 'draws', 'debut', 'height', 'reach',
						'stance', 'boxrecid', 'www', 'nickname');
				foreach($profile as $row)
					$tvars['vars'][$row] = $boxer[$row];
				
				$extra_position = 	array('—' => '—', '-6' => '<h4>Чемпіон</h4>', '-5' => '<h4>СуперЧемпіон</h4>', '-4' => '<h4>Обєднаний Чемпіон</h4>', '-3' => '<h4>Регулярний Чемпіон</h4>', '-2' => '<h4>Тимчасовий Чемпіон</h4>', '-1' => '<h4>Офіційний претендент</h4>');
				
				if(array_key_exists($tvars['vars']['position'], $extra_position))
					$tvars['vars']['position'] = $extra_position[$tvars['vars']['position']];

				$tvars['vars']['flag'] = '/engine/plugins/boxers/flags/'.$flags[$boxer['country']];
				$tvars['vars']['photo'] = pluginGetVariable('boxers', 'photos.url').$boxer['photo'];
				$tvars['vars']['profile_link'] = generatePluginLink('boxers', 'profile', array('name' => $boxer['translit_name']), array(), false, true);
				
				$tpl -> template('plugin.rating.entries.all.one', $tpath['plugin.rating.entries.all.one']);
				$tpl -> vars('plugin.rating.entries.all.one', $tvars);
				$ttvars['vars']['entries'] .= $tpl->show('plugin.rating.entries.all.one');
			}
			
			if(!$flag) $ttvars['vars']['entries'] = 'Чемпіон вакантно'.$ttvars['vars']['entries']; 
			
			$ttvars['vars']['name'] = '';
			$tpl -> template('plugin.rating.all.one', $tpath['plugin.rating.all.one']);
			$tpl -> vars('plugin.rating.all.one', $ttvars);
			$ratings .= $tpl -> show('plugin.rating.all.one');
	}

		
	unset($tvars);

	$tvars['vars']['orgs'] = MakeOrgList($orgs, 'orgs', $_GET['orgs']);
	$tvars['vars']['weights'] = MakeWeightList($weights, 'weights', $_GET['weights']);
	$tvars['vars']['history'] = MakeDropDown($history, "history\" id=\"history\" onchange=\"reload()", $history_get);
	$tvars['vars']['ratings'] = $ratings;
	
	$tpl -> template('plugin.ratings', $tpath['plugin.ratings']);
	$tpl -> vars('plugin.ratings', $tvars);
		
	$template['vars']['mainblock'] = $tpl->show('plugin.ratings');	
	
}

function showAllBoxers(){

	global $mysql, $tpl, $template;
	
	$letters = $mysql->select('SELECT SUBSTRING(full_name, 1, 1) AS letter FROM '.prefix.'_boxers GROUP BY letter ORDER BY letter', 1);
	$boxers = $mysql->select('SELECT full_name, translit_name, SUBSTRING(full_name,1,1) AS letter FROM '.prefix.'_boxers', 1);
	
	foreach($letters as $letter)
        $list[$letter['letter']] = array();
		
	foreach($boxers as $boxer)
		if( array_key_exists($boxer['letter'], $list) )
			$list[$boxer['letter']][] = $boxer;
	
	$tpath = locatePluginTemplates(array('plugin.letter', 'plugin.alphabet'), 'boxers', 1);
		
	foreach($list as $letter => $row){
		$tvars['vars']['letter'] = $letter;
		$tvars['vars']['boxers'] = '';
		foreach($row as $key => $value){
			$tvars['vars']['boxers'] .= '<a  target="_blank" href="'.generatePluginLink('boxers', 'profile', array('name' => $value['translit_name']), array(), false, true).'">'.$value['full_name'].'</a><br />';
		}
		$tpl->template('plugin.letter', $tpath['plugin.letter']);
		$tpl->vars('plugin.letter', $tvars);
		$ttvars['vars']['entries'] .= $tpl->show('plugin.letter');
	}	
	
	$tpl->template('plugin.alphabet', $tpath['plugin.alphabet']);
	$tpl->vars('plugin.alphabet', $ttvars);
	$template['vars']['mainblock'] .= $tpl->show('plugin.alphabet');
}