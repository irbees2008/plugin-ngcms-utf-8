<?php
if (!defined('NGCMS')) {
    die('HAL');
}

pluginsLoadConfig();
LoadPluginLang('scn', 'config', '', '', '#');
global $lang, $twig;

$saved = false;
$error = '';

$number = (int) pluginGetVariable('scn', 'number');
$number = $number > 0 ? $number : 5;
$orderby = (string) pluginGetVariable('scn', 'orderby');
$orderby = in_array($orderby, ['0', '1', '2', '3', '4'], true) ? $orderby : '1';
$mantemplate = (string) pluginGetVariable('scn', 'mantemplate');
$mantemplate = in_array($mantemplate, ['0', '1'], true) ? $mantemplate : '0';
$template = (string) pluginGetVariable('scn', 'template');

if (isset($_POST['submit'])) {
    $submittedNumber = filter_var($_POST['number'] ?? null, FILTER_VALIDATE_INT);
    $submittedOrderby = (string) ($_POST['orderby'] ?? '');
    $submittedMantemplate = (string) ($_POST['mantemplate'] ?? '');
    $submittedTemplate = $_POST['template'] ?? null;

    if ($submittedNumber === false || $submittedNumber === null || $submittedNumber < 1
        || !in_array($submittedOrderby, ['0', '1', '2', '3', '4'], true)
        || !in_array($submittedMantemplate, ['0', '1'], true)
        || !is_string($submittedTemplate)) {
        $error = $lang['scn#config_invalid'];
    } else {
        $number = $submittedNumber;
        $orderby = $submittedOrderby;
        $mantemplate = $submittedMantemplate;
        $template = $submittedTemplate;

        pluginSetVariable('scn', 'number', $number);
        pluginSetVariable('scn', 'orderby', $orderby);
        pluginSetVariable('scn', 'mantemplate', $mantemplate);
        pluginSetVariable('scn', 'template', $template);
        pluginsSaveConfig();
        $saved = true;
    }
}

$tpath = locatePluginTemplates(['config_main'], 'scn', 1);
$tVars = [
    'number' => $number,
    'orderby' => $orderby,
    'mantemplate' => $mantemplate,
    'template' => $template,
    'order_options' => [
        ['value' => '0', 'label' => $lang['scn#config_order_random']],
        ['value' => '1', 'label' => $lang['scn#config_order_date_desc']],
        ['value' => '2', 'label' => $lang['scn#config_order_date_asc']],
        ['value' => '3', 'label' => $lang['scn#config_order_id_desc']],
        ['value' => '4', 'label' => $lang['scn#config_order_id_asc']],
    ],
    'template_options' => [
        ['value' => '0', 'label' => $lang['scn#config_template_default']],
        ['value' => '1', 'label' => $lang['scn#config_template_custom']],
    ],
    'lang' => $lang,
    'saved' => $saved,
    'error' => $error,
];

$template = $twig->loadTemplate($tpath['config_main'] . 'config_main.tpl');
print $template->render($tVars);
