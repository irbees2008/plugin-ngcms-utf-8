<?php
// #====================================================================================#
// # Наименование плагина: finance [ Finance manager ]                                  #
// # Разрешено к использованию с: Next Generation CMS                                   #
// # Автор: Vitaly A Ponomarev, vp7@mail.ru                                             #
// #====================================================================================#
// #====================================================================================#
// # Ядро плагина                                                                       #
// #====================================================================================#
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
include_once __DIR__ . '/inc/finance.php';
financeInitCache();
loadPluginLang('finance', 'main', '', '', ':');

if (function_exists('getPluginStatusActive') && getPluginStatusActive('payments')) {
	load_extras('core', 'payments');
}
if (function_exists('payments_register_paid_handler')) {
	payments_register_paid_handler('finance', 'finance_handle_paid_order');
}
//
// Фильтр новостей (для управления ценой)
//
class FinanceNewsFilter extends NewsFilter {

	function addNewsForm(&$tvars) {
		$tvars['plugin']['finance'] = $this->renderNewsPriceForm([]);
		return 1;
	}

	function addNews(&$tvars, &$SQL) {
		finance_ensure_news_price_columns();
		for ($blockNumber = 1; $blockNumber <= finance_news_block_count(); $blockNumber++) {
			$field = finance_news_price_field($blockNumber);
			$SQL[$field] = finance_normalize_news_price($_REQUEST[$field] ?? '0');
		}
		return 1;
	}

	function editNewsForm($newsID, $SQLold, &$tvars) {
		$prices = [];
		for ($blockNumber = 1; $blockNumber <= finance_news_block_count(); $blockNumber++) {
			$field = finance_news_price_field($blockNumber);
			$prices[$blockNumber] = finance_normalize_news_price($SQLold[$field] ?? '0');
		}
		$tvars['plugin']['finance'] = $this->renderNewsPriceForm($prices);
		return 1;
	}

	private function renderNewsPriceForm(array $prices) {
		global $lang, $twig;
		$view = $twig->loadTemplate('plugins/finance/tpl/news.price.tpl');
		$blocks = [];
		for ($blockNumber = 1; $blockNumber <= finance_news_block_count(); $blockNumber++) {
			$blocks[] = [
				'number' => $blockNumber,
				'field' => finance_news_price_field($blockNumber),
				'price' => $prices[$blockNumber] ?? '0.00',
			];
		}
		return $view->render([
			'title' => $lang['finance:news.access'] ?? 'Paid access',
			'label' => $lang['finance:news.price'] ?? 'Access price',
			'help' => $lang['finance:news.price_help'] ?? '',
			'block_label' => $lang['finance:news.block'] ?? 'Block',
			'blocks' => $blocks,
		]);
	}

	function editNews($newsID, $SQLold, &$SQLnew, &$tvars) {
		finance_ensure_news_price_columns();
		for ($blockNumber = 1; $blockNumber <= finance_news_block_count(); $blockNumber++) {
			$field = finance_news_price_field($blockNumber);
			$SQLnew[$field] = finance_normalize_news_price($_REQUEST[$field] ?? '0');
		}
		return 1;
	}

	function showNews($newsID, $SQLnews, &$tvars, $mode = array()) {
		global $mysql, $userROW, $lang;
		$this->displayPendingNotice((int)$SQLnews['id']);
		$loggedIn = is_array($userROW) && !empty($userROW['id']);
		$hasPrice = false;
		$hasAccess = true;
		$hasPaidAccess = false;
		$firstPrice = null;
		$blocks = [];
		$replacements = [];
		for ($blockNumber = 1; $blockNumber <= 5; $blockNumber++) {
			$field = finance_news_price_field($blockNumber);
			$rawPrice = trim((string)($SQLnews[$field] ?? ''));
			$priceMinor = $rawPrice === '' ? 0 : finance_amount_to_minor($rawPrice);
			$invalidPrice = $priceMinor === false;
			$price = $invalidPrice ? 0 : $priceMinor / 100;
			$blockHasPrice = $invalidPrice || $price > 0;
			$blockHasAccess = !$blockHasPrice;
			if ($blockHasPrice) {
				$hasPrice = true;
				$firstPrice = $firstPrice ?? ($invalidPrice ? null : $price);
				if ($invalidPrice) {
					\Plugins\logger(
						'Invalid stored paid-news price for news #' . (int)$SQLnews['id'] .
						', block #' . $blockNumber . '.',
						'error',
						'finance.log'
					);
				} elseif ($loggedIn) {
					$blockHasAccess = finance_user_has_paid_news_access(
						(int)$SQLnews['id'],
						(int)$userROW['id'],
						$blockNumber
					);
					if (!$blockHasAccess) {
						$blockHasAccess = finance_has_temporary_session_access(
							(int)$SQLnews['id'],
							(int)$userROW['id'],
							true,
							$blockNumber
						);
					}
				}
				if (!$blockHasAccess) {
					$hasAccess = false;
				} else {
					$hasPaidAccess = true;
				}
			}
			$blocks[$blockNumber] = [
				'number' => $blockNumber,
				'is_premium' => $blockHasPrice,
				'has_access' => $blockHasAccess,
				'is_locked' => $blockHasPrice && !$blockHasAccess,
				'price' => $invalidPrice ? '' : number_format($price, 2, '.', ''),
			];
			$tvars['vars'][$field] = $blocks[$blockNumber]['price'];
		}

		$tvars['vars']['finance'] = [
			'is_premium' => $hasPrice,
			'has_access' => $hasAccess,
			'is_locked' => $hasPrice && !$hasAccess,
			'price' => $firstPrice === null ? '' : number_format($firstPrice, 2, '.', ''),
			'currency' => pluginGetVariable('finance', 'syscurrency') ?: 'RUR',
			'blocks' => $blocks,
		];
		$tvars['regx']["#\\[finance\\](.*?)\\[/finance\\]#s"] = $hasPrice ? '$1' : '';
		$tvars['regx']["#\\[fin_on\\](.*?)\\[/fin_on\\]#s"] = $hasAccess ? '$1' : '';
		$tvars['regx']["#\\[fin_off\\](.*?)\\[/fin_off\\]#s"] = $hasAccess ? '' : '$1';

		$balance = $loggedIn ? finance_check_money($userROW['name']) / 100 : 0;
		for ($blockNumber = 1; $blockNumber <= 5; $blockNumber++) {
			$block = $blocks[$blockNumber];
			if (!$block['is_locked']) {
				$replacements[$blockNumber] = null;
				continue;
			}
			if ($block['price'] === '') {
				$replacements[$blockNumber] = finance_render_template('message', [
					'type' => 'danger',
					'message' => $lang['finance:news_invalid_price'] ?? 'Invalid paid-news price.',
				]);
				continue;
			}
			$vars = [
				'price' => $block['price'],
				'currency' => pluginGetVariable('finance', 'syscurrency') ?: 'RUR',
				'ubalance' => number_format($balance, 2, '.', ''),
				'newsid' => (int)$SQLnews['id'],
				'block_number' => $blockNumber,
				'token' => genUToken('finance.pay'),
				'pay_url' => generateLink('core', 'plugin', ['plugin' => 'finance'], ['mode' => 'pay']),
				'finance_url' => generateLink('core', 'plugin', ['plugin' => 'finance']),
				'backurl' => isset($_SERVER['REQUEST_URI']) ? (string)$_SERVER['REQUEST_URI'] : '/',
				'lang' => finance_lang_vars(),
			];
			$priceMinor = finance_amount_to_minor($block['price']);
			$templateName = !$loggedIn ? 'needauth' : (
				$priceMinor !== false && finance_check_enough_money($userROW['name'], 0, 0, $priceMinor)
					? 'enough'
					: 'notenough'
			);
			$replacements[$blockNumber] = finance_render_template($templateName, $vars);
		}
		$this->replaceLockedContent($tvars, $replacements);
		if ($hasPaidAccess) {
			$this->protectPaidContent($tvars);
		}
	}

