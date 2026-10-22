<style type="text/css">
	@import url("../engine/plugins/show_comments/styles.css");
</style>

<script type="text/javascript">
	$(document).ready(function() {
		$("#maincb").on("change", function() {
			$(".check:enabled").prop("checked", this.checked);
		});
	});
</script>


<?php

// Ensure ng-helpers is loaded
if (!function_exists('Plugins\\logger')) {
	$ngHelpersPath = __DIR__ . '/../ng-helpers/ng-helpers.php';
	if (file_exists($ngHelpersPath)) {
		require_once $ngHelpersPath;
	}
}

use function Plugins\{logger, sanitize, get_ip, truncate_html, is_post};

pluginsLoadConfig();
LoadPluginLang('show_comments', 'config', '', '', '#');
global $lang, $twig;

function show_comments()
{

	global $mysql, $config, $parse, $lang;

	$startTime = microtime(true);

	$perpage = (int) extra_get_param('show_comments', 'perpage');
	if ($perpage < 1) {
		$perpage = 5;
	}
	$order = extra_get_param('show_comments', 'order');
	if ($order == 'desc') {
		$order = "DESC";
	} elseif ($order == 'asc') {
		$order = "ASC";
	} else {
		$order = "ASC";
	}
	$comm_length = (int) extra_get_param('show_comments', 'comm_length');
	if (($comm_length < 10) || ($comm_length > 5000)) {
		$comm_length = 50;
	}
	// Выбираем из БД общее количество записей
	$query = "SELECT COUNT(*) as cnt FROM " . prefix . "_comments c"
		. " INNER JOIN " . prefix . "_news n ON c.post = n.id WHERE n.approve = 1";
	$res = $mysql->record($query, 1);
	$total = (int) ($res['cnt'] ?? 0);

	logger('Loading comments list: total=' . $total . ', perpage=' . $perpage . ', order=' . $order, 'info', 'show_comments.log');
	// Проверяем передан ли номер текущей страницы
	$pageValue = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT);
	$page = ($pageValue !== false && $pageValue > 0) ? $pageValue : 1;
	// Сколько всего получится страниц
	$cnt_pages = max(1, (int) ceil($total / $perpage));
	if ($page > $cnt_pages) $page = $cnt_pages;
	// Начальная позиция
	$start = ($page - 1) * $perpage;
	if ($start < 0) {
		$start = 0;
	}
	$query = "select c.id, c.postdate, c.author, c.author_id, c.mail, c.text, c.ip, n.id as nid, n.title, n.alt_name, n.catid, n.postdate as npostdate from " . prefix . "_comments c left join " . prefix . "_news n on c.post=n.id where n.approve=1 order by c.id " . $order . " limit " . $start . ", " . $perpage;
	$result = $mysql->select($query);
	// Выводим "шапку" таблицы
	$csrfToken = htmlspecialchars(genUToken('admin.extra-config'), ENT_QUOTES, 'UTF-8');
	echo '<form action="" method="post" name="select_comments"><input type="hidden" name="action" value="commit"><input type="hidden" name="token" value="' . $csrfToken . '"><div id="ttr">';
	echo '<table><tbody>';
	echo '<tr>';
	echo '<th>' . $lang['show_comments#tbl_date'] . '</th>';
	echo '<th>' . $lang['show_comments#tbl_comment'] . '</th>';
	echo '<th>' . $lang['show_comments#tbl_news'] . '</th>';
	echo '<th style="width:14%;">' . $lang['show_comments#tbl_author'] . '</th>';
	echo '<th style="width:13%;">' . $lang['show_comments#tbl_ip'] . '</th>';
	echo '<th><input type="checkbox" id="maincb" name="master_box" title="' . $lang['show_comments#tbl_check_all'] . '"></th>';
	echo '</tr>';
	foreach ($result as $prd) {
		// Parse comments
		$text = $prd['text'];
		if (!empty($config['blocks_for_reg'])) {
			$text = $parse->userblocks($text);
		}
		if (!empty($config['use_bbcodes'])) {
			$text = $parse->bbcodes($text);
		}
		if (!empty($config['use_htmlformatter'])) {
			$text = $parse->htmlformatter($text);
		}
		if (!empty($config['use_smilies'])) {
			$text = $parse->smilies($text);
		}
		if (strlen($text) > $comm_length) {
			$text = truncate_html($text, $comm_length);
		}
		echo '<tr>';
		echo '<th>' . langdate('d.m.Y H:i:s', $prd['postdate']) . ' [<a href="/engine/admin.php?mod=editcomments&newsid=' . $prd['nid'] . '&comid=' . $prd['id'] . '" target="_blank">#</a>] [<a href="/engine/admin.php?mod=editcomments&subaction=deletecomment&newsid=' . $prd['nid'] . '&comid=' . $prd['id'] . '&poster=' . sanitize($prd['author']) . '" target="_blank">X</a>]</th>';
		echo '<th>' . $text . '</th>';
		echo '<th><a href="' . newsGenerateLink(array('id' => $prd['nid'], 'alt_name' => $prd['alt_name'], 'catid' => $prd['catid'], 'postdate' => $prd['npostdate'])) . '" target="_blank">' . sanitize($prd['title']) . '</a> [<a href="/engine/admin.php?mod=news&action=edit&id=' . $prd['nid'] . '" target="_blank">E</a>]</th>';
		if ($prd['author_id']) {
			echo '<th><a href="/engine/admin.php?mod=users&action=editForm&id=' . $prd['author_id'] . '" target="_blank">' . sanitize($prd['author']) . '</a><br/><small><a href="mailto:' . sanitize($prd['mail']) . '">' . sanitize($prd['mail']) . '</a></small></th>';
		} else {
			echo '<th>' . sanitize($prd['author']) . '<br/><small><a href="mailto:' . sanitize($prd['mail']) . '">' . sanitize($prd['mail']) . '</a></small></th>';
		}
		echo '<th>[<a href="/engine/admin.php?mod=ipban&iplock=' . sanitize($prd['ip']) . '" target="_blank">' . sanitize($prd['ip']) . '</a>] [<a href="http://www.nic.ru/whois/?ip=' . sanitize($prd['ip']) . '" target="_blank">W</a>]</th>';
		echo '<th><input type="checkbox" class="check" name="type[]" value="' . (int) $prd['id'] . '"></th>';
		echo '</tr>';
	}
	echo '</tbody></table></div>';
	echo '<script>$("tr:odd").css("background-color", "#f7fbff");</script>';

	$elapsed = (microtime(true) - $startTime) * 1000;
	logger('Comments list rendered: items=' . count($result) . ', elapsed=' . round($elapsed, 2) . 'ms', 'info', 'show_comments.log');

	$type = is_post() && isset($_POST['type']) && is_array($_POST['type']) ? $_POST['type'] : [];
	$selectedIds = [];
	foreach ($type as $value) {
		if (!is_scalar($value)) {
			continue;
		}
		$id = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
		if ($id !== false) {
			$selectedIds[$id] = $id;
		}
	}
	$type = array_values($selectedIds);
	if (!empty($type)) {
		$query = "DELETE FROM " . prefix . "_comments WHERE id IN (" . implode(',', $type) . ")";
		// Выполняем запрос
		if (!$mysql->query($query)) {
			echo "<br>" . $mysql->db_error() . "<br>";
			echo $query . "<br>";
		} else {
			foreach ($mysql->select("select n.id, count(c.id) as cid from " . prefix . "_news n left join " . prefix . "_comments c on c.post=n.id group by n.id") as $row) {
				$mysql->query("update " . prefix . "_news set com=" . $row['cid'] . " where id = " . $row['id']);
			}
			// Обновляем счетчик постов у юзеров
			foreach ($mysql->select("select author_id, count(*) as cnt from " . prefix . "_news group by author_id") as $row) {
				$mysql->query("update " . uprefix . "_users set news=" . $row['cnt'] . " where id = " . $row['author_id']);
			}
			// Обновляем счетчик комментариев у юзеров
			foreach ($mysql->select("select author_id, count(*) as cnt from " . prefix . "_comments group by author_id") as $row) {
				$mysql->query("update " . uprefix . "_users set com=" . $row['cnt'] . " where id = " . $row['author_id']);
			}

			logger('Comments deleted: count=' . count($type) . ', ip=' . get_ip(), 'info', 'show_comments.log');
			echo "<META HTTP-EQUIV='Refresh' Content='0'>";
		}
	}
	$queryParameters = $_GET;
	unset($queryParameters['page']);
	$uri = strtok($_SERVER['REQUEST_URI'], '?') . '?';
	$queryString = http_build_query($queryParameters);
	if ($queryString !== '') {
		$uri .= $queryString . '&';
	}
	$startpage = $page2left = $page1left = $page1right = $page2right = $endpage = '';
	// Строим постраничную навигацию
	if ($cnt_pages > 1) {
		//   echo '<div style="margin:1em 0">&nbsp;Страницы:';
		// Проверяем нужна ли стрелка "В начало"
		if ($page > 3)
			$startpage = '<a href="' . $uri . 'page=1">&lt;</a> .. ';
		else
			$startpage = '';
		// Проверяем нужна ли стрелка "В конец"
		if ($page < ($cnt_pages - 2))
			$endpage = ' .. <a href="' . $uri . 'page=' . $cnt_pages . '">&gt;</a>';
		else
			$endpage = '';
		// Находим две ближайшие станицы с обоих краев, если они есть
		if ($page - 2 > 0)
			$page2left = ' <a href="' . $uri . 'page=' . ($page - 2) . '">' . ($page - 2) . '</a> | ';
		else
			$page2left = '';
		if ($page - 1 > 0)
			$page1left = ' <a href="' . $uri . 'page=' . ($page - 1) . '">' . ($page - 1) . '</a> | ';
		else
			$page1left = '';
		if ($page + 2 <= $cnt_pages)
			$page2right = ' | <a href="' . $uri . 'page=' . ($page + 2) . '">' . ($page + 2) . '</a>';
		else
			$page2right = '';
		if ($page + 1 <= $cnt_pages)
			$page1right = ' | <a href="' . $uri . 'page=' . ($page + 1) . '">' . ($page + 1) . '</a>';
		else
			$page1right = '';
		// Выводим меню
		// echo $startpage.$page2left.$page1left.'<strong>'.$page.'</strong>'.$page1right.$page2right.$endpage;
		echo '</div>';
	}
	echo '<table><tr><td>' . $startpage . $page2left . $page1left . '<strong>' . $page . '</strong>' . $page1right . $page2right . $endpage . '&nbsp;&nbsp;&nbsp;&nbsp;</td><td style="align:right"><input class="button" type="submit" value="' . $lang['show_comments#tbl_delete'] . '"></td></tr></table></form>';
}

