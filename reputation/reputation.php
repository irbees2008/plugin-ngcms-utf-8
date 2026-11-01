<?php

/*
 * Reputation for NextGeneration CMS (http://ngcms.ru/)
 * Copyright (C) 2010 Alexandr N. Pidlisniy (LinMas) (http://linmas.org.ua)
 * http://linmas.org.ua
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 */

if (!defined('NGCMS')) die ('HAL');

register_plugin_page('reputation','change','reputation_change', 0);
register_plugin_page('reputation','changes','reputation_changes', 0);
register_plugin_page('reputation','apply','reputation_apply', 0);
register_plugin_page('reputation','','reputation_pp', 0);

loadPluginLibrary('uprofile', 'lib');
loadPluginLibrary('comments','lib');

class uReputationFilter extends p_uprofileFilter {
	function showProfile($userID, $SQLrow, &$tvars) {
		$tvars['vars']['reputation'] = reputation($userID,$SQLrow['name'],$SQLrow['reputation'],2);
		return 1;
	}
	function editProfileForm($userID, $SQLrow, &$tvars) {
		$tvars['vars']['reputation'] = reputation($userID,$SQLrow['name'],$SQLrow['reputation'],2);
		return 1;
	}
}

register_filter('plugin.uprofile','reputation', new uReputationFilter);

class commentsReputationFilter extends FilterComments {
	function showComments($newsID, $commRec, $comnum, &$tvars) { 
		$tvars['vars']['reputation'] = reputation($commRec['author_id'],$commRec['author'],$commRec['users_reputation'],1);
		return 1; 
	}

	function commentsJoinFilter() {
		return array('users' => array('fields' =>array('reputation')));
	}
}

register_filter('comments','reputation', new commentsReputationFilter);

function reputation_pp() {
	msg(array("type" => "error", "text" => "Упс. Ошибочка вышла!".'<META HTTP-EQUIV="refresh" CONTENT="1;URL=/">'));
}

function reputation_change() {
	global $tpl, $template, $userROW, $config, $mysql;
	if (!is_array($userROW)) {
		msg(array("type" => "error", "text" => "Гости не могут изменять репутацию!".'<META HTTP-EQUIV="refresh" CONTENT="2;URL='.$config['home_url'].'">'));
		return;
	}
	if ($userROW['name']==$_REQUEST['uname']) {
		msg(array("type" => "error", "text" => "Самому себе нельзя изменять репутацию!".'<META HTTP-EQUIV="refresh" CONTENT="2;URL='.$config['home_url'].'">'));
		return;
	} 
	

	$timelimit = PluginGetVariable('reputation','timelimit')?PluginGetVariable('reputation','timelimit'):1;
	$timetype = PluginGetVariable('reputation','timetype')?PluginGetVariable('reputation','timetype'):4;
	switch ($timetype) {
            case 1: $timelimit = $timelimit; break;
            case 2: $timelimit = $timelimit*60; break;
            case 3: $timelimit = $timelimit*60*60; break;
            case 4: $timelimit = $timelimit*60*60*24; break;
        }
	$db = $mysql->record("select date from ".prefix."_reputation where (from_id = ".db_squote($userROW['id'])." and to_id=".db_squote($_REQUEST['uid']).") ORDER BY date DESC LIMIT 0,1;");
	if ($db['date']+$timelimit>time()) {
		msg(array("type" => "error", "text" => "Еще не прошло минимальное время после последнего Вашего изменения репутации этому пользователю!<br /> Попробуйте позже.".'<META HTTP-EQUIV="refresh" CONTENT="5;URL='.$config['home_url'].'">'));
		return;
	}


	$tpath = locatePluginTemplates(array('change'), 'reputation', PluginGetVariable('reputation', 'localsource'));
	$lang['plus']='Повысить';
	$lang['minus']='Понизить';
	$act['plus']='+1';
	$act['minus']='-1';
	$tvars['vars'] = array ( 
		'changername' 	=> $userROW['name'],
		'username' 	=> $_REQUEST['uname'],
		'action' 	=> $lang[$_REQUEST['act']],
		'formurl' 	=> generatePluginLink('reputation', 'apply'),
		'url'		=> $_SERVER['HTTP_REFERER'],
		'uid'		=> $_REQUEST['uid'],
		'act'		=> $act[$_REQUEST['act']],
	);
	$tpl -> template('change', $tpath['change']);
	$tpl -> vars('change', $tvars);
	$template['vars']['mainblock'] = $tpl -> show('change');
}

