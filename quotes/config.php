<?php
/*
=====================================================
 NG Quotes v0.01
-----------------------------------------------------
 Author: Nail' R. Davydov (ROZARD)
-----------------------------------------------------
 Jabber: ROZARD@ya.ru
 E-mail: ROZARD@list.ru
-----------------------------------------------------
 © Настоящий программист никогда не ставит
 комментариев. То, что писалось с трудом, должно
 пониматься с трудом. :))
-----------------------------------------------------
 Данный код защищен авторскими правами
=====================================================
*/
if (!defined('NGCMS')) {
    exit('HAL');
}

require_once __DIR__ . '/lib/runtime.php';

pluginsLoadConfig();
LoadPluginLang('quotes', 'config', '', '', ':');
global $twig, $lang;

$action = $_REQUEST['action'] ?? '';
quotes_log('admin_request', [
    'action' => (string)$action,
    'method' => $_SERVER['REQUEST_METHOD'] ?? '',
    'post_fields' => array_keys($_POST),
    'content_present' => array_key_exists('content', $_POST),
    'content_bytes' => is_string($_POST['content'] ?? null) ? strlen($_POST['content']) : 0,
    'content_valid_utf8' => is_string($_POST['content'] ?? null) && preg_match('//u', $_POST['content']) === 1,
]);
if ($action === 'commit') {
    $settings = [
        'localsource' => in_array((int)($_POST['localsource'] ?? 0), [0, 1], true) ? (int)($_POST['localsource'] ?? 0) : 0,
        'max_char' => max(0, (int)($_POST['max_char'] ?? 80)),
        'count' => max(1, (int)($_POST['count'] ?? 5)),
        'redirect_delay' => max(0, (int)($_POST['redirect_delay'] ?? 5)),
        'date' => trim((string)($_POST['date'] ?? 'j.m.Y - H:i')),
        'description' => trim((string)($_POST['description'] ?? '')),
        'keywords' => trim((string)($_POST['keywords'] ?? '')),
        'rand' => in_array((int)($_POST['rand'] ?? 0), [0, 1], true) ? (int)($_POST['rand'] ?? 0) : 0,
        'adm_count' => max(1, (int)($_POST['adm_count'] ?? 10)),
        'cache' => in_array((int)($_POST['cache'] ?? 0), [0, 1], true) ? (int)($_POST['cache'] ?? 0) : 0,
        'cacheExpire' => max(1, (int)($_POST['cacheExpire'] ?? 10800)),
    ];
    foreach ($settings as $key => $value) {
        pluginSetVariable('quotes', $key, $value);
    }
    pluginsSaveConfig();
    quotes_log('settings_saved', ['user_id' => (int)($userROW['id'] ?? 0)]);
    print_commit_complete('quotes');
}

$activeTab = ($action === 'add') ? 'add' : 'settings';
if (in_array($action, ['showlist', 'edit', 'del', 'modify'], true)) {
    $activeTab = 'quotes';
}
$importResult = null;
if ($action === 'import') {
    $activeTab = 'import';
    $importResult = importQuotesFromFile();
}

ob_start();
$addResult = addform($action === 'add');
$addHtml = ob_get_clean();
if ($action === 'add' && $addResult === true) {
    $activeTab = 'quotes';
}

ob_start();
switch ($action) {
    case 'showlist':
        sortform();
        showlist();
        break;
    case 'edit':
        editform();
        break;
    case 'add':
        sortform();
        showlist();
        break;
    case 'del':
        delform();
        sortform();
        showlist();
        break;
    case 'modify':
        massQuotesModify();
        sortform();
        showlist();
        break;
    default:
        sortform();
        showlist();
}
$workHtml = ob_get_clean();
renderQuotesConfig($workHtml, $addHtml, $activeTab, $importResult);