	private function replaceLockedContent(array &$tvars, array $replacements): void
	{
		$replace = static function (string $content) use ($replacements): string {
			for ($blockNumber = 1; $blockNumber <= 5; $blockNumber++) {
				$tag = $blockNumber === 1 ? 'fin_lock' : 'fin_lock' . $blockNumber;
				$replacement = $replacements[$blockNumber] ?? null;
				$content = preg_replace_callback(
					'#\[' . $tag . '\](.*?)\[/' . $tag . '\]#s',
					static function (array $matches) use ($replacement): string {
						return $replacement ?? $matches[1];
					},
					$content
				);
			}
			return $content;
		};
		foreach (['story', 'short-story', 'full-story'] as $field) {
			if (isset($tvars['vars'][$field]) && is_string($tvars['vars'][$field])) {
				$tvars['vars'][$field] = $replace($tvars['vars'][$field]);
			}
		}
		foreach (['short', 'full'] as $field) {
			if (isset($tvars['vars']['news'][$field]) && is_string($tvars['vars']['news'][$field])) {
				$tvars['vars']['news'][$field] = $replace($tvars['vars']['news'][$field]);
			}
		}
	}

	private function protectPaidContent(array &$tvars): void
	{
		$settings = finance_content_protection_settings();
		if (!$settings['copy_dots'] && !$settings['copy_watermark']
			&& !$settings['disable_selection'] && !$settings['visible_watermark']) {
			return;
		}

		finance_register_content_protection_assets($settings);
		$watermark = htmlspecialchars($settings['watermark_text'], ENT_QUOTES, 'UTF-8');
		$attributes = $settings['visible_watermark']
			? ' data-finance-watermark="' . $watermark . '"'
			: '';
		$wrap = static function (string $content) use ($attributes, $settings, $watermark): string {
			$label = $settings['visible_watermark']
				? '<span class="finance-visible-watermark" aria-hidden="true">' . $watermark . '</span>'
				: '';
			return '<div class="finance-protected-content"' . $attributes . '>' . $content . $label . '</div>';
		};
		foreach (['story', 'short-story', 'full-story'] as $field) {
			if (isset($tvars['vars'][$field]) && is_string($tvars['vars'][$field])) {
				$tvars['vars'][$field] = $wrap($tvars['vars'][$field]);
			}
		}
		foreach (['short', 'full'] as $field) {
			if (isset($tvars['vars']['news'][$field]) && is_string($tvars['vars']['news'][$field])) {
				$tvars['vars']['news'][$field] = $wrap($tvars['vars']['news'][$field]);
			}
		}
	}

	private function displayPendingNotice(int $newsId): void
	{
		global $userROW;
		$sessionKey = 'finance_news_payment_notice';
		$notice = $_SESSION[$sessionKey] ?? null;
		if (!is_array($notice)) {
			return;
		}

		if ((int)($notice['created_at'] ?? 0) < time() - 600
			|| !is_array($userROW)
			|| (int)($notice['user_id'] ?? 0) !== (int)($userROW['id'] ?? 0)) {
			unset($_SESSION[$sessionKey]);
			return;
		}
		if ((int)($notice['news_id'] ?? 0) !== $newsId) {
			return;
		}

		unset($_SESSION[$sessionKey]);
		$messageKeys = ['news_paid_success', 'news_already_paid'];
		if (!in_array($notice['message_key'] ?? null, $messageKeys, true)) {
			return;
		}
		$message = finance_lang_vars()[$notice['message_key']];
		msgStickerSite($message, 'success');
	}
}

