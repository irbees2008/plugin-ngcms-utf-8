<?php

// Защита от прямого доступа
if (!defined('NGCMS')) die('HAL');

register_plugin_page('content_parser', '', 'plugin_content_parse', 0);

/**
 * Парсинг RSS-канала и создание новостей
 */
function parseRssFeed($rssUrl, $count)
{
	// Загружаем RSS-канал через cURL
	$rss = loadRssFeed($rssUrl);
	if ($rss === false) {
		throw new Exception('Ошибка загрузки RSS-канала');
	}

	$items = [];
	$parsedCount = 0;

	// Проверяем наличие тегов <item>
	if (!isset($rss->channel->item)) {
		throw new Exception('Некорректная структура RSS-канала: отсутствуют теги <item>');
	}

	foreach ($rss->channel->item as $item) {
		if ($parsedCount >= $count) {
			break;
		}

		// Извлекаем данные
		$title = secure_html((string)$item->title);
		$content = extractDescription((string)$item->description); // Обрабатываем описание
		$pubDate = strtotime((string)$item->pubDate);

		$items[] = [
			'title' => $title,
			'content' => $content,
			'postdate' => $pubDate,
		];

		$parsedCount++;
	}

	return $items;
}

function loadRssFeed($rssUrl)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $rssUrl);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Таймаут 10 секунд
	$response = curl_exec($ch);

	if (curl_errno($ch)) {
		error_log("Ошибка cURL: " . curl_error($ch));
		curl_close($ch);
		return false;
	}

	curl_close($ch);

	// Преобразуем ответ в SimpleXML
	$rss = simplexml_load_string($response);
	if ($rss === false) {
		error_log("Ошибка разбора XML: " . $rssUrl);
		return false;
	}

	return $rss;
}

function extractDescription($description)
{
	// Удаляем HTML-теги
	$text = strip_tags($description);

	return $text;
}

function createContentFromRss($type, $items)
{
	global $SUPRESS_TEMPLATE_SHOW;

	foreach ($items as $item) {
		if ($type === 'news') {
			// Готовим данные для добавления через addNews
			$_REQUEST['title'] = $item['title'];
			$_REQUEST['ng_news_content'] = $item['content'];
			$_REQUEST['approve'] = -1;
			$_REQUEST['mainpage'] = 1;
			$_REQUEST['postdate'] = $item['postdate'];

			// Добавляем новость через функцию CMS
			include_once(root . 'includes/inc/lib_admin.php');
			addNews(['no.token' => true]);

			error_log("Новость добавлена: " . $item['title']);
		}
	}
}

function plugin_content_parse()
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
	$rssUrl = $_REQUEST['rss_url'] ?? '';

	if ($count < 1 || empty($rssUrl)) {
		echo json_encode(['error' => 'Invalid count or RSS URL']);
		exit();
	}

	try {
		// Парсим RSS-канал
		$items = parseRssFeed($rssUrl, $count);

		// Создаем новости
		createContentFromRss('news', $items);

		ob_end_clean();
		echo json_encode([
			'status' => 'success',
			'count' => $count,
			'action' => $action
		]);
	} catch (Exception $e) {
		error_log("Ошибка в plugin_content_parse: " . $e->getMessage());
		echo json_encode(['error' => $e->getMessage()]);
	}

	exit();
}