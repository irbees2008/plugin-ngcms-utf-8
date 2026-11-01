<?php
if (!defined('NGCMS')) die ('HAL');

register_plugin_page("topplayers","","ppage",0);
add_act('index', 'topplayers');


function ppage() {
	global $config, $mysql, $template, $tpl, $userROW;
	$users=explode("\r\n",PluginGetVariable('topplayers','editusers'));
	if ((is_array($userROW)) && (in_array($userROW['name'],$users))) {$y=1;}
	switch($_REQUEST['do']) {
		case 'edit':
			if (!$y) {msg(array("type" => "error", "text" => "Недостатньо прав"));}
			else { $template['vars']['mainblock']=edit();}
			break;
		case 'delete':
			if (!$y) {msg(array("type" => "error", "text" => "Недостатньо прав"));}
			else { $template['vars']['mainblock']=delete();}
			break;
		case 'save':
			if (!$y) {msg(array("type" => "error", "text" => "Недостатньо прав"));}
			else {$template['vars']['mainblock']=save();}
			break;
		case 'editprofile':
			if (!$y) {msg(array("type" => "error", "text" => "Недостатньо прав"));}
			else {$template['vars']['mainblock']=editprofile();}
			break;
		case 'refresh': 
			if (!$y) {msg(array("type" => "error", "text" => "Недостатньо прав"));}
			else {$template['vars']['mainblock']=refresh();}
			break;
		case 'top':
			$template['vars']['mainblock']=top();
			break;
		case 'profile': 
			$template['vars']['mainblock']=profile();
			break;
		
		default: 
			msg(array("type" => "error", "text" => "Невірна команда"));
			break;
	}
}

function profile() {
	global $config, $mysql, $template, $tpl, $parse, $SYSTEM_FLAGS;
	if (!array_key_exists('id',$_REQUEST)) {msg(array("type" => "error", "text" => "Невірна команда"));}
	else {
		$flags = PluginGetVariable('topplayers','flags');
		$photos = PluginGetVariable('topplayers','photos');
		$tpath = locatePluginTemplates(array('profile'), 'topplayers', PluginGetVariable('topplayers', 'localsource'));
		$id=$_REQUEST['id'];
		$bd=$mysql->select("SELECT id, name, scores, country, flag, pos, rating, biography, hand, birthdate, birthplace, photo FROM ".prefix."_topplayers where id=$id");
		$bd=$bd[0];
		$SYSTEM_FLAGS['info']['title']['group']='Профіль гравця : '.$bd['name'];
		$SYSTEM_FLAGS['info']['title']['title']=$bd['name'];
		$text=$bd['biography'];
		$text = $parse -> userblocks($text);
		$text = $parse -> htmlformatter($text);
		$text = $parse -> bbcodes($text);
		$text = $parse -> smilies($text);
		$tvars['vars'] = array (
			'name'			=> $bd['name'],
			'id'			=> $bd['id'],
			'pos'			=> $bd['pos'],
			'scores'		=> $bd['scores'],
			'birthdate'		=> $bd['birthdate'],
			'birthplace'	=> $bd['birthplace'],
			'hand'			=> $bd['hand'],
			'country'		=> $bd['country'],
			'flag'			=> $flags.$bd['flag'],
			'photo'			=> $photos.$bd['photo'],
			'rating'		=> PluginGetVariable('topplayers', 'rating'.$bd['rating'].'name'),
			'biography'		=> $text,
		);
		$tpl -> template('profile', $tpath['profile']);
		$tpl -> vars('profile', $tvars);
		$output = $tpl -> show('profile');
	}
	return $output;
}