function finance_register_content_protection_assets(array $settings): void
{
	static $registered = false;
	if ($registered) {
		return;
	}
	$registered = true;
	$options = [
		'copyDots' => $settings['copy_dots'],
		'copyWatermark' => $settings['copy_watermark'],
		'disableSelection' => $settings['disable_selection'],
		'watermark' => $settings['watermark_text'],
	];
	$optionsJson = json_encode($options, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
	if ($optionsJson === false) {
		throw new RuntimeException('Unable to encode Finance content protection settings.');
	}

	$css = '';
	if ($settings['disable_selection']) {
		$css .= '.finance-protected-content{-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none}';
	}
	if ($settings['visible_watermark']) {
		$css .= '.finance-visible-watermark{display:block;margin-top:.75em;font-size:.8em;opacity:.55;pointer-events:none}';
	}
	$script = '<script>(function(){var cfg=' . $optionsJson . ';var selector=".finance-protected-content";' .
		'function protectedTarget(node){while(node&&node!==document){if(node.matches&&node.matches(selector))return node;node=node.parentNode;}return null;}' .
		'if(cfg.disableSelection){["selectstart","contextmenu","dragstart"].forEach(function(name){document.addEventListener(name,function(e){if(protectedTarget(e.target))e.preventDefault();},true);});}' .
		'if(cfg.copyDots||cfg.copyWatermark){document.addEventListener("copy",function(e){var block=protectedTarget(e.target);if(!block||!e.clipboardData)return;var selected=window.getSelection?String(window.getSelection()):"";if(!selected)return;var copied=cfg.copyDots?".":selected;if(cfg.copyDots){copied=new Array(selected.length+1).join(".");}if(cfg.copyWatermark){copied+=(copied?"\\n\\n":"")+cfg.watermark;}e.clipboardData.setData("text/plain",copied);e.preventDefault();},true);}' .
		'})();</script>';
	register_htmlvar('plain', '<style>' . $css . '</style>' . $script);
}

register_filter('news', 'finance', new FinanceNewsFilter);
register_plugin_page('finance', '', 'plugin_finance_screen', 0);
add_act('index', 'finance_info');

function finance_user_balance_display() {
	global $userROW;
	if (!is_array($userROW) || empty($userROW['id'])) {
		return '';
	}
	return sprintf('%.02f', finance_check_money($userROW['name']) / 100) . ' ' .
		(pluginGetVariable('finance', 'syscurrency') ?: 'RUR');
}

function finance_info() {
	global $template;
	$template['vars']['plugin_finance_balance'] = finance_user_balance_display();
}

class FinanceCoreUserMenuFilter extends CoreFilter
{
	public function showUserMenu(&$tVars)
	{
		$tVars['plugin_finance_balance'] = finance_user_balance_display();
	}
}

register_filter('core.userMenu', 'finance', new FinanceCoreUserMenuFilter);

//
// Generate PluginPage
//
function plugin_finance_screen() {

	global $template;
	$mode = isset($_REQUEST['mode']) ? (string)$_REQUEST['mode'] : '';
	switch ($mode) {
		case 'pay':
			plugin_finance_pay();
			break;
		case 'topup':
			plugin_finance_create_topup();
			break;
		case 'private_download':
			plugin_finance_private_download();
			break;
		default:
			plugin_finance_report();
			break;
	}
}

function plugin_finance_private_download(): void
{
	$fileId = filter_var($_REQUEST['file_id'] ?? null, FILTER_VALIDATE_INT);
	if ($fileId === false || $fileId < 1) {
		finance_private_download_fail('file_missing', 404);
		return;
	}
	$launch = $_REQUEST['launch'] ?? null;
	if (is_string($launch) && preg_match('/^[a-f0-9]{64}$/i', $launch)) {
		finance_prepare_private_file_download((int)$fileId, $launch);
		return;
	}
	$token = $_REQUEST['token'] ?? null;
	if (is_string($token) && preg_match('/^[a-f0-9]{64}$/i', $token)) {
		finance_stream_private_file((int)$fileId, $token);
		return;
	}
	finance_private_download_fail('file_access_denied', 403);
}

function finance_private_download_fail(string $messageKey, int $status): void
{
	http_response_code($status);
	$messages = finance_lang_vars();
	finance_set_message('danger', $messages[$messageKey] ?? 'The protected file cannot be downloaded.');
}

function finance_prepare_private_file_download(int $fileId, string $launchToken): void
{
	global $mysql, $userROW;
	$userId = is_array($userROW) ? (int)($userROW['id'] ?? 0) : 0;
	$tokenHash = hash('sha256', $launchToken);
	$launch = $_SESSION['finance_file_launches'][$tokenHash] ?? null;
	if ($userId < 1 || !is_array($launch)
		|| (int)($launch['user_id'] ?? 0) !== $userId
		|| (int)($launch['file_id'] ?? 0) !== $fileId
		|| (int)($launch['expires_at'] ?? 0) < time()) {
		finance_private_download_fail('file_access_denied', 403);
		return;
	}

	$file = $mysql->record(
		"SELECT id, storage, linked_ds, linked_id FROM " . prefix . "_files WHERE id=" .
		db_squote($fileId) . " AND storage=2 AND linked_ds=1 LIMIT 1"
	);
	if (!$file || (int)$file['linked_id'] !== (int)$launch['news_id']) {
		finance_private_download_fail('file_missing', 404);
		return;
	}
	$news = $mysql->record(
		"SELECT fin_price, approve FROM " . prefix . "_news WHERE id=" . db_squote($launch['news_id']) . " LIMIT 1"
	);
	if (!$news || ((int)$news['approve'] < 1 && (int)($userROW['status'] ?? 99) > 2)) {
		finance_private_download_fail('file_missing', 404);
		return;
	}
	$priceMinor = finance_amount_to_minor($news['fin_price'] ?? '0');
	$userHasCurrentAccess = finance_user_has_paid_news_access((int)$launch['news_id'], $userId)
		|| finance_has_temporary_session_access((int)$launch['news_id'], $userId);
	if ($priceMinor === false
		|| ($priceMinor > 0 && empty($launch['authorized_paid']) && !$userHasCurrentAccess)) {
		finance_private_download_fail('file_access_denied', 403);
		return;
	}
	$path = finance_private_file_path((string)$file['folder'], (string)$file['name']);
	if (!is_file($path) || !is_readable($path)) {
		finance_private_download_fail('file_missing', 404);
		return;
	}

	if (!isset($_SESSION['finance_file_streams']) || !is_array($_SESSION['finance_file_streams'])) {
		$_SESSION['finance_file_streams'] = [];
	}
	$streamToken = is_string($launch['stream_token'] ?? null) ? $launch['stream_token'] : '';
	$streamTokenHash = preg_match('/^[a-f0-9]{64}$/i', $streamToken)
		? hash('sha256', $streamToken)
		: '';
	$existingStream = $streamTokenHash !== ''
		? ($_SESSION['finance_file_streams'][$streamTokenHash] ?? null)
		: null;
	if (!is_array($existingStream) || (int)($existingStream['user_id'] ?? 0) !== $userId
		|| (int)($existingStream['file_id'] ?? 0) !== $fileId
		|| (int)($existingStream['expires_at'] ?? 0) < time()) {
		$streamToken = bin2hex(random_bytes(32));
		$streamTokenHash = hash('sha256', $streamToken);
		$_SESSION['finance_file_streams'][$streamTokenHash] = [
			'user_id' => $userId,
			'file_id' => $fileId,
			'news_id' => (int)$launch['news_id'],
			'authorized_paid' => !empty($launch['authorized_paid']) || $userHasCurrentAccess,
			'expires_at' => time() + 21600,
		];
	}
	$_SESSION['finance_file_launches'][$tokenHash]['stream_token'] = $streamToken;
	\Plugins\redirect(generateLink('core', 'plugin', ['plugin' => 'finance'], [
		'mode' => 'private_download',
		'file_id' => $fileId,
		'token' => $streamToken,
	]));
}

function finance_consume_private_download(int $userId, int $fileId, string $tokenHash): bool
{
	global $mysql;
	finance_ensure_file_download_schema();
	$mysql->query('START TRANSACTION');
	try {
		$mysql->query(
			"INSERT IGNORE INTO " . prefix . "_finance_file_download_counters " .
			"(user_id, file_id, downloads) VALUES (" . db_squote($userId) . ", " .
			db_squote($fileId) . ", 0)"
		);
		$counter = $mysql->record(
			"SELECT downloads FROM " . prefix . "_finance_file_download_counters WHERE user_id=" .
			db_squote($userId) . " AND file_id=" . db_squote($fileId) . " FOR UPDATE"
		);
		if (!$counter) {
			throw new RuntimeException('Unable to lock the Finance file download counter.');
		}
		$existing = $mysql->record(
			"SELECT id FROM " . prefix . "_finance_file_downloads WHERE user_id=" .
			db_squote($userId) . " AND file_id=" . db_squote($fileId) .
			" AND token_hash=" . db_squote($tokenHash) . " LIMIT 1"
		);
		if ($existing) {
			$mysql->query('COMMIT');
			return true;
		}
		if ((int)$counter['downloads'] >= finance_private_download_limit()) {
			$mysql->query('ROLLBACK');
			return false;
		}
		$mysql->query(
			"INSERT INTO " . prefix . "_finance_file_downloads " .
			"(user_id, file_id, token_hash, created_at) VALUES (" .
			db_squote($userId) . ", " . db_squote($fileId) . ", " .
			db_squote($tokenHash) . ", " . db_squote(time()) . ")"
		);
		$mysql->query(
			"UPDATE " . prefix . "_finance_file_download_counters SET downloads=downloads+1 " .
			"WHERE user_id=" . db_squote($userId) . " AND file_id=" . db_squote($fileId)
		);
		$mysql->query('COMMIT');
		return true;
	} catch (Throwable $e) {
		$mysql->query('ROLLBACK');
		throw $e;
	}
}

function finance_stream_private_file(int $fileId, string $token): void
{
	global $mysql, $userROW;
	$userId = is_array($userROW) ? (int)($userROW['id'] ?? 0) : 0;
	$tokenHash = hash('sha256', $token);
	$stream = $_SESSION['finance_file_streams'][$tokenHash] ?? null;
	if ($userId < 1 || !is_array($stream)
		|| (int)($stream['user_id'] ?? 0) !== $userId
		|| (int)($stream['file_id'] ?? 0) !== $fileId
		|| (int)($stream['expires_at'] ?? 0) < time()) {
		finance_private_download_fail('file_access_denied', 403);
		return;
	}

	$file = $mysql->record(
		"SELECT * FROM " . prefix . "_files WHERE id=" . db_squote($fileId) .
		" AND storage=2 AND linked_ds=1 AND linked_id=" .
		db_squote($stream['news_id']) . " LIMIT 1"
	);
	$news = $mysql->record(
		"SELECT fin_price, approve FROM " . prefix . "_news WHERE id=" . db_squote($stream['news_id']) . " LIMIT 1"
	);
	if (!$file || !$news || ((int)$news['approve'] < 1 && (int)($userROW['status'] ?? 99) > 2)) {
		finance_private_download_fail('file_missing', 404);
		return;
	}
	$priceMinor = finance_amount_to_minor($news['fin_price'] ?? '0');
	if ($priceMinor === false || ($priceMinor > 0 && empty($stream['authorized_paid']))) {
		finance_private_download_fail('file_access_denied', 403);
		return;
	}
	$path = finance_private_file_path((string)$file['folder'], (string)$file['name']);
	if (!is_file($path) || !is_readable($path)) {
		finance_private_download_fail('file_missing', 404);
		return;
	}
	$fileSize = filesize($path);
	if ($fileSize === false) {
		throw new RuntimeException('Unable to determine the size of a private Finance attachment.');
	}

	$start = 0;
	$end = max(0, $fileSize - 1);
	$status = 200;
	$rangeHeader = trim((string)($_SERVER['HTTP_RANGE'] ?? ''));
	if ($rangeHeader !== '') {
		if (!preg_match('/^bytes=(\d*)-(\d*)$/', $rangeHeader, $range) || $fileSize < 1
			|| ($range[1] === '' && $range[2] === '')) {
			header('Content-Range: bytes */' . $fileSize);
			finance_private_download_fail('file_range_invalid', 416);
			return;
		}
		if ($range[1] === '') {
			$suffixLength = (int)$range[2];
			if ($suffixLength < 1) {
				header('Content-Range: bytes */' . $fileSize);
				finance_private_download_fail('file_range_invalid', 416);
				return;
			}
			$start = max(0, $fileSize - $suffixLength);
		} else {
			$start = (int)$range[1];
		}
		$end = $range[2] === '' ? $fileSize - 1 : min((int)$range[2], $fileSize - 1);
		if ($start >= $fileSize || $end < $start) {
			header('Content-Range: bytes */' . $fileSize);
			finance_private_download_fail('file_range_invalid', 416);
			return;
		}
		$status = 206;
	}

	$handle = fopen($path, 'rb');
	if ($handle === false) {
		finance_private_download_fail('file_missing', 404);
		return;
	}
	$isPrivileged = (int)($userROW['status'] ?? 99) <= 2;
	$isHeadRequest = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET')) === 'HEAD';
	if (!$isHeadRequest && !$isPrivileged && !finance_consume_private_download($userId, $fileId, $tokenHash)) {
		fclose($handle);
		finance_private_download_fail('file_limit_reached', 429);
		return;
	}

	while (ob_get_level() > 0) {
		ob_end_clean();
	}
	$mimeType = function_exists('mime_content_type') ? mime_content_type($path) : false;
	$mimeType = is_string($mimeType) && $mimeType !== '' ? $mimeType : 'application/octet-stream';
	$downloadName = (string)($file['orig_name'] ?: $file['name']);
	$asciiName = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($downloadName));
	$disposition = preg_match('#^(audio|video)/#i', $mimeType) ? 'inline' : 'attachment';
	http_response_code($status);
	header('Content-Type: ' . $mimeType);
	header('X-Content-Type-Options: nosniff');
	header('Cache-Control: private, no-store, max-age=0');
	header('Referrer-Policy: no-referrer');
	header('Accept-Ranges: bytes');
	header("Content-Disposition: " . $disposition . '; filename="' . $asciiName .
		'"; filename*=UTF-8\'\'' . rawurlencode(basename($downloadName)));
	if ($status === 206) {
		header('Content-Range: bytes ' . $start . '-' . $end . '/' . $fileSize);
	}
	$contentLength = $fileSize === 0 ? 0 : $end - $start + 1;
	header('Content-Length: ' . $contentLength);
	if (session_status() === PHP_SESSION_ACTIVE) {
		session_write_close();
	}
	if ($isHeadRequest) {
		fclose($handle);
		exit;
	}
	fseek($handle, $start);
	$remaining = $contentLength;
	while ($remaining > 0 && !feof($handle)) {
		$chunk = fread($handle, min(8192, $remaining));
		if ($chunk === false || $chunk === '') {
			\Plugins\logger('Failed while streaming private Finance file #' . $fileId . '.', 'error', 'finance.log');
			break;
		}
		echo $chunk;
		$remaining -= strlen($chunk);
		flush();
		if (connection_aborted()) {
			break;
		}
	}
	fclose($handle);
	exit;
}

