<?php
// ================================================================
// Глобальные переменные
// ================================================================
global $FINANCE_MONEY_ACCEPTORS;
$FINANCE_MONEY_ACCEPTORS = array();
$FINANCE_CACHE = array();

function finance_news_access_durations(): array {
	return [
		'permanent' => null,
		'1h' => 3600,
		'2h' => 7200,
		'3h' => 10800,
		'page' => null,
		'session' => null,
	];
}

function finance_news_access_duration(): string {
	$duration = pluginGetVariable('finance', 'news_access_duration');
	if ($duration === false || $duration === null || $duration === '') {
		return 'permanent';
	}
	if (!is_string($duration) || !array_key_exists($duration, finance_news_access_durations())) {
		throw new RuntimeException('Invalid Finance paid-news access duration setting.');
	}
	return $duration;
}

function finance_news_block_count(): int {
	$value = pluginGetVariable('finance', 'news_block_count');
	if ($value === false || $value === null || $value === '') {
		return 1;
	}
	$count = filter_var($value, FILTER_VALIDATE_INT);
	if ($count === false || $count < 1 || $count > 5) {
		throw new RuntimeException('Invalid Finance paid-news block count setting.');
	}
	return $count;
}

function finance_news_price_field(int $blockNumber): string {
	if ($blockNumber < 1 || $blockNumber > 5) {
		throw new InvalidArgumentException('Finance news block number must be between 1 and 5.');
	}
	return $blockNumber === 1 ? 'fin_price' : 'fin_price' . $blockNumber;
}

function finance_ensure_news_price_columns(): void {
	static $ready = false;
	if ($ready) {
		return;
	}
	global $mysql;
	$table = prefix . '_news';
	for ($blockNumber = 1; $blockNumber <= 5; $blockNumber++) {
		$field = finance_news_price_field($blockNumber);
		$schema = $mysql->record(
			"SHOW COLUMNS FROM " . $table . " LIKE " . db_squote($field)
		);
		if (!is_array($schema)) {
			$mysql->query(
				"ALTER TABLE " . $table . " ADD COLUMN `" . $field . "` char(40) default ''"
			);
			$schema = $mysql->record(
				"SHOW COLUMNS FROM " . $table . " LIKE " . db_squote($field)
			);
		}
		if (!is_array($schema)) {
			throw new RuntimeException('Finance could not create the news price field: ' . $field);
		}
	}
	$ready = true;
}

function finance_content_protection_settings(): array {
	global $config;
	$settings = [];
	foreach (['copy_dots', 'copy_watermark', 'disable_selection', 'visible_watermark'] as $key) {
		$value = pluginGetVariable('finance', 'protect_' . $key);
		if (in_array($value, [1, '1', true], true)) {
			$settings[$key] = true;
		} elseif (in_array($value, [0, '0', false, null, ''], true)) {
			$settings[$key] = false;
		} else {
			throw new RuntimeException('Invalid Finance content protection setting: ' . $key);
		}
	}
	$watermark = pluginGetVariable('finance', 'protect_watermark_text');
	if ($watermark !== false && $watermark !== null && !is_string($watermark)) {
		throw new RuntimeException('Invalid Finance watermark text setting.');
	}
	$siteTitle = is_array($config) ? trim((string)($config['home_title'] ?? '')) : '';
	$settings['watermark_text'] = is_string($watermark) && trim($watermark) !== ''
		? trim($watermark)
		: ($siteTitle !== '' ? $siteTitle : 'Finance');
	return $settings;
}

function finance_private_download_limit(): int {
	$value = pluginGetVariable('finance', 'max_file_downloads');
	if ($value === false || $value === null || $value === '') {
		return 10;
	}
	$limit = filter_var($value, FILTER_VALIDATE_INT);
	if ($limit === false || $limit < 1 || $limit > 10) {
		throw new RuntimeException('Invalid Finance private file download limit setting.');
	}
	return $limit;
}

