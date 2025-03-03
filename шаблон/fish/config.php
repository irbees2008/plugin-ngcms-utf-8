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
	return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
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

	$pname = ''; // Предыдущее название категории
	$tname = ''; // Временное название категории
	$prev = 0;   // ID предыдущей категории

	// Проход по всем категориям
	foreach ($catz as $k => $v) {
		// Пропускаем категории с ID 1, 3 и 4
		if ($v['id'] != 1 && $v['id'] != 3 && $v['id'] != 4) {
			// Формируем название новости на основе уровня вложенности категории
			if ($v['parent'] == $prev && $v['poslevel'] >= 2) {
				$newsname = $pname . ' - ' . $v['name'];
				$tname = $pname;
			} elseif ($v['poslevel'] >= 2) {
				$newsname = $tname . ' - ' . $v['name'];
			} else {
				$newsname = $v['name'];
			}

			// Генерация случайного текста для новости
			$fish = generate_random_text(4, 6);

			// Начало транзакции
			$mysql->query("START TRANSACTION");

			try {
				// Вставка новости в таблицу _news
				$mysql->query("insert into " . prefix . "_news (
                    `postdate`, `author`, `author_id`, `title`, `content`, `alt_name`, `mainpage`, `approve`, `catid`, `tags`
                ) values (
                    now(), 'admin', '1', " . db_squote($newsname) . ", " . db_squote($fish) . ", " . db_squote($v['alt'] . '1') . ", '0', '1', " . db_squote($v['id']) . ", " . db_squote($v['name']) . "
                )");
				$id = intval($mysql->lastid('news'));

				// Связывание новости с категорией в таблице _news_map
				$mysql->query("insert into " . prefix . "_news_map (`newsID`, `categoryID`) values (" . db_squote($id) . ", " . db_squote($v['id']) . ")");

				// Обновление тегов в таблице _tags
				$mysql->query("insert into " . prefix . "_tags (tag) values (" . db_squote($v['name']) . ") on duplicate key update posts = posts + 1");
				$tagid = intval($mysql->lastid('tags'));

				// Связывание новости с тегом в таблице _tags_index
				$mysql->query("insert into " . prefix . "_tags_index (newsID, tagID) values (" . db_squote($id) . ", " . db_squote($tagid) . ")");

				// Фиксация изменений
				$mysql->query("COMMIT");

				// Вывод информации о созданной новости
				echo $id . ' - ' . $newsname . '<br>';
			} catch (Exception $e) {
				// Откат изменений в случае ошибки
				$mysql->query("ROLLBACK");
				echo "Ошибка при создании новости: " . $e->getMessage() . '<br>';
			}

			// Обновление переменных для следующей итерации
			$prev = $v['id'];
			$pname = $v['name'];
		}
	}
}

// Основной блок кода
$cfg = array();
array_push($cfg, array(
	'descr' => '<input type="button" value ="Поехали!!!!" onmousedown="javascript:window.location.href=\'{admin_url}/admin.php?mod=extra-config&plugin=fish&action=run />'
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