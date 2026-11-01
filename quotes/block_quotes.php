<?php
/*
=====================================================
 NG Quotes block v0.01
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
    die('HAL');
}

require_once __DIR__ . '/lib/runtime.php';

add_act('index', 'plugin_quotes_block');

function plugin_quotes_block()
{
    global $template, $twig, $mysql, $config, $parse;

    $tpath = locatePluginTemplates(array('quotes_block', 'show_block'), 'quotes', pluginGetVariable('quotes', 'localsource'), '', 'block');

    $randomMode = (int)pluginGetVariable('quotes', 'rand');
    switch ($randomMode) {
        case 0:
            $quoteCount = (int)$mysql->result('SELECT COUNT(id) FROM ' . prefix . '_quotes');
            if ($quoteCount < 1) {
                quotes_log('block_no_quotes', ['random_mode' => $randomMode]);
                $template['vars']['quotes_block'] = $twig->render($tpath['show_block'] . 'show_block.tpl', [
                    'name' => 'Цитат не существует',
                ]);
                return;
            }
            $offset = mt_rand(0, $quoteCount - 1);
            $result = $mysql->select('SELECT content FROM ' . prefix . '_quotes ORDER BY id LIMIT ' . $offset . ', 1');
            break;
        case 1:
        default:
            $result = $mysql->select('SELECT content FROM ' . prefix . '_quotes ORDER BY RAND() LIMIT 1');
    }

    if (empty($result)) {
        quotes_log('block_query_empty', ['random_mode' => $randomMode]);
        $template['vars']['quotes_block'] = $twig->render($tpath['show_block'] . 'show_block.tpl', [
            'name' => 'Цитат не существует',
        ]);
        return;
    }

    quotes_log('block_quote_selected', ['random_mode' => $randomMode, 'result_count' => count($result)]);

    $output = '';

    foreach ($result as $row) {
        $content = $row['content'];

        $content = quotes_filter_hidden_blocks($content);
        if ($config['use_htmlformatter']) {
            $content = $parse->htmlformatter($content);
        }
        if ($config['use_bbcodes']) {
            $content = $parse->bbcodes($content);
        }
        if ($config['use_smilies']) {
            $content = $parse->smilies($content);
        }

        $output .= $twig->render($tpath['show_block'] . 'show_block.tpl', [
            'name' => $content,
        ]);
    }

    $template['vars']['quotes_block'] = $twig->render($tpath['quotes_block'] . 'quotes_block.tpl', [
        'entries' => $output,
        'tpl_url' => tpl_url,
    ]);
}
