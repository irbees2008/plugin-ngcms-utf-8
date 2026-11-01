<?php
if (!defined('NGCMS')) {
    die('HAL');
}

include_once __DIR__ . '/inc/finance.php';
pluginsLoadConfig();
LoadPluginLang('finance', 'config', '', 'finance', '#');

global $lang, $twig, $mysql;
$financeLang = isset($lang['finance']) && is_array($lang['finance']) ? $lang['finance'] : [];
$requiredKeys = [
    'config_title', 'action_settings', 'config_description', 'currency', 'currency_help',
    'balance_header', 'monetary', 'monetary_yes', 'monetary_no', 'type', 'description',
    'news_access_duration', 'duration_permanent', 'duration_1h', 'duration_2h', 'duration_3h',
    'duration_page', 'duration_session', 'duration_help', 'protection_header',
    'news_block_count', 'news_block_count_help',
    'protect_copy_dots', 'protect_copy_watermark', 'protect_disable_selection',
    'protect_visible_watermark', 'watermark_text', 'watermark_text_help',
    'max_file_downloads', 'max_file_downloads_help', 'save', 'saved', 'invalid',
];
foreach ($requiredKeys as $key) {
    if (!isset($financeLang[$key]) || !is_string($financeLang[$key]) || $financeLang[$key] === '') {
        throw new RuntimeException('Missing finance configuration translation: ' . $key);
    }
}

$settings = [
    'currency' => pluginGetVariable('finance', 'syscurrency') ?: 'RUR',
    'news_access_duration' => finance_news_access_duration(),
    'news_block_count' => finance_news_block_count(),
    'protection' => finance_content_protection_settings(),
    'max_file_downloads' => finance_private_download_limit(),
    'balances' => [],
];
foreach ($mysql->select("SELECT * FROM " . prefix . "_balance_manager ORDER BY id") as $row) {
    $settings['balances'][(int)$row['id']] = [
        'monetary' => (int)$row['monetary'],
        'type' => (string)$row['type'],
        'description' => (string)$row['description'],
    ];
}
for ($i = 1; $i <= 4; $i++) {
    if (!isset($settings['balances'][$i])) {
        $settings['balances'][$i] = ['monetary' => 1, 'type' => '', 'description' => ''];
    }
}