function edit(){
global $config, $mysql, $template, $tpl, $parse, $TemplateCache;
	$tpath = locatePluginTemplates(array('edittoppage.entries','edittoppage'), 'topplayers', PluginGetVariable('topplayers', 'localsource'));
	$flags = PluginGetVariable('topplayers','flags');
	$photos = PluginGetVariable('topplayers','photos');
	
	$rating=$_REQUEST['rating'];
	
	$ratings='';
	$ratingscount=PluginGetVariable('topplayers', 'ratings');
			
	for ($i = 1; $i <= $ratingscount; $i++) {
		if ($bd['rating']!=$i) {$ratings .= '<option value="'.$i.'">'.PluginGetVariable('topplayers', 'rating'.$i.'name').'</option>';}
		else {$ratings .= '<option selected="selected" value="'.$i.'">'.PluginGetVariable('topplayers', 'rating'.$i.'name').'</option>';}
	}
	$page=$_REQUEST['page']?$_REQUEST['page']:1;
	$count=PluginGetVariable('topplayers','ppedittop');
	$begin=($page-1)*$count;
	$end=$page*$count;
	if (array_key_exists('rating',$_REQUEST)) { 
		$bd=$mysql->select("SELECT id, name, scores, country, flag, pos, photo, birthplace, birthdate, rating FROM ".prefix."_topplayers WHERE rating=$rating LIMIT $begin,$end");
		$pagecount=$mysql->result("SELECT count(id) FROM ".prefix."_topplayers WHERE rating=$rating");
	} else {
		$bd=$mysql->select("SELECT id, name, scores, country, flag, pos, photo, birthplace, birthdate, rating FROM ".prefix."_topplayers LIMIT $begin,$end");
		$pagecount=$mysql->result("SELECT count(id) FROM ".prefix."_topplayers");
	}
	$pagecount=ceil($pagecount/$count);
	$count=$begin;
	foreach ($bd as $line) {
		$count++;
		$tvars['vars'] = array (
			'id'		=> $line['id'],
			'number'	=> $count,
			'name'	 	=> $line['name'],
			'editurl'	=> "/plugin/topplayers/?do=editprofile&id=".$line['id'],
			'photo'  	=> $photos.$line['photo'],
			'pos'	 	=> $line['pos'],
			'flag'	 	=> $flags.$line['flag'],
			'country'	=> $line['country'],
			'scores' 	=> $line['scores'],
			'birthdate' => $line['birthdate'],
			'birthplace'=> $line['birthplace'],
			'rating'	=> PluginGetVariable('topplayers', 'rating'.$line['rating'].'name')
		);
		$tpl -> template('edittoppage.entries', $tpath['edittoppage.entries']);
		$tpl -> vars('edittoppage.entries', $tvars);
		$vars .= $tpl -> show('edittoppage.entries');
	}
	templateLoadVariables(true);
	$navigations = $TemplateCache['site']['#variables']['navigation'];
	$tvars['vars'] = array (
		'entries'	=> $vars,
		'newplayer'	=> "/plugin/topplayers/?do=editprofile&id=-1",
		'pagination' => generatePagination($page, 1, $pagecount, 10, array('pluginName' => 'topplayers', 'params' => array('do'=>'edit'), 'xparams' => array(), 'paginator' => array('page', 0, 0)), $navigations),
	);
	$tpl -> template('edittoppage', $tpath['edittoppage']);
	$tpl -> vars('edittoppage', $tvars);
	$output = $tpl -> show('edittoppage');	
	return $output;
}

function delete(){
	global $mysql;
	$selected = $_REQUEST['selected'];
	if($selected) {
		foreach ($selected as $id) {
			$mysql->query("delete from ".prefix."_topplayers where `id`=".db_squote($id).";");
		}
	}
	$content = '<META HTTP-EQUIV="refresh" CONTENT="0;URL='.$_SERVER['HTTP_REFERER'].'">';
	return $content;
}

