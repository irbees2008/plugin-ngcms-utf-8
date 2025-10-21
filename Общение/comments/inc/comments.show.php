<?php
//
// Copyright (C) 2006-2011 Next Generation CMS (http://ngcms.ru/)
// Name: comments.show.php
// Description: Routines for showing comments
// Author: Vitaly Ponomarev, Alexey Zinchenko
//
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
//
// Show comments for a news
// $newsID - [required] ID of the news for that comments should be showed
// $commID - [optional] ID of comment for showing in case if we just added it
// $commDisplayNum - [optional] num that is showed in 'show comment' template
// $callingParams
//		'plugin'  => if is called from plugin - ID of plugin
//		'overrideTemplateName' => alternative template for display
//		'overrideTemplatePath' => alternative path for searching of template
//		'limitStart' => order comment no to start (for pagination)
//		'limitCount' => number of comments to show (for pagination)
//		'outprint'	 => flag: if set, output will be returned, elsewhere - will be added to mainblock
//		'total'		=> total number of comments in this news
function comments_show($newsID, $commID = 0, $commDisplayNum = 0, $callingParams = array()) {

	global $mysql, $tpl, $template, $config, $userROW, $parse, $lang, $PFILTERS, $TemplateCache;
	// Preload template configuration variables
	templateLoadVariables();
	// Use default <noavatar> file
	// - Check if noavatar is defined on template level
	$tplVars = $TemplateCache['site']['#variables'];
	$noAvatarURL = (isset($tplVars['configuration']) && is_array($tplVars['configuration']) && isset($tplVars['configuration']['noAvatarImage']) && $tplVars['configuration']['noAvatarImage']) ? (tpl_url . "/" . $tplVars['configuration']['noAvatarImage']) : (avatars_url . "/noavatar.gif");
	// -> desired template path
	$templatePath = ($callingParams['overrideTemplatePath']) ? $callingParams['overrideTemplatePath'] : (root . '/plugins/comments/tpl/');
	// -> desired template
	if ($callingParams['overrideTemplateName']) {
		$templateName = $callingParams['overrideTemplateName'];
	} else {
		$templateName = 'comments.show';
	}
	$usePluginTemplate = file_exists($templatePath . $templateName . '.tpl');
	if (!$usePluginTemplate) {
		$templatePath = tpl_site . 'plugins/comments';
		$tpl->template($templateName, $templatePath);
	}
	$joinFilter = array();
	if ($config['use_avatars']) {
		$joinFilter = array('users' => array('fields' => array('avatar')));
	}
	// RUN interceptors
	if (isset($PFILTERS['comments']) && is_array($PFILTERS['comments']))
		foreach ($PFILTERS['comments'] as $k => $v) {
			$xcfg = $v->commentsJoinFilter();
			if (is_array($xcfg) && isset($xcfg['users']) && isset($xcfg['users']['fields']) && is_array($xcfg['users']['fields'])) {
				$joinFilter['users']['fields'] = array_unique(array_merge($joinFilter['users']['fields'], $xcfg['users']['fields']));
			}
		}
	//print "ARRAY CFG: <pre>".var_export($joinFilter, true)."</pre>";
	function _cs_am($k) {

		return 'u.' . $k . ' as `users_' . $k . '`';
	}

	if (isset($joinFilter['users']) && isset($joinFilter['users']['fields']) && is_array($joinFilter['users']['fields']) && (count($joinFilter['users']['fields']) > 0)) {
		$sql = "select c.*, " .
			join(", ", array_map('_cs_am', $joinFilter['users']['fields'])) .
			' from ' . prefix . '_comments c' .
			' left join ' . uprefix . '_users u on c.author_id = u.id where c.post=' . db_squote($newsID) . ' and c.moderated=1' . ($commID ? (" and c.id=" . db_squote($commID)) : '');
	} else {
		$sql = "select c.* from " . prefix . "_comments c WHERE c.post=" . db_squote($newsID) . ' and c.moderated=1' . ($commID ? (" and c.id=" . db_squote($commID)) : '');
	}
	$sql .= " order by c.id" . (pluginGetVariable('comments', 'backorder') ? ' desc' : '');
	// Comments counter
	$comnum = 0;
	// Check if we need to use limits
	$limitStart = isset($callingParams['limitStart']) ? intval($callingParams['limitStart']) : 0;
	$limitCount = isset($callingParams['limitCount']) ? intval($callingParams['limitCount']) : 0;
	if ($limitStart || $limitCount) {
		$sql .= ' limit ' . $limitStart . ", " . $limitCount;
		$comnum = $limitStart;
	}
	$timestamp = pluginGetVariable('comments', 'timestamp');
	if (!$timestamp)
		$timestamp = 'j.m.Y - H:i';
	$output = '';
	foreach ($mysql->select($sql) as $row) {
		$comnum++;
		$tvars['vars']['id'] = $row['id'];
		$tvars['vars']['delete_token'] = genUToken($row['id']);
		$tvars['vars']['author'] = $row['author'];
		$tvars['vars']['mail'] = $row['mail'];
		// Информация о редактировании
		if ($row['edit_date'] && $row['edit_date'] > 0) {
			$tvars['vars']['edit_info'] = '<br/><small><i>Изменено: ' . LangDate($timestamp, $row['edit_date']) . '</i></small>';
		} else {
			$tvars['vars']['edit_info'] = '';
		}
		$tvars['vars']['date'] = LangDate($timestamp, $row['postdate']);
		if ($row['reg'] && getPluginStatusActive('uprofile')) {
			$tvars['vars']['profile_link'] = checkLinkAvailable('uprofile', 'show') ?
				generateLink('uprofile', 'show', array('name' => $row['author'], 'id' => $row['author_id'])) :
				generateLink('core', 'plugin', array('plugin' => 'uprofile', 'handler' => 'show'), array('id' => $row['author_id']));
			$tvars['regx']["'\[profile\](.*?)\[/profile\]'si"] = '$1';
		} else {
			$tvars['vars']['profile_link'] = '';
			$tvars['regx']["'\[profile\](.*?)\[/profile\]'si"] = '';
		}
		// Add [hide] tag processing
		$text = $row['text'];
		if ($config['blocks_for_reg']) {
			$text = $parse->userblocks($text);
		}
		if ($config['use_bbcodes']) {
			$text = $parse->bbcodes($text);
		}
		if ($config['use_htmlformatter']) {
			$text = $parse->htmlformatter($text);
		}
		if ($config['use_smilies']) {
			$text = $parse->smilies($text);
		}
		/*
		if (intval($config['com_wrap']) && (strlen($text) > $config['com_wrap'])) {
			$tvars['vars']['comment-short']	=	substr($text, 0, $config['com_wrap']);
			$tvars['vars']['comment-full']	=	substr($text, $config['com_wrap']);
			$tvars['regx']["'\[comment_full\](.*?)\[/comment_full\]'si"] = '$1';
		} else {
		*/
		$tvars['vars']['comment-short'] = $text;
		$tvars['regx']["'\[comment_full\](.*?)\[/comment_full\]'si"] = '';
		/* } */
		if ($commID && $commDisplayNum) {
			$tvars['vars']['comnum'] = $commDisplayNum;
		} else {
			if (pluginGetVariable('comments', 'backorder') && (intval($callingParams['total']) > 0)) {
				$tvars['vars']['comnum'] = intval($callingParams['total']) - $comnum + 1;
			} else {
				$tvars['vars']['comnum'] = $comnum;
			}
		}
		$tvars['vars']['alternating'] = ($comnum % 2) ? "comment_even" : "comment_odd";
		if ($config['use_avatars']) {
			if ($row['users_avatar']) {
				$tvars['vars']['avatar'] = "<img src=\"" . avatars_url . "/" . $row['users_avatar'] . "\" alt=\"" . $row['author'] . "\" />";
			} else {
				// If gravatar integration is active, show avatar from GRAVATAR.COM
				if ($config['avatars_gravatar']) {
					$tvars['vars']['avatar'] = '<img src="http://www.gravatar.com/avatar/' . md5(strtolower($row['mail'])) . '.jpg?s=' . $config['avatar_wh'] . '&amp;d=' . urlencode($noAvatarURL) . '" alt=""/>';
				} else {
					$tvars['vars']['avatar'] = "<img src=\"" . $noAvatarURL . "\" alt=\"\" />";
				}
			}
		} else {
			$tvars['vars']['avatar'] = '';
		}
		if ($config['use_bbcodes']) {
			$tvars['regx']["'\[quote\](.*?)\[/quote\]'si"] = '$1';
		} else {
			$tvars['regx']["'\[quote\](.*?)\[/quote\]'si"] = '';
		}
		if ($row['answer'] != '') {
			$answer = $row['answer'];
			if ($config['blocks_for_reg']) {
				$answer = $parse->userblocks($answer);
			}
			if ($config['use_htmlformatter']) {
				$answer = $parse->htmlformatter($answer);
			}
			if ($config['use_bbcodes']) {
				$answer = $parse->bbcodes($answer);
			}
			if ($config['use_smilies']) {
				$answer = $parse->smilies($answer);
			}
			$tvars['vars']['answer'] = $answer;
			$tvars['vars']['name'] = $row['name'];
			$tvars['regx']["'\[answer\](.*?)\[/answer\]'si"] = '$1';
		} else {
			$tvars['regx']["'\[answer\](.*?)\[/answer\]'si"] = '';
		}
		// Check permissions for edit/delete/reply
		$canEdit = false;
		$canDelete = false;
		$canAdminReply = false;
		if (is_array($userROW)) {
			// Admin or editor can edit/delete any comment and give admin replies
			if (($userROW['status'] == 1) || ($userROW['status'] == 2)) {
				$canEdit = true;
				$canDelete = true;
				$canAdminReply = true;
			}
			// User can edit/delete own comment
			elseif ($row['author_id'] == $userROW['id']) {
				$canEdit = true;
				$canDelete = true;
			}
		}
		
		$tvars['vars']['can_edit'] = $canEdit;
		$tvars['vars']['can_delete'] = $canDelete;
		$tvars['vars']['can_admin_reply'] = $canAdminReply;
		$tvars['vars']['text'] = $tvars['vars']['comment-short'];
		$tvars['vars']['newsid'] = $newsID;
		$tvars['vars']['admin_url'] = admin_url;
		
		if (is_array($userROW) && (($userROW['status'] == 1) || ($userROW['status'] == 2))) {
			$edit_link = admin_url . "/admin.php?mod=editcomments&amp;newsid=" . $newsID . "&amp;comid=" . $row['id'];
			$delete_link = generateLink('core', 'plugin', array('plugin' => 'comments', 'handler' => 'delete'), array('id' => $row['id'], 'uT' => genUToken($row['id'])), true);
			$tvars['vars']['[edit-com]'] = "<a href=\"" . $edit_link . "\" target=\"_blank\" title=\"" . $lang['addanswer'] . "\">";
			$tvars['vars']['[/edit-com]'] = "</a>";
			$tvars['vars']['[del-com]'] = "<a href=\"" . $delete_link . "\" title=\"" . $lang['comdelete'] . "\">";
			$tvars['vars']['[/del-com]'] = "</a>";
			$tvars['vars']['ip'] = "<a href=\"http://www.nic.ru/whois/?ip=$row[ip]\" title=\"" . $lang['whois'] . "\">" . $lang['whois'] . "</a>";
			$tvars['vars']['[if-have-perm]'] = '';
			$tvars['vars']['[/if-have-perm]'] = '';
		} else {
			$tvars['regx']["'\\[edit-com\\].*?\\[/edit-com\\]'si"] = '';
			$tvars['regx']["'\\[del-com\\].*?\\[/del-com\\]'si"] = '';
			$tvars['vars']['ip'] = '';
			$tvars['regx']['#\[if-have-perm\].*?\[\/if-have-perm\]#si'] = '';
		}
		$tvars['regx']['#\[is-logged\](.+?)\[/is-logged\]#is'] = is_array($userROW) ? '$1' : '';
		$tvars['regx']['#\[isnt-logged\](.+?)\[/isnt-logged\]#is'] = is_array($userROW) ? '' : '$1';
		// RUN interceptors
		if (isset($PFILTERS['comments']) && is_array($PFILTERS['comments']))
			foreach ($PFILTERS['comments'] as $k => $v)
				$v->showComments($newsID, $row, $comnum, $tvars);
		// run OLD-STYLE interceptors
		exec_acts('comments', $row);
		// Show template
		if ($usePluginTemplate) {
			// Use Twig template from plugin
			global $twig, $lang;
			loadPluginLang('comments', 'site', '', '', ':');
			$tvars['vars']['is_logged'] = is_array($userROW);
			$tvars['vars']['config'] = ['use_bbcodes' => $config['use_bbcodes']];
			$tvars['vars']['lang'] = $lang;
			$xt = $twig->loadTemplate('plugins/comments/comments.show.tpl');
			$output .= $xt->render($tvars['vars']);
		} else {
			// Use old template system
			$tpl->vars($templateName, $tvars);
			$output .= $tpl->show($templateName);
		}
	}
	if ($callingParams['outprint']) {
		return $output;
	}
	$template['vars']['mainblock'] .= $output;
}