function finance_normalize_news_price($value) {
	if (!is_scalar($value)) {
		\Plugins\logger('Rejected non-scalar paid-news price.', 'warn', 'finance.log');
		return '0.00';
	}
	$value = trim((string)$value);
	if ($value === '') {
		return '0.00';
	}
	$value = str_replace(',', '.', $value);
	if (!preg_match('/^\d{1,8}(?:\.\d{1,2})?$/', $value)) {
		\Plugins\logger('Rejected invalid paid-news price.', 'warn', 'finance.log');
		return '0.00';
	}
	$minor = finance_amount_to_minor($value);
	if ($minor === false || $minor > 2147483647) {
		\Plugins\logger('Rejected out-of-range paid-news price.', 'warn', 'finance.log');
		return '0.00';
	}
	return finance_format_minor($minor);
}

function finance_amount_to_minor($value) {
	if (!is_scalar($value)) {
		return false;
	}
	$value = trim(str_replace(',', '.', (string)$value));
	if (!preg_match('/^\d+(?:\.\d{1,2})?$/', $value)) {
		return false;
	}
	$parts = explode('.', $value, 2);
	if (strlen($parts[0]) > 8) {
		return false;
	}
	$whole = (int)$parts[0];
	$fraction = isset($parts[1]) ? (int)str_pad($parts[1], 2, '0') : 0;
	return $whole * 100 + $fraction;
}