function editprofile() {
	global $config, $mysql, $template, $tpl;
	if (!array_key_exists('id',$_REQUEST)) {msg(array("type" => "error", "text" => "Невірна команда"));}
	else {
		$id=$_REQUEST['id'];
		$rating='';
			$ratings=PluginGetVariable('topplayers', 'ratings');
			
			for ($i = 1; $i <= $ratings; $i++) {
				$rating[$i] = PluginGetVariable('topplayers', 'rating'.$i.'name');
			}
			$ext = PluginGetVariable('topplayers', 'flagext');
			$flagfiles=ListFiles(PluginGetVariable('topplayers', 'flagdir'), $ext);
			foreach ($flagfiles as $flag) 
				$flags[$flag.'.'.$ext]=$flag;
		if ($id!=-1) {
			$tpath = locatePluginTemplates(array('edit.profile'), 'topplayers', PluginGetVariable('topplayers', 'localsource'));
			$bd=$mysql->select("SELECT id, name, scores, country, flag, pos, rating, biography, hand, birthdate, birthplace, photo FROM ".prefix."_topplayers where id=$id");
			$bd=$bd[0];
			
			$tvars['vars'] = array (
				'action'		=> 'Редагування профілю гравця : '.$bd['name'],
				'name'			=> $bd['name'],
				'id'			=> $bd['id'],
				'scores'		=> $bd['scores'],
				'birthdate'		=> $bd['birthdate'],
				'birthplace'	=> $bd['birthplace'],
				'hand'			=> $bd['hand'],
				'country'		=> $bd['country'],
				'flag'			=> MakeDropDown($flags, "flag", $bd['flag']), 
				'photo'			=> $bd['photo'],
				'rating'		=> MakeDropDown($rating, "rating", $bd['rating']),
				'biography'		=> $bd['biography'],
			);
		} else {
			$tpath = locatePluginTemplates(array('edit.profile'), 'topplayers', PluginGetVariable('topplayers', 'localsource'));
			$tvars['vars'] = array (
				'action'		=> 'Створення нового профілю гравця',
				'name'			=> '',
				'id'			=> $id,
				'scores'		=> '',
				'birthdate'		=> '',
				'birthplace'	=> '',
				'hand'			=> '',
				'country'		=> '',
				'flag'			=> MakeDropDown($flags, "flag"),
				'photo'			=> '',
				'rating'		=> MakeDropDown($rating, "rating"),
				'biography'		=> '',
			);
		}
 
			$tpl -> template('edit.profile', $tpath['edit.profile']);
			$tpl -> vars('edit.profile', $tvars);
			$output = $tpl -> show('edit.profile');	
			return $output;
	} 
}

function refresh() {
	global $config, $mysql, $template, $tpl;
	$ratings=PluginGetVariable('topplayers', 'ratings');
	for ($i = 1; $i <= $ratings; $i++) {
		$bd=$mysql->select("SELECT id, pos, scores FROM ".prefix."_topplayers where rating=".$i." ORDER BY `scores` DESC;");
		$j=0;
		foreach ($bd as $line) {
			$j++;
			$mysql->query("UPDATE ".prefix."_topplayers SET `pos`=".$j." WHERE id=".$line['id'].";");
		}
	}
	$content = <<<HTML
		<script type="text/javascript">self.location.href='/plugin/topplayers/?do=edit';</script>	
HTML;
	return $content;
}