if (!getPluginStatusInstalled('comments')) {
	echo $lang['show_comments#err_no_comments_plugin'];

	return false;
}

$saved = false;
$error = '';
$perpageSetting = (int) pluginGetVariable('show_comments', 'perpage');
$perpageSetting = $perpageSetting > 0 ? $perpageSetting : 5;
$commLengthSetting = (int) pluginGetVariable('show_comments', 'comm_length');
$commLengthSetting = ($commLengthSetting >= 10 && $commLengthSetting <= 5000) ? $commLengthSetting : 50;
$orderSetting = (string) pluginGetVariable('show_comments', 'order');
$orderSetting = in_array($orderSetting, ['asc', 'desc'], true) ? $orderSetting : 'asc';

if (isset($_POST['save_config'])) {
	$submittedPerpage = filter_var($_POST['perpage'] ?? null, FILTER_VALIDATE_INT);
	$submittedCommLength = filter_var($_POST['comm_length'] ?? null, FILTER_VALIDATE_INT);
	$submittedOrder = (string) ($_POST['order'] ?? '');

	if ($submittedPerpage === false || $submittedPerpage === null || $submittedPerpage < 1
		|| $submittedCommLength === false || $submittedCommLength === null
		|| $submittedCommLength < 10 || $submittedCommLength > 5000
		|| !in_array($submittedOrder, ['asc', 'desc'], true)) {
		$error = $lang['show_comments#config_invalid'];
	} else {
		$perpageSetting = $submittedPerpage;
		$commLengthSetting = $submittedCommLength;
		$orderSetting = $submittedOrder;

		pluginSetVariable('show_comments', 'perpage', $perpageSetting);
		pluginSetVariable('show_comments', 'comm_length', $commLengthSetting);
		pluginSetVariable('show_comments', 'order', $orderSetting);
		pluginsSaveConfig();
		$saved = true;
	}
}

show_comments();

$tpath = locatePluginTemplates(['config_main'], 'show_comments', 1);
$xt = $twig->loadTemplate($tpath['config_main'] . 'config_main.tpl');
print $xt->render([
	'perpage' => $perpageSetting,
	'comm_length' => $commLengthSetting,
	'order' => $orderSetting,
	'saved' => $saved,
	'error' => $error,
	'token' => htmlspecialchars(genUToken('admin.extra-config'), ENT_QUOTES, 'UTF-8'),
	'lang' => $lang,
]);
