<?php

if (!defined('NGCMS')) die('HAL');

register_plugin_page('reputation', 'change', 'reputation_change', 0);
register_plugin_page('reputation', 'changes', 'reputation_changes', 0);
register_plugin_page('reputation', 'apply', 'reputation_apply', 0);
register_plugin_page('reputation', '', 'reputation_pp', 0);

loadPluginLibrary('uprofile', 'lib');
loadPluginLibrary('comments', 'lib');

class uReputationFilter extends p_uprofileFilter
{
	public function showProfile($userID, $SQLrow, &$tvars)
	{
		$tvars['vars']['reputation'] = reputation($userID, $SQLrow['name'], $SQLrow['reputation'], 2);
		return 1;
	}

	public function editProfileForm($userID, $SQLrow, &$tvars)
	{
		$tvars['vars']['reputation'] = reputation($userID, $SQLrow['name'], $SQLrow['reputation'], 2);
		return 1;
	}
}

register_filter('plugin.uprofile', 'reputation', new uReputationFilter);

class commentsReputationFilter extends FilterComments
{
	public function showComments($newsID, $commRec, $comnum, &$tvars)
	{
		$tvars['vars']['reputation'] = reputation($commRec['author_id'], $commRec['author'], $commRec['users_reputation'], 1);
		return 1;
	}

	public function commentsJoinFilter()
	{
		return ['users' => ['fields' => ['reputation']]];
	}
}

register_filter('comments', 'reputation', new commentsReputationFilter);

function reputation_pp()
{
	msg(['type' => 'error', 'text' => 'Упс. Ошибочка вышла!' . '<META HTTP-EQUIV="refresh" CONTENT="1;URL=/">']);
}

function reputation_change()
{
	global $tpl, $template, $userROW, $config, $mysql;

	if (!is_array($userROW)) {
		msg(['type' => 'error', 'text' => 'Гости не могут изменять репутацию!' . '<META HTTP-EQUIV="refresh" CONTENT="2;URL=' . $config['home_url'] . '">']);
		return;
	}

	if ($userROW['name'] === $_REQUEST['uname']) {
		msg(['type' => 'error', 'text' => 'Самому себе нельзя изменять репутацию!' . '<META HTTP-EQUIV="refresh" CONTENT="2;URL=' . $config['home_url'] . '">']);
		return;
	}

	$timelimit = PluginGetVariable('reputation', 'timelimit') ?? 1;
	$timetype = PluginGetVariable('reputation', 'timetype') ?? 4;

	$timelimit = match ($timetype) {
		1 => $timelimit,
		2 => $timelimit * 60,
		3 => $timelimit * 60 * 60,
		4 => $timelimit * 60 * 60 * 24,
		default => $timelimit * 60 * 60 * 24,
	};

	$db = $mysql->record("SELECT date FROM " . prefix . "_reputation WHERE (from_id = " . db_squote($userROW['id']) . " AND to_id = " . db_squote($_REQUEST['uid']) . ") ORDER BY date DESC LIMIT 0,1;");
	if (($db['date'] + $timelimit) > time()) {
		msg(['type' => 'error', 'text' => 'Еще не прошло минимальное время после последнего Вашего изменения репутации этому пользователю!<br /> Попробуйте позже.' . '<META HTTP-EQUIV="refresh" CONTENT="5;URL=' . $config['home_url'] . '">']);
		return;
	}

	$tpath = locatePluginTemplates(['change'], 'reputation', PluginGetVariable('reputation', 'localsource'));
	$lang = ['plus' => 'Повысить', 'minus' => 'Понизить'];
	$act = ['plus' => '+1', 'minus' => '-1'];

	$tvars['vars'] = [
		'changername' => $userROW['name'],
		'username' => $_REQUEST['uname'],
		'action' => $lang[$_REQUEST['act']] ?? '',
		'formurl' => generatePluginLink('reputation', 'apply'),
		'url' => $_SERVER['HTTP_REFERER'],
		'uid' => $_REQUEST['uid'],
		'act' => $act[$_REQUEST['act']] ?? '',
	];

	$tpl->template('change', $tpath['change']);
	$tpl->vars('change', $tvars);
	$template['vars']['mainblock'] = $tpl->show('change');
}

