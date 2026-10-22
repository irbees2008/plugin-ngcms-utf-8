<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

class NewsvoteNewsfilter extends NewsFilter {
	function addNewsForm(&$tvars) {
	    global $tpl, $lang;
		LoadPluginLang('newsvotes', 'main', '', '', ':');
		$tpath = locatePluginTemplates(array('nv_addnews'), 'newsvotes', 0, 'default');

		$tpl -> template('nv_addnews', $tpath['nv_addnews']);
		$tpl -> vars('nv_addnews', array('vars' => array(
			'form_heading' => $lang['newsvotes:form_heading'],
			'form_enable' => $lang['newsvotes:form_enable'],
			'form_question' => $lang['newsvotes:form_question'],
			'form_default_question' => $lang['newsvotes:form_default_question'],
			'form_answers' => $lang['newsvotes:form_answers'],
			'form_answers_hint' => $lang['newsvotes:form_answers_hint'],
			'form_default_answers' => $lang['newsvotes:answer_yes']."\n".$lang['newsvotes:answer_no'],
			'form_access' => $lang['newsvotes:form_access'],
			'form_registered' => $lang['newsvotes:form_registered'],
			'form_guests' => $lang['newsvotes:form_guests'],
		)));
		$tvars['plugin']['newsvote'] = $tpl -> show('nv_addnews');

		return 1;
	}
    

    ///////////
    function addNews(&$tvars, &$SQL) {
        if (!empty($_REQUEST['nvenable']))
           $SQL['nvote'] = 1;
        
		return 1;
	}

	function addNewsNotify(&$tvars, $SQL, $newsid) {
		global $mysql;
        
        if (empty($_REQUEST['nvenable']))
           return 1;
           
        $vtitle = trim($_REQUEST['nvtitle'] ?? '');
        if (!strlen($vtitle)) return 1;
        $vtitle = db_squote($vtitle);
        
        if (!empty($_REQUEST['nvpu']) && !empty($_REQUEST['nvpg'])) $whovote = 3;
        elseif (!empty($_REQUEST['nvpg'])) $whovote = 2;
        else $whovote = 1;
        
        $mysql->query("insert into ".prefix."_newsvote (newsid, title, whovote) values (".$newsid.", ".$vtitle.", ".$whovote.")");
        $vid = $mysql->lastid("newsvote");
        
        addAnswers($_REQUEST['nvanswers'] ?? '', $vid);
        
		return 1;
	}

	function editNewsForm($newsID, $SQLold, &$tvars) {
	    global $tpl, $mysql, $lang;
		LoadPluginLang('newsvotes', 'main', '', '', ':');
            
        $vote = $SQLold['nvote'] > 0
            ? $mysql->record("select id, title, whovote from ".prefix."_newsvote where newsid=".intval($newsID))
            : false;

        if (is_array($vote)) {
            $result = '';
            $k = 1;
            foreach ($mysql->select("select id, name from ".prefix."_newsanswer where voteid=".intval($vote['id'])." order by id ASC") as $answer) {
                    $dt['vars']['num'] = $k++;
                    $dt['vars']['id'] = $answer['id'];
                    $dt['vars']['name'] = $answer['name'];
                   
                   $tpath = locatePluginTemplates(array('edit_answer'), 'newsvotes', 0, 'default');
                   $tpl -> template('edit_answer', $tpath['edit_answer']);
                   $tpl -> vars('edit_answer', $dt);
                   $result .= $tpl -> show('edit_answer');
                }
            
            $selvu = ''; $selvg = '';
            if($vote['whovote'] == 1 || $vote['whovote'] == 3) $selvu = ' checked="checked"';
            if($vote['whovote'] == 2 || $vote['whovote'] == 3) $selvg = ' checked="checked"';
    
            $tpath = locatePluginTemplates(array('nv_editnews'), 'newsvotes', 0, 'default');
    		$tpl -> template('nv_editnews', $tpath['nv_editnews']);
    		$tpl -> vars('nv_editnews', array('vars' => array(
				'answers' => $result,
				'title' => secure_html($vote['title']),
				'voteid' => intval($vote['id']),
				'selvu' => $selvu,
				'selvg' => $selvg,
				'edit_heading' => $lang['newsvotes:edit_heading'],
				'edit_delete' => $lang['newsvotes:edit_delete'],
				'edit_question' => $lang['newsvotes:edit_question'],
				'edit_answers' => $lang['newsvotes:edit_answers'],
				'edit_add_answers' => $lang['newsvotes:edit_add_answers'],
				'edit_update_answers' => $lang['newsvotes:edit_update_answers'],
				'edit_access' => $lang['newsvotes:edit_access'],
				'edit_registered' => $lang['newsvotes:edit_registered'],
				'edit_guests' => $lang['newsvotes:edit_guests'],
				'edit_reset' => $lang['newsvotes:edit_reset'],
			)));
    		$tvars['plugin']['newsvote_edit'] = $tpl -> show('nv_editnews');
        }
        else
        {
            $tpath = locatePluginTemplates(array('nv_addnews'), 'newsvotes', 0, 'default');

    		$tpl -> template('nv_addnews', $tpath['nv_addnews']);
    		$tpl -> vars('nv_addnews', array('vars' => array(
				'form_heading' => $lang['newsvotes:form_heading'],
				'form_enable' => $lang['newsvotes:form_enable'],
				'form_question' => $lang['newsvotes:form_question'],
				'form_default_question' => $lang['newsvotes:form_default_question'],
				'form_answers' => $lang['newsvotes:form_answers'],
				'form_answers_hint' => $lang['newsvotes:form_answers_hint'],
				'form_default_answers' => $lang['newsvotes:answer_yes']."\n".$lang['newsvotes:answer_no'],
				'form_access' => $lang['newsvotes:form_access'],
				'form_registered' => $lang['newsvotes:form_registered'],
				'form_guests' => $lang['newsvotes:form_guests'],
			)));
            $tvars['plugin']['newsvote_edit'] = $tpl -> show('nv_addnews');
        }
        
		return 1;
	}

