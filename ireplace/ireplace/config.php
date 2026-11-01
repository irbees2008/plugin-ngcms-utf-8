<?php

// Protect against hack attempts.
if (!defined('NGCMS')) {
    die('HAL');
}

pluginsLoadConfig();
LoadPluginLang('ireplace', 'main', '', '', ':');

$cfg = [];
array_push($cfg, [
    'descr' => $lang['ireplace:descr'],
]);
array_push($cfg, [
    'name' => 'area',
    'title' => $lang['ireplace:area'],
    'descr' => $lang['ireplace:area.descr'],
    'type' => 'select',
    'values' => [
        '' => $lang['ireplace:area.choose'],
        'news' => $lang['ireplace:area.news'],
        'news_xfields' => $lang['ireplace:area.news_xfields'],
        'static' => $lang['ireplace:area.static'],
        'comments' => $lang['ireplace:area.comments'],
    ]
]);
array_push($cfg, [
    'name' => 'src',
    'title' => $lang['ireplace:source'],
    'type' => 'input',
    'html_flags' => 'size=40',
    'value' => '',
]);
array_push($cfg, [
    'name' => 'dest',
    'title' => $lang['ireplace:destination'],
    'type' => 'input',
    'html_flags' => 'size=40',
    'value' => '',
]);

if ('commit' == $_REQUEST['action']) {
    // Perform a replace.
    $count = 0;
    $query = null;

    // Check src/dest values.
    if (!strlen($src = $_REQUEST['src']) or !strlen($dest = $_REQUEST['dest'])) {
        msg(['type' => 'error', 'text' => $lang['ireplace:error.notext']]);

        return generate_config_page($plugin, $cfg);
    }

    // Check area.
    switch ($_REQUEST['area']) {
        case 'news':
            $query = "update " . prefix . "_news set content = replace(content, " . db_squote($src) . ", " . db_squote($dest) . ")";
            break;

        case 'news_xfields':
            $mysql->query("SET NAMES 'utf-8'");
            // 1 Select.
            if ($xrows = $mysql->select("SELECT id, xfields FROM " . prefix . "_news")) {
                foreach ($xrows as $xrow) {
                    // 2 Change.
                    if (substr($xrow['xfields'], 0, 4) == "SER|") {
                        $xfield = str_replace($src, $dest, unserialize(substr($xrow['xfields'], 4)), $changed);
                        if ($changed) {
                            // 3 Update.
                            $mysql->query("UPDATE " . prefix . "_news SET xfields=" . db_squote('SER|' . serialize($xfield)) . " WHERE id=" . (int) $xrow['id']);
                            $count++;
                        }
                    }
                }
            }
            break;

        case 'static':
            $query = "update " . prefix . "_static set content = replace(content, " . db_squote($src) . ", " . db_squote($dest) . ")";
            break;
        case 'comments':
            $query = "update " . prefix . "_comments set text = replace(text, " . db_squote($src) . ", " . db_squote($dest) . ")";
            break;

            // No area selected.
        default:
            msg(['type' => 'error', 'text' => $lang['ireplace:error.noarea']]);

            return generate_config_page($plugin, $cfg);
    }

    // Check if we should make replacement.
    if ($query) {
        $mysql->query($query);
        $count = $mysql->affected_rows($mysql->connect);
    }

    msg([
        'type' => 'info',
        'info' => empty($count)
            ? $lang['ireplace:info.nochange']
            : str_replace('{count}', $count, $lang['ireplace:info.done'])
    ]);

    return print_commit_complete($plugin);
}

generate_config_page($plugin, $cfg);
