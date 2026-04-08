<?php
if (!defined('NGCMS'))
    die('HAL');
function plugin_block_freelance_market($number, $templateName = 'block/block_freelance_market')
{
    global $mysql, $twig;
    $number = intval($number);
    if ($number < 1 || $number > 50) {
        $number = 5;
    }
    $rows = $mysql->select('SELECT j.id, j.title, j.price FROM ' . prefix . '_freelance_jobs j WHERE j.status = 1 ORDER BY j.id DESC LIMIT ' . $number);
    $tVars = [
        'entries' => $rows,
    ];
    $tpath = locatePluginTemplates([$templateName], 'freelance_market', pluginGetVariable('freelance_market', 'localsource'));
    $xt = $twig->loadTemplate($tpath[$templateName] . $templateName . '.tpl');
    return $xt->render($tVars);
}
function plugin_block_freelance_market_showTwig($params)
{
    return plugin_block_freelance_market($params['number'] ?? 5, $params['template'] ?? 'block/block_freelance_market');
}
twigRegisterFunction('freelance_market', 'show', 'plugin_block_freelance_market_showTwig');
