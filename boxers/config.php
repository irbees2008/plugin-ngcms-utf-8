<?php

# protect against hack attempts
if (!defined('NGCMS'))die ('Galaxy in danger');

pluginsLoadConfig();

LoadPluginLang('boxers', 'config', '', '', ':');

switch ($_REQUEST['action']) {
	case 'config': showConfig(); break;
	case 'config-save': saveConfig(); break;
	case 'boxers': showBoxersList(); break;
	case 'weights': showWeightList(); break;
	case 'editboxer': editBoxer(); break;
	case 'addboxer': addBoxer(); break;
	case 'editboxer-save': editBoxerSave(); break;
	case 'addboxer-save': addBoxerSave(); break;
	case 'delboxer': delBoxer(); break;
	case 'addweight': addWeight(); break;
	case 'addweight-save': addWeightSave(); break;
	case 'editweight': editWeight(); break;
	case 'editweight-save': editWeightSave(); break;
	case 'delweight': delWeight(); break;
	case 'organization': showOrganization(); break;
	case 'addorganization': addOrganization(); break;
	case 'addorganization-save': addOrganizationSave(); break;
	case 'editorganization': editOrganization(); break;
	case 'editorganization-save': editOrganizationSave(); break;
	case 'delorganization' : delOrganization(); break;
	case 'ratings': showRatings(); break;
	case 'ratings-save': saveRatings(); break;
	case 'history-ratings-save': saveRatingsHistory(); break;
	case 'history-ratings-delete': deleteRatingsHistory(); break;
	case 'addfight-save': addFightSave(); break;
	case 'delfight': delFight(); break;
	case 'editfight': editFight(); break;
	case 'editfight-save': editFightSave(); break;
	
	default: showBoxersList();
}

function MakeWeightList($options, $name, $selected = "FALSE") {
	$output = "<select name=\"".$name."\">";
	foreach ($options as $k)
		$output .= "<option value=\"".$k['id']."\"".(($selected==$k['id'])?" selected=\"selected\"":'').">".$k['name']."</option>";
	$output .= "</select>";

	return $output;
}

function MakeOrgList($options, $name, $selected = "FALSE") {
	$output = "<select size=1 name=\"".$name."\">";
	foreach ($options as $k)
		$output .= "<option value=\"".$k['db_name']."\"".(($selected==$k['db_name'])?" selected=\"selected\"":'').">".$k['name']."</option>";
	$output .= "</select>";

	return $output;
}

function showConfig(){

	global $tpl, $lang;
	
	$tpath = locatePluginTemplates(array('conf.main', 'conf.config'), 'boxers', 1);
	
	$ttvars['vars']['photos.url'] = pluginGetVariable('boxers', 'photos.url');
	$ttvars['vars']['photos.dir'] = pluginGetVariable('boxers', 'photos.dir');
	$ttvars['vars']['pagination'] = pluginGetVariable('boxers', 'pagination');
	$ttvars['vars']['cacheExpire']  = pluginGetVariable('boxers', 'cacheExpire'); 
	
	if(pluginGetVariable('boxers', 'localsource') == 0){
			$ttvars['vars']['selected_localsource_0'] = 'selected';
			$ttvars['vars']['selected_localsource_1'] = '';
	} else {
			$ttvars['vars']['selected_localsource_0'] = '';
			$ttvars['vars']['selected_localsource_1'] = 'selected';
	}
	
	if(pluginGetVariable('boxers', 'cache') == 0){
			$ttvars['vars']['selected_cache_0'] = 'selected';
			$ttvars['vars']['selected_cache_1'] = '';
	} else {
			$ttvars['vars']['selected_cache_0'] = '';
			$ttvars['vars']['selected_cache_1'] = 'selected';
	}
	
	$tpl->template('conf.config', $tpath['conf.config']);
	$tpl->vars('conf.config', $ttvars);
	$tvars['vars']['entries'] = $tpl->show('conf.config');
		
 
	$tvars['vars']['action'] = $lang['boxers:button_list.config'];
	
	$tpl->template('conf.main', $tpath['conf.main']);
	$tpl->vars('conf.main', $tvars);
	print $tpl->show('conf.main');
}

function saveConfig(){
	if(!empty($_POST['photos_url']))
		pluginSetVariable('boxers', 'photos.url', $_POST['photos_url']);
	if(!empty($_POST['photos_dir']))
		pluginSetVariable('boxers', 'photos.dir', $_POST['photos_dir']);
	if(!empty($_POST['photos_dir']))
		pluginSetVariable('boxers', 'pagination', intval($_POST['pagination']));		
	if(!empty($_POST['cacheExpire']))
		pluginSetVariable('boxers', 'cacheExpire', intval($_POST['cacheExpire']));
	
	pluginSetVariable('boxers', 'localsource', $_POST['localsource']);
	pluginSetVariable('boxers', 'cache', $_POST['cache']);
	
	pluginsSaveConfig();	
	
	echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL='.admin_url.'/admin.php?mod=extra-config&amp;plugin=boxers&amp;action=config">';
}