function finance_ensure_file_download_schema(): void {
	static $ready = false;
	if ($ready) {
		return;
	}
	global $mysql;
	$tables = [
		prefix . '_finance_file_downloads' =>
			'CREATE TABLE IF NOT EXISTS ' . prefix . '_finance_file_downloads (' .
			'id INT NOT NULL AUTO_INCREMENT, user_id INT NOT NULL, file_id INT NOT NULL, ' .
			'token_hash CHAR(64) NOT NULL, created_at INT NOT NULL DEFAULT 0, ' .
			'PRIMARY KEY (id), UNIQUE KEY token_key (user_id, file_id, token_hash), ' .
			'KEY file_user (file_id, user_id)) ENGINE=InnoDB',
		prefix . '_finance_file_download_counters' =>
			'CREATE TABLE IF NOT EXISTS ' . prefix . '_finance_file_download_counters (' .
			'user_id INT NOT NULL, file_id INT NOT NULL, downloads INT NOT NULL DEFAULT 0, ' .
			'PRIMARY KEY (user_id, file_id)) ENGINE=InnoDB',
	];
	foreach ($tables as $tableName => $createSql) {
		$status = $mysql->record("SHOW TABLE STATUS WHERE Name=" . db_squote($tableName));
		if (!$status) {
			$mysql->query($createSql);
			$status = $mysql->record("SHOW TABLE STATUS WHERE Name=" . db_squote($tableName));
		}
		$engine = is_array($status) ? ($status['Engine'] ?? ($status['Type'] ?? '')) : '';
		if (strcasecmp((string)$engine, 'InnoDB') !== 0) {
			throw new RuntimeException('Finance file download table must use InnoDB: ' . $tableName);
		}
	}
	$ready = true;
}

function finance_private_storage_dir(bool $create = false): string {
	if (!function_exists('ngcms_private_upload_dir')) {
		$uploadClass = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'includes' .
			DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'upload.class.php';
		if (!is_file($uploadClass)) {
			throw new RuntimeException('The core private upload storage helper is unavailable.');
		}
		include_once $uploadClass;
	}
	return ngcms_private_upload_dir($create);
}

function finance_private_file_path(string $folder, string $name): string {
	finance_private_storage_dir();
	if (!function_exists('ngcms_private_upload_path')) {
		throw new RuntimeException('The core private upload path helper is unavailable.');
	}
	return ngcms_private_upload_path($folder, $name);
}

function finance_news_requires_private_files(int $newsId): bool {
	global $mysql;
	$row = null;
	if ($newsId > 0) {
		$row = $mysql->record(
			"SELECT * FROM " . prefix . "_news WHERE id=" . db_squote($newsId) . " LIMIT 1"
		);
		if (!is_array($row)) {
			throw new RuntimeException('Unable to load the news record before deciding its attachment storage.');
		}
	}
	for ($blockNumber = 1; $blockNumber <= 5; $blockNumber++) {
		$rawPrice = $row[$field = finance_news_price_field($blockNumber)] ?? null;
		if ($newsId === 0 && array_key_exists($field, $_REQUEST)) {
			$rawPrice = $_REQUEST[$field];
		}
		$minor = finance_amount_to_minor($rawPrice ?? '0');
		if ($minor === false || $minor > 0) {
			return true;
		}
	}
	return false;
}