// $callingParams
//		'plugin'  => if is called from plugin - ID of plugin
//		'overrideTemplateName'	=> alternative template for display
//		'overrideTemplatePath'	=> alternative path for searching of template
//		'noajax'		=> DISABLE AJAX mode
//		'outprint'	 	=> flag: if set, output will be returned, elsewhere - will be added to mainblock
function comments_showform($newsID, $callingParams = array()) {

	global $mysql, $config, $template, $tpl, $userROW, $PFILTERS;
	// -> desired template path
	$templatePath = ($callingParams['overrideTemplatePath']) ? $callingParams['overrideTemplatePath'] : (root . '/plugins/comments/tpl/');
	// -> desired template
	if ($callingParams['overrideTemplateName']) {
		$templateName = $callingParams['overrideTemplateName'];
	} else {
		$templateName = 'comments.form';
	}
	$usePluginTemplate = file_exists($templatePath . $templateName . '.tpl');
	if (!$usePluginTemplate) {
		$templatePath = tpl_site . 'plugins/comments';
		$tpl->template($templateName, $templatePath);
	}
	if ($config['use_smilies']) {
		$tvars['vars']['smilies'] = InsertSmilies('comments', 10);
	} else {
		$tvars['vars']['smilies'] = "";
	}
	// Lock AJAX calls if required
	$tvars['regx']['#\[ajax\](.*?)\[\/ajax\]#is'] = $callingParams['noajax'] ? '' : '$1';
	if ($_COOKIE['com_username'] && trim($_COOKIE['com_username']) != "") {
		$tvars['vars']['savedname'] = secure_html(urldecode($_COOKIE['com_username']));
		$tvars['vars']['savedmail'] = secure_html(urldecode($_COOKIE['com_usermail']));
	} else {
		$tvars['vars']['savedname'] = '';
		$tvars['vars']['savedmail'] = '';
	}
	if (!is_array($userROW)) {
		$tvars['vars']['[not-logged]'] = "";
		$tvars['vars']['[/not-logged]'] = "";
		$tvars['vars']['not_logged'] = true;
	} else {
		$tvars['regx']["'\[not-logged\].*?\[/not-logged\]'si"] = "";
		$tvars['vars']['not_logged'] = false;
	}
	$tvars['vars']['admin_url'] = admin_url;
	$tvars['vars']['rand'] = rand(00000, 99999);
	if ($config['use_captcha'] && (!is_array($userROW))) {
		$_SESSION['captcha'] = rand(00000, 99999);
		$tvars['regx']["'\[captcha\](.*?)\[/captcha\]'si"] = '$1';
		$tvars['vars']['use_captcha'] = true;
	} else {
		$tvars['regx']["'\[captcha\](.*?)\[/captcha\]'si"] = '';
		$tvars['vars']['use_captcha'] = false;
	}
	$tvars['vars']['captcha_url'] = admin_url . "/captcha.php";
	$tvars['vars']['bbcodes'] = BBCodes("'content'");
	$tvars['vars']['skins_url'] = skins_url;
	$tvars['vars']['newsid'] = $newsID . '#' . genUToken('comment.add.' . $newsID);
	$tvars['vars']['request_uri'] = secure_html($_SERVER['REQUEST_URI']);
	// Generate request URL
	$link = generateLink('core', 'plugin', array('plugin' => 'comments', 'handler' => 'add'));
	$tvars['vars']['post_url'] = $link;
	// Generate delete URL
	$delete_link = generateLink('core', 'plugin', array('plugin' => 'comments', 'handler' => 'delete'));
	$tvars['vars']['delete_url'] = $delete_link;
	// Generate edit URL
	$edit_link = generateLink('core', 'plugin', array('plugin' => 'comments', 'handler' => 'edit'));
	$tvars['vars']['edit_url'] = $edit_link;
	// Generate delete token (will be concatenated with comment ID in JavaScript)
	$tvars['vars']['delete_token'] = substr(genUToken(''), 0, 8);
	// RUN interceptors
	if (is_array($PFILTERS['comments']))
		foreach ($PFILTERS['comments'] as $k => $v)
			$v->addCommentsForm($newsID, $tvars);
	// RUN interceptors ( OLD-style )
	exec_acts('comments_form', $row);
	if ($usePluginTemplate) {
		// Use Twig template from plugin
		global $twig, $lang;
		loadPluginLang('comments', 'site', '', '', ':');
		$tvars['vars']['noajax'] = $callingParams['noajax'] ? true : false;
		$tvars['vars']['use_moderation'] = pluginGetVariable('comments', 'moderation') ? true : false;
		$tvars['vars']['lang'] = $lang;
		$xt = $twig->loadTemplate('plugins/comments/comments.form.tpl');
		$output = $xt->render($tvars['vars']);
	} else {
		// Use old template system
		$tpl->vars($templateName, $tvars);
		$output = $tpl->show($templateName);
	}
	if ($callingParams['outprint']) {
		return $output;
	}
	$template['vars']['mainblock'] .= $output;
}

// preload plugins
load_extras('comments');
load_extras('comments:show');