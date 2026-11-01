<?php
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
	$tvars = array ( 'vars' => array ( 'entries' => $entries, 'tag' => $tag, 'pages' => $pages));
	$tvars['regx']['#\[paginator\](.*?)\[\/paginator\]#is'] = ($pagesCount > 1)?'$1':'';

	// Check if we have template `tag`
	$tplName = isset($tpath['cloud.tag'])?'cloud.tag':'cloud';
	$tpl -> template($tplName, $tpath[$tplName]);
	$tpl -> vars($tplName, $tvars);
	$result = $tpl -> show($tplName);
    return $result;
?>