function finance_format_minor($minor) {
	return number_format(((int)$minor) / 100, 2, '.', '');
}

function finance_currency_code($currency) {
	$currency = strtoupper(trim((string)$currency));
	return $currency === 'RUR' ? 'RUB' : $currency;
}

function finance_lang_vars() {
	global $lang;
	$keys = [
		'title', 'balance', 'topup', 'topup_amount', 'topup_help', 'submit',
		'not_logged_in', 'payments_unavailable', 'currency_mismatch',
		'transactions_unsupported', 'balance_limit',
		'invalid_amount', 'payment_created', 'payment_failed', 'history',
		'history_empty', 'date', 'operation', 'amount', 'description',
		'operation_income', 'operation_expense', 'news_pay',
		'news_access', 'news_already_paid', 'news_free', 'news_paid_success',
		'news_not_enough', 'news_not_found', 'news_login_required',
		'news_invalid_price',
		'news_price', 'news_price_help', 'news_need_topup', 'news.block',
		'file_access_denied', 'file_limit_reached', 'file_missing', 'file_range_invalid',
	];
	$result = [];
	foreach ($keys as $key) {
		$result[$key] = isset($lang['finance:' . $key]) ? $lang['finance:' . $key] : $key;
	}
	return $result;
}

function finance_render_template($name, array $vars) {
	global $twig;
	$paths = locatePluginTemplates([$name], 'finance', 1);
	if (empty($paths[$name])) {
		throw new RuntimeException('Finance Twig template could not be located: ' . $name);
	}
	return $twig->loadTemplate($paths[$name] . $name . '.tpl')->render($vars);
}