$toasts = [];
if (isset($_POST['operation'])) {
    if (!isset($_REQUEST['action'], $_POST['token'])
        || $_REQUEST['action'] !== 'commit'
        || !is_string($_POST['token'])
        || !hash_equals((string)genUToken('admin.extra-config'), $_POST['token'])) {
        throw new RuntimeException('Invalid finance configuration request token.');
    }
    if ($_POST['operation'] !== 'save_settings') {
        throw new RuntimeException('Unknown finance configuration operation.');
    }

    $currency = $_POST['currency'] ?? '';
    $valid = is_string($currency) && in_array($currency, ['RUR', 'EUR', 'USD'], true);
    $accessDuration = $_POST['news_access_duration'] ?? '';
    if (!is_string($accessDuration)
        || !array_key_exists($accessDuration, finance_news_access_durations())) {
        $valid = false;
    }
    $newsBlockCount = filter_var($_POST['news_block_count'] ?? null, FILTER_VALIDATE_INT);
    if ($newsBlockCount === false || $newsBlockCount < 1 || $newsBlockCount > 5) {
        $valid = false;
    }
    $protection = [];
    foreach (['copy_dots', 'copy_watermark', 'disable_selection', 'visible_watermark'] as $key) {
        $value = $_POST['protect_' . $key] ?? null;
        if (!is_string($value) || !in_array($value, ['0', '1'], true)) {
            $valid = false;
        }
        $protection[$key] = $value === '1';
    }
    $watermarkText = $_POST['protect_watermark_text'] ?? '';
    if (!is_string($watermarkText) || mb_strlen($watermarkText, 'UTF-8') > 200) {
        $valid = false;
        $watermarkText = '';
    }
    $downloadLimit = filter_var($_POST['max_file_downloads'] ?? null, FILTER_VALIDATE_INT);
    if ($downloadLimit === false || $downloadLimit < 1 || $downloadLimit > 10) {
        $valid = false;
    }
    $balances = [];
    for ($i = 1; $i <= 4; $i++) {
        $monetary = filter_var($_POST['balance' . $i . '_monetary'] ?? null, FILTER_VALIDATE_INT);
        $typeValue = $_POST['balance' . $i . '_type'] ?? '';
        $descriptionValue = $_POST['balance' . $i . '_description'] ?? '';
        if (!is_string($typeValue) || !is_string($descriptionValue)) {
            $valid = false;
        }
        $type = is_string($typeValue) ? trim($typeValue) : '';
        $description = is_string($descriptionValue) ? trim($descriptionValue) : '';
        if (!in_array($monetary, [0, 1], true) || strlen($type) > 30 || strlen($description) > 4000) {
            $valid = false;
        }
        $balances[$i] = [
            'monetary' => $monetary,
            'type' => $type,
            'description' => $description,
        ];
    }

    if (!$valid) {
        $toasts[] = ['type' => 'error', 'message' => $financeLang['invalid']];
    } else {
        $mysql->query('START TRANSACTION');
        try {
            for ($i = 1; $i <= 4; $i++) {
                $exists = $mysql->record(
                    "SELECT id FROM " . prefix . "_balance_manager WHERE id=" . db_squote($i) . " LIMIT 1"
                );
                if ($exists) {
                    $mysql->query(
                        "UPDATE " . prefix . "_balance_manager SET monetary=" . db_squote($balances[$i]['monetary']) .
                        ", type=" . db_squote($balances[$i]['type']) . ", description=" .
                        db_squote($balances[$i]['description']) . " WHERE id=" . db_squote($i)
                    );
                } else {
                    $mysql->query(
                        "INSERT INTO " . prefix . "_balance_manager (id, monetary, type, description) VALUES (" .
                        db_squote($i) . ", " . db_squote($balances[$i]['monetary']) . ", " .
                        db_squote($balances[$i]['type']) . ", " . db_squote($balances[$i]['description']) . ")"
                    );
                }
            }
            $mysql->query('COMMIT');
        } catch (Throwable $e) {
            $mysql->query('ROLLBACK');
            throw $e;
        }

        $balanceRows = $mysql->select("SELECT * FROM " . prefix . "_balance_manager ORDER BY id");
        if (!is_array($balanceRows)) {
            throw new RuntimeException('Unable to refresh finance balance configuration cache.');
        }
        cacheStoreFile('master.txt', serialize($balanceRows), 'finance');
        pluginSetVariable('finance', 'syscurrency', $currency);
        pluginSetVariable('finance', 'news_access_duration', $accessDuration);
        pluginSetVariable('finance', 'news_block_count', (string)$newsBlockCount);
        foreach ($protection as $key => $enabled) {
            pluginSetVariable('finance', 'protect_' . $key, $enabled ? '1' : '0');
        }
        pluginSetVariable('finance', 'protect_watermark_text', trim($watermarkText));
        pluginSetVariable('finance', 'max_file_downloads', (string)$downloadLimit);
        if (!pluginsSaveConfig()) {
            throw new RuntimeException('Unable to save finance configuration.');
        }
        $settings = [
            'currency' => $currency,
            'news_access_duration' => $accessDuration,
            'news_block_count' => $newsBlockCount,
            'protection' => array_merge($protection, ['watermark_text' => trim($watermarkText)]),
            'max_file_downloads' => $downloadLimit,
            'balances' => $balances,
        ];
        $toasts[] = ['type' => 'success', 'message' => $financeLang['saved']];
    }
}

$templatePaths = locatePluginTemplates(['config'], 'finance', 1, '', 'config');
if (empty($templatePaths['config'])) {
    throw new RuntimeException('The finance configuration template could not be located.');
}
$documentationButton = '';
$documentationModal = '';
if (function_exists('plugin_documentation_markup')) {
    list($documentationButton, $documentationModal) = plugin_documentation_markup('finance');
}
$toastJson = json_encode($toasts, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
if ($toastJson === false) {
    throw new RuntimeException('Unable to encode finance toast notifications.');
}
$view = $twig->loadTemplate($templatePaths['config'] . 'config.tpl');
print $view->render([
    'settings' => $settings,
    'toast_json' => $toastJson,
    'token' => htmlspecialchars((string)genUToken('admin.extra-config'), ENT_QUOTES, 'UTF-8'),
    'lang' => $financeLang,
    'plugin_id' => 'finance',
    'action' => $financeLang['action_settings'],
    'extras_label' => isset($lang['extras']) ? $lang['extras'] : 'Plugins',
    'documentation_button' => $documentationButton,
    'documentation_modal' => $documentationModal,
]);
