<?php
/*
 * Plugin "Private message" for NextGeneration CMS (http://ngcms.ru/)
 * Copyright (C) 2010-2011 Alexey N. Zhukov (http://digitalplace.ru), Alexey Zinchenko
 * http://digitalplace.ru
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
# protect against hack attempts
if (!defined('NGCMS')) die('Galaxy in danger');
register_plugin_page('pm', '', 'pm', 0);
loadPluginLang('pm', 'main', '', '', ':');
add_act('usermenu', 'new_pm');
LoadPluginLibrary('pm', 'lib');
LoadPluginLibrary('uprofile', 'lib');
// Где-то в начале кода, после загрузки настроек плагина
$maxMessages = intval(pluginGetVariable('pm', 'max_messages'));
if ($maxMessages <= 0) {
	$maxMessages = 0; // 0 означает "без лимита"
}
define('INBOX_LINK', generatePluginLink('pm', null, ($_GET['location'] ? array('action' => $_GET['location']) : array())));
/*
  fill variables in usermenu.tpl
  + {{ p.pm.pm_unread }} - кол-во новых входящих сообщений
  + {{ p.pm.pm_all }} - общее кол-во входящих сообщений
  + {{ p.pm.link }} - URL на страницу со входящими сообщениями
*/
function checkPMLimits()
{
	global $mysql, $userROW, $maxMessages, $lang;
	if ($maxMessages <= 0) return true; // Лимит отключён
	// Проверка для исходящих сообщений
	$currentOutbox = $mysql->result("SELECT COUNT(*) FROM " . prefix . "_pm WHERE from_id = " . db_squote($userROW['id']) . " AND folder='outbox'");
	if ($currentOutbox >= $maxMessages) {
		msg(array(
			"type" => "error",
			"text" => $lang['pm:msge_limit'],
			"info" => str_replace(array('{current}', '{max}'), array($currentOutbox, $maxMessages), $lang['pm:msgi_limit']) . $lang['pm:html_back']
		));
		return false;
	}
	// Проверка для входящих сообщений (если нужно)
	$currentInbox = $mysql->result("SELECT COUNT(*) FROM " . prefix . "_pm WHERE to_id = " . db_squote($userROW['id']) . " AND folder='inbox'");
	if ($currentInbox >= $maxMessages) {
		msg(array(
			"type" => "error",
			"text" => $lang['pm:msge_inbox_limit'],
			"info" => str_replace(array('{current}', '{max}'), array($currentInbox, $maxMessages), $lang['pm:msgi_inbox_limit']) . $lang['pm:html_back']
		));
		return false;
	}
	return true;
}
class PMCoreFilter extends CoreFilter
{
	function showUserMenu(&$tVars)
	{
		global $mysql, $userROW, $lang;
		if (!$userROW['id']) return 0;
		# if 'sync' then fill varaiables without SQL query from user's table
		if ($userROW['pm_sync']) {
			$tVars['p']['pm']['pm_unread'] = !$userROW['pm_unread'] ? $userROW['pm_unread'] :  $userROW['pm_unread'];
			$tVars['p']['pm']['pm_all'] = $userROW['pm_all'];
			$tVars['p']['pm']['link'] = generatePluginLink('pm', null);
			return;
		}
		$notViewed = 0;
		$viewed = 0;
		foreach ($mysql->select("SELECT COUNT(viewed) AS pm_viewed, viewed FROM " . prefix . "_pm WHERE (`to_id` = " . db_squote($userROW['id']) . " AND `folder` = 'inbox') GROUP BY viewed") as $row) {
			if ($row['viewed'] == '1') {
				$viewed = $row['pm_viewed'];
				continue;
			}
			if ($row['viewed'] == '0') {
				$notViewed = $row['pm_viewed'];
			}
		}
		$viewed += $notViewed;
		# update pm counters
		$mysql->query('UPDATE ' . uprefix . '_users SET `pm_sync` = 1, `pm_all` = ' . $viewed . ', `pm_unread` = ' . $notViewed . ' WHERE `id` = ' . db_squote($userROW['id']));
		if ($notViewed != 0) $notViewed =  $notViewed;
		$tVars['p']['pm']['pm_unread'] = $notViewed;
		$tVars['p']['pm']['pm_all'] = $viewed;
		$tVars['p']['pm']['link'] = generatePluginLink('pm', null);
	}
}
# show inbox messages list
function pm_inbox()
{
	global $mysql, $config, $lang, $userROW, $tpl, $template, $TemplateCache, $twig, $maxMessages, $PHP_SELF;
	if ($maxMessages > 0) {
		$currentInbox = $mysql->result("SELECT COUNT(*) FROM " . prefix . "_pm WHERE to_id = " . db_squote($userROW['id']) . " AND folder='inbox'");
		if ($currentInbox >= $maxMessages) {
			msg(array(
				"type" => "error",
				"text" => $lang['pm:msge_inbox_limit'],
				"info" => str_replace(array('{current}', '{max}'), array($currentInbox, $maxMessages), $lang['pm:msgi_inbox_limit'])
			));
			return;
		}
	}
	$tpath = locatePluginTemplates(array('inbox'), 'pm', intval(pluginGetVariable('pm', 'localsource')));
	# messages per page
	$msg_per_page = intval(pluginGetVariable('pm', 'msg_per_page')) <= 0 ? 10 : intval(pluginGetVariable('pm', 'msg_per_page'));
	$page = 1;
	if (isset($_REQUEST['page'])) $page = intval($_REQUEST['page']);
	# range of messages
	$limit = 'LIMIT ' . ($page - 1) * $msg_per_page . ', ' . $msg_per_page;
	# count all inbox messages
	$countMsg = $mysql->result("SELECT COUNT(*) FROM " . prefix . "_pm WHERE `to_id` = " . db_squote($userROW['id']) . " AND `folder` = 'inbox'");
	foreach ($mysql->select("SELECT pm.*, u.id as uid, u.name as uname FROM " . prefix . "_pm pm LEFT JOIN " . uprefix . "_users u ON pm.from_id=u.id WHERE pm.to_id = " . db_squote($userROW['id']) . " AND folder='inbox' ORDER BY viewed ASC, date DESC " . $limit) as $row) {
		$author = '';
		$avatar = array(0, '', ''); // По умолчанию - нет аватарки
		if ($row['from_id'] && $row['uid']) {
			$alink = checkLinkAvailable('uprofile', 'show') ?
				generateLink('uprofile', 'show', array('name' => $row['uname'], 'id' => $row['uid'])) :
				generateLink('core', 'plugin', array('plugin' => 'uprofile', 'handler' => 'show'), array('name' => $row['uname'], 'id' => $row['uid']));
			$author = '<a href="' . $alink . '">' . $row['uname'] . '</a>';
			// Получаем аватарку отправителя
			$userData = $mysql->record("SELECT * FROM " . uprefix . "_users WHERE id = " . db_squote($row['from_id']));
			$avatar = userGetAvatar($userData);
		} else if ($row['from_id']) {
			$author = $lang['pm:udeleted'];
		} else {
			$author = $lang['pm:messaging'];
		}
		$tEntries[] = array(
			'php_self' => $PHP_SELF,
			'pmid'     => $row['id'],
			'pmdate'   => $row['date'],
			'subject'  => $row['subject'],
			'link'     => $author,
			'viewed'   => $row['viewed'],
			'avatar'   => $avatar[1], // URL аватарки
			'readURL'  => generatePluginLink('pm', null, array('action' => 'read'), array('pmid' => $row['id'], 'location' => 'inbox')),
			'flags'    => array(
				'hasAvatar' => $avatar[0], // Флаг наличия аватарки
			),
		);
	}
	$maxMessages = intval(pluginGetVariable('pm', 'max_messages'));
	$currentCount = $mysql->result("SELECT COUNT(*) FROM " . prefix . "_pm WHERE to_id = " . db_squote($userROW['id']) . " AND folder='inbox'");
	// One-time flash flag from session (prefer session over query)
	$flashSent = 0;
	if (session_status() == PHP_SESSION_NONE) {
		@session_start();
	}
	if (!empty($_SESSION['flash_pm_sent'])) {
		$flashSent = 1;
		unset($_SESSION['flash_pm_sent']);
	}
	// read flash for deletion result
	$flashDel = null;
	if (session_status() == PHP_SESSION_NONE) {
		@session_start();
	}
	if (isset($_SESSION['flash_pm_del'])) {
		$flashDel = $_SESSION['flash_pm_del'];
		unset($_SESSION['flash_pm_del']);
	}

	$tVars = array(
		'php_self' => $PHP_SELF,
		'entries'  => $tEntries,
		'tpl_url'  => tpl_url,
		'pm_inbox_link' => generatePluginLink('pm', null),
		'pm_outbox_link' => generatePluginLink('pm', null, array('action' => 'outbox')),
		'pm_set_link' => generatePluginLink('pm', null, array('action' => 'set')),
		'pm_read_link' => generatePluginLink('pm', null, array('action' => 'read')),
		'pm_del_link' => generatePluginLink('pm', null, array('action' => 'delete')),
		'pm_write_link' => generatePluginLink('pm', null, array('action' => 'write')),
		'max_messages' => $maxMessages,
		'current_messages' => $currentCount,
		'user' => array('id' => $userROW['id']),
		'sent' => $flashSent ? 1 : (isset($_GET['sent']) ? intval($_GET['sent']) : 0),
		'delFlash' => $flashDel,
	);
	$pages_count = ceil($countMsg / $msg_per_page);
	$paginationParams = array('pluginName' => 'pm', 'params' => array(), 'xparams' => array(), 'paginator' => array('page', 0, false));
	# generate pagination if count of pages > 1
	if ($pages_count > 1) {
		templateLoadVariables(true);
		$navigations = $TemplateCache['site']['#variables']['navigation'];
		$tVars['pagination'] = generatePagination($page, 1, $pages_count, 9, $paginationParams, $navigations);
	} else $tVars['pagination'] = '';
	$xt = $twig->loadTemplate($tpath['inbox'] . 'inbox.tpl');
	$template['vars']['mainblock'] = $xt->render($tVars);
}
# show outbox messages list
function pm_outbox()
{
	global $mysql, $lang, $userROW, $tpl, $template, $TemplateCache, $twig, $maxMessages, $PHP_SELF;
	$tpath = locatePluginTemplates(array('outbox'), 'pm', intval(pluginGetVariable('pm', 'localsource')));
	# messages per page
	$msg_per_page = intval(pluginGetVariable('pm', 'msg_per_page')) <= 0 ? 10 : intval(pluginGetVariable('pm', 'msg_per_page'));
	$page = 1;
	if (isset($_REQUEST['page'])) $page = intval($_REQUEST['page']);
	# range of messages
	$limit = 'LIMIT ' . ($page - 1) * $msg_per_page . ', ' . $msg_per_page;
	# count all outbox messages
	$countMsg = $mysql->result("SELECT COUNT(*) FROM " . prefix . "_pm WHERE `from_id` = " . db_squote($userROW['id']) . " AND `folder` = 'outbox'");
	foreach ($mysql->select("SELECT pm.*, u.id as uid, u.name as uname FROM " . prefix . "_pm pm LEFT JOIN " . uprefix . "_users u ON pm.to_id=u.id WHERE pm.from_id = " . db_squote($userROW['id']) . " AND folder='outbox' ORDER BY date DESC " . $limit) as $row) {
		$author = '';
		$avatar = array(0, '', ''); // По умолчанию - нет аватарки
		if ($row['to_id'] && $row['uid']) {
			$alink = checkLinkAvailable('uprofile', 'show') ?
				generateLink('uprofile', 'show', array('name' => $row['uname'], 'id' => $row['uid'])) :
				generateLink('core', 'plugin', array('plugin' => 'uprofile', 'handler' => 'show'), array('name' => $row['uname'], 'id' => $row['uid']));
			$author = '<a href="' . $alink . '">' . $row['uname'] . '</a>';
			// Получаем аватарку получателя
			$userData = $mysql->record("SELECT * FROM " . uprefix . "_users WHERE id = " . db_squote($row['to_id']));
			$avatar = userGetAvatar($userData);
		} else if ($row['to_id']) {
			$author = $lang['pm:udeleted'];
		} else {
			$author = $lang['pm:messaging'];
		}
		$tEntries[] = array(
			'php_self' => $PHP_SELF,
			'pmid'     => $row['id'],
			'pmdate'   => $row['date'],
			'subject'  => $row['subject'],
			'link'     => $author,
			'avatar'   => $avatar[1], // URL аватарки
			'readURL'  => generatePluginLink('pm', null, array('action' => 'read'), array('pmid' => $row['id'], 'location' => 'outbox')),
			'flags'    => array(
				'hasAvatar' => $avatar[0], // Флаг наличия аватарки
			),
		);
	}
	$maxMessages = intval(pluginGetVariable('pm', 'max_messages'));
	// Для исходящих (outbox) - считаем только отправленные
	$currentCount = $mysql->result("SELECT COUNT(*) FROM " . prefix . "_pm WHERE from_id = " . db_squote($userROW['id']) . " AND folder='outbox'");
	// read flash for deletion result
	$flashDel = null;
	if (session_status() == PHP_SESSION_NONE) {
		@session_start();
	}
	if (isset($_SESSION['flash_pm_del'])) {
		$flashDel = $_SESSION['flash_pm_del'];
		unset($_SESSION['flash_pm_del']);
	}

	$tVars = array(
		'php_self' => $PHP_SELF,
		'entries'  => $tEntries,
		'tpl_url'  => tpl_url,
		'pm_inbox_link' => generatePluginLink('pm', null),
		'pm_outbox_link' => generatePluginLink('pm', null, array('action' => 'outbox')),
		'pm_set_link' => generatePluginLink('pm', null, array('action' => 'set')),
		'pm_read_link' => generatePluginLink('pm', null, array('action' => 'read')),
		'pm_del_link' => generatePluginLink('pm', null, array('action' => 'delete')),
		'pm_write_link' => generatePluginLink('pm', null, array('action' => 'write')),
		'max_messages' => $maxMessages, // Добавлено!
		'current_messages' => $currentCount,
		'user' => array('id' => $userROW['id']), // Для доступа в шаблоне
		'delFlash' => $flashDel,
	);
	$pages_count = ceil($countMsg / $msg_per_page);
	$paginationParams = array('pluginName' => 'pm', 'params' => array(), 'xparams' => array('action' => 'outbox'), 'paginator' => array('page', 0, false));
	# generate pagination if count of pages > 1
	if ($pages_count > 1) {
		templateLoadVariables(true);
		$navigations = $TemplateCache['site']['#variables']['navigation'];
		$tVars['pagination'] = generatePagination($page, 1, $pages_count, 9, $paginationParams, $navigations);
	} else $tVars['pagination'] = '';
	$xt = $twig->loadTemplate($tpath['outbox'] . 'outbox.tpl');
	$template['vars']['mainblock'] = $xt->render($tVars);
}
# show read message form
function pm_read()
{
	global $mysql, $config, $lang, $userROW, $tpl, $mod, $parse, $template, $twig, $PHP_SELF;
	$tpath = locatePluginTemplates(array('read'), 'pm', intval(pluginGetVariable('pm', 'localsource')));
	$pmid = intval($_REQUEST['pmid']);
	if ($row = $mysql->record("SELECT * FROM " . prefix . "_pm WHERE id = " . db_squote($pmid) . " AND ((`from_id`=" . db_squote($userROW['id']) . " AND `folder`='outbox') OR (`to_id`=" . db_squote($userROW['id']) . ") AND `folder`='inbox')")) {
		$author = '';
		$authorID = $row['folder'] == 'inbox' ? $row['from_id'] : $row['to_id'];
		$avatar = array(0, '', ''); // По умолчанию - нет аватарки
		$row_user = $mysql->record("SELECT id, name, avatar FROM " . uprefix . "_users WHERE id = " . $authorID);
		if ($row_user['id']) {
			$alink = checkLinkAvailable('uprofile', 'show') ?
				generateLink('uprofile', 'show', array('name' => $row_user['name'], 'id' => $row_user['id'])) :
				generateLink('core', 'plugin', array('plugin' => 'uprofile', 'handler' => 'show'), array('name' => $row_user['name'], 'id' => $row_user['id']));
			$author = '<a href="' . $alink . '">' . $row_user['name'] . '</a>';
			// Получаем аватарку
			$avatar = userGetAvatar($row_user);
		} else {
			$author = $lang['pm:udeleted'];
		}
		$tVars = array(
			'php_self' => $PHP_SELF,
			'pmid'     => $row['id'],
			'subject'  => $row['subject'],
			'location' => $row['folder'],
			'pmdate'   => $row['date'],
			'content'  => $parse->htmlformatter($parse->smilies($parse->bbcodes($row['message']))),
			'author'   => $author,
			'avatar'   => $avatar[1], // URL аватарки
			'flags'    => array(
				'hasAvatar' => $avatar[0], // Флаг наличия аватарки
			),
			'ifinbox'  => ($row['folder'] == 'inbox') ? 1 : 0,
			'pm_inbox_link' => generatePluginLink('pm', null),
			'pm_outbox_link' => generatePluginLink('pm', null, array('action' => 'outbox')),
			'pm_set_link' => generatePluginLink('pm', null, array('action' => 'set')),
			'delURL'   => generatePluginLink('pm', null, array('action' => 'delete'), array('pmid' => $row['id'], 'location' => $row['folder'])),
			'replyURL' => generatePluginLink('pm', null, array('action' => 'reply'), array('pmid' => $row['id'])),
		);
		$xt = $twig->loadTemplate($tpath['read'] . 'read.tpl');
		$template['vars']['mainblock'] = $xt->render($tVars);
		# update pm counters
		if ((!$row['viewed']) && ($row['to_id'] == $userROW['id']) && ($row['folder'] == 'inbox')) {
			$mysql->query("UPDATE " . prefix . "_pm SET `viewed` = '1' WHERE `id` = " . db_squote($row['id']));
			$mysql->query("UPDATE " . uprefix . "_users SET `pm_unread` = `pm_unread` - 1 WHERE `id` = " . db_squote($userROW['id']));
		}
	} else {
		msg(array("type" => "error", "text" => $lang['pm:msge_bad'] . str_replace('{url}', INBOX_LINK, $lang['pm:html_reload'])));
	}
}
# delete message(s)
function pm_delete()
{
	global $mysql, $config, $lang, $userROW, $tpl;
	$selected_pm = isset($_REQUEST['selected_pm']) ? $_REQUEST['selected_pm'] : [];
	$pmid = intval($_REQUEST['pmid']);
	$location = isset($_REQUEST['location']) && in_array($_REQUEST['location'], ['inbox', 'outbox']) ? $_REQUEST['location'] : 'inbox';

	// prepare redirect target
	$redirectUrl = ($location == 'outbox') ? generatePluginLink('pm', null, array('action' => 'outbox')) : generatePluginLink('pm', null);

	if (!$pmid) {
		if (!$selected_pm || !is_array($selected_pm) || count($selected_pm) == 0) {
			if (session_status() == PHP_SESSION_NONE) {
				@session_start();
			}
			$_SESSION['flash_pm_del'] = ['ok' => 0, 'message' => $lang['pm:msge_select']];
			if (!headers_sent()) {
				header('Location: ' . $redirectUrl);
				exit;
			}
			// Fallback
			if ($location == 'outbox') {
				pm_outbox();
			} else {
				pm_inbox();
			}
			return;
		}
		$ids = array_map('intval', $selected_pm);
		$mysql->query("DELETE FROM " . prefix . "_pm WHERE `id` IN (" . join(',', $ids) . ") AND ((`from_id`=" . db_squote($userROW['id']) . " AND `folder`='outbox') OR (`to_id`=" . db_squote($userROW['id']) . ") AND `folder`='inbox')");
		$mysql->query("UPDATE " . uprefix . "_users SET `pm_sync` = 0 WHERE `id` = " . db_squote($userROW['id']));
		if (session_status() == PHP_SESSION_NONE) {
			@session_start();
		}
		$_SESSION['flash_pm_del'] = ['ok' => 1, 'count' => count($ids)];
		if (!headers_sent()) {
			header('Location: ' . $redirectUrl);
			exit;
		}
		// Fallback
		if ($location == 'outbox') {
			pm_outbox();
		} else {
			pm_inbox();
		}
		return;
	} else {
		$row = $mysql->record("SELECT id, viewed, folder FROM " . prefix . "_pm WHERE `id`=" . db_squote($pmid) . " AND ((`from_id`=" . db_squote($userROW['id']) . " AND `folder`='outbox') OR (`to_id`=" . db_squote($userROW['id']) . ") AND `folder`='inbox')");
		if ($row) {
			$mysql->query("DELETE FROM " . prefix . "_pm WHERE `id`=" . db_squote($pmid));
			#update pm counter
			if ($row['folder'] == 'inbox') {
				if ($row['viewed'])
					$mysql->query("UPDATE " . uprefix . "_users SET `pm_all` = `pm_all` - 1 WHERE `id` = " . db_squote($userROW['id']));
				else
					$mysql->query("UPDATE " . uprefix . "_users SET `pm_all` = `pm_all` - 1, `pm_unread` = `pm_unread` - 1 WHERE `id` = " . db_squote($userROW['id']));
			}
			if (session_status() == PHP_SESSION_NONE) {
				@session_start();
			}
			$_SESSION['flash_pm_del'] = ['ok' => 1, 'count' => 1];
		} else {
			if (session_status() == PHP_SESSION_NONE) {
				@session_start();
			}
			$_SESSION['flash_pm_del'] = ['ok' => 0, 'message' => $lang['pm:msge_bad_del']];
		}
		// Redirect back to list based on detected folder
		$redir = ($row && ($row['folder'] == 'outbox')) ? generatePluginLink('pm', null, array('action' => 'outbox')) : generatePluginLink('pm', null);
		if (!headers_sent()) {
			header('Location: ' . $redir);
			exit;
		}
		// Fallback
		if ($row && $row['folder'] == 'outbox') {
			pm_outbox();
		} else {
			pm_inbox();
		}
		return;
	}
}
# show write message form
function pm_write()
{
	global $config, $lang, $tpl, $template, $twig, $PHP_SELF;
	$tpath = locatePluginTemplates(array('write'), 'pm', intval(pluginGetVariable('pm', 'localsource')));
	$tVars = array(
		'php_self'  => $PHP_SELF,
		'username'  => trim($_REQUEST['name']),
		'title' => isset($_REQUEST['title']) ? $_REQUEST['title'] : '',
		'quicktags' => BBCodes("'pm_content'"),
		'smilies' => ($config['use_smilies'] == "1") ? InsertSmilies('', 10, 'pm_content') : '',
		'pm_inbox_link' => generatePluginLink('pm', null),
		'pm_outbox_link' => generatePluginLink('pm', null, array('action' => 'outbox')),
		'pm_set_link' => generatePluginLink('pm', null, array('action' => 'set')),
		'pm_send_link' => generatePluginLink('pm', null, array('action' => 'send')),
		'skins_url' => skins_url,
	);
	$xt = $twig->loadTemplate($tpath['write'] . 'write.tpl');
	$template['vars']['mainblock'] = $xt->render($tVars);
}
# send message
function pm_send()
{
	global $mysql, $config, $lang, $userROW, $maxMessages;
	// Получаем актуальное значение лимита
	$maxMessages = intval(pluginGetVariable('pm', 'max_messages'));
	// Проверяем лимит исходящих сообщений
	$currentOutbox = $mysql->result("SELECT COUNT(*) FROM " . prefix . "_pm
                                   WHERE from_id = " . db_squote($userROW['id']) . "
                                   AND folder='outbox'");
	if ($maxMessages > 0 && $currentOutbox >= $maxMessages) {
		msg(array(
			"type" => "error",
			"text" => $lang['pm:msge_limit'],
			"info" => str_replace(
				array('{current}', '{max}'),
				array($currentOutbox, $maxMessages),
				$lang['pm:msgi_limit']
			) . $lang['pm:html_back']
		));
		return;
	}
	$pm = new pm();
	$status = $pm->sendMsg($_POST['to_username'], $userROW['id'], $_POST['title'], $_POST['content'], false, $_POST['saveoutbox']);
	# if all right
	if (!$status) {
		// Set session-based flash and redirect to clean inbox URL
		if (session_status() == PHP_SESSION_NONE) {
			@session_start();
		}
		$_SESSION['flash_pm_sent'] = 1;
		$redirectUrl = generatePluginLink('pm', null);
		if (!headers_sent()) {
			header('Location: ' . $redirectUrl);
			exit;
		}
		// Fallback: no redirect possible (headers already sent) -> render inbox with flag
		$_GET['sent'] = 1;
		pm_inbox();
		return 0;
	}
	# if some error
	switch ($status) {
		case -1:
			msg(array(
				"type" => "error",
				"text" => $lang['pm:msge_title'],
				"info" => str_replace('{length}', pluginGetVariable('pm', 'title_length'), $lang['pm:msgi_title']) .
					$lang['pm:html_back']
			));
			break;
		case -2:
			msg(array(
				"type" => "error",
				"text" => $lang['pm:msge_title'],
				"info" => $lang['pm:msgi_no_title'] .
					$lang['pm:html_back']
			));
			break;
		case -3:
			msg(array(
				"type" => "error",
				"text" => $lang['pm:msge_message'],
				"info" => str_replace('{length}', pluginGetVariable('pm', 'message_length'), $lang['pm:msgi_message']) .
					$lang['pm:html_back']
			));
			break;
		case -4:
			msg(array(
				"type" => "error",
				"text" => $lang['pm:msge_message'],
				"info" => $lang['pm:msgi_no_message'] .
					$lang['pm:html_back']
			));
			break;
		case -5:
			msg(array(
				"type" => "error",
				"text" => $lang['pm:msge_nouser'],
				"info" => $lang['pm:msgi_nouser'] .
					$lang['pm:html_back']
			));
			break;
	}
}
# show reply form
function pm_reply()
{
	global $mysql, $config, $lang, $userROW, $tpl, $parse, $template, $maxMessages, $twig, $PHP_SELF;
	// Проверка лимита перед ответом
	$maxMessages = intval(pluginGetVariable('pm', 'max_messages'));
	$currentOutbox = $mysql->result("SELECT COUNT(*) FROM " . prefix . "_pm
									WHERE from_id = " . db_squote($userROW['id']) . "
									AND folder='outbox'");
	if ($maxMessages > 0 && $currentOutbox >= $maxMessages) {
		msg(array(
			"type" => "error",
			"text" => $lang['pm:msge_limit'],
			"info" => str_replace(
				array('{current}', '{max}'),
				array($currentOutbox, $maxMessages),
				$lang['pm:msgi_limit']
			) . $lang['pm:html_back']
		));
		return;
	}
	$tpath = locatePluginTemplates(array('reply'), 'pm', intval(pluginGetVariable('pm', 'localsource')));
	$pmid = $_REQUEST['pmid'];
	$save = $_REQUEST['saveoutbox'];
	if ($row = $mysql->record("SELECT * FROM " . prefix . "_pm WHERE id = " . db_squote($pmid) . " AND (to_id = " . db_squote($userROW['id']) . " OR from_id=" . db_squote($userROW['id']) . ")")) {
		if ($row['folder'] == 'outbox') {
			msg(array("type" => "error", "text" => $lang['pm:msge_notreply'] . $lang['pm:html_back']));
			return 0;
		}
		if (!$row['from_id']) {
			msg(array("type" => "error", "text" => $lang['pm:msge_reply'] . $lang['pm:html_back']));
			return;
		}
		$tVars = array(
			'php_self'    => $PHP_SELF,
			'pmid'        => $row['id'],
			'title'       => 'Re:' . $row['subject'],
			'to_username' => $row['from_id'],
			'quicktags'   => BBCodes("'pm_content'"),
			'smilies' => ($config['use_smilies'] == "1") ? InsertSmilies('', 10, 'pm_content') : '',
			'pm_inbox_link' => generatePluginLink('pm', null),
			'pm_outbox_link' => generatePluginLink('pm', null, array('action' => 'outbox')),
			'pm_set_link' => generatePluginLink('pm', null, array('action' => 'set')),
			'pm_send_link' => generatePluginLink('pm', null, array('action' => 'send')),
		);
		$xt = $twig->loadTemplate($tpath['reply'] . 'reply.tpl');
		$template['vars']['mainblock'] = $xt->render($tVars);
	} else {
		msg(array("type" => "error", "text" => $lang['pm:msge_bad'] . $lang['pm:html_back']));
	}
}
# user settings
function pm_set()
{
	global $userROW, $template, $tpl, $mysql, $twig, $PHP_SELF;
	$checked = $userROW['pm_email'];
	if (isset($_POST['check'])) {
		if ($_POST['email']) {
			$mysql->query('UPDATE ' . uprefix . '_users SET pm_email = 1 WHERE id = ' . $userROW['id']);
			$checked = true;
		} else {
			$mysql->query('UPDATE ' . uprefix . '_users SET pm_email = 0 WHERE id = ' . $userROW['id']);
			$checked = false;
		}
	}
	$tpath = locatePluginTemplates(array('set'), 'pm', intval(pluginGetVariable('pm', 'localsource')));
	$tVars = array(
		'php_self' => $PHP_SELF,
		'checked'  => $checked ? 'checked="checked"' : '',
		'pm_inbox_link' => generatePluginLink('pm', null),
		'pm_outbox_link' => generatePluginLink('pm', null, array('action' => 'outbox')),
		'pm_set_link' => generatePluginLink('pm', null, array('action' => 'set')),
	);
	$xt = $twig->loadTemplate($tpath['set'] . 'set.tpl');
	$template['vars']['mainblock'] = $xt->render($tVars);
}
function pm()
{
	global $userROW, $template, $lang, $SYSTEM_FLAGS;
	$SYSTEM_FLAGS['info']['title']['group'] = $lang['pm:pm'];
	if (!$userROW['id']) {
		msg(array("type" => "info", "info" => $lang['pm:err.noAuthorization']));;
		return 1;
	}
	// Toast уведомления реализуются на клиенте через /lib/notify.js
	$tpath = locatePluginTemplates(array(':pm.css'), 'pm', intval(pluginGetVariable('pm', 'localsource')));
	register_stylesheet($tpath['url::pm.css'] . '/pm.css');
	switch ($_REQUEST['action']) {
		case "read":
			pm_read();
			break;
		case "reply":
			// Проверка лимитов для ответов
			if (!checkPMLimits()) {
				return;
			}
			pm_reply();
			break;
		case "send":
			pm_send();
			break;
		case "write":
			pm_write();
			break;
		case "delete":
			pm_delete();
			break;
		case "outbox":
			pm_outbox();
			break;
		case "set":
			pm_set();
			break;
		default:
			pm_inbox();
	}
	return 0;
}
register_filter('core.userMenu', 'pm', new PMCoreFilter);
// Функция для интеграции с админкой - подсчет новых сообщений
function new_pm()
{
	global $mysql, $userROW, $lang, $template, $config;
	if (!is_array($userROW) || !$userROW['id']) {
		return '';
	}
	// Получаем количество непрочитанных сообщений
	$newpm = $mysql->result("SELECT COUNT(*) FROM " . $config['prefix'] . "_pm WHERE to_id = " . intval($userROW['id']) . " AND folder='inbox' AND viewed = '0'");
	// Формируем текст для отображения
	$newpmText = ($newpm > 0) ? $newpm . ' новых сообщений' : 'Нет новых сообщений';
	// Передаем переменные в шаблон
	$template['vars']['newpm'] = $newpm;
	$template['vars']['newpmText'] = $newpmText;

	// Встраиваем клиентский скрипт показа notify-тоста через {{ htmlvars }} в <head>
	// Делаем это только если есть непрочитанные сообщения
	if (intval($newpm) > 0) {
		$inboxURL = generatePluginLink('pm', null);
		$script = "<script>document.addEventListener('DOMContentLoaded',function(){try{if(!window.showToast){return;}var unread=" . intval($newpm) . ";var KEY='ng_pm_unread';var prev=parseInt(sessionStorage.getItem(KEY)||'0',10);if(!prev||prev!==unread){var link='<a href=\"" . $inboxURL . "\">Открыть входящие</a>';window.showToast('У вас '+unread+' непрочитанных сообщений. '+link,{type:'info',title:'Личные сообщения'});sessionStorage.setItem(KEY,String(unread));}}catch(e){}});</script>";
		if (!isset($template['vars']['htmlvars'])) {
			$template['vars']['htmlvars'] = '';
		}
		$template['vars']['htmlvars'] .= $script;
	}
	return $newpmText;
}