function showBoxersList(){

	global $tpl, $lang, $mysql;
	
	$tpath = locatePluginTemplates(array('conf.main', 'conf.list.boxers', 'conf.list.boxers.row'), 'boxers', 1);
	
	if($_GET['weight'])
		$where = ' WHERE weight_class = '.intval($_GET['weight']);
	else
		$where = '';
	
	# messages per page
	$msg_per_page = pluginGetVariable('boxers', 'pagination');
	$page = 1;
	if(isset($_REQUEST['page'])) $page = intval($_REQUEST['page']);
	# range of messages
	$limit = 'LIMIT '.($page - 1) * $msg_per_page.', '.$msg_per_page;
	# count all boxers
	$countMsg = $mysql->result('SELECT COUNT(*) FROM '.prefix.'_boxers'.$where);
	
	$boxers = $mysql->select('SELECT * FROM '.prefix.'_boxers '.$where.' ORDER BY full_name ASC '.$limit);
	
	if(!$boxers)
		msg(array('type' => 'info', 'info' => $lang['boxers:msgi_not.weight.boxers']));

	$list_row = '';
	
	foreach ($boxers as $row){
	
		$pvars['vars']['id'] = $row['id'];
		$pvars['vars']['full_name'] = $row['full_name'];
			
		$tpl->template('conf.list.boxers.row', $tpath['conf.list.boxers.row']);
		$tpl -> vars('conf.list.boxers.row', $pvars);
		$list_row .= $tpl->show('conf.list.boxers.row');
	}
	
	$ttvars['vars']['entries'] = $list_row;
	
	$pages_count = ceil($countMsg / $msg_per_page);
	
	# generate pagination if count of pages > 1
	if ($pages_count > 1){ 
		$lang['nextpage'] = 'далее ї';
		$lang['prevpage'] = 'Ђ назад';
		$ttvars['vars']['pagination'] = generateAdminPagelist( array('current' => $page, 'count' => $pages_count, 'url' => admin_url.'/admin.php?mod=extra-config&amp;plugin=boxers&amp;action=boxers&page=%page%'));
	} else $ttvars['vars']['pagination'] = '';
	
	
	$tpl->template('conf.list.boxers', $tpath['conf.list.boxers']);
	$tpl->vars('conf.list.boxers', $ttvars);
	$tvars['vars']['entries'] = $tpl->show('conf.list.boxers');
		
 
	$tvars['vars']['action'] = $lang['boxers:button_list.boxers'];
	
	$tpl->template('conf.main', $tpath['conf.main']);
	$tpl->vars('conf.main', $tvars);
	print $tpl->show('conf.main');
}

function showWeightList(){

	global $tpl, $lang, $mysql;
	
	$tpath = locatePluginTemplates(array('conf.main', 'conf.list.weights', 'conf.list.weights.row'), 'boxers', 1);
	
	$boxers = $mysql->select('SELECT * FROM '.prefix.'_boxers_weight_class');

	$list_row = '';
	
	foreach ($boxers as $row){
	
		$pvars['vars']['id'] = $row['id'];
		$pvars['vars']['name'] = $row['name'];
			
		$tpl->template('conf.list.weights.row', $tpath['conf.list.weights.row']);
		$tpl -> vars('conf.list.weights.row', $pvars);
		$list_row .= $tpl->show('conf.list.weights.row');
	}

	$ttvars['vars']['entries'] = $list_row;
	
	$tpl->template('conf.list.weights', $tpath['conf.list.weights']);
	$tpl->vars('conf.list.weights', $ttvars);
	$tvars['vars']['entries'] = $tpl->show('conf.list.weights');
		
 
	$tvars['vars']['action'] = $lang['boxers:button_list.weights'];
	
	$tpl->template('conf.main', $tpath['conf.main']);
	$tpl->vars('conf.main', $tvars);
	print $tpl->show('conf.main');
}