function finance_private_file_url(int $fileId, int $newsId): string {
	static $urls = [];
	global $userROW, $mysql;
	$key = $newsId . ':' . $fileId;
	if (isset($urls[$key])) {
		return $urls[$key];
	}

	$userId = is_array($userROW) ? (int)($userROW['id'] ?? 0) : 0;
	if ($userId < 1) {
		return $urls[$key] = '';
	}

	$news = $mysql->record(
		"SELECT * FROM " . prefix . "_news WHERE id=" . db_squote($newsId) . " LIMIT 1"
	);
	if (!$news) {
		return $urls[$key] = '';
	}
	$hasPaidBlocks = false;
	for ($blockNumber = 1; $blockNumber <= 5; $blockNumber++) {
		$blockPrice = finance_amount_to_minor($news[finance_news_price_field($blockNumber)] ?? '0');
		if ($blockPrice === false || $blockPrice > 0) {
			$hasPaidBlocks = true;
			break;
		}
	}
	$isPrivileged = (int)($userROW['status'] ?? 99) <= 2;
	$hasAccess = !$hasPaidBlocks || $isPrivileged
		|| finance_user_has_paid_news_access($newsId, $userId)
		|| finance_has_temporary_session_access($newsId, $userId);
	if (!$hasAccess) {
		return $urls[$key] = '';
	}

	if (!isset($_SESSION['finance_file_launches']) || !is_array($_SESSION['finance_file_launches'])) {
		$_SESSION['finance_file_launches'] = [];
	}
	foreach ($_SESSION['finance_file_launches'] as $tokenHash => $launch) {
		if (!is_array($launch) || (int)($launch['expires_at'] ?? 0) < time()) {
			unset($_SESSION['finance_file_launches'][$tokenHash]);
		}
	}
	$token = bin2hex(random_bytes(32));
	$_SESSION['finance_file_launches'][hash('sha256', $token)] = [
		'user_id' => $userId,
		'file_id' => $fileId,
		'news_id' => $newsId,
		'expires_at' => time() + 600,
		'authorized_paid' => $hasPaidBlocks,
	];
	return $urls[$key] = generateLink('core', 'plugin', ['plugin' => 'finance'], [
		'mode' => 'private_download',
		'file_id' => $fileId,
		'launch' => $token,
	]);
}

// ================================================================
// Шаблон класса пополнения баланса
// ================================================================
class Finance_Acceptor {

	// Конструктор
	function Finance_Acceptor() {

		$this->active = 0;
		$this->type = '';
		$this->id = '';
		$this->name = '';
		$this->description = '';
	}

	// Форма принятия платежа
	function paymentAcceptForm($sum = 0) {

		return 1;
	}

	// Приём платежа
	function paymentAccept() {

		return 1;
	}

	// Подтверждение платежа :: генератор формы
	function payConfirmForm($sum = 0) {

		return 1;
	}

	// Подтверждение платежа :: действия
	function payConfirm() {

		return 1;
	}

	// Проведение платежа :: генератор формы
	function payForm() {

		return 1;
	}

	// Проведение платежа :: форма
	function pay() {

		return 1;
	}
}

// =============================================== //
// Scope of new financial functions                //
// =============================================== //
// Инициализация кеша. В нём сохраняем наиболее часть используемые данные - файл
// balance_manager, описывающие типы балансов [ expire = 3600 ]
function financeInitCache() {

	global $FINANCE_CACHE, $mysql;
	$data = cacheRetrieveFile('master.txt', 3600, 'finance');
	if ($data != false) {
		// We got data from cache, fine
		$BM = unserialize($data);
	} else {
		// We need to preload data into cache
		$BM = $mysql->select("select * from " . prefix . "_balance_manager");
		cacheStoreFile('master.txt', serialize($BM), 'finance');
	}
	$FINANCE_CACHE['BM'] = $BM;
}

//
// Зарегистрировать новый аксептор денег (средство ввода денег на сайт)
//
function finance_register_acceptor(&$ref) {

	global $FINANCE_MONEY_ACCEPTORS;
	array_push($FINANCE_MONEY_ACCEPTORS, $ref);
}

