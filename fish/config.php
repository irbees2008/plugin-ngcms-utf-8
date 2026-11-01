<?php

// Защита от прямого доступа
if (!defined('NGCMS')) {
	die('HAL');
}

// Загрузка конфигурации плагинов и языковых файлов
pluginsLoadConfig();
LoadPluginLang('ireplace', 'main', '', '', ':');

// Проверка прав доступа (функция должна быть определена в CMS)
function is_admin()
{
	// Проверка для NGCMS (пример)
	return isset($GLOBALS['userROW']['status']) && $GLOBALS['userROW']['status'] == 1;
}

// Функция для генерации случайного текста
function generate_random_text($paragraphs = 3, $sentences_per_paragraph = 5)
{
	$words = [
		'Lorem',
		'ipsum',
		'dolor',
		'sit',
		'amet',
		'consectetur',
		'adipiscing',
		'elit',
		'sed',
		'do',
		'eiusmod',
		'tempor',
		'incididunt',
		'ut',
		'labore',
		'et',
		'dolore',
		'magna',
		'aliqua',
		'Ut',
		'enim',
		'ad',
		'minim',
		'veniam',
		'quis',
		'nostrud',
		'exercitation',
		'ullamco',
		'laboris',
		'nisi',
		'aliquip',
		'ex',
		'ea',
		'commodo',
		'consequat',
		'Duis',
		'aute',
		'irure',
		'dolor',
		'in',
		'reprehenderit',
		'in',
		'voluptate',
		'velit',
		'esse',
		'cillum',
		'eu',
		'fugiat',
		'nulla',
		'pariatur'
	];

	$text = '';
	for ($p = 0; $p < $paragraphs; $p++) {
		$paragraph = '';
		for ($s = 0; $s < $sentences_per_paragraph; $s++) {
			$sentence_length = rand(5, 15);
			$sentence = '';
			for ($w = 0; $w < $sentence_length; $w++) {
				$sentence .= $words[array_rand($words)] . ' ';
			}
			$sentence = ucfirst(trim($sentence)) . '. ';
			$paragraph .= $sentence;
		}
		$text .= '<p>' . trim($paragraph) . '</p>';
	}

	return $text;
}

// Основная функция для создания новостей
function nowfilling()
{
	global $mysql, $catz;

	// Проверка прав доступа
	if (!is_admin()) {
		die('Access Denied');
	}

	foreach ($catz as $k => $v) {
		if ($v['id'] != 1 && $v['id'] != 3 && $v['id'] != 4) {
			$newsname = $v['name'];
			$alt_name = strtolower(str_replace(' ', '-', $v['name'])) . '-' . time();
			$fish = generate_random_text(4, 6);

			// Используем текущего пользователя как автора
			$author = $GLOBALS['userROW']['name'];
			$author_id = $GLOBALS['userROW']['id'];

			$mysql->query("START TRANSACTION");

			try {
				// Вставка новости
				$mysql->query("INSERT INTO " . prefix . "_news (
                    `postdate`, `author`, `author_id`, `title`, `content`, `alt_name`, `mainpage`, `approve`, `catid`, `tags`, `description`, `keywords`
                ) VALUES (
                    UNIX_TIMESTAMP(), " . db_squote($author) . ", " . db_squote($author_id) . ", " . db_squote($newsname) . ", " . db_squote($fish) . ", " . db_squote($alt_name) . ", '1', '1', " . db_squote($v['id']) . ", " . db_squote($v['name']) . ", '', ''
                )");
				$id = intval($mysql->lastid('news'));

				// Связывание с категорией
				$mysql->query("INSERT INTO " . prefix . "_news_map (`newsID`, `categoryID`) VALUES (" . db_squote($id) . ", " . db_squote($v['id']) . ")");

				// Обновление тегов
				$mysql->query("INSERT INTO " . prefix . "_tags (tag) VALUES (" . db_squote($v['name']) . ") ON DUPLICATE KEY UPDATE posts = posts + 1");
				$tagid = intval($mysql->lastid('tags'));

				$mysql->query("INSERT INTO " . prefix . "_tags_index (newsID, tagID) VALUES (" . db_squote($id) . ", " . db_squote($tagid) . ")");

				$mysql->query("COMMIT");

				echo $id . ' - ' . $newsname . '<br>';
			} catch (Exception $e) {
				$mysql->query("ROLLBACK");
				echo "Ошибка при создании новости: " . $e->getMessage() . '<br>';
			}
		}
	}
}

// Основной блок кода
$cfg = array();
array_push($cfg, array(
	'descr' => '<input type="button" class="btn btn-outline-success" value="жми меня - Поехали!!!!" onmousedown="window.location.href=\'{admin_url}/admin.php?mod=extra-config&plugin=fish&action=run\'">'
));

if ($_REQUEST['action'] == 'commit') {
	// Если действие "commit", выполняем nowfilling и выводим сообщение о завершении
	nowfilling();
	print_commit_complete($plugin);
} elseif ($_REQUEST['action'] == 'run') {
	// Если действие "run", выполняем nowfilling
	nowfilling();
} else {
	// В остальных случаях генерируем страницу конфигурации плагина
	generate_config_page($plugin, $cfg);
}