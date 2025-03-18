<?php

// Защита от прямого доступа
if (!defined('NGCMS')) die('HAL');

register_plugin_page('content_generator', '', 'plugin_content_generator', 0);

function generateContent($type, $count)
{
	include_once(root . 'includes/inc/lib_admin.php');
	include_once(__DIR__ . '/lib/Faker/autoload.php');
	
	include_once(__DIR__ . '/lib/addStatics.php');

	global $SQL; // Получаем объект SQL

	$faker = Faker\Factory::create('ru_RU');

	for ($i = 0; $i < $count; $i++) {
		$title = $faker->realText(30, 1); // Генерация заголовка
		$content = $faker->realText();   // Генерация контента

		if ($type === 'news') {
			$_REQUEST['title'] = $title;
			$_REQUEST['ng_news_content'] = $content;
			$_REQUEST['approve'] = 1;
			$_REQUEST['mainpage'] = 1;
			
			addNews(['no.token' => true]);
		} elseif ($type === 'static') {
			// Подготовка данных для addStatics()
			$_REQUEST['title'] = $title;
			$_REQUEST['content'] = $content;
			$_REQUEST['flag_published'] = 1;
			
			// Вызов addStatics()
			addStatics(['no.token' => true]);
		}
	}
}

function plugin_content_generator()
{
	global $SUPRESS_TEMPLATE_SHOW, $SYSTEM_FLAGS;

	$SUPRESS_TEMPLATE_SHOW = 1;
	$SUPRESS_MAINBLOCK_SHOW = 1;

	@header('Content-type: application/json; charset=utf-8');
	$SYSTEM_FLAGS['http.headers'] = [
		'content-type'  => 'application/json; charset=utf-8',
		'cache-control' => 'private',
	];

	$count = (int)($_REQUEST['real_count'] ?? 0);
	$action = $_REQUEST['actionName'] ?? '';

	if ($count < 1) {
		echo json_encode(['error' => 'Invalid count']);
		exit();
	}

	try {
		switch ($action) {
			case 'generate_news':
				generateContent('news', $count);
				break;
			case 'generate_static':
				generateContent('static', 12);
				break;
			default:
				echo json_encode(['error' => 'Invalid action']);
				exit();
		}

		ob_end_clean();
		echo json_encode([
			'status' => 'success',
			'count' => $count,
			'action' => $action
		]);
	} catch (Exception $e) {
		error_log("Ошибка в plugin_content_generator: " . $e->getMessage());
		echo json_encode(['error' => $e->getMessage()]);
	}

	exit();
}