//
// Проверка баланса пользователя (в единицах валюты)
// параметры:
// $userlogin	- логин пользователя чей баланс проверяем
// $balance_no	- номер баланса для проверки (0 - основной баланс, -1 - считать общую сумму со всех монетарных балансов)
// возвращаемое значение:
// * сумма баланса указанного пользователя (если пользователь не найден, то 0)
function finance_check_money($userlogin, $balance_no = -1) {

	global $mysql, $userROW, $FINANCE_CACHE;
	$balance_no = (int)$balance_no;
	if (is_array($userROW) && ($userlogin == $userROW['name'])) {
		$res = $userROW;
	} else {
		$res = $mysql->record("select * from " . uprefix . "_users where name=" . db_squote($userlogin));
	}
	if ($res) {
		if ($balance_no >= 0) {
			$column = $balance_no === 0 ? 'balance' : 'balance' . $balance_no;
			return isset($res[$column]) ? $res[$column] : 0;
		}
		$sum = isset($res['balance']) ? $res['balance'] : 0;
		// Проверяем нет ли данных уже в кеше
		if (isset($FINANCE_CACHE['BM']) && is_array($FINANCE_CACHE['BM'])) {
			foreach ($FINANCE_CACHE['BM'] as $row)
				if (($row['monetary'] == 1) && isset($res['balance' . $row['id']]))
					$sum += $res['balance' . $row['id']];
		} else {
			foreach ($mysql->select("select * from " . prefix . "_balance_manager where monetary = 1") as $row) {
				$sum += $res['balance' . $row['id']];
			}
		}

		return $sum;
	}

	return 0;
}

//
// Проверка доступности средств у пользователя для определённого типа действия
// $userlogin   - логин пользователя
// $type	- тип действия
// $tprice	- цена в "единицах" при наличии баланса по данному типу
// $price	- цена в валюте
function finance_check_enough_money($userlogin, $type, $tprice, $price) {

	global $mysql, $userROW, $FINANCE_CACHE;
	if (is_array($userROW) && ($userlogin == $userROW['name'])) {
		$res = $userROW;
	} else {
		$res = $mysql->record("select * from " . uprefix . "_users where name=" . db_squote($userlogin));
	}
	//print "call finance_check_enough_money('$userlogin','$type','$tprice','$price')<br />\n";
	if ($res) {
		// Пользователь найден
		// Проверяем наличие средств на основном (монетарном) балансе
		if (isset($res['balance']) && ($res['balance'] > $price)) {
			return 1;
		}
		// Проверяем наличие поинтов на выделенных балансах
		if ($type) {
			$tbalance = 0;
			foreach ($mysql->select("select * from " . prefix . "_balance_manager where type=" . db_squote($type)) as $row) {
				$tbalance += $res['balance' . $row['id']];
			}
			if ($tbalance >= $tprice) {
				return 1;
			}
		}
		// Проверяем наличие средств на всех монетарных балансах
		$balance = isset($res['balance']) ? $res['balance'] : 0;
		// Обращаемся к кешу если он есть
		if (isset($FINANCE_CACHE['BM']) && is_array($FINANCE_CACHE['BM'])) {
			foreach ($FINANCE_CACHE['BM'] as $row)
				if (($row['monetary'] == 1) && isset($res['balance' . $row['id']]))
					$balance += $res['balance' . $row['id']];
		} else {
			foreach ($mysql->select("select * from " . prefix . "_balance_manager where monetary = 1") as $row) {
				$balance += $res['balance' . $row['id']];
			}
		}
		if ($balance >= $price) {
			return 1;
		}
	}

	return 0;
}

//
// Retrieve balance type
// $balance_no	- balance number
// Returns: empty string if monetary, filled string if type is present
//
function finance_get_balance_type($balance_no) {

	global $mysql;
	$res = $mysql->record("select * from " . prefix . "_balance_manager where id=" . intval($balance_no));
	if ($res) {
		return $res['monetary'] ? '' : $res['type'];
	}

	return 0;
}