function editBoxer(){
	global $mysql, $tpl, $lang;
	
	$id = isset($_REQUEST['id'])?intval($_REQUEST['id']):0;
	
	$tpath = locatePluginTemplates(array('conf.main', 'conf.edit.boxers.form', 'conf.fights'), 'boxers', 1);
	
	$boxer = $mysql->record('SELECT * FROM '.prefix.'_boxers WHERE `id` = '.db_squote($id));
	
	if(!$boxer)
		echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL='.admin_url.'/admin.php?mod=extra-config&amp;plugin=boxers&amp;action=boxer">';
	
	# load weight classes
	$weight_list = $mysql->select('SELECT * FROM '.prefix.'_boxers_weight_class', 1);
	
	# load countries with flags
	$flags = parse_ini_file(extras_dir.'/boxers/flags.ini');
	$flag_options = '<option value="">Ч</option>';
	foreach($flags as $key => $value)
		$flag_options .= ' <option value="'.$key.'" '.($key == $boxer['country'] ? 'selected="selected"' : '').' title="/engine/plugins/boxers/flags/'.$value.'">'.$key.'</option>';
	
	# these fields use as {field_name}
	$profile = array(	'id', 'full_name', 'biography', 'birthday', 'hometown', 'knockouts', 'photo', 'translit_name', 'wins', 'defeats', 'draws', 'debut', 'height', 'reach',
						'stance', 'boxrecid', 'www', 'nickname');
	foreach($profile as $row)
		$ttvars['vars'][$row] = $boxer[$row];
	
	$ttvars['vars']['flags'] = $flag_options;
	$ttvars['vars']['weight_list'] = MakeWeightList($weight_list, 'weight_list', $boxer['weight_class']);
	
	# load organizations
	$orgs = array();
	$orgs = unserialize(pluginGetVariable('boxers', 'organization'));

	$extra_position = 	array('Ч' => 'Ч', '-6' => '„емп≥он', '-5' => '—упер„емп≥он', '-4' => 'ќбъединенный „емп≥он', '-3' => '–егул€рный „емп≥он', '-2' => '¬ременный „емп≥он', '-1' => 'ќфициальный претендент');
	
	# generate ratings fields
	foreach($orgs as $row)
	$ttvars['vars']['ratings'] .= '<tr>
<td width="50%" class="contentEntry1">'.$row['name'].'</td>
<td width="50%" class="contentEntry2"><input type="text" size="80" style="width:400px;" name="rating_'.$row['db_name'].'" value="'.$boxer['rating_'.$row['db_name']].'" /></td>
</tr>
<tr>
<td width="50%" class="contentEntry1">'.$lang['boxers:extra_position'].'</td>
<td width="50%" class="contentEntry2">
'.MakeDropDown($extra_position, 'extra_position_'.$row['db_name'], $boxer['rating_'.$row['db_name']]).'</td>
</tr>';

	$fights = $mysql->select('SELECT f.*, b.translit_name, b.full_name FROM '.prefix.'_boxers_fights f LEFT JOIN '.prefix.'_boxers b ON f.id_with = b.id WHERE id_who = '.$id.' ORDER BY date DESC', 1);
	
	if($fights){ 
		$tpl->template('conf.fights', $tpath['conf.fights']);
	}
	
	$ttvars['vars']['fights'] = '';
	$i = count($fights);
	
	foreach($fights as $fight){
		$tvars['vars']['number'] = $i--;
		$tvars['vars']['date'] = $fight['date'];
		$tvars['vars']['boxer_link'] = generatePluginLink('boxers', 'profile', array('name' => $fight['translit_name']), array(), false, true);
		$tvars['vars']['boxer_name'] = $fight['full_name'];
		$tvars['vars']['news'] = $fight['news'] ? '<a target="_blank" href="'.$fight['news'].'">ƒетальн≥ше</a>' : '';
		$tvars['vars']['del_link'] = '/engine/admin.php?mod=extra-config&amp;plugin=boxers&amp;action=delfight&amp;id_who='.$fight['id_who'].'&amp;id_with='.$fight['id_with'].'&amp;date='.$fight['date'];
		$tvars['vars']['edit_link'] = '/engine/admin.php?mod=extra-config&amp;plugin=boxers&amp;action=editfight&amp;id_who='.$fight['id_who'].'&amp;id_with='.$fight['id_with'].'&amp;date='.$fight['date'];
		$tvars['vars']['photo'] = $fight['photo'] ? '<a target="_blank" href="'.$fight['photo'].'">‘ото</a>' : '';
		$tvars['vars']['video'] = $fight['video'] ? '<a target="_blank" href="'.$fight['video'].'">¬≥део</a>' : '';
		$tpl->vars('conf.fights', $tvars);
		$ttvars['vars']['fights'] .= $tpl->show('conf.fights');
	}
	
	unset($tvars);
	
	$tpl->template('conf.edit.boxers.form', $tpath['conf.edit.boxers.form']);
	$tpl->vars('conf.edit.boxers.form', $ttvars);
	$tvars['vars']['entries'] = $tpl->show('conf.edit.boxers.form');
	
	$tvars['vars']['action'] = $lang['boxers:button_edit'];
	
	$tpl->template('conf.main', $tpath['conf.main']);
	$tpl->vars('conf.main', $tvars);
	print $tpl->show('conf.main');

}

