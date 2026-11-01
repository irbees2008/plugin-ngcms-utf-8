<?php
if (!defined('NGCMS')) {
	die('HAL');
}

// Подключение библиотеки комментариев
loadPluginLibrary('comments', 'lib');

class RangCommentsFilter extends FilterComments {
	/**
	 * Базовый путь к изображениям.
	 */
	

		
	/**
	 * Показать комментарии.
	 *
	 * @param int $newsID ID новости
	 * @param array $commRec Данные комментария
	 * @param int $comnum Количество комментариев
	 * @param array &$tvars Переменные шаблона
	 * @return int
	 */
	function showComments($newsID, $commRec, $comnum, &$tvars)
	{
		// Отладка: Выводим данные пользователя
/* 		echo "<pre>";
		print_r($commRec);
		echo "</pre>"; */
		
		$imagePath = '/engine/plugins/rangs/img/';

		// Группы пользователей
		$groups = [
			1 => "Администратор",
			2 => "Редактор",
			3 => "Журналист",
			4 => "Комментатор",
		];

		// Расчет рейтинга
		$rankComment = ($commRec['users_com'] ?? 0) * 1.0;
		$rankNews = ($commRec['users_news'] ?? 0) * 10.0;
		$rank = $rankComment + $rankNews;

		// Отладка: Выводим рейтинг
		//echo "Рейтинг: $rank<br>";

		// Определение рангов
		$ranks = [
			50 => ['procent' => function ($rank) {
				return ceil($rank * 2);
			}, 'title' => 'Рядовой', 'image' => '01.jpg'],
			100 => ['procent' => function ($rank) {
				return ceil(($rank - 50) * 2);
			}, 'title' => 'Младший сержант', 'image' => '02.jpg'],
			150 => ['procent' => function ($rank) {
				return ceil(($rank - 100) * 2);
			}, 'title' => 'Сержант', 'image' => '03.jpg'],
			300 => ['procent' => function ($rank) {
				return ceil(($rank - 150) / 1.5);
			}, 'title' => 'Старший сержант', 'image' => '04.jpg'],
			400 => ['procent' => function ($rank) {
				return ceil(($rank - 300) / 1.5);
			}, 'title' => 'Младший лейтенант', 'image' => '05.jpg'],
			500 => ['procent' => function ($rank) {
				return ceil($rank - 400);
			}, 'title' => 'Лейтенант', 'image' => '06.jpg'],
			600 => ['procent' => function ($rank) {
				return ceil($rank - 500);
			}, 'title' => 'Старший лейтенант', 'image' => '07.jpg'],
			700 => ['procent' => function ($rank) {
				return ceil($rank - 600);
			}, 'title' => 'Капитан', 'image' => '08.jpg'],
			800 => ['procent' => function ($rank) {
				return ceil(($rank - 700) / 3);
			}, 'title' => 'Майор', 'image' => '09.jpg'],
			900 => ['procent' => function ($rank) {
				return ceil(($rank - 800) / 5);
			}, 'title' => 'Подполковник', 'image' => '10.jpg'],
			1000 => ['procent' => function ($rank) {
				return ceil(($rank - 900) / 5);
			}, 'title' => 'Полковник', 'image' => '11.jpg'],
			1100 => ['procent' => function ($rank) {
				return ceil(($rank - 1000) / 5);
			}, 'title' => 'Генерал-майор', 'image' => '12.jpg'],
			1200 => ['procent' => function ($rank) {
				return ceil(($rank - 1100) / 5);
			}, 'title' => 'Генерал-лейтенант', 'image' => '13.jpg'],
			1300 => ['procent' => function ($rank) {
				return ceil(($rank - 1200) / 5);
			}, 'title' => 'Генерал-полковник', 'image' => '14.jpg'],
			1400 => ['procent' => function ($rank) {
				return ceil(($rank - 1300) / 5);
			}, 'title' => 'Маршал', 'image' => '15.jpg'],
		];

		// Поиск подходящего ранга
		foreach ($ranks as $threshold => $data) {
			if ($rank >= $threshold) {
				$rankProcent = $data['procent']($rank);
				$news = "<div title='{$rankProcent}%' class='meter orange nostripes'><span style='width: {$rankProcent}%'></span></div>";
				$status = "<img border='0' title='{$data['title']}' src='{$imagePath}{$data['image']}' />";
			} else {
				break;
			}
		}
		
		$tvars['vars']['news'] = $news ? $news : '';
		$tvars['vars']['status'] = $status ? $status : '';

		// Если ранг выше 1400
		if ($rank >= 1400) {
			$tvars['vars']['news'] = '';
			$tvars['vars']['status'] = "<img border='0' title='Командор' src='{$imagePath}16.jpg' />";
		}

		// Дополнительные переменные
		$tvars['vars']['group'] = $groups[$commRec['users_status']] ?? 'Пользователь';
		
		// Отладка: Выводим переменные шаблона
		//echo "<pre>";
		//print_r($tvars['vars']);
		//echo "</pre>";

	}

	/**
	 * Фильтр для объединения данных комментариев.
	 *
	 * @return array
	 */

	function commentsJoinFilter() {
		return array ( 'users' => array ( 'fields' => array ( 'com', 'news', 'status' )));
	}
		
}

// Регистрация фильтра
pluginRegisterFilter('comments', 'rangs', new RangCommentsFilter);