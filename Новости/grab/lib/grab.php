<?php
function getcontents($url)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
	$output = curl_exec($ch);
	curl_close($ch);
	return $output;
}

$url = pluginGetVariable('grab', 'url');
if (!$url) die('URL не указан');

$html = getcontents($url);
if (!$html) die('Не удалось загрузить страницу');

$doc = phpQuery::newDocument($html);

// Получаем заголовок
$header_selector = pluginGetVariable('grab', 'header');
$grab_h1 = $doc->find($header_selector)->text();

// Получаем контент
$content_selector = pluginGetVariable('grab', 'content');
$grab_text = $doc->find($content_selector)->html();

// Очистка от ненужных элементов
$grab_text = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $grab_text);
$grab_text = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $grab_text);
$grab_text = strip_tags($grab_text, '<p><a><img><ul><ol><li><strong><em><b><i><u><h1><h2><h3><h4><h5><h6><br><hr><table><tr><td><th>');

// Отладочный вывод (можно удалить после проверки)
if (empty($grab_h1)) die("Заголовок не найден. Проверьте селектор: {$header_selector}");
if (empty($grab_text)) die("Контент не найден. Проверьте селектор: {$content_selector}");