function save() {
	global $config, $mysql, $template, $tpl, $parse;
	if (!array_key_exists('id',$_REQUEST)) {msg(array("type" => "error", "text" => "Невірна команда")); return;}
		$db['name']=$_REQUEST['name'];
		$db['flag']=$_REQUEST['flag'];
		$db['scores']=$_REQUEST['scores'];
		$db['photo']=$_REQUEST['photo'];
		$db['birthdate']=$_REQUEST['birthdate'];
		$db['birthplace']=$_REQUEST['birthplace'];
		$db['biography']=$_REQUEST['biography'];
		$db['hand']=$_REQUEST['hand'];
		$db['rating']=$_REQUEST['rating'];
		$db['country']=$_REQUEST['country'];
		$photoname = $parse -> translit($db['name']);
		$photoname .='.'.end(explode(".",$_FILES['photofile']['name']));
		$destination = PluginGetVariable('topplayers','photodir').$photoname;
		if (move_uploaded_file($_FILES['photofile']['tmp_name'], $destination)) 
			{
				$f=1;
				$db['photo'] = $photoname;
			}
	if ($_REQUEST['id']!=-1) {
		$id=$_REQUEST['id'];
		$mysql->query("UPDATE ".prefix."_topplayers SET name='".$db['name']."', flag='".$db['flag']."', scores=".$db['scores'].", photo='".$db['photo']."', birthdate='".$db['birthdate']."', birthplace='".$db['birthplace']."', biography='".$db['biography']."', hand='".$db['hand']."', rating=".$db['rating'].", country='".$db['country']."' WHERE id=".$id.""); 
	} else {
		$mysql->query("INSERT INTO ".prefix."_topplayers (name, flag, scores, photo, birthdate, birthplace, biography, hand, rating, country) VALUES ('".$db['name']."','".$db['flag']."',".$db['scores'].",'".$db['photo']."','".$db['birthdate']."','".$db['birthplace']."','".$db['biography']."','".$db['hand']."',".$db['rating'].",'".$db['country']."')");
	}
	$content = <<<HTML
		<script type="text/javascript">self.location.href='/plugin/topplayers/?do=edit';</script>	
HTML;
	return $content;
}

function topplayers() {
	$ratings=PluginGetVariable('topplayers', 'ratings');
	for ($i = 1; $i <= $ratings; $i++) {
		if (PluginGetVariable('topplayers', 'rating'.$i)) {generateblock($i);}
	}
}

function generateblock($rating) {
	global $config, $mysql, $template, $tpl;
	$cacheFileName = md5('topplayers'.$config['theme'].$config['default_lang'].$rating).'.txt';
	if (PluginGetVariable('topplayers','cache')) {
		$cacheData = cacheRetrieveFile($cacheFileName, PluginGetVariable('topplayers','cacheExpire'), 'topplayers');
		if ($cacheData != false) {
			$template['vars']['topplayers'.$rating]=$cacheData;
		}
	}
	
	$tpath = locatePluginTemplates(array('main','main.entries','plugin.lastusers','pluginpage'), 'topplayers', PluginGetVariable('topplayers', 'localsource'));
	$flags=PluginGetVariable('topplayers','flags');
	
	$count=PluginGetVariable('topplayers','top');
	$bd=$mysql->select("SELECT id, name, scores, country, flag, pos FROM ".prefix."_topplayers where rating=$rating ORDER BY scores DESC LIMIT 0,$count");
	$count=0;
	foreach ($bd as $line) {
		$tvars['vars'] = array (
			'url'	 => generatePluginLink('topplayers', null, array('do'=>'profile','id'=>$line['id']), array(), 1, 1),
			'name'	 => $line['name'],
			'pos'	 => $line['pos'],
			'flag'	 => $flags.$line['flag'],
			'country'=> $line['country'],
			'scores' => $line['scores'],
		);
		$tpl -> template('main.entries', $tpath['main.entries']);
		$tpl -> vars('main.entries', $tvars);
		$vars .= $tpl -> show('main.entries');
	}
	unset($tvars);
	$count=PluginGetVariable('topplayers','topua');
	$bd=$mysql->select("SELECT id, name, scores, country, flag, pos FROM ".prefix."_topplayers where (country='Україна' and rating=$rating) ORDER BY scores DESC LIMIT 0,$count");
	$count=0;
	$varss="";
	foreach ($bd as $line) {
		$tvars['vars'] = array (
			'url'    => generatePluginLink('topplayers', null, array('do'=>'profile','id'=>$line['id']), array(), 1, 1),
			'name'	 => $line['name'],
			'pos'	 => $line['pos'],
			'flag'	 => $flags.$line['flag'],
			'country'=> $line['country'],
			'scores' => $line['scores'],
		);
		$tpl -> template('main.entries', $tpath['main.entries']);
		$tpl -> vars('main.entries', $tvars);
		$varss .= $tpl -> show('main.entries');
	}
	
	unset($tvars);
	$tvars['vars']=array(
		'rating'	=> PluginGetVariable('topplayers', 'rating'.$rating.'name'),
		'entries'	=> $vars,
		'entriesua'	=> $varss,
		'url'		=> generatePluginLink('topplayers', null, array('do'=>'top','rating'=>$rating), array(), 1, 1),
	);
 
	$tpl -> template('main', $tpath['main']);
	$tpl -> vars('main', $tvars);
	$output = $tpl -> show('main');	
	if (PluginGetVariable('topplayers','cache')) {
		cacheStoreFile($cacheFileName, $output, 'topplayers');
	}
	$template['vars']['topplayers'.$rating]=$output;
}

