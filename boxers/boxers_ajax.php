<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('Galaxy in danger');

rpcRegisterFunction('boxer_get_name', 'plugin_boxer_ajax_get_name');

function plugin_boxer_ajax_get_name($params){
	global $userROW, $mysql;

	if (!is_array($userROW)) {
		return array('status' => 0, 'errorCode' => 3, 'errorText' => 'Access denied');
	}

	$searchName = iconv('UTF-8', 'Windows-1251', $params);
	

	$SQL = 'SELECT full_name FROM '.uprefix.'_boxers WHERE full_name LIKE '.db_squote('%'.$searchName.'%').' ORDER BY full_name DESC LIMIT 10';

	$output = array();
	foreach ($mysql->select($SQL) as $row) {
		$output[] = array(iconv('Windows-1251', 'UTF-8', $row['full_name']));
	}

	return array('status' => 1, 'errorCode' => 0, 'data' => array($params, $output));
}