function finance_payments_ready() {
	if (!function_exists('getPluginStatusActive') || !getPluginStatusActive('payments')
		|| !function_exists('payments_active_adapters')) {
		return false;
	}
	return count(payments_active_adapters()) > 0;
}

function finance_transactions_available() {
	static $available = null;
	if ($available !== null) {
		return $available;
	}

	global $mysql;
	$tables = [
		prefix . '_basket_orders',
		prefix . '_finance_payment_orders',
		prefix . '_finance_history',
		uprefix . '_users',
	];
	foreach ($tables as $tableName) {
		$status = $mysql->record("SHOW TABLE STATUS WHERE Name=" . db_squote($tableName));
		$engine = is_array($status) ? ($status['Engine'] ?? ($status['Type'] ?? '')) : '';
		if (strcasecmp((string)$engine, 'InnoDB') !== 0) {
			$available = false;
			return $available;
		}
	}
	$available = true;
	return $available;
}

function finance_currency_compatible() {
	$financeCurrency = finance_currency_code(pluginGetVariable('finance', 'syscurrency') ?: 'RUR');
	$paymentsCurrency = finance_currency_code(pluginGetVariable('payments', 'currency') ?: 'RUB');
	return $financeCurrency !== '' && $financeCurrency === $paymentsCurrency;
}

function finance_set_message($type, $message) {
	global $template;
	$template['vars']['mainblock'] = finance_render_template('message', [
		'type' => $type,
		'message' => $message,
	]);
}

function finance_user_has_paid_news_access(int $newsId, int $userId, ?int $blockNumber = null): bool {
	global $mysql;
	$now = date('Y-m-d H:i:s');
	$blockCondition = $blockNumber === null
		? ''
		: ' AND (access_group_id IS NULL OR access_group_id=0 OR access_group_id=' . db_squote($blockNumber) . ')';
	// Older permanent subscriptions may contain MySQL's zero date; compare against the valid DATETIME lower bound.
	return (bool)$mysql->record(
		"SELECT id FROM " . prefix . "_subscribe_manager WHERE user_id=" . db_squote($userId) .
		" AND access_element_id=" . db_squote($newsId) .
		$blockCondition .
		" AND (expiration_date IS NULL OR expiration_date<'1000-01-01 00:00:00' OR expiration_date>" .
		db_squote($now) . ") LIMIT 1"
	);
}

function finance_has_temporary_session_access(int $newsId, int $userId, bool $consumePageAccess = false, ?int $blockNumber = null): bool {
	$sessionKey = 'finance_news_temporary_access';
	if (!isset($_SESSION[$sessionKey]) || !is_array($_SESSION[$sessionKey])) {
		return false;
	}
	if ($blockNumber === null) {
		$accessKeys = [];
		foreach (array_keys($_SESSION[$sessionKey]) as $key) {
			if ((string)$key === (string)$newsId || strpos((string)$key, $newsId . ':') === 0) {
				$accessKeys[] = (string)$key;
			}
		}
	} else {
		$accessKeys = [$newsId . ':' . $blockNumber, (string)$newsId];
	}

	foreach (array_unique($accessKeys) as $accessKey) {
		$access = $_SESSION[$sessionKey][$accessKey] ?? null;
		if (!is_array($access)) {
			continue;
		}
		if ((int)($access['user_id'] ?? 0) !== $userId
			|| !in_array($access['mode'] ?? null, ['page', 'session'], true)
			|| ($access['mode'] === 'page' && (int)($access['created_at'] ?? 0) < time() - 600)) {
			unset($_SESSION[$sessionKey][$accessKey]);
			continue;
		}
		if ($access['mode'] === 'page' && $consumePageAccess) {
			unset($_SESSION[$sessionKey][$accessKey]);
			if (empty($_SESSION[$sessionKey])) {
				unset($_SESSION[$sessionKey]);
			}
		}
		return true;
	}
	return false;
}

function finance_store_paid_news_access(int $newsId, int $userId, string $duration, int $blockNumber = 0): void {
	global $mysql;
	if ($duration === 'page' || $duration === 'session') {
		return;
	}

	$durationSeconds = finance_news_access_durations()[$duration] ?? null;
	$subscriptionDate = date('Y-m-d H:i:s');
	if ($duration === 'permanent') {
		$mysql->query(
			"INSERT INTO " . prefix . "_subscribe_manager " .
			"(user_id, special_access_type, access_group_id, access_element_id, subscription_date) VALUES (" .
			db_squote($userId) . ", 0, " . db_squote($blockNumber) . ", " .
			db_squote($newsId) . ", " . db_squote($subscriptionDate) . ")"
		);
		return;
	}
	if (!is_int($durationSeconds) || $durationSeconds < 1) {
		throw new RuntimeException('Unsupported Finance paid-news access duration.');
	}

	$expirationDate = date('Y-m-d H:i:s', time() + $durationSeconds);
	$mysql->query(
		"INSERT INTO " . prefix . "_subscribe_manager " .
		"(user_id, special_access_type, access_group_id, access_element_id, subscription_date, expiration_date) VALUES (" .
		db_squote($userId) . ", 0, " . db_squote($blockNumber) . ", " . db_squote($newsId) . ", " .
		db_squote($subscriptionDate) . ", " . db_squote($expirationDate) . ")"
	);
}

