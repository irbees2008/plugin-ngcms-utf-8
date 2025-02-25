<?php
if (!defined('NGCMS')) die('HAL');
register_plugin_page('userlist', '', 'show_userlist');

function show_userlist()
{
	global $mysql, $template, $config;

	// Стили для таблицы
	$class1 = "border-bottom:1px solid #D1DCEB; border-right:1px solid #D1DCEB ; border-top:1px solid #FFFFFF; border-left:1px solid #FFFFFF; background-color: #D1DCEB; color: #7A9ABC;";
	$class2 = "border-right:1px solid #D1DCEB; border-bottom:1px solid #D1DCEB; border-top:1px solid #FFFFFF; border-left:1px solid #FFFFFF; background-color:#EDF1F6;";
	$class3 = "border-bottom:1px solid #D1DCEB; border-right:1px solid #D1DCEB ; border-top:1px solid #FFFFFF; border-left:1px solid #FFFFFF; background-color: #438DAF; color: #FFFFFF;";

	// Получение общего количества пользователей
	$res = $mysql->query("SELECT COUNT(*) FROM " . prefix . "_users");
	if (!$res) {
		die('Ошибка выполнения запроса: ' . $mysql->errorInfo()[2]);
	}
	$res1 = $res->fetch(PDO::FETCH_NUM); // Используем fetch(PDO::FETCH_NUM) для получения массива
	$count = (int)$res1[0];

	// Настройки пагинации
	$perpage = extra_get_param('userlist', 'perpage') ?: "25";
	$sort = extra_get_param('userlist', 'sort') ?: "name";
	$order = extra_get_param('userlist', 'order') ?: "ASC";

	// Определение поля сортировки
	switch ($sort) {
		case "nickname":
			$sort = "name";
			break;
		case "status":
			$sort = "status";
			break;
		case "num_news":
			$sort = "news";
			break;
		case "num_com":
			$sort = "com";
			break;
		case "regdate":
			$sort = "reg";
			break;
		default:
			$sort = "name";
	}

	// Определение порядка сортировки
	$order = strtoupper($order) === "DESC" ? "DESC" : "ASC";

	// Формирование ссылки для пагинации
	$href = $_SERVER['QUERY_STRING'];
	$raz = "?";

	// Пагинация
	list($pag, $limit) = dr_pagination((int)$perpage, $count, $href . $raz);

	// Получение списка пользователей
	$row = $mysql->query("SELECT * FROM " . prefix . "_users ORDER BY $sort $order $limit");
	if (!$row) {
		die('Ошибка выполнения запроса: ' . $mysql->errorInfo()[2]);
	}

	// Формирование HTML-кода
	$output = "<table width='100%' border='0' cellpadding='5' cellspacing='0'>";
	$output .= "<tr><th colspan='5' style='$class3'>Пользователи</th></tr>";
	$output .= "<tr>
        <th width='30%' style='$class1'>Имя</th>
        <th width='30%' style='$class1'>Статус</th>
        <th width='20%' style='$class1'>Новостей</th>
        <th width='20%' style='$class1'>Комментариев</th>
        <th width='20%' style='$class1'>Зарегистрирован</th>
    </tr>";

	while ($bos = $row->fetch(PDO::FETCH_ASSOC)) { // Используем fetch(PDO::FETCH_ASSOC)
		$username = htmlspecialchars($bos['name']);
		$userstatus = '';
		switch ((int)$bos['status']) {
			case 1:
				$userstatus = "Администратор";
				break;
			case 2:
				$userstatus = "Редактор";
				break;
			case 3:
				$userstatus = "Журналист";
				break;
			case 4:
				$userstatus = "Пользователь";
				break;
			default:
				$userstatus = "Неизвестный статус";
		}

		$usernews = (int)$bos['news'];
		$usercom = (int)$bos['com'];
		$userreg = date((extra_get_param('userlist', 'fdate') ?: "d-m-Y"), (int)$bos['reg']);

		$alink = checkLinkAvailable('uprofile', 'show')
			? generateLink('uprofile', 'show', ['name' => $bos['name'], 'id' => $bos['id']])
			: generateLink('core', 'plugin', ['plugin' => 'uprofile', 'handler' => 'show'], ['name' => $bos['name'], 'id' => $bos['id']]);

		$output .= "<tr>";
		$output .= "<td style='$class2'><a href='http://{$_SERVER['SERVER_NAME']}" . $alink . "'>$username</a></td>";
		$output .= <<<HTML
            <td style='$class2'>$userstatus</td>
            <td style='$class2'><div align="center">$usernews</div></td>
            <td style='$class2'><div align="center">$usercom</div></td>
            <td style='$class2'>$userreg</td>
        </tr>
HTML;
	}

	$output .= "</table><br>";
	$output .= $pag;

	if (!isset($template['vars'])) {
		$template['vars'] = [];
	}
	$template['vars']['mainblock'] = $output;
}