function importQuotesFromFile()
{
    global $lang, $mysql, $userROW, $config;

    if (!isset($_POST['token']) || !is_string($_POST['token']) || !hash_equals((string)genUToken('admin.extra-config'), $_POST['token'])) {
        quotes_log('file_import_rejected_csrf');
        return ['type' => 'danger', 'message' => $lang['quotes:config.import_csrf_failed']];
    }
    $file = $_FILES['quote_file'] ?? null;
    if (!is_array($file) || (int)($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return ['type' => 'warning', 'message' => $lang['quotes:config.import_file_required']];
    }

    $tmpName = $file['tmp_name'] ?? null;
    if ((int)($file['error'] ?? -1) !== UPLOAD_ERR_OK || !is_string($tmpName) || $tmpName === '' || !is_uploaded_file($tmpName)) {
        quotes_log('file_import_rejected_upload', ['upload_error' => (int)($file['error'] ?? -1)]);
        return ['type' => 'danger', 'message' => $lang['quotes:config.import_upload_failed']];
    }
    $fileName = is_string($file['name'] ?? null) ? $file['name'] : '';
    if (strtolower(pathinfo($fileName, PATHINFO_EXTENSION)) !== 'txt') {
        return ['type' => 'danger', 'message' => $lang['quotes:config.import_file_type_invalid']];
    }

    $maxFileSize = 2 * 1024 * 1024;
    $fileSize = filesize($tmpName);
    if ($fileSize === false || $fileSize > $maxFileSize) {
        return ['type' => 'danger', 'message' => $lang['quotes:config.import_file_too_large']];
    }

    $content = file_get_contents($tmpName);
    if ($content === false) {
        return ['type' => 'danger', 'message' => $lang['quotes:config.import_read_failed']];
    }
    $parsed = quotes_parse_import_content($content, max(0, (int)pluginGetVariable('quotes', 'max_char')));
    if (($parsed['error'] ?? '') === 'invalid_encoding') {
        quotes_log('file_import_rejected_encoding', ['file_bytes' => $fileSize]);
        return ['type' => 'danger', 'message' => $lang['quotes:config.import_invalid_encoding']];
    }
    if (($parsed['error'] ?? '') === 'too_many_quotes') {
        return ['type' => 'danger', 'message' => $lang['quotes:config.import_too_many_quotes']];
    }
    if (($parsed['error'] ?? '') === 'read_failed') {
        return ['type' => 'danger', 'message' => $lang['quotes:config.import_read_failed']];
    }

    $quotes = array_map('secure_html', $parsed['quotes']);
    if (!$quotes) {
        return ['type' => 'warning', 'message' => $lang['quotes:config.import_no_quotes']];
    }

    $postdate = time() + ((int)($config['date_adjust'] ?? 0) * 60);
    $author = mb_substr((string)($userROW['name'] ?? 'admin'), 0, 100, 'UTF-8');
    $authorId = (int)($userROW['id'] ?? 0);
    $values = [];
    foreach ($quotes as $quote) {
        $values[] = '(' . db_squote($postdate) . ', ' . db_squote($quote) . ', 1, ' . db_squote($author) . ', ' . db_squote($authorId) . ')';
    }

    $result = $mysql->query('INSERT INTO ' . prefix . '_quotes (postdate, content, approve, author, author_id) VALUES ' . implode(', ', $values));
    if ($result === null || $result === false) {
        quotes_log('file_import_insert_failed', ['quote_count' => count($quotes)]);
        return ['type' => 'danger', 'message' => $lang['quotes:config.import_database_failed']];
    }

    quotes_log('file_import_completed', [
        'quote_count' => count($quotes),
        'skipped_empty' => $parsed['skipped_empty'],
        'truncated' => $parsed['truncated'],
        'user_id' => $authorId,
    ]);
    return [
        'type' => 'success',
        'message' => sprintf($lang['quotes:config.import_success'], count($quotes), $parsed['skipped_empty'], $parsed['truncated']),
    ];
}

function sortform()
{
    global $twig, $mysql, $f3, $r, $f2, $lang;

    $options_date = '';
    $options = '';
    $postdate = (int) ($_REQUEST['postdate'] ?? 0);

    foreach ($mysql->select("SELECT DATE_FORMAT(FROM_UNIXTIME(MAX(postdate)), '%b %Y') AS monthes, COUNT(id) AS cnt FROM " . prefix . "_quotes GROUP BY YEAR(FROM_UNIXTIME(postdate)), MONTH(FROM_UNIXTIME(postdate)) ORDER BY MAX(postdate) DESC") as $row) {
        $ifselected = '';
        $post_date['ru']    =    str_replace($f3, $r, $row['monthes']);
        $post_date['en']    =    str_replace($f3, $f2, $row['monthes']);
        $arch_url            =    explode(" ", $post_date['en']);
        $post_date['en']    =    $arch_url[1] . $arch_url[0];

        if ($post_date['en'] == $postdate) {
            $ifselected = "selected";
        }

        $options_date .= "<option value='{$post_date['en']}' {$ifselected}>{$post_date['ru']}</option>";
    }

    $options .= "<option value='0' selected>{$lang['quotes:config.all']}</option>";
    foreach ($mysql->select("SELECT COUNT(q.id) AS cnt, u.id, u.name FROM " . prefix . "_users AS u JOIN " . prefix . "_quotes AS q ON q.author_id = u.id GROUP BY u.id, u.name ORDER BY u.name") as $row) {
        $options .= "<option value='{$row['id']}'" . ($row['id'] == (int) ($_REQUEST['author'] ?? 0) ? ' selected' : '') . ">{$row['name']} [{$row['cnt']}]</option>\n";
    }
    $status = (int) ($_REQUEST['status'] ?? 0);

    $tvars['vars'] = array(
        'selectdate' => $options_date,
        'author' => $options,
        'status'    =>     '<option value="0" selected>' . $lang['quotes:config.all'] . '</option><option value="1"' . (($status == 1) ? ' selected' : '') . '>' . $lang['quotes:config.yes'] . '</option><option value="2"' . (($status == 2) ? ' selected' : '') . '>' . $lang['quotes:config.no'] . '</option>',
        'sort'    =>    makeSortList($_REQUEST['sort'] ?? ''),
        'sort_label' => $lang['quotes:config.sort_by'],
        'month_label' => $lang['quotes:config.month'],
        'all_months' => $lang['quotes:config.all'],
        'author_label' => $lang['quotes:config.author'],
        'status_label' => $lang['quotes:config.active'],
        'show_button' => $lang['quotes:config.show'],
    );

    $tpath = locatePluginTemplates(array('config/conf.sort'), 'quotes', pluginGetVariable('quotes', 'localsource'));

    print $twig->render($tpath['config/conf.sort'] . 'config/conf.sort.tpl', $tvars['vars']);
}

function showlist()
{
    global $mysql, $twig, $config, $parse, $lang;
    $tpath = locatePluginTemplates(array('config/conf.quotes', 'config/conf.quotes.row'), 'quotes', pluginGetVariable('quotes', 'localsource'));

    $author = (int) ($_REQUEST['author'] ?? 0);
    $status = (int) ($_REQUEST['status'] ?? 0);
    $postdate = (int) ($_REQUEST['postdate'] ?? 0);

    $postyear        =    substr($postdate, 0, 4);
    $postmonth        =    substr($postdate, 4, 2);

    $sort = array();
    if ($postdate == true) {
        array_push($sort, "postdate > '" . mktime(0, 0, 0, $postmonth, 1, $postyear) . "'");
        array_push($sort, "postdate < '" . mktime(23, 59, 59, $postmonth, date("t", mktime(0, 0, 0, $postmonth, 1, $postyear)), $postyear) . "'");
    }

    if ($author) {
        array_push($sort, "author_id = {$author}");
    }

    if ($status == 1 or $status == 2) {
        array_push($sort, "approve = " . (($status == 1) ? '1' : '0'));
    }

    $sortBy = '';
    switch ($_REQUEST['sort'] ?? '') {
        case 'id':
            $sortBy = 'id';
            break;
        case 'id_desc':
            $sortBy = 'id desc';
            break;
        case 'postdate':
            $sortBy = 'postdate';
            break;
        case 'postdate_desc':
            $sortBy = 'postdate desc';
            break;
    }

    if ($sortBy) {
        $sortBy = " order by {$sortBy}";
    } else {
        $sortBy = "order by id desc";
    }

    $news_per_page = intval(pluginGetVariable('quotes', 'adm_count'));

    if (($news_per_page < 2) || ($news_per_page > 2000)) $news_per_page = 10;

    $pageNo = (int) ($_REQUEST['page'] ?? 0);
    if ($pageNo < 1)    $pageNo = 1;
    $start_from = ($pageNo - 1) * $news_per_page;

    $sql_endr = "from " . prefix . "_quotes " . (count($sort) ? "where " . implode(" AND ", $sort) : '') . " {$sortBy}";
    $sql_count = "select count(id) as cid {$sql_endr}";
    $sql = "select * {$sql_endr}";

    $cnt = $mysql->record($sql_count);
    $all_count_news = $cnt['cid'];
    $countPages = ceil($all_count_news / $news_per_page);

    $result = "{$sql} LIMIT {$start_from}, {$news_per_page}";

    $output = '';
    foreach ($mysql->select($result) as $row) {
        $content = $row['content'];
        if ($config['use_htmlformatter']) {
            $content = $parse->htmlformatter($content);
        }
        if ($config['use_bbcodes']) {
            $content = $parse->bbcodes($content);
        }
        if ($config['use_smilies']) {
            $content = $parse->smilies($content);
        }

        switch ($row['approve']) {
            case 1:
                $active = $lang['quotes:config.yes'];
                break;
            case 0:
                $active = $lang['quotes:config.no'];
                break;
            default:
                $active = $lang['quotes:config.error'];
        }

        $output .= $twig->render($tpath['config/conf.quotes.row'] . 'config/conf.quotes.row.tpl', [
            'id' => (int)$row['id'],
            'date' => LangDate(pluginGetVariable('quotes', 'date'), $row['postdate']),
            'content' => $content,
            'active' => $active,
            'edit_url' => '?mod=extra-config&plugin=quotes&action=edit&id=' . (int)$row['id'],
            'delete_url' => '?mod=extra-config&plugin=quotes&action=del&id=' . (int)$row['id'],
            'confirm_edit' => $lang['quotes:config.confirm_edit'],
            'confirm_delete' => $lang['quotes:config.confirm_delete'],
            'edit_label' => $lang['quotes:config.edit'],
            'delete_label' => $lang['quotes:config.delete'],
        ]);
    }
    $tvars['vars']['pagesss'] = generateAdminPagelist(array('current' => $pageNo, 'count' => $countPages, 'url' => admin_url . '/admin.php?mod=extra-config&plugin=quotes&action=showlist' . (!empty($_REQUEST['news_per_page']) ? '&news_per_page=' . $news_per_page : '') . ($author ? '&author=' . $author : '') . (!empty($_REQUEST['sort']) ? '&sort=' . $_REQUEST['sort'] : '') . ($postdate ? '&postdate=' . $postdate : '') . ($status ? '&status=' . $status : '') . '&page=%page%'));
    $tvars['vars']['entries'] = $output;
    $tvars['vars']['settings_title'] = $lang['quotes:config.settings_title'];
    $tvars['vars']['quote_list_title'] = $lang['quotes:config.quote_list_title'];
    $tvars['vars']['id_label'] = $lang['quotes:config.id'];
    $tvars['vars']['date_label'] = $lang['quotes:config.date'];
    $tvars['vars']['content_label'] = $lang['quotes:config.content'];
    $tvars['vars']['active_label'] = $lang['quotes:config.active'];
    $tvars['vars']['select_all'] = $lang['quotes:config.select_all'];
    $tvars['vars']['choose_action'] = $lang['quotes:config.choose_action'];
    $tvars['vars']['delete_action'] = $lang['quotes:config.delete'];
    $tvars['vars']['approve_action'] = $lang['quotes:config.activate'];
    $tvars['vars']['forbid_action'] = $lang['quotes:config.deactivate'];
    $tvars['vars']['ok_button'] = $lang['quotes:config.ok'];

    print $twig->render($tpath['config/conf.quotes'] . 'config/conf.quotes.tpl', [
        'quote_list_title' => $tvars['vars']['quote_list_title'],
        'id_label' => $tvars['vars']['id_label'],
        'date_label' => $tvars['vars']['date_label'],
        'content_label' => $tvars['vars']['content_label'],
        'active_label' => $tvars['vars']['active_label'],
        'select_all' => $tvars['vars']['select_all'],
        'entries' => $output,
        'pagesss' => $tvars['vars']['pagesss'],
        'choose_action' => $tvars['vars']['choose_action'],
        'delete_action' => $tvars['vars']['delete_action'],
        'approve_action' => $tvars['vars']['approve_action'],
        'forbid_action' => $tvars['vars']['forbid_action'],
        'ok_button' => $tvars['vars']['ok_button'],
    ]);
}

function makeSortList($selected)
{
    global $lang;

    return    '<option value="id_desc"' . ($selected == "id_desc" ? ' selected' : '') . '>' . $lang['quotes:config.sort_id_desc'] . '</option>' .
        '<option value="id"' . ($selected == "id" ? ' selected' : '') . '>' . $lang['quotes:config.sort_id'] . '</option>' .
        '<option value="postdate_desc"' . ($selected == "postdate_desc" ? ' selected' : '') . '>' . $lang['quotes:config.sort_date_desc'] . '</option>' .
        '<option value="postdate"' . ($selected == "postdate" ? ' selected' : '') . '>' . $lang['quotes:config.sort_date'] . '</option>';
}

function massQuotesModify()
{
    global $mysql, $lang;

    $selected_news = $_REQUEST['selected_news'] ?? array();
    $selected_news = is_array($selected_news) ? array_map('intval', $selected_news) : array();
    $selected_news = array_filter($selected_news, static function ($id) {
        return $id > 0;
    });
    $id = implode(',', $selected_news);
    $subaction = $_REQUEST['subaction'] ?? '';

    if (empty($id)) {
        return msg(array("type" => "error", "text" => $lang['quotes:config.no_quotes_selected']));
    }

    switch ($subaction) {
        case 'do_mass_approve':
            $approve = 'approve = 1';
            break;
        case 'do_mass_forbidden':
            $approve = 'approve = 0';
            break;
        case 'do_mass_delete':
            $del = true;
            break;
        default:
            return msg(array("type" => "error", "text" => $lang['quotes:config.unknown_action']));
    }
    if (isset($approve)) {
        $result = $mysql->query("UPDATE " . prefix . "_quotes SET {$approve} WHERE id IN ({$id})");
        if ($result === null || $result === false) {
            quotes_log('bulk_update_failed', ['ids' => array_values($selected_news), 'action' => $subaction]);
            msg(array("type" => "error", "text" => $lang['quotes:config.quote_save_failed']));
        } else {
            quotes_log('bulk_update_succeeded', ['ids' => array_values($selected_news), 'action' => $subaction]);
            msg(array("type" => "info", "info" => sprintf($lang['quotes:config.bulk_status_updated'], $id)));
        }
    }
    if (isset($del)) {
        $result = $mysql->query("DELETE FROM " . prefix . "_quotes WHERE id IN ({$id})");
        if ($result === null || $result === false) {
            quotes_log('bulk_delete_failed', ['ids' => array_values($selected_news)]);
            msg(array("type" => "error", "text" => $lang['quotes:config.quote_save_failed']));
        } else {
            quotes_log('bulk_delete_succeeded', ['ids' => array_values($selected_news)]);
            msg(array("type" => "info", "info" => sprintf($lang['quotes:config.bulk_deleted'], $id)));
        }
    }
}

function addform($processSubmission = true)
{
    global $twig, $mysql, $userROW, $config, $lang;
    $tpath = locatePluginTemplates(array('config/conf.add'), 'quotes', pluginGetVariable('quotes', 'localsource'));

    $num_max = pluginGetVariable('quotes', 'max_char');

    $time['time'] = time() + ($config['date_adjust'] * 60);
    $submittedContent = is_string($_POST['content'] ?? null) ? $_POST['content'] : '';
    $content = str_replace("\r\n", "\n", $submittedContent);
    $data['active'] = (int) ($_REQUEST['active'] ?? 0);
    $error_flag = false;
    $error_text = array();

    if ((int)$num_max > 0) {
        $content = mb_substr($content, 0, (int)$num_max, 'UTF-8');
    }
    $data['content'] = secure_html($content);

    $submissionAttempted = $processSubmission && isset($_POST['actions']);
    if ($submissionAttempted) {
        quotes_log('admin_add_attempt', [
            'method' => $_SERVER['REQUEST_METHOD'] ?? '',
            'content_present' => array_key_exists('content', $_POST),
            'content_bytes' => strlen($submittedContent),
            'content_valid_utf8' => preg_match('//u', $submittedContent) === 1,
            'processed_bytes' => strlen($data['content']),
            'content_empty' => trim($data['content']) === '',
            'user_id' => (int)($userROW['id'] ?? 0),
        ]);

        if (trim($data['content']) === '') {
            $error_flag = true;
            $error_text[] = msg(array("type" => "error", "text" => $lang['quotes:config.quote_required']));
            quotes_log('admin_add_rejected_empty', ['user_id' => (int)($userROW['id'] ?? 0)]);
        }

        if (trim($data['content']) !== '') {
            $insertResult = $mysql->query("INSERT INTO " . prefix . "_quotes
                (postdate, content, approve, author, author_id)
                VALUES (" . db_squote($time['time']) . ", " . db_squote($data['content']) . ", " . db_squote($data['active']) . ", " . db_squote($userROW['name']) . ", " . db_squote((int)$userROW['id']) . ")");
            if ($insertResult === null || $insertResult === false) {
                $error_flag = true;
                $error_text[] = msg(array("type" => "error", "text" => $lang['quotes:config.quote_save_failed']));
                quotes_log('admin_add_insert_failed', ['user_id' => (int)($userROW['id'] ?? 0)]);
            } else {
                quotes_log('admin_add_inserted', ['user_id' => (int)($userROW['id'] ?? 0)]);
                msg(array("type" => "info", "info" => $lang['quotes:config.quote_added']));
                return true;
            }
        }
    }

    $maxCharAlert = json_encode(
        sprintf($lang['quotes:config.max_chars_warning'], $num_max),
        JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP
    );
    print $twig->render($tpath['config/conf.add'] . 'config/conf.add.tpl', [
        'lang' => $lang,
        'max_char' => (int)$num_max,
        'max_char_alert' => $maxCharAlert,
        'active_options' => '<option value="1" selected>' . $lang['quotes:config.allowed'] . '</option><option value="0">' . $lang['quotes:config.forbidden'] . '</option>',
        'smilies' => InsertSmilies('comments', 10),
        'bbcodes' => BBCodes(),
        'content' => secure_html(str_replace("\r\n", "\n", $submittedContent)),
        'error' => $error_flag ? implode('<br />', $error_text) : '',
        'content_label' => $lang['quotes:config.content'],
        'active_label' => $lang['quotes:config.active'],
        'save_button' => $lang['quotes:config.save'],
    ]);
    return $submissionAttempted ? false : null;
}

function editform()
{
    global $twig, $mysql, $lang, $userROW;
    $tpath = locatePluginTemplates(array('config/conf.edit'), 'quotes', pluginGetVariable('quotes', 'localsource'));

    $id = (int) ($_REQUEST['id'] ?? 0);
    $num_max = pluginGetVariable('quotes', 'max_char');
    if ($id < 1) {
        quotes_log('edit_rejected_invalid_id', ['quote_id' => $id]);
        return msg(array("type" => "error", "text" => $lang['quotes:config.id_missing']));
    }

    $row = $mysql->record('SELECT * FROM ' . prefix . '_quotes WHERE id = ' . db_squote($id));
    if (!is_array($row)) {
        quotes_log('edit_rejected_missing_quote', ['quote_id' => $id]);
        return msg(array("type" => "error", "text" => $lang['quotes:config.id_missing']));
    }

    $submittedContent = is_string($_POST['content'] ?? null) ? $_POST['content'] : null;
    $rawContent = $submittedContent === null ? $row['content'] : $submittedContent;
    if ($submittedContent !== null && (int)$num_max > 0) {
        $rawContent = mb_substr($rawContent, 0, (int)$num_max, 'UTF-8');
    }
    $data['content'] = $submittedContent === null ? $row['content'] : secure_html(str_replace("\r\n", "\n", $rawContent));
    $data['active'] = isset($_POST['active']) ? (int)$_POST['active'] : (int)$row['approve'];
    $error_flag = false;
    $error_text = array();

    if (isset($_POST['actions'])) {
        quotes_log('admin_edit_attempt', [
            'quote_id' => $id,
            'content_present' => $submittedContent !== null,
            'content_bytes' => strlen((string)$submittedContent),
            'content_valid_utf8' => $submittedContent === null || preg_match('//u', $submittedContent) === 1,
            'content_empty' => trim($data['content']) === '',
            'user_id' => (int)($userROW['id'] ?? 0),
        ]);
        if (trim($data['content']) === '') {
            $error_flag = true;
            $error_text[] = msg(array("type" => "error", "text" => $lang['quotes:config.quote_required']));
        } else {
            $result = $mysql->query('UPDATE ' . prefix . '_quotes SET content = ' . db_squote($data['content']) . ', approve = ' . db_squote($data['active']) . ' WHERE id = ' . db_squote($id) . ' LIMIT 1');
            if ($result === null || $result === false) {
                $error_flag = true;
                $error_text[] = msg(array("type" => "error", "text" => $lang['quotes:config.quote_save_failed']));
                quotes_log('edit_failed', ['quote_id' => $id]);
            } else {
                quotes_log('edit_succeeded', ['quote_id' => $id]);
                msg(array("type" => "info", "info" => sprintf($lang['quotes:config.quote_edited'], $id)));
                sortform();
                showlist();
                return;
            }
        }
    }

    $maxCharAlert = json_encode(
        sprintf($lang['quotes:config.max_chars_warning'], $num_max),
        JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP
    );
    print $twig->render($tpath['config/conf.edit'] . 'config/conf.edit.tpl', [
        'lang' => $lang,
        'id' => $id,
        'content' => $data['content'],
        'max_char' => (int)$num_max,
        'max_char_alert' => $maxCharAlert,
        'active_options' => '<option value="1"' . (($data['active'] == 1) ? ' selected' : '') . '>' . $lang['quotes:config.allowed'] . '</option><option value="0"' . (($data['active'] == 0) ? ' selected' : '') . '>' . $lang['quotes:config.forbidden'] . '</option>',
        'smilies' => InsertSmilies('comments', 10),
        'bbcodes' => BBCodes(),
        'error' => $error_flag ? implode('<br />', $error_text) : '',
        'edit_form_title' => sprintf($lang['quotes:config.edit_form_title'], $id),
        'content_label' => $lang['quotes:config.content'],
        'active_label' => $lang['quotes:config.active'],
        'save_button' => $lang['quotes:config.save'],
    ]);
}

function delform()
{
    global $mysql, $lang;
    $id = (int) ($_REQUEST['id'] ?? 0);
    if ($id < 1) {
        return msg(array("type" => "error", "text" => $lang['quotes:config.id_missing']));
    }
    $result = $mysql->query("DELETE FROM " . prefix . "_quotes WHERE id = {$id}");
    if ($result === null || $result === false) {
        quotes_log('delete_failed', ['quote_id' => $id]);
        return msg(array("type" => "error", "text" => $lang['quotes:config.quote_save_failed']));
    }
    quotes_log('delete_succeeded', ['quote_id' => $id]);
    msg(array("type" => "info", "info" => sprintf($lang['quotes:config.quote_deleted'], $id)));
}

function renderQuotesConfig($workHtml, $addHtml, $activeTab, $importResult = null)
{
    global $twig, $lang;
    $tpath = locatePluginTemplates(['config/main', 'config/automation'], 'quotes', 1);
    $defaults = [
        'localsource' => 0,
        'max_char' => 80,
        'count' => 5,
        'redirect_delay' => 5,
        'date' => 'j.m.Y - H:i',
        'description' => '',
        'keywords' => '',
        'rand' => 0,
        'adm_count' => 10,
        'cache' => 0,
        'cacheExpire' => 10800,
    ];
    $settings = [];
    foreach ($defaults as $key => $default) {
        $value = pluginGetVariable('quotes', $key);
        $settings[$key] = ($value === false || $value === null || $value === '') ? $default : $value;
    }
    $documentationButton = '';
    $documentationModal = '';
    if (function_exists('plugin_documentation_markup')) {
        list($documentationButton, $documentationModal) = plugin_documentation_markup('quotes');
    }
    $automationHtml = $twig->render($tpath['config/automation'] . 'config/automation.tpl', [
        'lang' => $lang,
        'settings' => $settings,
        'active_tab' => $activeTab,
        'work_html' => $workHtml,
        'add_html' => $addHtml,
        'import_result' => $importResult,
        'token' => genUToken('admin.extra-config'),
    ]);
    echo $twig->render($tpath['config/main'] . 'config/main.tpl', [
        'entries' => $automationHtml,
        'plugin_name' => 'quotes',
        'current_title' => $lang['quotes:config.settings_title'],
        'documentation_button' => $documentationButton,
        'documentation_modal' => $documentationModal,
        'lang' => $lang,
    ]);
}
