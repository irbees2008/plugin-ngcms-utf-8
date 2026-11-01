<?php

# Protect against hack attempts
if (!defined('NGCMS')) die('Galaxy in danger');

add_act('index', 'posters');

function posters_getcolumn($sql)
{
    global $mysql;

    $data = array();
    foreach ($mysql->select($sql) as $row) {
        $data[] = reset($row);
    }

    return $data;
}

function posters()
{

    global $mysql, $tpl, $template, $lang;

    LoadPluginLang('posters', 'main', '', '', ':');

    $cacheDir = get_plugcache_dir('posters');
    $postersCacheData = is_file($cacheDir . 'posters.txt')
        ? file_get_contents($cacheDir . 'posters.txt')
        : false;
    $inactiveCacheData = is_file($cacheDir . 'posters_inactive.txt')
        ? file_get_contents($cacheDir . 'posters_inactive.txt')
        : false;
    $postersCache = is_string($postersCacheData) && $postersCacheData !== ''
        ? unserialize($postersCacheData, array('allowed_classes' => false))
        : false;
    $inactiveCache = is_string($inactiveCacheData) && $inactiveCacheData !== ''
        ? unserialize($inactiveCacheData, array('allowed_classes' => false))
        : false;

    if (!is_array($postersCache) || !is_array($inactiveCache)) {
        $posters = $mysql->select('SELECT * FROM ' . prefix . '_posters WHERE active = 1');
        file_put_contents($cacheDir . 'posters.txt', serialize($posters));
        $inactive = posters_getcolumn('SELECT id FROM ' . prefix . '_posters WHERE active = 0');
        file_put_contents($cacheDir . 'posters_inactive.txt', serialize($inactive));
    } else {
        $posters = $postersCache;
        $inactive = $inactiveCache;
    }

    $tpath = locatePluginTemplates(ListFiles(extras_dir . '/posters/tpl/', 'tpl'), 'posters', 1);

    foreach ($posters as $poster) {

        $tvars['vars'] = array(
            'title' => $poster['title'],
            'date'    =>    $poster['date'],
            'text'    =>    $poster['text'],
            'online_broadcasts' => $lang['posters:online_broadcasts'],
            'current_topics' => $lang['posters:current_topics'],
        );

        $tpl->template($poster['tpl'], $tpath[$poster['tpl']]);
        $tpl->vars($poster['tpl'], $tvars);

        $template['vars']['posters_' . $poster['id']] = $tpl->show($poster['tpl']);
    }

    foreach ($inactive as $id)
        $template['vars']['posters_' . $id] = '';
}