	function editNews($newsID, $SQLold, &$SQLnew, &$tvars) {
        if (!empty($_REQUEST['nvenable'])) $SQLnew['nvote'] = 1;
        return 1;
	}
    
	// Make changes in DB after EditNews was successfully executed
	function editNewsNotify($newsID, $SQLnews, &$SQLnew, &$tvars) {
		global $mysql;
        
        if (($_REQUEST['mode'] ?? '') === "addv")
        {
            if (empty($_REQUEST['nvenable']))
               return 1;
               
            $vtitle = trim($_REQUEST['nvtitle'] ?? '');
            if (!strlen($vtitle)) return 1;
            $vtitle = db_squote($vtitle);
            
            if (!empty($_REQUEST['nvpu']) && !empty($_REQUEST['nvpg'])) $whovote = 3;
            elseif (!empty($_REQUEST['nvpg'])) $whovote = 2;
            else $whovote = 1;
            
            $mysql->query("insert into ".prefix."_newsvote (newsid, title, whovote) values (".$newsID.", ".$vtitle.", ".$whovote.")");
            $vid = $mysql->lastid("newsvote");
            
            addAnswers($_REQUEST['nvanswers'] ?? '', $vid);
        }
        else
        {
            $vid = intval($_REQUEST['voteid'] ?? 0);
            if (!empty($_REQUEST['nvdelete']))
            {
                deleteVote($vid, $newsID);
                return 1;
            }
            
            if (!empty($_REQUEST['nvpu']) && !empty($_REQUEST['nvpg'])) $whovote = 3;
            elseif (!empty($_REQUEST['nvpg'])) $whovote = 2;
            else $whovote = 1;
            
            $mysql->query("update ".prefix."_newsvote set title = ".db_squote(trim($_REQUEST['nvtitle'] ?? '')).", whovote=".$whovote." where newsid = ".intval($newsID));
            
            if (isset($_REQUEST['nvupdate']))
            {
                foreach ((array) ($_REQUEST['ans'] ?? []) as $aid => $aval)
                {
                    $mysql->query("update ".prefix."_newsanswer set name = ".db_squote(trim(htmlspecialchars($aval, ENT_QUOTES)))." where id = ".intval($aid));
                }
                
                addAnswers($_REQUEST['nvanswers'] ?? '', $vid);
            }
            
            if (!empty($_REQUEST['nvrefresh']))
            {
                $mysql->query("update ".prefix."_newsanswer set number = 0 where voteid = ".$vid);
                $mysql->query("update ".prefix."_newsvote set count = 0 where id = ".$vid);
                $mysql->query("delete from ".prefix."_newsvoted where voteid = ".$vid);    
            }
               
        }
        
        return 1;
	}