function finance_redirect_to_news(array $news, int $userId, string $messageKey): void {
	$_SESSION['finance_news_payment_notice'] = [
		'user_id' => $userId,
		'news_id' => (int)$news['id'],
		'message_key' => $messageKey,
		'created_at' => time(),
	];
	\Plugins\redirect(newsGenerateLink($news));
}

function finance_handle_paid_order($orderId, $method, $externalId, array $rawData, $paidAmount, $paidCurrency) {
	global $mysql;
	$orderId = (int)$orderId;
	$deposit = $mysql->record(
		"SELECT * FROM " . prefix . "_finance_payment_orders WHERE order_id = " . db_squote($orderId) . " LIMIT 1"
	);
	if (!$deposit) {
		return;
	}
	if (!finance_transactions_available()) {
		throw new RuntimeException('Finance top-up requires InnoDB tables.');
	}

	$order = payments_get_order($orderId);
	if (!$order || $order['status'] !== 'paid'
		|| (int)$order['user_id'] !== (int)$deposit['user_id']
		|| finance_amount_to_minor($order['total']) !== finance_amount_to_minor($deposit['amount'])) {
		throw new RuntimeException('Finance payment order validation failed for order #' . $orderId);
	}

	$expectedMinor = finance_amount_to_minor($deposit['amount']);
	$reportedMinor = finance_amount_to_minor($paidAmount);
	if ($expectedMinor === false || $reportedMinor === false || $reportedMinor !== $expectedMinor) {
		throw new RuntimeException('Payment gateway amount mismatch for finance order #' . $orderId);
	}
	if ($paidCurrency !== null && $paidCurrency !== ''
		&& finance_currency_code($paidCurrency) !== finance_currency_code(pluginGetVariable('finance', 'syscurrency') ?: 'RUR')) {
		throw new RuntimeException('Payment gateway currency mismatch for finance order #' . $orderId);
	}

	$mysql->query('START TRANSACTION');
	try {
		$deposit = $mysql->record(
			"SELECT * FROM " . prefix . "_finance_payment_orders WHERE order_id = " .
			db_squote($orderId) . " FOR UPDATE"
		);
		if (!$deposit) {
			throw new RuntimeException('Finance payment ledger entry disappeared for order #' . $orderId);
		}
		if ($deposit['status'] === 'credited') {
			$mysql->query('COMMIT');
			return;
		}
		if ($deposit['status'] !== 'pending') {
			throw new RuntimeException('Unexpected finance payment state for order #' . $orderId);
		}

		$user = $mysql->record(
			"SELECT name, balance FROM " . uprefix . "_users WHERE id = " . db_squote($deposit['user_id']) . " FOR UPDATE"
		);
		if (!$user || (int)$user['balance'] > 2147483647 - $expectedMinor
			|| !finance_add_money($user['name'], 0, (string)$deposit['amount'], 'Balance top-up via payments order #' . $orderId)) {
			throw new RuntimeException('Could not credit finance balance for payment order #' . $orderId);
		}

		$mysql->query(
			"UPDATE " . prefix . "_finance_payment_orders SET status='credited', credited_at=" .
			db_squote(time()) . " WHERE order_id=" . db_squote($orderId) . " AND status='pending'"
		);
		$mysql->query('COMMIT');
		\Plugins\logger('Credited finance payment order #' . $orderId . ' via ' . $method . '.', 'info', 'finance.log');
	} catch (Throwable $e) {
		$mysql->query('ROLLBACK');
		\Plugins\logger('Finance payment order #' . $orderId . ' failed: ' . $e->getMessage(), 'error', 'finance.log');
		throw $e;
	}
}

function plugin_finance_pay() {
	global $mysql, $userROW;
	$lang = finance_lang_vars();
	if (!is_array($userROW) || empty($userROW['id'])) {
		finance_set_message('warning', $lang['news_login_required']);
		return;
	}
	if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST'
		|| !isset($_POST['token'])
		|| !is_string($_POST['token'])
		|| !hash_equals((string)genUToken('finance.pay'), $_POST['token'])) {
		throw new RuntimeException('Invalid finance content-payment request token.');
	}

	$newsId = filter_var($_POST['access_element_id'] ?? null, FILTER_VALIDATE_INT);
	$blockNumber = filter_var($_POST['block_number'] ?? 1, FILTER_VALIDATE_INT);
	if (!$newsId || $newsId < 1 || $blockNumber === false || $blockNumber < 1 || $blockNumber > 5) {
		finance_set_message('error', $lang['news_not_found']);
		return;
	}
	$row = $mysql->record(
		"SELECT * FROM " . prefix . "_news WHERE id=" .
		db_squote($newsId) . " LIMIT 1"
	);
	if (!$row) {
		finance_set_message('error', $lang['news_not_found']);
		return;
	}
	$priceMinor = finance_amount_to_minor($row[finance_news_price_field((int)$blockNumber)] ?? '0');
	if ($priceMinor === false) {
		\Plugins\logger(
			'Invalid stored paid-news price for news #' . $newsId . ', block #' . $blockNumber . '.',
			'error',
			'finance.log'
		);
		finance_set_message('error', $lang['news_invalid_price']);
		return;
	}
	if ($priceMinor < 1) {
		finance_set_message('info', $lang['news_free']);
		return;
	}
	$mysql->query('START TRANSACTION');
	try {
		$userLock = $mysql->record(
			"SELECT id FROM " . uprefix . "_users WHERE id=" . db_squote($userROW['id']) . " FOR UPDATE"
		);
		if (!$userLock) {
			throw new RuntimeException('Finance payment user no longer exists.');
		}
		$userId = (int)$userROW['id'];
		if (finance_user_has_paid_news_access($newsId, $userId, (int)$blockNumber)
			|| finance_has_temporary_session_access($newsId, $userId, false, (int)$blockNumber)) {
			$mysql->query('COMMIT');
			finance_redirect_to_news($row, $userId, 'news_already_paid');
			return;
		}
		$accessDuration = finance_news_access_duration();
		if (!finance_pay(
			['id' => $userId],
			[
				'type' => 'money',
				'value' => ['money' => $priceMinor],
				'description' => 'Payment for news #' . $newsId . ', block #' . $blockNumber,
			]
		)) {
			$mysql->query('ROLLBACK');
			finance_set_message('warning', $lang['news_not_enough']);
			return;
		}

		finance_store_paid_news_access($newsId, $userId, $accessDuration, (int)$blockNumber);
		$mysql->query('COMMIT');
	} catch (Throwable $e) {
		$mysql->query('ROLLBACK');
		throw $e;
	}
	if ($accessDuration === 'page' || $accessDuration === 'session') {
		$_SESSION['finance_news_temporary_access'][$newsId . ':' . $blockNumber] = [
			'user_id' => $userId,
			'mode' => $accessDuration,
			'created_at' => time(),
		];
	}
	finance_redirect_to_news($row, $userId, 'news_paid_success');
}

