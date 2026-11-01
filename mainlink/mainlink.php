<?php

// Protect against hack attempts
if (!defined('NGCMS')) die('HAL');

add_act('index', 'plugin_mainlink');

function plugin_mainlink()
{
    global $template;
    require_once($_SERVER['DOCUMENT_ROOT'] . '/' . extra_get_param('mainlink', 'folder') . '/ML.php');


    $mlcfg = array();

    if (extra_get_param('mainlink', 'debugmode') == 1) $mlcfg['debugmode'] = 1;
    if (extra_get_param('mainlink', 'charset') != 'win') $mlcfg['charset'] = extra_get_param('mainlink', 'charset');
    if (extra_get_param('mainlink', 'splitter') != '') $mlcfg['splitter'] = extra_get_param('mainlink', 'splitter');
    if (extra_get_param('mainlink', 'htmlbefore') != '') $mlcfg['htmlbefore'] = extra_get_param('mainlink', 'htmlbefore');
    if (extra_get_param('mainlink', 'htmlafter') != '') $mlcfg['htmlafter'] = extra_get_param('mainlink', 'htmlafter');
    if (extra_get_param('mainlink', 'style') != '') $mlcfg['style'] = extra_get_param('mainlink', 'style');
    if (extra_get_param('mainlink', 'class_name') != '') $mlcfg['class_name'] = extra_get_param('mainlink', 'class_name');


    $ml->Set_Config($mlcfg);

    if ((int)extra_get_param('mainlink', 'count_links') == 0) $template['vars']['mainlink'] = $ml->Get_Links();
    else {
        $template['vars']['mainlink'] = $ml->Get_Links((int)extra_get_param('mainlink', 'count_links'));
        $template['vars']['mainlink2'] = $ml->Get_Links();
    }
}
