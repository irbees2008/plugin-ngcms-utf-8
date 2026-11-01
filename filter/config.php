<?php

if (!defined('NGCMS')) {
    die('HAL');
}

pluginsLoadConfig();
LoadPluginLang('filter', 'config', '', '', ':');

global $lang, $twig;

$replace = pluginGetVariable('filter', 'replace');
$block = pluginGetVariable('filter', 'block');
$replace = is_string($replace) ? $replace : '';
$block = is_string($block) ? $block : '';
$errors = [];
$success = false;

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['save'])) {
    $submittedReplace = $_POST['replace'] ?? null;
    $submittedBlock = $_POST['block'] ?? null;

    if (!is_string($submittedReplace) || !is_string($submittedBlock)) {
        $errors[] = $lang['filter:config_invalid'];
    } else {
        $replace = $submittedReplace;
        $block = $submittedBlock;
        pluginSetVariable('filter', 'replace', $replace);
        pluginSetVariable('filter', 'block', $block);
        pluginsSaveConfig();
        $success = true;
    }
}

$tpath = locatePluginTemplates(['config'], 'filter', 1, '', 'config');
$template = $twig->loadTemplate($tpath['config'] . 'config.tpl');
echo $template->render([
    'title' => $lang['filter:config_title'],
    'description' => $lang['filter:config_description'],
    'replace_title' => $lang['filter:replace_title'],
    'replace_help' => $lang['filter:replace_help'],
    'block_title' => $lang['filter:block_title'],
    'block_help' => $lang['filter:block_help'],
    'save_label' => $lang['filter:save'],
    'replace' => $replace,
    'block' => $block,
    'errors' => $errors,
    'success' => $success,
    'success_message' => $lang['filter:config_saved'],
]);