function editBoxerSave(){
	
	global $mysql, $parse, $lang;

	if(empty($_POST['full_name'])){
		msg(array('type' => 'info', 'info' => $lang['boxers:msgi_not.boxer.name']));
		return;
	}
	
	$full_name = secure_html($_POST['full_name']);
	
	$orgs = array();
	$orgs = unserialize(pluginGetVariable('boxers', 'organization'));

	$db_column = array('full_name', 'weight_class', 'birthday', 'country', 'translit_name', 'wins', 'defeats', 'draws', 'height', 'reach', 'stance', 'boxrecid', 'www', 'nickname', 'debut', 'biography', 'hometown', 'knockouts');
	$db_value  = array(	db_squote($full_name), intval($_POST['weight_list']), db_squote($_POST['birthday']), 
						($_POST['flags'] != 'Ч' ? db_squote($_POST['flags']) : 'NULL'), 
						($_POST['translit_name'] ? db_squote($_POST['translit_name']) : db_squote($parse->translit($full_name))) , 
						intval($_POST['wins']),
						intval($_POST['defeats']),
						intval($_POST['draws']),
						intval($_POST['height']),
						intval($_POST['reach']),
						db_squote($_POST['stance']),
						intval($_POST['boxrecid']),
						db_squote($_POST['www']),
						db_squote($_POST['nickname']),
						db_squote($_POST['debut']),
						db_squote($_POST['biography']),
						db_squote($_POST['hometown']),
						intval($_POST['knockouts']));
	
	foreach($orgs as $row){
		$column = 'rating_'.$row['db_name'];
		$extra = 'extra_position_'.$row['db_name'];
		
		if(isset($_POST[$column])){
			array_push($db_column, $column);
			if($_POST[$extra] != 'Ч')
				array_push($db_value, intval($_POST[$extra]));
			elseif($_POST[$column])
				array_push($db_value, intval($_POST[$column]));
			else
				array_push($db_value, 'NULL');
		}
	}
	
	array_push($db_column, 'photo');
	
	$photoname = $parse -> translit($full_name).'.'.pathinfo($_FILES['photofile']['name'], PATHINFO_EXTENSION);
	$destination = pluginGetVariable('boxers','photos.dir').$photoname;
	if (move_uploaded_file($_FILES['photofile']['tmp_name'], $destination))
		array_push($db_value, db_squote($photoname));
	else
		array_push($db_value, db_squote($_POST['photo']));
	
	$setter = array();
	$count = count($db_column);
	for($i=0; $i<$count; $i++)
		array_push($setter, $db_column[$i].' = '.$db_value[$i]);
	
	$mysql->query('UPDATE '.prefix.'_boxers SET 
							'.implode(',', $setter).'
							WHERE `id` = '.intval($_POST['id']));

	echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL='.admin_url.'/admin.php?mod=extra-config&amp;plugin=boxers&amp;action=boxer">';

}

function addBoxer(){
	global $mysql, $tpl, $lang;
	
	$tpath = locatePluginTemplates(array('conf.main', 'conf.add.boxers.form'), 'boxers', 1);
	
	$weight_list = $mysql->select('SELECT * FROM '.prefix.'_boxers_weight_class', 1);
	$ttvars['vars']['weight_list'] = MakeWeightList($weight_list, 'weight_list');
	
	$flags = parse_ini_file(extras_dir.'/boxers/flags.ini');
	$flag_options = '<option value="">Ч</option>';
	foreach($flags as $key => $value)
		$flag_options .= ' <option value="'.$key.'" title="/engine/plugins/boxers/flags/'.$value.'">'.$key.'</option>';
	
	$ttvars['vars']['flags'] = $flag_options;
	
	$orgs = array();
	$orgs = unserialize(pluginGetVariable('boxers', 'organization'));

	$extra_position = 	array('Ч' => 'Ч', '-6' => '„емп≥он', '-5' => '—упер„емп≥он', '-4' => 'ќбъединенный „емп≥он', '-3' => '–егул€рный „емп≥он', '-2' => '¬ременный „емп≥он', '-1' => 'ќфициальный претендент');
	
	foreach($orgs as $row)
	$ttvars['vars']['ratings'] .= '<tr>
<td width="50%" class="contentEntry1">'.$row['name'].'</td>
<td width="50%" class="contentEntry2"><input type="text" size="80" style="width:400px;" name="rating_'.$row['db_name'].'" value="'.$boxer['rating_'.$row['db_name']].'" /></td>
</tr>
<tr>
<td width="50%" class="contentEntry1">'.$lang['boxers:extra_position'].'</td>
<td width="50%" class="contentEntry2">
'.MakeDropDown($extra_position, 'extra_position_'.$row['db_name']).'
</td>
</tr>';

	$tpl->template('conf.add.boxers.form', $tpath['conf.add.boxers.form']);
	$tpl->vars('conf.add.boxers.form', $ttvars);
	$tvars['vars']['entries'] = $tpl->show('conf.add.boxers.form');
	
	$tvars['vars']['action'] = $lang['boxers:button_add'];
	
	$tpl->template('conf.main', $tpath['conf.main']);
	$tpl->vars('conf.main', $tvars);
	print $tpl->show('conf.main');	
}

function addBoxerSave(){
	global $mysql, $parse;
	
	if(empty($_POST['full_name'])){
		msg(array('type' => 'info', 'info' => $lang['boxers:msgi_not.boxer.name']));
		return;
	}
	
	$full_name = secure_html($_POST['full_name']);
	
	$orgs = array();
	$orgs = unserialize(pluginGetVariable('boxers', 'organization'));

	$db_column = array('full_name', 'weight_class', 'birthday', 'country', 'translit_name', 'wins', 'defeats', 'draws', 'height', 'reach', 'stance', 'boxrecid', 'www', 'nickname', 'debut', 'biography', 'hometown', 'knockouts');
	$db_value  = array(	db_squote($full_name), intval($_POST['weight_list']), db_squote($_POST['birthday']), 
						($_POST['flags'] != 'Ч' ? db_squote($_POST['flags']) : 'NULL'), 
						($_POST['translit_name'] ? db_squote($_POST['translit_name']) : db_squote($parse->translit($full_name))) , 
						intval($_POST['wins']),
						intval($_POST['defeats']),
						intval($_POST['draws']),
						intval($_POST['height']),
						intval($_POST['reach']),
						db_squote($_POST['stance']),
						intval($_POST['boxrecid']),
						db_squote($_POST['www']),
						db_squote($_POST['nickname']),
						db_squote($_POST['debut']),
						db_squote($_POST['biography']),
						db_squote($_POST['hometown']),
						intval($_POST['knockouts']));
	
	foreach($orgs as $row){
		$column = 'rating_'.$row['db_name'];
		$extra = 'extra_position_'.$row['db_name'];
		
		if(isset($_POST[$column])){
			array_push($db_column, $column);
			if($_POST[$extra] != 'Ч')
				array_push($db_value, intval($_POST[$extra]));
			elseif($_POST[$column])
				array_push($db_value, intval($_POST[$column]));
			else
				array_push($db_value, 'NULL');
		}
	}
	
	array_push($db_column, 'photo');
	
	$photoname = $parse -> translit($full_name).'.'.pathinfo($_FILES['photofile']['name'], PATHINFO_EXTENSION);
	$destination = pluginGetVariable('boxers','photos.dir').$photoname;
	if (move_uploaded_file($_FILES['photofile']['tmp_name'], $destination))
		array_push($db_value, db_squote($photoname));
	else
		array_push($db_value, db_squote($_POST['photo']));
		
	$mysql->query('INSERT INTO '.prefix.'_boxers ('.implode(',', $db_column).') VALUES('.implode(',', $db_value).')');

	showBoxersList();
}

function delBoxer(){
	global $mysql;
	
	if(!empty($_GET['id']) && $row = $mysql->record('SELECT id FROM '.prefix.'_boxers WHERE id = '.db_squote($_GET['id'])))
		$mysql->query('DELETE FROM '.prefix.'_boxers WHERE id = '.db_squote($_GET['id']));
	
	echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL='.admin_url.'/admin.php?mod=extra-config&amp;plugin=boxers&amp;action=boxer">';
}

function addWeight(){
	global $mysql, $tpl, $lang;
	
	$tpath = locatePluginTemplates(array('conf.main', 'conf.add.weight.form'), 'boxers', 1);
	
	$tpl->template('conf.add.weight.form', $tpath['conf.add.weight.form']);
	$tpl->vars('conf.add.weight.form', $ttvars);
	$tvars['vars']['entries'] = $tpl->show('conf.add.weight.form');
	
	$tvars['vars']['action'] = $lang['boxers:button_add'];
	
	$tpl->template('conf.main', $tpath['conf.main']);
	$tpl->vars('conf.main', $tvars);
	print $tpl->show('conf.main');	
}

function addWeightSave(){
	global $mysql;
	
	if(empty($_POST['name'])) return;
	
	$name = secure_html($_POST['name']);
		
	$mysql->query('INSERT INTO '.prefix.'_boxers_weight_class (`name`) VALUES('.db_squote($name).')');

	echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL='.admin_url.'/admin.php?mod=extra-config&amp;plugin=boxers&amp;action=weights">';
}

function editWeight(){
	global $mysql, $tpl, $lang;
	
	$tpath = locatePluginTemplates(array('conf.main', 'conf.edit.weight.form'), 'boxers', 1);
	
	$id = isset($_REQUEST['id'])?intval($_REQUEST['id']):0;
		
	$weight = $mysql->record('SELECT * FROM '.prefix.'_boxers_weight_class WHERE `id` = '.db_squote($id));
	
	if(!$weight)
		echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL='.admin_url.'/admin.php?mod=extra-config&amp;plugin=boxers&amp;action=weights">';
		
	$ttvars['vars']['id'] = $weight['id'];
	$ttvars['vars']['name'] = $weight['name'];
	
	$tpl->template('conf.edit.weight.form', $tpath['conf.edit.weight.form']);
	$tpl->vars('conf.edit.weight.form', $ttvars);
	$tvars['vars']['entries'] = $tpl->show('conf.edit.weight.form');
	
	$tvars['vars']['action'] = $lang['boxers:button_edit'];
	
	$tpl->template('conf.main', $tpath['conf.main']);
	$tpl->vars('conf.main', $tvars);
	print $tpl->show('conf.main');	
}

function editWeightSave(){
	global $mysql;

	if(empty($_POST['name'])) return;
	
	$name = secure_html($_POST['name']);
		
	$mysql->query('UPDATE '.prefix.'_boxers_weight_class SET 
							name		=	'.db_squote($name).
							'WHERE `id` = '.intval($_POST['id']));

	echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL='.admin_url.'/admin.php?mod=extra-config&amp;plugin=boxers&amp;action=weights">';

}

function delWeight(){
	global $mysql, $lang;
	
	if(!empty($_GET['id']) && $row = $mysql->record('SELECT id FROM '.prefix.'_boxers_weight_class WHERE id = '.db_squote($_GET['id'])))
		if(!$mysql->record('SELECT id FROM '.prefix.'_boxers WHERE weight_class = '.db_squote($row['id'])))
			$mysql->query('DELETE FROM '.prefix.'_boxers_weight_class WHERE id = '.db_squote($_GET['id']));
		else
			msg(array("type" => "error", "info" => $lang['boxers:msgi_notdelweight']));

	echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL='.admin_url.'/admin.php?mod=extra-config&amp;plugin=boxers&amp;action=weights">';
}

function showOrganization(){

	global $tpl, $lang;
	
	$orgs = array();
	$orgs = unserialize(pluginGetVariable('boxers', 'organization'));
	
	$tpath = locatePluginTemplates(array('conf.main', 'conf.list.organization', 'conf.list.organization.row'), 'boxers', 1);
	
	$list_row = '';
	
	foreach ($orgs as $row){
	
		$pvars['vars']['name'] = $row['name'];
		$pvars['vars']['db_name'] = $row['db_name'];
			
		$tpl->template('conf.list.organization.row', $tpath['conf.list.organization.row']);
		$tpl -> vars('conf.list.organization.row', $pvars);
		$list_row .= $tpl->show('conf.list.organization.row');
	}

	$ttvars['vars']['entries'] = $list_row;
	
	$tpl->template('conf.list.organization', $tpath['conf.list.organization']);
	$tpl->vars('conf.list.organization', $ttvars);
	$tvars['vars']['entries'] = $tpl->show('conf.list.organization');
		
 
	$tvars['vars']['action'] = $lang['boxers:button_list.organization'];
	
	$tpl->template('conf.main', $tpath['conf.main']);
	$tpl->vars('conf.main', $tvars);
	print $tpl->show('conf.main');	
}

function addOrganization(){
	
	global $mysql, $tpl, $lang;
	
	$tpath = locatePluginTemplates(array('conf.main', 'conf.add.organization.form'), 'boxers', 1);
	
	$tpl->template('conf.add.organization.form', $tpath['conf.add.organization.form']);
	$tpl->vars('conf.add.organization.form', $ttvars);
	$tvars['vars']['entries'] = $tpl->show('conf.add.organization.form');
	
	$tvars['vars']['action'] = $lang['boxers:button_add'];
	
	$tpl->template('conf.main', $tpath['conf.main']);
	$tpl->vars('conf.main', $tvars);
	print $tpl->show('conf.main');	
}

function addOrganizationSave(){

	global $mysql, $lang;
	
	if(empty($_POST['name'])) return;
	
	$name = secure_html($_POST['name']);
	
	$orgs = array();
	$orgs = unserialize(pluginGetVariable('boxers', 'organization'));
		
	$_POST['db_name'] = preg_replace('/[^a-zA-Zј-яа-€0-9\s]/', '' , trim($_POST['db_name']));
	foreach($orgs as $row){
		if($row['db_name'] == $_POST['db_name'])
			msg(array('type' => 'error', 'info' => $lang['boxers:msgi_orgcolexists']));
	}
		
	array_push($orgs, array('name' => $name, 'db_name' => $_POST['db_name']));
	pluginSetVariable('boxers', 'organization', serialize($orgs));
	pluginsSaveConfig();
	
	$mysql->query('ALTER TABLE '.prefix.'_boxers ADD rating_'.$_POST['db_name'].' int(5) DEFAULT NULL');
	$mysql->query('ALTER TABLE '.prefix.'_boxers_ratings_history ADD rating_'.$_POST['db_name'].' int(5) DEFAULT NULL');

	echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL='.admin_url.'/admin.php?mod=extra-config&amp;plugin=boxers&amp;action=organization">';
}

function editOrganization(){
	
	global $mysql, $tpl, $lang;
	
	$tpath = locatePluginTemplates(array('conf.main', 'conf.edit.organization.form'), 'boxers', 1);
		
	$orgs = array();
	$orgs = unserialize(pluginGetVariable('boxers', 'organization'));
	
	$flag = false;
	foreach($orgs as $row){
		if($row['db_name'] == $_GET['id']){
			$ttvars['vars']['name'] = $row['name'];
			$ttvars['vars']['db_name'] = $row['db_name']; 
			$flag = true;
			break;
		}
	}		
	
	if(!$flag)
		echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL='.admin_url.'/admin.php?mod=extra-config&amp;plugin=boxers&amp;action=organization">';
		
	$tpl->template('conf.edit.organization.form', $tpath['conf.edit.organization.form']);
	$tpl->vars('conf.edit.organization.form', $ttvars);
	$tvars['vars']['entries'] = $tpl->show('conf.edit.organization.form');
	
	$tvars['vars']['action'] = $lang['boxers:button_edit'];
	
	$tpl->template('conf.main', $tpath['conf.main']);
	$tpl->vars('conf.main', $tvars);
	print $tpl->show('conf.main');	
}

function editOrganizationSave(){
	global $mysql, $lang;
	
	if(empty($_POST['name'])) return;
	
	$name = secure_html($_POST['name']);
	
	$orgs = array();
	$orgs = unserialize(pluginGetVariable('boxers', 'organization'));
		
	$_POST['db_name'] = preg_replace('/[^a-zA-Zј-яа-€0-9\s]/', '' , trim($_POST['db_name']));
	foreach($orgs as &$row){
		if($row['db_name'] == $_POST['id']){
			
			if($row['db_name'] != $_POST['db_name']){
				$mysql->query('ALTER TABLE '.prefix.'_boxers CHANGE rating_'.$row['db_name'].' rating_'.$_POST['db_name'].' int(5) DEFAULT NULL');
				$mysql->query('ALTER TABLE '.prefix.'_boxers_ratings_history CHANGE rating_'.$row['db_name'].' rating_'.$_POST['db_name'].' int(5) DEFAULT NULL');
				$row['db_name'] = $_POST['db_name'];
			}	
			
			if($row['name'] != $_POST['name'])
				$row['name'] = $_POST['name'];
			
			pluginSetVariable('boxers', 'organization', serialize($orgs));
			pluginsSaveConfig();
			
			break;
		}
	}
	echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL='.admin_url.'/admin.php?mod=extra-config&amp;plugin=boxers&amp;action=organization">';
}

function delOrganization(){
	global $mysql, $lang;
	
	$_GET['id'] = preg_replace('/[^a-zA-Zј-яа-€0-9\s]/', '' , trim($_GET['id']));
	
	if(!empty($_GET['id']) && !$row = $mysql->record('SELECT id FROM '.prefix.'_boxers WHERE rating_'.$_GET['id'].' LIMIT 1')){
		$mysql->query('ALTER TABLE '.prefix.'_boxers DROP rating_'.$_GET['id']);
		$mysql->query('ALTER TABLE '.prefix.'_boxers_ratings_history DROP rating_'.$_GET['id']);
			
		$orgs = array();
		$orgs = unserialize(pluginGetVariable('boxers', 'organization'));
		
		$count = count($orgs);
		for($i=0; $i<$count; $i++)
			if($orgs[$i]['db_name'] == $_GET['id']){
				unset($orgs[$i]);
				pluginSetVariable('boxers', 'organization', serialize($orgs));
				pluginsSaveConfig();
				break;
			}
		}
		else
			msg(array("type" => "error", "info" => $lang['boxers:msgi_notdelorg']));

	echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL='.admin_url.'/admin.php?mod=extra-config&amp;plugin=boxers&amp;action=organization">';
}

function showRatings(){
	global $mysql, $tpl, $lang;
	
	$tpath = locatePluginTemplates(array('conf.ratings', 'conf.main'), 'boxers', 1);
	$count = pluginGetVariable('boxers', 'ratings.count');
	
	$ttvars['vars']['ratings.count'] = pluginGetVariable('boxers', 'ratings.count');
	
	$orgs = array();
	$orgs = unserialize(pluginGetVariable('boxers', 'organization'));
	
	$weight_list = $mysql->select('SELECT * FROM '.prefix.'_boxers_weight_class', 1);
	
	for($i=1; $i<=$count; $i++) 
		$ttvars['vars']['ratings'] .= '<fieldset>
<legend><b>'.$i.'</b></legend>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%" class="contentEntry1">'.$lang['boxers:button_list.organization'].'</td>
<td width="50%" class="contentEntry2">'.MakeOrgList($orgs, 'orgs_'.$i, pluginGetVariable('boxers', 'orgs_'.$i)).'</td>
</tr>
<tr>
<td width="50%" class="contentEntry1">'.$lang['boxers:button_list.weights'].'</td>
<td width="50%" class="contentEntry2">'.MakeWeightList($weight_list, 'weight_'.$i, pluginGetVariable('boxers', 'weight_'.$i)).'</td>
</tr>
<tr>
<td width="50%" class="contentEntry1">'.$lang['boxers:rating.boxers.count'].'</td>
<td width="50%" class="contentEntry2"><input type="text" style="width:410px;" name="rating_count_'.$i.'" value="'.pluginGetVariable('boxers', 'rating.count_'.$i).'"></td>
</tr>
<tr>
<td width="50%" class="contentEntry1">'.$lang['boxers:var'].'<b>{boxer_rating_'.$i.'}</b></td>
</tr>
</table>
</fieldset>';

	$history = array();
	if(file_get_contents(extras_dir.'/boxers/history.txt'))
		$history = unserialize(file_get_contents(extras_dir.'/boxers/history.txt')); 
	
	$ttvars['vars']['history'] = MakeDropDown($history, 'history');
	
	$tpl->template('conf.ratings', $tpath['conf.ratings']);
	$tpl->vars('conf.ratings', $ttvars);
	$tvars['vars']['entries'] = $tpl->show('conf.ratings');
	
	$tvars['vars']['action'] = $lang['boxers:button_list.ratings'];
	
	$tpl->template('conf.main', $tpath['conf.main']);
	$tpl->vars('conf.main', $tvars);
	print $tpl->show('conf.main');
}

function saveRatings(){
	if(!empty($_POST['ratings_count']))
		pluginSetVariable('boxers', 'ratings.count', intval($_POST['ratings_count']));
		
	for($i=1; $i<=pluginGetVariable('boxers', 'ratings.count'); $i++){
		pluginSetVariable('boxers', 'orgs_'.$i, $_POST['orgs_'.$i]);
		pluginSetVariable('boxers', 'weight_'.$i, $_POST['weight_'.$i]);
		pluginSetVariable('boxers', 'rating.count_'.$i, $_POST['rating_count_'.$i]);
	}
		
	pluginsSaveConfig();
	
	echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL='.admin_url.'/admin.php?mod=extra-config&amp;plugin=boxers&amp;action=ratings">';
}

function saveRatingsHistory(){
	
	global $mysql;
	
	$orgs = array();
	$orgs = unserialize(pluginGetVariable('boxers', 'organization'));
	
	$db_column = array('id_boxers', 'date', 'weight');
		
	$boxers = $mysql->select('SELECT * FROM '.prefix.'_boxers');
	
	foreach($orgs as $row)
		array_push($db_column, 'rating_'.$row['db_name']);
	
	$history = array();
	if(file_get_contents(extras_dir.'/boxers/history.txt'))
		$history = unserialize(file_get_contents(extras_dir.'/boxers/history.txt')); 
	if(!array_search(date('Y-m-d'), $history))
		array_push($history, date('Y-m-d'));
	file_put_contents(extras_dir.'/boxers/history.txt', serialize($history));	
	
	foreach($boxers as $boxer){
		unset($db_value);
		$db_value = array($boxer['id'], db_squote(date('Y-m-d')), $boxer['weight_class']);		
		foreach($orgs as $row)
			array_push($db_value, $boxer['rating_'.$row['db_name']] ? $boxer['rating_'.$row['db_name']] : 'NULL');
		
		$mysql->query('INSERT INTO '.prefix.'_boxers_ratings_history ('.implode(',' ,$db_column).') VALUES ('.implode(',' ,$db_value).')');
	}
	
	echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL='.admin_url.'/admin.php?mod=extra-config&amp;plugin=boxers&amp;action=ratings">';
}

function deleteRatingsHistory(){
	global $mysql;
	
	$id = $_POST['history'];
	$id =  isset($_POST['history']) ? intval($_POST['history']) : -1;
	
	if(file_get_contents(extras_dir.'/boxers/history.txt'))
		$history = unserialize(file_get_contents(extras_dir.'/boxers/history.txt')); 	

	if(!array_key_exists($id, $history))
		$id = -1;
	
	if($id != -1){
		$mysql->query('DELETE FROM '.prefix.'_boxers_ratings_history WHERE date = '.db_squote($history[$id]));
		
		unset($history[$id]);
		file_put_contents(extras_dir.'/boxers/history.txt', serialize($history));	
	}
	
	echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL='.admin_url.'/admin.php?mod=extra-config&amp;plugin=boxers&amp;action=ratings">';
}

function addFightSave(){

	global $mysql;
	
	$with = $mysql->record('SELECT id FROM '.prefix.'_boxers WHERE full_name = '.db_squote($_POST['f_name']));
	
	if(!$with){
		msg(array('type' => 'info', 'info' => '—оперник не найден в базе данных'));
		return;
	}
	
	$db_value = array(intval($_POST['id']), $with['id'], db_squote($_POST['f_date']), db_squote($_POST['f_video']), db_squote($_POST['f_photo']), db_squote($_POST['f_news']));
	
	$mysql->query('INSERT INTO '.prefix.'_boxers_fights (id_who, id_with, date, video, photo, news) VALUES('.implode(', ', $db_value).')');
	$mysql->query('INSERT INTO '.prefix.'_boxers_fights (id_with, id_who, date, video, photo, news) VALUES('.implode(', ', $db_value).')');
	
	echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL='.admin_url.'/admin.php?mod=extra-config&amp;plugin=boxers&amp;action=editboxer&amp;id='.$_POST['id'].'">';
}

function delFight(){
	
	global $mysql;
	
	$fight = $mysql->record('SELECT id_who FROM '.prefix.'_boxers_fights WHERE id_with = '.intval($_GET['id_with']).' AND id_who = '.intval($_GET['id_who']).' AND date = '.db_squote($_GET['date']));
	
	if(!$fight)
		echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL='.admin_url.'/admin.php?mod=extra-config&amp;plugin=boxers&amp;action=boxer">';
	
	$mysql->query('DELETE FROM '.prefix.'_boxers_fights WHERE id_with = '.intval($_GET['id_with']).' AND id_who = '.intval($_GET['id_who']).' AND date = '.db_squote($_GET['date']));
	$mysql->query('DELETE FROM '.prefix.'_boxers_fights WHERE id_with = '.intval($_GET['id_who']).' AND id_who = '.intval($_GET['id_with']).' AND date = '.db_squote($_GET['date']));
	
	echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL='.admin_url.'/admin.php?mod=extra-config&amp;plugin=boxers&amp;action=editboxer&amp;id='.$_GET['id_who'].'">';
}

function editFight(){

	global $mysql, $tpl;
	
	$fight = $mysql->record('SELECT f.*, b.full_name FROM '.prefix.'_boxers_fights f LEFT JOIN '.prefix.'_boxers b ON f.id_with = b.id WHERE id_with = '.intval($_GET['id_with']).' AND id_who = '.intval($_GET['id_who']).' AND date = '.db_squote($_GET['date']));
	
	if(!$fight)
		echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL='.admin_url.'/admin.php?mod=extra-config&amp;plugin=boxers&amp;action=boxer">';
	
	$tpath = locatePluginTemplates(array('conf.main', 'conf.edit.fights'), 'boxers', 1);
	
	$ttvars['vars']['id_who'] = $_GET['id_who'];
	$ttvars['vars']['id_with'] = $_GET['id_with'];
	$ttvars['vars']['date'] = $_GET['date'];
	
	$ttvars['vars']['f_name'] = $fight['full_name'];
	$ttvars['vars']['f_video'] = $fight['video'];
	$ttvars['vars']['f_photo'] = $fight['photo'];
	$ttvars['vars']['f_news'] = $fight['news'];
	$ttvars['vars']['f_date'] = $fight['date'];
	
	$tpl->template('conf.edit.fights', $tpath['conf.edit.fights']);
	$tpl->vars('conf.edit.fights', $ttvars);
	$tvars['vars']['entries'] = $tpl->show('conf.edit.fights');
	
	$tvars['vars']['action'] = '–едактировать бой';
	
	$tpl->template('conf.main', $tpath['conf.main']);
	$tpl->vars('conf.main', $tvars);
	print $tpl->show('conf.main');
	
}

function editFightSave(){
	
	global $mysql;
	
	$db_column = array('date', 'video', 'photo', 'news');
	$db_value = array(db_squote($_POST['f_date']), db_squote($_POST['f_video']), db_squote($_POST['f_photo']), db_squote($_POST['f_news']));
	
	$setter = array();
	$count = count($db_column);
	for($i=0; $i<$count; $i++)
		array_push($setter, $db_column[$i].' = '.$db_value[$i]);
	
	$mysql->query('UPDATE '.prefix.'_boxers_fights SET '.implode(', ', $setter).' WHERE id_with = '.intval($_POST['id_with']).' AND id_who = '.intval($_POST['id_who']).' AND date = '.db_squote($_POST['date']));
	$mysql->query('UPDATE '.prefix.'_boxers_fights SET '.implode(', ', $setter).' WHERE id_with = '.intval($_POST['id_who']).' AND id_who = '.intval($_POST['id_with']).' AND date = '.db_squote($_POST['date']));

	echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL='.admin_url.'/admin.php?mod=extra-config&amp;plugin=boxers&amp;action=editboxer&amp;id='.intval($_POST['id_who']).'">';
}