<?php

if (!defined('NGCMS')) {
    die('HAL');
}

LoadPluginLang('posters', 'main', '', '', ':');

switch ($_GET['action'] ?? '') {
    case 'add':
        poster_add();
        break;
    case 'add-save':
        poster_addSave();
        break;
    case 'edit':
        poster_edit();
        break;
    case 'edit-save':
        poster_editSave();
        break;
    case 'delete':
        poster_delete();
        break;
    default:
        poster_list();
}

function poster_render_admin($templateName, array $vars)
{
    global $tpl;

    $tpath = locatePluginTemplates([$templateName], 'posters', 1, '', 'admin');
    $tpl->template($templateName, $tpath[$templateName]);
    $tpl->vars($templateName, ['vars' => $vars]);

    return $tpl->show($templateName);
}

function poster_admin_redirect()
{
    $url = admin_url . '/admin.php?mod=poster';
    echo '<meta http-equiv="refresh" content="0;url=' . htmlspecialchars($url, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '">';
}

function poster_invalidate_cache()
{
    file_put_contents(get_plugcache_dir('posters') . 'posters_inactive.txt', '');
}

function poster_template_is_valid($template)
{
    return is_string($template)
        && in_array($template, ListFiles(extras_dir . '/posters/tpl/', 'tpl'), true);
}

function poster_list()
{
    global $mysql, $lang;

    $entries = '';
    foreach ($mysql->select('SELECT * FROM ' . prefix . '_posters ORDER BY active DESC, id DESC', 1) as $poster) {
        $id = (int) $poster['id'];
        $entries .= poster_render_admin('admin.list.entry', [
            'id' => $id,
            'title' => htmlspecialchars($poster['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
            'date' => htmlspecialchars($poster['date'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
            'status' => $poster['active'] ? $lang['posters:admin_active'] : $lang['posters:admin_inactive'],
            'edit_url' => admin_url . '/admin.php?mod=poster&action=edit&id=' . $id,
            'delete_url' => admin_url . '/admin.php?mod=poster&action=delete&id=' . $id,
            'edit_label' => $lang['posters:admin_edit'],
            'delete_label' => $lang['posters:admin_delete'],
        ]);
    }

    if ($entries === '') {
        $entries = poster_render_admin('admin.list.empty', [
            'message' => $lang['posters:admin_empty'],
        ]);
    }

    echo poster_render_admin('admin.list', [
        'page_title' => $lang['posters:admin_page_title'],
        'date_label' => $lang['posters:admin_date'],
        'title_label' => $lang['posters:admin_title'],
        'status_label' => $lang['posters:admin_status'],
        'actions_label' => $lang['posters:admin_actions'],
        'add_url' => admin_url . '/admin.php?mod=poster&action=add',
        'add_label' => $lang['posters:admin_add_link'],
        'entries' => $entries,
    ]);
}

function poster_add()
{
    global $lang;

    echo poster_render_admin('admin.form', [
        'form_heading' => $lang['posters:admin_add_heading'],
        'date_label' => $lang['posters:admin_date'],
        'title_label' => $lang['posters:admin_title'],
        'text_label' => $lang['posters:admin_text'],
        'template_label' => $lang['posters:admin_template'],
        'active_label' => $lang['posters:admin_active'],
        'save_label' => $lang['posters:admin_save'],
        'cancel_label' => $lang['posters:admin_cancel'],
        'action_url' => admin_url . '/admin.php?mod=poster&action=add-save',
        'id' => '',
        'date' => '',
        'title' => '',
        'text' => '',
        'active' => '',
        'template_options' => MakeDropDown(ListFiles(extras_dir . '/posters/tpl/', 'tpl'), 'tpl', 'poster_1'),
    ]);
}

function poster_edit()
{
    global $mysql, $lang;

    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    $poster = $mysql->record('SELECT * FROM ' . prefix . '_posters WHERE id = ' . $id);
    if (!is_array($poster)) {
        poster_admin_redirect();
        return;
    }

    echo poster_render_admin('admin.form', [
        'form_heading' => $lang['posters:admin_edit_heading'],
        'date_label' => $lang['posters:admin_date'],
        'title_label' => $lang['posters:admin_title'],
        'text_label' => $lang['posters:admin_text'],
        'template_label' => $lang['posters:admin_template'],
        'active_label' => $lang['posters:admin_active'],
        'save_label' => $lang['posters:admin_save'],
        'cancel_label' => $lang['posters:admin_cancel'],
        'action_url' => admin_url . '/admin.php?mod=poster&action=edit-save&id=' . (int) $poster['id'],
        'id' => (int) $poster['id'],
        'date' => htmlspecialchars($poster['date'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
        'title' => htmlspecialchars($poster['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
        'text' => htmlspecialchars($poster['text'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
        'active' => $poster['active'] ? 'checked="checked"' : '',
        'template_options' => MakeDropDown(
            ListFiles(extras_dir . '/posters/tpl/', 'tpl'),
            'tpl',
            $poster['tpl']
        ),
    ]);
}

function poster_addSave()
{
    global $mysql;

    $title = $_POST['title'] ?? '';
    if (!is_string($title) || trim($title) === '') {
        global $lang;
        msg(['type' => 'error', 'text' => $lang['posters:admin_title_required']]);
        poster_add();
        return;
    }

    $date = $_POST['date'] ?? '';
    $text = $_POST['text'] ?? '';
    $template = $_POST['tpl'] ?? '';
    if (!is_string($date) || !is_string($text) || !poster_template_is_valid($template)) {
        global $lang;
        msg(['type' => 'error', 'text' => $lang['posters:admin_invalid_data']]);
        poster_add();
        return;
    }

    $mysql->query(
        'INSERT INTO ' . prefix . '_posters (date, title, text, active, tpl) VALUES (' .
        db_squote(trim($date)) . ', ' .
        db_squote(trim($title)) . ', ' .
        db_squote($text) . ', ' .
        (isset($_POST['active']) ? '1' : '0') . ', ' .
        db_squote(trim($template)) . ')'
    );

    poster_invalidate_cache();
    poster_admin_redirect();
}

function poster_editSave()
{
    global $mysql, $lang;

    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $poster = $mysql->record('SELECT id FROM ' . prefix . '_posters WHERE id = ' . $id);
    if (!$poster) {
        poster_admin_redirect();
        return;
    }

    $date = $_POST['date'] ?? '';
    $title = $_POST['title'] ?? '';
    $text = $_POST['text'] ?? '';
    $template = $_POST['tpl'] ?? '';
    if (!is_string($date) || !is_string($title) || trim($title) === '' || !is_string($text) || !poster_template_is_valid($template)) {
        msg(['type' => 'error', 'text' => $lang['posters:admin_invalid_data']]);
        poster_edit();
        return;
    }

    $mysql->query(
        'UPDATE ' . prefix . '_posters SET ' .
        'date = ' . db_squote(trim($date)) . ', ' .
        'title = ' . db_squote(trim($title)) . ', ' .
        'text = ' . db_squote($text) . ', ' .
        'active = ' . (isset($_POST['active']) ? '1' : '0') . ', ' .
        'tpl = ' . db_squote(trim($template)) .
        ' WHERE id = ' . $id
    );

    poster_invalidate_cache();
    poster_admin_redirect();
}

function poster_delete()
{
    global $mysql;

    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    if ($id > 0) {
        $mysql->query('DELETE FROM ' . prefix . '_posters WHERE id = ' . $id);
        poster_invalidate_cache();
    }

    poster_admin_redirect();
}