	function showNews($newsID, $SQLnews, &$tvars, $mode = array()) {
		global $mysql, $tpl, $userROW, $ip, $lang;
        if (empty($SQLnews['nvote'])) {
            $tvars['vars']['newsvote'] = '';
            return 1;
        }

        LoadPluginLang('newsvotes', 'main', '', '', ':');

        if (
            isset($_POST['mode'], $_REQUEST['voteid'], $_REQUEST['ans'])
            && $_POST['mode'] === 'vote'
        ) {
            process_voting($_REQUEST['voteid'], $_REQUEST['ans'], $newsID);
            unset($_POST['mode']);
        }

        $nvdata = $mysql->record(
            "select id, title, count, whovote from ".prefix."_newsvote where newsid=".intval($newsID)
        );
        if (!is_array($nvdata)) {
            $tvars['vars']['newsvote'] = '';
            return 1;
        }

        $notrate = 0;
        if ($nvdata['whovote'] == 1 && !is_array($userROW)) {
            $notrate = 1;
        } elseif ($nvdata['whovote'] == 2 && is_array($userROW)) {
            $notrate = 1;
        }

        if (
            !$notrate
            && $mysql->result(
                "select id from ".prefix."_newsvoted where ip=".db_squote($ip)." AND voteid=".intval($nvdata['id'])
            )
        ) {
            $notrate = 1;
        }

        $colors = array("DarkBlue", "Blue", "DodgerBlue", "LightSeaGreen", "BlueViolet", "Aquamarine4", "Gainsboro", "CornflowerBlue");
        $answers = '';
        $colorIndex = 0;
        $answerRows = $mysql->select(
            "select id, name, number from ".prefix."_newsanswer where voteid=".intval($nvdata['id'])." order by id ASC"
        );
        foreach ($answerRows as $answer) {
            $dt['vars']['id'] = $answer['id'];
            $dt['vars']['answer'] = $answer['name'];
            $dt['vars']['number'] = (int) $answer['number'];
            $dt['vars']['width'] = (int) $nvdata['count'] > 0
                ? round(((int) $answer['number'] / (int) $nvdata['count']) * 100, 2)
                : 0;
            $dt['vars']['color'] = $colors[$colorIndex];
            $colorIndex = ($colorIndex + 1) % count($colors);
            $dt['regx']['#\[is-rate\](.*?)\[\/is-rate\]#is'] = (!$notrate) ? '$1' : '';
            $dt['regx']['#\[not-rate\](.*?)\[\/not-rate\]#is'] = ($notrate) ? '$1' : '';

            $tpath = locatePluginTemplates(array('answer'), 'newsvotes', 0, 'default');
            $tpl->template('answer', $tpath['answer']);
            $tpl->vars('answer', $dt);
            $answers .= $tpl->show('answer');
        }

        $tpath = locatePluginTemplates(array('vote'), 'newsvotes', 0, 'default');
        $ptvars['vars']['vote'] = secure_html($nvdata['title']);
        $ptvars['vars']['voteid'] = (int) $nvdata['id'];
        $ptvars['vars']['total'] = (int) $nvdata['count'];
        $ptvars['vars']['answers'] = $answers;
        $ptvars['vars']['self'] = htmlspecialchars($_SERVER['REQUEST_URI'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $ptvars['vars']['vote_submit'] = $lang['newsvotes:vote_submit'];
        $ptvars['vars']['total_votes_label'] = $lang['newsvotes:total_votes'];
        $ptvars['regx']['#\[is-rate\](.*?)\[\/is-rate\]#is'] = (!$notrate) ? '$1' : '';
        $ptvars['regx']['#\[not-rate\](.*?)\[\/not-rate\]#is'] = ($notrate) ? '$1' : '';

        $tpl->template('vote', $tpath['vote']);
        $tpl->vars('vote', $ptvars);
        $tvars['vars']['newsvote'] = $tpl->show('vote');

		return 1;
	}

	// Delete news call
	function deleteNews($newsID, $SQLnews) {
		global $mysql;

        if($SQLnews['nvote'] != 0)
        {
            $voteid = $mysql->result("select id from ".prefix."_newsvote where newsid=".intval($newsID)."");
            if($voteid) deleteVote($voteid, $newsID);
        }

		return 1;
	}

	// Mass news modify
	function massModifyNewsNotify($idList, $setValue, $currentData) {
		return 1;
	}
}

register_filter('news','newsvotes', new NewsvoteNewsfilter);

function addAnswers($area, $voteID) {
    global $mysql;

    $answers = [];
    foreach (explode("\n", is_string($area) ? $area : '') as $answer) {
        $answer = trim(htmlspecialchars($answer, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
        if ($answer === '') {
            continue;
        }
        $answers[] = db_squote($answer);
    }

    foreach ($answers as $answer) {
        $mysql->query("insert into ".prefix."_newsanswer (voteid, name) values (".intval($voteID).", ".$answer.")");
    }
}
            
   function deleteVote($voteID, $newsID) {
	        global $mysql;
            
            $mysql->query("delete from ".prefix."_newsanswer where voteid = ".$voteID);
            $mysql->query("delete from ".prefix."_newsvoted where voteid = ".$voteID);
            $mysql->query("delete from ".prefix."_newsvote where id = ".$voteID);
            $mysql->query("update ".prefix."_news set nvote = 0 where id = ".intval($newsID));
    }


function process_voting($voteID, $answerID, $newsID) {
 global $mysql, $userROW, $ip;
 
    if($mysql->result("select id from ".prefix."_newsvoted where ip=".db_squote($ip)." AND voteid=".intval($voteID).""))
       return 1;
    
    $mysql->query("insert into ".prefix."_newsvoted (voteid, ip) values (".intval($voteID).", ".db_squote($ip).")");
    $mysql->query("update ".prefix."_newsanswer set number = number+1 where id = ".intval($answerID));
    $mysql->query("update ".prefix."_newsvote set count = count+1 where id = ".intval($voteID));
    $mysql->query("update ".prefix."_news set views = views-1 where id = ".intval($newsID));
 }