function plugin_finance_report() {
	global $template, $userROW, $mysql;
	if (!is_array($userROW) || empty($userROW['id'])) {
		finance_set_message('warning', finance_lang_vars()['not_logged_in']);
		return;
	}

	$paymentsReady = finance_payments_ready();
	$currencyCompatible = finance_currency_compatible();
	$transactionsAvailable = finance_transactions_available();
	$canTopup = $paymentsReady && $currencyCompatible && $transactionsAvailable;
	$history = [];
	foreach ($mysql->select(
		"SELECT * FROM " . prefix . "_finance_history WHERE user_id=" . db_squote($userROW['id']) . " ORDER BY id DESC LIMIT 10"
	) as $row) {
		$amount = (int)($row['balance'] ?? 0);
		for ($i = 1; $i <= 4; $i++) {
			$amount += (int)($row['balance' . $i] ?? 0);
		}
		$history[] = [
			'date' => $row['dt'],
			'description' => $row['description'],
			'amount' => finance_format_minor($amount),
			'is_income' => (int)$row['operation_type'] === 1,
		];
	}
	$template['vars']['mainblock'] = finance_render_template('screen', [
		'balance' => number_format(finance_check_money($userROW['name']) / 100, 2, '.', ''),
		'currency' => pluginGetVariable('finance', 'syscurrency') ?: 'RUR',
		'topup_url' => generateLink('core', 'plugin', ['plugin' => 'finance'], ['mode' => 'topup']),
		'topup_token' => genUToken('finance.topup'),
		'can_topup' => $canTopup,
		'payments_active' => $paymentsReady,
		'currency_compatible' => $currencyCompatible,
		'transactions_available' => $transactionsAvailable,
		'history' => $history,
		'lang' => finance_lang_vars(),
	]);
}

function plugin_finance_create_topup() {
	global $template, $userROW, $mysql;
	$lang = finance_lang_vars();
	if (!is_array($userROW) || empty($userROW['id'])) {
		finance_set_message('warning', $lang['not_logged_in']);
		return;
	}
	if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST'
		|| !isset($_POST['token'])
		|| !is_string($_POST['token'])
		|| !hash_equals((string)genUToken('finance.topup'), $_POST['token'])) {
		throw new RuntimeException('Invalid finance top-up request token.');
	}
	if (!finance_payments_ready()) {
		finance_set_message('error', $lang['payments_unavailable']);
		return;
	}
	if (!finance_currency_compatible()) {
		finance_set_message('error', $lang['currency_mismatch']);
		return;
	}
	if (!finance_transactions_available()) {
		finance_set_message('error', $lang['transactions_unsupported']);
		return;
	}

	$amountMinor = finance_amount_to_minor($_POST['amount'] ?? '');
	if ($amountMinor === false || $amountMinor < 1 || $amountMinor > 2147483647) {
		finance_set_message('error', $lang['invalid_amount']);
		return;
	}

	$amount = finance_format_minor($amountMinor);
	$balance = $mysql->record(
		"SELECT balance FROM " . uprefix . "_users WHERE id=" . db_squote($userROW['id']) . " LIMIT 1"
	);
	if (!$balance || (int)$balance['balance'] > 2147483647 - $amountMinor) {
		finance_set_message('error', $lang['balance_limit']);
		return;
	}
	$uniqid = bin2hex(random_bytes(16));
	$items = [[
		'title' => 'Finance account top-up',
		'price' => $amount,
		'count' => 1,
		'xfields' => [],
	]];
	$itemsJson = json_encode($items, JSON_UNESCAPED_UNICODE);
	if ($itemsJson === false) {
		throw new RuntimeException('Unable to encode finance top-up order items.');
	}

	$mysql->query('START TRANSACTION');
	try {
		$mysql->query(
			"INSERT INTO " . prefix . "_basket_orders (user_id, cookie, uniqid, total, status, items_json, contact_json, created_at) VALUES (" .
			db_squote($userROW['id']) . ", '', " . db_squote($uniqid) . ", " . db_squote($amount) .
			", 'new', " . db_squote($itemsJson) . ", " . db_squote('{}') . ", " . db_squote(time()) . ")"
		);
		$orderId = (int)$mysql->lastid();
		if ($orderId < 1) {
			throw new RuntimeException('Payment plugin did not create a finance top-up order.');
		}
		$mysql->query(
			"INSERT INTO " . prefix . "_finance_payment_orders (order_id, user_id, amount, status, created_at) VALUES (" .
			db_squote($orderId) . ", " . db_squote($userROW['id']) . ", " . db_squote($amount) .
			", 'pending', " . db_squote(time()) . ")"
		);
		$mysql->query('COMMIT');
	} catch (Throwable $e) {
		$mysql->query('ROLLBACK');
		\Plugins\logger('Could not create finance top-up order: ' . $e->getMessage(), 'error', 'finance.log');
		throw $e;
	}

	\Plugins\redirect(generatePluginLink('payments', 'pay', [], ['order_id' => $orderId, 'uniqid' => $uniqid]));
}