function top(){
global $config, $mysql, $template, $tpl, $parse, $TemplateCache, $SYSTEM_FLAGS;
	$tpath = locatePluginTemplates(array('toppage.entries','toppage','profile'), 'topplayers', PluginGetVariable('topplayers', 'localsource'));
	
	$flags=PluginGetVariable('topplayers','flags');
	$photos=PluginGetVariable('topplayers','photos');
	
	if (array_key_exists('rating',$_REQUEST)) {
	$rating=$_REQUEST['rating'];
	$page=$_REQUEST['page']?$_REQUEST['page']:1;
	$count=PluginGetVariable('topplayers','pptop');
	$begin=($page-1)*$count;
	$end=$page*$count;
	$bd=$mysql->select("SELECT id, name, scores, country, flag, pos, photo, rating FROM ".prefix."_topplayers WHERE rating='$rating' ORDER BY scores DESC LIMIT $begin,$end");
	$pagecount=$mysql->result("SELECT count(id) FROM ".prefix."_topplayers WHERE rating=$rating");
	$pagecount=ceil($pagecount/$count);
	$count=$begin;
	foreach ($bd as $line) {
		$count++;
		$tvars['vars'] = array (
			'name'	 => $line['name'],
			'photo'  => $photos.$line['photo'],
			'pos'	 => $line['pos'],
			'flag'	 => $flags.$line['flag'],
			'country'=> $line['country'],
			'scores' => $line['scores'],
			'number' => $count,
			'url'	 => generatePluginLink('topplayers', null, array('do'=>'profile','id'=>$line['id']), array(), 1, 1),
		);
		$tpl -> template('toppage.entries', $tpath['toppage.entries']);
		$tpl -> vars('toppage.entries', $tvars);
		$vars .= $tpl -> show('toppage.entries');
	}
	templateLoadVariables(true);
	$navigations = $TemplateCache['site']['#variables']['navigation'];
	$SYSTEM_FLAGS['info']['title']['group']='Топ гравців : '.PluginGetVariable('topplayers', 'rating'.$rating.'name');
	$SYSTEM_FLAGS['info']['title']['title']=PluginGetVariable('topplayers', 'rating'.$rating.'name');
	$tvars['vars'] = array (
		'entries' 		=> $vars,
		'rating'  		=> PluginGetVariable('topplayers', 'rating'.$rating.'name'),
		'pagination' 	=> generatePagination($page, 1, $pagecount, 10, array('pluginName' => 'topplayers', 'params' => array('do'=>'top','rating'=>$rating), 'xparams' => array(), 'paginator' => array('page', 0, 0)), $navigations),
	);
 
	$tpl -> template('toppage', $tpath['toppage']);
	$tpl -> vars('toppage', $tvars);
	$output = $tpl -> show('toppage');	
	} else {
		msg(array("type" => "error", "text" => "Невірна команда"));
	}
	return $output;
}

?>