function reputation_apply()
{
	global $tpl, $template, $mysql, $userROW, $config;

	$tpath = locatePluginTemplates(['change'], 'reputation', PluginGetVariable('reputation', 'localsource'));
	$time = time();

	$timelimit = PluginGetVariable('reputation', 'timelimit') ?? 1;
	$timetype = PluginGetVariable('reputation', 'timetype') ?? 4;

	$timelimit = match ($timetype) {
		1 => $timelimit,
		2 => $timelimit * 60,
		3 => $timelimit * 60 * 60,
		4 => $timelimit * 60 * 60 * 24,
		default => $timelimit * 60 * 60 * 24,
	};

	$db = $mysql->record("SELECT date FROM " . prefix . "_reputation WHERE (from_id = " . db_squote($userROW['id']) . " AND to_id = " . db_squote($_REQUEST['uid']) . ") ORDER BY date DESC LIMIT 0,1;");
	if (($db['date'] + $timelimit) > time()) {
		msg(['type' => 'error', 'text' => 'Еще не прошло минимальное время после последнего Вашего изменения репутации этому пользователю!<br /> Попробуйте позже.' . '<META HTTP-EQUIV="refresh" CONTENT="5;URL=' . $config['home_url'] . '">']);
		return;
	}

	$mysql->query("INSERT INTO " . prefix . "_reputation (to_id, from_id, comment, date, action, url) VALUES (" . db_squote($_POST['uid']) . "," . db_squote($userROW['id']) . "," . db_squote($_POST['comment']) . "," . db_squote($time) . "," . db_squote($_POST['act']) . "," . db_squote($_POST['url']) . ");");

	if ($_POST['act'] === '+1') {
		$mysql->query("UPDATE " . uprefix . "_users SET `reputation` = `reputation`+1 WHERE `id` = " . db_squote($_POST['uid']));
	} else {
		$mysql->query("UPDATE " . uprefix . "_users SET `reputation` = `reputation`-1 WHERE `id` = " . db_squote($_POST['uid']));
	}

	msg(['type' => 'info', 'info' => 'Репутация успешно изменена!' . '<META HTTP-EQUIV="refresh" CONTENT="2;URL=' . $_POST["url"] . '">']);
}

function reputation_changes()
{
	global $tpl, $template, $mysql, $parse, $config;

	$tpath = locatePluginTemplates(['changes', 'entries'], 'reputation', PluginGetVariable('reputation', 'localsource'));
	$id = $_REQUEST['uid'];
	$count = 0;
	$db = $mysql->select("SELECT r.*, u.name FROM " . prefix . "_reputation r LEFT JOIN " . uprefix . "_users u ON r.from_id = u.id WHERE r.to_id = " . db_squote($id) . ";");
	$timeformat = PluginGetVariable('reputation', 'timeformat') ?? 'H:i:s d-m-Y';

	foreach ($db as $line) {
		$count++;
		$text = $line['comment'];
		$text = $parse->userblocks($text);
		$text = $parse->bbcodes($text);
		$text = $parse->htmlformatter($text);
		$text = $parse->smilies($text);

		$tvars['vars'] = [
			'number' => $count,
			'action' => $line['action'],
			'comment' => $text,
			'date' => LangDate($timeformat, $line['date'] + ($config['date_adjust'] * 60)),
			'url' => $line['url'],
			'user' => $line['name'],
		];

		$tpl->template('entries', $tpath['entries']);
		$tpl->vars('entries', $tvars);
		$vars .= $tpl->show('entries');
	}

	unset($tvars);
	$tvars['vars'] = [
		'entries' => $vars,
		'user' => $_REQUEST['uname'],
	];

	$tpl->template('changes', $tpath['changes']);
	$tpl->vars('changes', $tvars);
	$template['vars']['mainblock'] = $tpl->show('changes');
}

function reputation($uid, $uname, $reputation, $tplid)
{
	global $template, $tpl, $userROW;

	$tplname = 'reputation.';
	$tplname .= match ($tplid) {
		1 => 'comments',
		2 => 'uprofile',
		default => 'unknown',
	};

	$tpath = locatePluginTemplates([$tplname], 'reputation', PluginGetVariable('reputation', 'localsource'));

	$tvars = [];
	$tvars['vars'] = [
		'reputation' => $reputation,
		'+url' => generatePluginLink('reputation', 'change', ['uid' => $uid, 'uname' => $uname, 'act' => 'plus']),
		'-url' => generatePluginLink('reputation', 'change', ['uid' => $uid, 'uname' => $uname, 'act' => 'minus']),
		'url' => generatePluginLink('reputation', 'changes', ['uid' => $uid, 'uname' => $uname]),
	];

	if (!is_array($userROW) || ($userROW['name'] === $uname)) {
		$tvars['regx']["'\[can-change\](.*?)\[/can-change\]'si"] = '';
	} else {
		$tvars['vars']['[can-change]'] = "";
		$tvars['vars']['[/can-change]'] = "";
	}

	$tpl->template($tplname, $tpath[$tplname]);
	$tpl->vars($tplname, $tvars);
	return $tpl->show($tplname);
}