function dr_pagination($rpp, $count, $href, $opts = [])
{
	$pages = ceil($count / $rpp);
	$pagedefault = isset($opts["lastpagedefault"]) && $opts["lastpagedefault"] ? floor(($count - 1) / $rpp) : 0;
	$pagedefault = max(0, $pagedefault);

	$page = isset($_GET["page"]) ? (int)$_GET["page"] : $pagedefault;
	$page = max(0, $page);

	$pager = "<td class=\"pager\">Страницы:</td><td class=\"pagebr\">&nbsp;</td>";
	$mp = $pages - 1;
	$as = "<b>«</b>";

	if ($page >= 1) {
		$pager .= "<td class=\"pager\"><a href=\"{$href}page=" . ($page - 1) . "\" style=\"text-decoration: none;\">$as</a></td><td class=\"pagebr\">&nbsp;</td>";
	}

	$as = "<b>»</b>";
	$pager2 = '';

	if ($page < $mp && $mp >= 0) {
		$pager2 .= "<td class=\"pager\"><a href=\"{$href}page=" . ($page + 1) . "\" style=\"text-decoration: none;\">$as</a></td>";
	}

	if ($count) {
		$pagerarr = [];
		$dotted = 0;
		$dotspace = 3;
		$dotend = $pages - $dotspace;
		$curdotend = $page - $dotspace;
		$curdotstart = $page + $dotspace;

		for ($i = 0; $i < $pages; $i++) {
			if (($i >= $dotspace && $i <= $curdotend) || ($i >= $curdotstart && $i < $dotend)) {
				if (!$dotted) {
					$pagerarr[] = "<td class=\"pager\">...</td><td class=\"pagebr\">&nbsp;</td>";
				}
				$dotted = 1;
				continue;
			}

			$dotted = 0;
			$start = $i * $rpp + 1;
			$end = min($start + $rpp - 1, $count);
			$text = $i + 1;

			if ($i != $page) {
				$pagerarr[] = "<td class=\"pager\"><a title=\"$start&nbsp;-&nbsp;$end\" href=\"{$href}page=$i\" style=\"text-decoration: none;\"><b>$text</b></a></td><td class=\"pagebr\">&nbsp;</td>";
			} else {
				$pagerarr[] = "<td class=\"highlight\"><b>$text</b></td><td class=\"pagebr\">&nbsp;</td>";
			}
		}

		$pagerstr = implode("", $pagerarr);
		$pagertop = "<table><tr>$pager $pagerstr $pager2</tr></table>\n";
		$pagerbottom = "Всего $count на $pages страницах по $rpp на каждой странице.<br /><br /><table class=\"main\">$pager $pagerstr $pager2</table>\n";
	} else {
		$pagertop = $pager;
		$pagerbottom = $pagertop;
	}

	$start = $page * $rpp;
	return [$pagertop, "LIMIT $start,$rpp"];
}