//
// Снятие средств с баланса пользователя
// Параметры:
// $identity - массив идентифицирующий пользователя. Заполняется один из параметров
//	# 'id'	- ID пользователя
//	# 'login'	- Login пользователя
// $payment - массив с параметрами платежа
//  # 'type' - тип проводимого платежа:
//	'money'  -	_только_ валютный
//	'points' -	_только_ в поинтах
//	'auto'   -	[default] сначала пытаемся списать в поинтах, если их недостаточно -
//					 списываем средства в валюте
//  # 'ptype' - тип поинтов для оплаты (в случае авто оплаты или оплаты по поинтам)
//  # 'value' - массив с параметрами платежа
//	'money'  =	сумма платежа в валюте
//	'points' =	массив (тип поинтов,кол-во поинтов) при списании в поинтах
//  # 'description' - описание платежа [ для помещения в таблицу finance_history ]
//
function finance_pay($identity, $payment) {

	global $mysql;
	// Если нам не передали identity пользователя - выходим
	if (!isset($identity['id']) && !isset($identity['login']))
		return false;
	if (isset($identity['id'])) {
		$res = $mysql->record("select * from " . uprefix . "_users where id=" . db_squote($identity['id']));
	} elseif (isset($identity['login'])) {
		$res = $mysql->record("select * from " . uprefix . "_users where name=" . db_squote($identity['login']));
	} else {
		$res = false;
	}
	// Выход если пользователь не найден
	if (!is_array($res)) return false;
	//print "Pay request for '$type' ($tprice) '$price' points<br />\n";
	// Формируем массив для обновления пользовательской таблицы
	$bupdate = array();
	$bdiff = array();
	$enough = 0;
	// Проверяем возможные форматы платежей. Если можно платить в поинтах - пытаемся
	if (!isset($payment['type']) || ($payment['type'] == 'auto') || ($payment['type'] == 'points')) {
		// Проверяем наличие достаточной суммы на балансах
		// $ptype - тип баланса; $pvalue - сумма списания с баланса
		$ptype = $payment['ptype'];
		$pcount = $payment['value']['points'];
		foreach ($mysql->select("select * from " . prefix . "_balance_manager where type=" . db_squote($ptype) . " order by id desc") as $row) {
			if ($pcount > $res['balance' . $row['id']]) {
				$pcount -= $res['balance' . $row['id']];
				array_push($bupdate, 'balance' . $row['id'] . ' = 0');
				$bdiff['balance' . $row['id']] = $res['balance' . $row['id']];
			} else {
				array_push($bupdate, 'balance' . $row['id'] . ' = ' . ($res['balance' . $row['id']] - $pcount));
				$bdiff['balance' . $row['id']] = $pcount;
				$pcount = 0;
				break;
			}
		}
		// Если средств хватило на доп. балансах - отмечаем
		if ($pcount <= 0) $enough = 1;
		//print "Bdiff ".implode(", ",$bupdate);
		//	$mysql->query("update ".uprefix."_users set ".implode(", ",$bupdate)." where name=".db_squote($userlogin));
		//	return 1;
	}
	// Если оплата была разрешена только в поинтах и их не хватает - выходим
	if (($payment['type'] == 'points') && (!$enough))
		return false;
	// Пытаемся заплатить в валюте
	if (!$enough) {
		// Обнуляем массив bupdate
		$bupdate = array();
		$price = $payment['value']['money'];
		// Проверяем наличие средств на всех монетарных балансах. Начинаем с дополнительных
		foreach ($mysql->select("select * from " . prefix . "_balance_manager where monetary = 1 order by id desc") as $row) {
			if ($price > $res['balance' . $row['id']]) {
				$price -= $res['balance' . $row['id']];
				array_push($bupdate, 'balance' . $row['id'] . ' = 0');
				$bdiff['balance' . $row['id']] = $res['balance' . $row['id']];
			} else {
				array_push($bupdate, 'balance' . $row['id'] . ' = ' . ($res['balance' . $row['id']] - $price));
				$bdiff['balance' . $row['id']] = $price;
				$price = 0;
				break;
			}
		}
		// Если на дополнительных средств не хватило - списываем с основного
		if ($price) {
			// Если осталось оплатить больше чем есть вообще - ошибка
			//print "Balance: ".$res['balance'].", ".$res['balance1'].", ".$res['balance2'].", ".$res['balance3']."<br />";
			if ($price > $res['balance']) {
				return false;
			}
			array_push($bupdate, 'balance = ' . ($res['balance'] - $price));
			$bdiff['balance'] = $price;
			$price = 0;
		}
	}
	// Если мы дошли до этой точки, значит готовы списывать средства со счета пользователя
	// * списание
	$mysql->query("update " . uprefix . "_users set " . implode(", ", $bupdate) . " where " . (isset($identity['id']) ? ('id=' . db_squote($identity['id'])) : 'login=' . db_squote($identity['name'])));
	// * сохранение лога
	$xk = array();
	$xv = array();
	foreach ($bdiff as $k => $v) {
		array_push($xk, $k);
		array_push($xv, db_squote($v));
	}
	$mysql->query("insert into " . prefix . "_finance_history (user_id, dt, operation_type, description, " . implode(", ", $xk) . ") values (" . db_squote($res['id']) . ", now(), 2, " . db_squote($payment['description']) . ", " . implode(", ", $xv) . ")");

	// * Возвращаем информацию об успешном списании
	return 1;
}