function reputation_apply() {
	global $tpl, $template, $mysql, $userROW, $config;
	$tpath = locatePluginTemplates(array('change'), 'reputation', PluginGetVariable('reputation', 'localsource'));
	$time = time();
	
	$timelimit = PluginGetVariable('reputation','timelimit')?PluginGetVariable('reputation','timelimit'):1;
	$timetype = PluginGetVariable('reputation','timetype')?PluginGetVariable('reputation','timetype'):4;
	switch ($timetype) {
            case 1: $timelimit = $timelimit; break;
            case 2: $timelimit = $timelimit*60; break;
            case 3: $timelimit = $timelimit*60*60; break;
            case 4: $timelimit = $timelimit*60*60*24; break;
        }
	$db = $mysql->record("select date from ".prefix."_reputation where (from_id = ".db_squote($userROW['id'])." and to_id=".db_squote($_REQUEST['uid']).") ORDER BY date DESC LIMIT 0,1;");
	if ($db['date']+$timelimit>time()) {
		msg(array("type" => "error", "text" => "Еще не прошло минимальное время после последнего Вашего изменения репутации этому пользователю!<br /> Попробуйте позже.".'<META HTTP-EQUIV="refresh" CONTENT="5;URL='.$config['home_url'].'">'));
		return;
	}
	
	$mysql->query("INSERT INTO ".prefix."_reputation (to_id, from_id, comment, date, action, url) VALUES (".db_squote($_POST['uid']).",".db_squote($userROW['id']).",".db_squote($_POST['comment']).",".db_squote($time).",".db_squote($_POST['act']).",".db_squote($_POST['url']).");");
	if ($_POST['act']=='+1')
		{$mysql->query("UPDATE ".uprefix."_users SET `reputation` = `reputation`+1 WHERE `id` = ".db_squote($_POST['uid']));}
	else
		{$mysql->query("UPDATE ".uprefix."_users SET `reputation` = `reputation`-1 WHERE `id` = ".db_squote($_POST['uid']));}
	msg(array("type" => "info", "info" => "Репутация успешно изменена!".'<META HTTP-EQUIV="refresh" CONTENT="2;URL='.$_POST["url"].'">'));
}

function reputation_changes() {
	global $tpl, $template, $mysql, $parse, $config;
	$tpath = locatePluginTemplates(array('changes','entries'), 'reputation', PluginGetVariable('reputation', 'localsource'));
	$id = $_REQUEST['uid'];
	$count=0;
	$db = $mysql ->select("select r.*, u.name from ".prefix."_reputation r left join ".uprefix."_users u on r.from_id = u.id where r.to_id=".db_squote($id).";");
	$timeformat = pluginGetVariable('reputation','timeformat')?PluginGetVariable('reputation','timeformat'):'H:i:s d-m-Y';
	foreach ($db as $line) {
		$count++;
		$text=$line['comment'];
		$text = $parse -> userblocks($text);
		$text = $parse -> bbcodes($text);		
		$text = $parse -> htmlformatter($text);
		$text = $parse -> smilies($text);
		$tvars['vars'] = array (
			'number' 	=> $count,
			'action' 	=> $line['action'],
			'comment' 	=> $text,
			'date'	 	=> LangDate($timeformat,$line['date'] + ($config['date_adjust'] * 60)),
			'url'	 	=> $line['url'],
			'user' 		=> $line['name'],
		);
		$tpl -> template('entries', $tpath['entries']);
		$tpl -> vars('entries', $tvars);
		$vars .= $tpl -> show('entries');
	}
	unset($tvars);
	$tvars['vars'] = array (
		'entries' => $vars,
		'user' => $_REQUEST['uname'],
	);
	$tpl -> template('changes', $tpath['changes']);
	$tpl -> vars('changes', $tvars);
	$template['vars']['mainblock'] = $tpl -> show('changes');
}

function reputation($uid,$uname,$reputation,$tplid) {
	global $template, $tpl, $userROW;
	$tplname='reputation.';
	if ($tplid==1) {$tplname.='comments';}
	elseif ($tplid==2) {$tplname.='uprofile';}
	$tpath = locatePluginTemplates(array($tplname), 'reputation', PluginGetVariable('reputation', 'localsource'));
	$tvars = array();
	$tvars['vars'] = array ( 
		'reputation' => $reputation,
		'+url' => generatePluginLink('reputation', 'change', array('uid' => $uid, 'uname' => $uname, 'act' => 'plus')),
		'-url' => generatePluginLink('reputation', 'change', array('uid' => $uid, 'uname' => $uname, 'act' => 'minus')),
		'url' => generatePluginLink('reputation', 'changes', array('uid' => $uid, 'uname' => $uname)),
	);
	
	if ((!is_array($userROW)) || ($userROW['name']==$uname)) {
		$tvars['regx']["'\[can-change\](.*?)\[/can-change\]'si"] = '';
	} else {
		$tvars['vars']['[can-change]'] = "";
		$tvars['vars']['[/can-change]'] = "";
	} 
	$tpl -> template($tplname, $tpath[$tplname]);
	$tpl -> vars($tplname, $tvars);
	return $tpl -> show($tplname);
}

?>