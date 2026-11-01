<?php
if (!defined('NGCMS')) die('HAL');
error_reporting(E_ALL);
ini_set('display_errors', 1);
pluginsLoadConfig();
LoadPluginLang('grab', 'config', '', '', '#');

include_once(dirname(__FILE__) . '/lib/phpQuery.php');

switch ($_REQUEST['action'] ?? 'main') {
	case 'test':
		test();
		break;
	case 'create':
		create();
		break;
	default:
		main();
}

function create()
{
	global $twig, $userROW, $mysql, $config, $PHP_SELF;

	include_once(dirname(__FILE__) . '/lib/grab.php');
	$tpath = locatePluginTemplates(['main', 'create'], 'grab', 1);

	if (isset($_REQUEST['submit'])) {
		require_once(root . '/includes/inc/lib_admin.php');
		LoadLang('addnews', 'admin', 'addnews');

		$_REQUEST['title'] = $grab_h1;
		$_REQUEST['ng_news_content'] = $grab_text;
		$_REQUEST['approve'] = 1;
		$_REQUEST['mainpage'] = 1;
		$_REQUEST['catid'] = (int)$_REQUEST['category'];

		addNews(['no.token' => true]);
		error_log("Grabber: Added news - " . $grab_h1);

		msg(["type" => "success", "text" => "Новость успешно добавлена"]);
		header("Location: " . admin_url . "/admin.php?mod=extra-config&plugin=grab");
		exit;
	}

	$xt = $twig->loadTemplate($tpath['create'] . 'create.tpl');
	$tVars = [
		'token' => genUToken('admin.news.add'),
		'php_self' => $PHP_SELF,
		'mastercat' => makeCategoryList(['doempty' => 1, 'greyempty' => false, 'nameval' => 0]),
		'quicktags' => ($config['use_bbcodes']) ? QuickTags('ng_news_content_short', 'news') : '',
		'grab_h1' => secure_html($grab_h1),
		'grab_text' => secure_html($grab_text)
	];

	$xg = $twig->loadTemplate($tpath['main'] . 'main.tpl');
	print $xg->render([
		'global' => 'Создать новость',
		'tabs' => getNavigationTabs('create'),
		'entries' => $xt->render($tVars)
	]);
}

function test()
{
	global $twig;

	include_once(dirname(__FILE__) . '/lib/grab.php');
	$tpath = locatePluginTemplates(['main', 'test'], 'grab', 1);

	$xt = $twig->loadTemplate($tpath['test'] . 'test.tpl');
	$tVars = [
		'grab_h1' => $grab_h1,
		'grab_text' => $grab_text,
	];

	$xg = $twig->loadTemplate($tpath['main'] . 'main.tpl');
	print $xg->render([
		'global' => 'Тест парсинга',
		'tabs' => getNavigationTabs('test'),
		'entries' => $xt->render($tVars)
	]);
}

function main()
{
	global $twig;

	$tpath = locatePluginTemplates(['main', 'general.from'], 'grab', 1);

	if (isset($_REQUEST['submit'])) {
		pluginSetVariable('grab', 'url', $_REQUEST['url']);
		pluginSetVariable('grab', 'header', $_REQUEST['header']);
		pluginSetVariable('grab', 'content', $_REQUEST['content']);
		pluginsSaveConfig();
		msg(["type" => "success", "text" => "Настройки сохранены"]);
	}

	$xt = $twig->loadTemplate($tpath['general.from'] . 'general.from.tpl');
	$tVars = [
		'url' => pluginGetVariable('grab', 'url'),
		'header' => pluginGetVariable('grab', 'header'),
		'content' => pluginGetVariable('grab', 'content')
	];

	$xg = $twig->loadTemplate($tpath['main'] . 'main.tpl');
	print $xg->render([
		'global' => 'Главная',
		'tabs' => getNavigationTabs('main'),
		'entries' => $xt->render($tVars)
	]);
}

function getNavigationTabs($active_tab)
{
	return [
		'main' => [
			'title' => 'Главная',
			'url' => '?mod=extra-config&plugin=grab',
			'active' => ($active_tab === 'main')
		],
		'test' => [
			'title' => 'Тест парсинга',
			'url' => '?mod=extra-config&plugin=grab&action=test',
			'active' => ($active_tab === 'test')
		],
		'create' => [
			'title' => 'Создать новость',
			'url' => '?mod=extra-config&plugin=grab&action=create',
			'active' => ($active_tab === 'create')
		]
	];
}