//
// Положить деньги на баланс пользователя
// $userlogin - логин пользователя
// $balance - номер баланса на который класть деньги (0 - core balance)
// $sum - сумма (в валюте или "поинтах") которую необходимо положить на счет
//
function finance_add_money($userlogin, $balance, $sum, $description = '') {

	global $mysql, $userROW, $ip;
	if (!is_scalar($sum)) {
		\Plugins\logger('Rejected non-scalar finance credit amount.', 'warn', 'finance.log');
		return 0;
	}
	$sum = trim((string)$sum);
	if (!preg_match('/^\d{1,8}(?:\.\d{1,2})?$/', $sum)) {
		\Plugins\logger('Rejected invalid finance credit amount.', 'warn', 'finance.log');
		return 0;
	}
	$amountMinor = (int)round((float)$sum * 100);
	$balance = (int)$balance;
	if ($balance < 0 || $balance > 4 || $amountMinor < 1) {
		\Plugins\logger('Rejected out-of-range finance credit.', 'warn', 'finance.log');
		return 0;
	}
	if (is_array($userROW) && ($userlogin == $userROW['name'])) {
		$res = $userROW;
	} else {
		$res = $mysql->record("select * from " . uprefix . "_users where name=" . db_squote($userlogin));
	}
	if ($res) {
		// Пользователь найден
		//print "Add money request for user '$userlogin', balance no '$balance' sum '$sum'<br />\n"."update ".uprefix.'_users set balance_'.$balance.'=balance_'.$balance.'+'.intval($sum*100).' where id = '.$res['id'];
		$column = $balance === 0 ? 'balance' : 'balance' . $balance;
		if ((int)$res[$column] > 2147483647 - $amountMinor) {
			\Plugins\logger('Finance credit would overflow the user balance.', 'error', 'finance.log');
			return 0;
		}
		$mysql->query("update " . uprefix . '_users set ' . $column . '=' . $column . '+' . $amountMinor . ' where id = ' . db_squote($res['id']));
		// * сохранение лога
		$mysql->query("insert into " . prefix . "_finance_history (user_id, dt, operation_type, " . $column . ", ip, description) values (" . db_squote($res['id']) . ", now(), 1, " . $amountMinor . ", " . db_squote(isset($ip) ? $ip : '') . ", " . db_squote($description) . ")");

		return 1;
	} else {
		\Plugins\logger('Unknown user for finance credit: ' . (string)$userlogin, 'error', 'finance.log');
	}

	return 0;
}