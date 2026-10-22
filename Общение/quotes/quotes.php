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

LoadPluginLang('quotes', 'index');

register_plugin_page('quotes', '', 'plugin_show_quotes');
register_plugin_page('quotes', 'add', 'plugin_add_quotes');
register_plugin_page('quotes', 'rss', 'plugin_rss_quotes');
register_plugin_page('quotes', 'show', 'plugin_fullshow_quotes');

function plugin_show_quotes($params)
{
    global $twig, $template, $SUPRESS_TEMPLATE_SHOW, $SYSTEM_FLAGS, $mysql, $config, $parse, $userROW, $TemplateCache, $CurrentHandler;

    //$SUPRESS_TEMPLATE_SHOW = 1;

    $title = '';
    $handlerPage = $CurrentHandler['params']['page'] ?? null;
    if ($handlerPage) {
        $title = " : {$handlerPage} Страница";
    }

    $getPage = $_GET['page'] ?? null;
    if ($getPage) {
        $title = " : {$getPage} Страница";
    }

    $SYSTEM_FLAGS['info']['title']['group'] = "Цитатник{$title}";
    $SYSTEM_FLAGS['meta']['description'] = pluginGetVariable('quotes', 'description');
    $SYSTEM_FLAGS['meta']['keywords'] = pluginGetVariable('quotes', 'keywords');

    $page = isset($params['page']) ? (int) $params['page'] : (int) ($_GET['page'] ?? 0);
    quotes_log('list_requested', ['page' => $page]);
    $cacheFileName = md5('quotes' . $config['home_url'] . $config['theme'] . $config['default_lang']) . "{$page}.txt";

    if (pluginGetVariable('quotes', 'cache')) {
        $cacheData = cacheRetrieveFile($cacheFileName, pluginGetVariable('quotes', 'cacheExpire'), 'quotes');
        if ($cacheData != false) {
            quotes_log('list_cache_hit', ['page' => $page]);
            $template['vars']['mainblock'] = $cacheData;
            return;
        }
    }

    $res = $mysql->result("select count(*) from " . prefix . "_quotes where approve = 1");

    $num = pluginGetVariable('quotes', 'count');

    $limitStart = 0;
    $limitCount = $num;
    $multi_scount = (int) $num;
    $pageCount = 1;
    if (($multi_scount > 0) && ($res > $multi_scount)) {
        $pageCount = ceil($res / $multi_scount);
        if ($page < 1) {
            $page = 1;
        }
        $limitCount = intval($num);
        $limitStart = ($page - 1) * intval($num);
    }

    $tvars['vars']['pages'] = '';
    if ($pageCount > 1) {
        $paginationParams = checkLinkAvailable('quotes', '') ?
            array('pluginName' => 'quotes', 'pluginHandler' => '', 'params' => array(), 'xparams' => array(), 'paginator' => array('page', 0, false)) :
            array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'quotes'), 'xparams' => array(), 'paginator' => array('page', 1, false));

        templateLoadVariables(true);
        $navigations = $TemplateCache['site']['#variables']['navigation'];

        $tvars['vars']['pages'] .= generatePagination($page, 1, $pageCount, 10, $paginationParams, $navigations);
    }

    $tpath = locatePluginTemplates(array('showquotes', 'shortquotes'), 'quotes', pluginGetVariable('quotes', 'localsource'));

    $output = '';
    foreach ($mysql->select("select id, content, approve, postdate, author, author_id from " . prefix . "_quotes where approve = 1 order by postdate DESC LIMIT " . abs((int) $limitStart) . ", " . abs((int) $limitCount)) as $row) {
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

        $userlink = checkLinkAvailable('uprofile', 'show') ?
            generateLink('uprofile', 'show', array('name' => $row['author'], 'id' => $row['author_id'])) :
            generateLink('core', 'plugin', array('plugin' => 'uprofile', 'handler' => 'show'), array('name' => $row['author'], 'id' => $row['author_id']));

        $showlink = checkLinkAvailable('quotes', 'show') ?
            generateLink('quotes', 'show', array('id' => $row['id'])) :
            generateLink('core', 'plugin', array('plugin' => 'quotes', 'handler' => 'show'), array('id' => $row['id']));

        $output .= $twig->render($tpath['shortquotes'] . 'shortquotes.tpl', [
            'content' => $content,
            'date' => LangDate(pluginGetVariable('quotes', 'date'), $row['postdate']),
            'author_url' => $userlink,
            'author_name' => $row['author'],
            'show_url' => $showlink,
        ]);
    }

    $addlink = checkLinkAvailable('quotes', 'add') ?
        generateLink('quotes', 'add') :
        generateLink('core', 'plugin', array('plugin' => 'quotes', 'handler' => 'add'));

    if ($output == null) {
        return error404();
    }

    $result = $twig->render($tpath['showquotes'] . 'showquotes.tpl', [
        'add_url' => $addlink,
        'short' => $output,
        'pages' => $tvars['vars']['pages'],
    ]);
    $template['vars']['mainblock'] = $result;
    if (pluginGetVariable('quotes', 'cache')) {
        cacheStoreFile($cacheFileName, $result, 'quotes');
    }
}

function plugin_fullshow_quotes($params)
{
    global $twig, $template, $SUPRESS_TEMPLATE_SHOW, $SYSTEM_FLAGS, $mysql, $config, $parse, $userROW;

    //$SUPRESS_TEMPLATE_SHOW = 1;

    $id = isset($params['id']) ? abs((int) $params['id']) : abs((int) ($_GET['id'] ?? 0));
    quotes_log('quote_requested', ['quote_id' => $id]);

    $quoteslink = checkLinkAvailable('quotes', '') ?
        generateLink('quotes', '') :
        generateLink('core', 'plugin', array('plugin' => 'quotes'));

    if ($id == null) {
        header("HTTP/1.0 404 Not Found");
        return quotes_toast_redirect('Не передан id цитаты', $quoteslink, 'error');
    }

    $SYSTEM_FLAGS['info']['title']['group'] = "Цитата №{$id}";
    $SYSTEM_FLAGS['meta']['description'] = pluginGetVariable('quotes', 'description');
    $SYSTEM_FLAGS['meta']['keywords'] = pluginGetVariable('quotes', 'keywords');

    $tpath = locatePluginTemplates(array('fullquotes'), 'quotes', pluginGetVariable('quotes', 'localsource'));

    $output = '';
    foreach ($mysql->select("select id, content, approve, postdate, author, author_id from " . prefix . "_quotes where approve = 1 and id = " . db_squote("{$id}")) as $row) {
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

        $userlink = checkLinkAvailable('uprofile', 'show') ?
            generateLink('uprofile', 'show', array('name' => $row['author'], 'id' => $row['author_id'])) :
            generateLink('core', 'plugin', array('plugin' => 'uprofile', 'handler' => 'show'), array('name' => $row['author'], 'id' => $row['author_id']));

        $output = $twig->render($tpath['fullquotes'] . 'fullquotes.tpl', [
            'content' => $content,
            'date' => LangDate(pluginGetVariable('quotes', 'date'), $row['postdate']),
            'author_url' => $userlink,
            'author_name' => $row['author'],
            'title' => "Цитата №{$id}",
            'tpl_url' => tpl_url,
        ]);
    }

    if ($output == null) {
        return error404();
    } else {
        $template['vars']['mainblock'] = $output;
    }
}

function plugin_add_quotes()
{
    global $twig, $template, $SYSTEM_FLAGS, $SUPRESS_TEMPLATE_SHOW, $userROW, $mysql, $config, $lang;

    //$SUPRESS_TEMPLATE_SHOW = 1;

    if (!is_array($userROW)) {
        quotes_log('public_add_rejected_anonymous');
        header("HTTP/1.0 404 Not Found");
        return quotes_toast_redirect('Для самых умных дарога лишь на сайт', home, 'error');
    }

    quotes_log('public_add_opened', [
        'method' => $_SERVER['REQUEST_METHOD'] ?? '',
        'post_fields' => array_keys($_POST),
        'content_present' => array_key_exists('content', $_POST),
        'content_bytes' => is_string($_POST['content'] ?? null) ? strlen($_POST['content']) : 0,
        'user_id' => (int)($userROW['id'] ?? 0),
    ]);

    $SYSTEM_FLAGS['info']['title']['group'] = 'Добавить сообщение в цитатник';

    $num_max = pluginGetVariable('quotes', 'max_char');
    $time['time'] = time() + ($config['date_adjust'] * 60);
    $error_flag = false;
    $error_text = array();

    $submittedContent = is_string($_POST['content'] ?? null) ? $_POST['content'] : '';
    $content = str_replace("\r\n", "\n", $submittedContent);
    if ((int)$num_max > 0) {
        $content = mb_substr($content, 0, (int)$num_max, 'UTF-8');
    }
    $data['content'] = secure_html($content);

    $addlink = checkLinkAvailable('quotes', '') ?
        generateLink('quotes', '') :
        generateLink('core', 'plugin', array('plugin' => 'quotes'));

    if ($userROW['status'] == 1) {
        $approve = 1;
    } else {
        $approve = 0;
    }

    if (isset($_POST['addpost'])) {
        quotes_log('public_add_attempt', [
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
            $error_text[] = msg(array("type" => "error", "text" => "Вы не добавили цитату"), 0, 2);
            quotes_log('public_add_rejected_empty', ['user_id' => (int)($userROW['id'] ?? 0)]);
        }
        if (trim($data['content']) !== '') {
            $insertResult = $mysql->query("INSERT INTO " . prefix . "_quotes
                (postdate, content, approve, author, author_id)
                VALUES (" . db_squote($time['time']) . ", " . db_squote($data['content']) . ", " . db_squote($approve) . ", " . db_squote($userROW['name']) . ", " . db_squote((int)$userROW['id']) . ")");
            if ($insertResult === null || $insertResult === false) {
                quotes_log('public_add_insert_failed', ['user_id' => (int)$userROW['id']]);
                return quotes_toast_redirect($lang['quotes_save_failed'], $addlink, 'error');
            }
            quotes_log('public_add_inserted', ['user_id' => (int)$userROW['id']]);
            return quotes_toast_redirect('Данные внесены', $addlink, 'success');
        }
    }

    $maxCharAlert = json_encode(
        sprintf('Текст сообщения не должен быть больше %d символов', (int)$num_max),
        JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP
    );

    $tpath = locatePluginTemplates(array('addquotes'), 'quotes', pluginGetVariable('quotes', 'localsource'));

    $template['vars']['mainblock'] = $twig->render($tpath['addquotes'] . 'addquotes.tpl', [
        'error' => $error_flag ? implode('<br />', $error_text) : '',
        'content' => $data['content'],
        'smilies' => InsertSmilies('comments', 10),
        'bbcodes' => BBCodes(),
        'max_char' => (int)$num_max,
        'max_char_alert' => $maxCharAlert,
    ]);
}

function plugin_rss_quotes()
{
    global $template, $config, $SUPRESS_TEMPLATE_SHOW, $SUPRESS_MAINBLOCK_SHOW, $mysql, $SYSTEM_FLAGS;

    $SUPRESS_TEMPLATE_SHOW = 1;
    $SUPRESS_MAINBLOCK_SHOW = 1;
    quotes_log('rss_requested');

    $SYSTEM_FLAGS['info']['title']['group'] = 'RSS из цитатника';

    $output = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
    $output .= '<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/"
    xmlns:wfw="http://wellformedweb.org/CommentAPI/">' . "\n";
    $output .= "<channel>\n";
    $output .= "<title>RSS цитатника</title>\n";
    $output .= "
        <link>
        <![CDATA[" . $config['home_url'] . "]]>
        </link>\n";
    $output .= "<language>ru</language>\n";
    $output .= "<description>
            <![CDATA[" . $config['description'] . "]]>
        </description>\n";
    $output .= "<generator>
            <![CDATA[Plugin rss_quotes (beta7) // Next Generation CMS (" . engineVersion . ")]]>
        </generator>\n";

    foreach ($mysql->select("select content, postdate from " . prefix . "_quotes") as $row) {
        $output .= " <item>\n";
        $output .= " <title>Цитата</title>\n";
        $output .= "
            <link>
            </link>\n";
        $output .= " <description>
                <![CDATA['{$row['content']}']]>
            </description>\n";
        $output .= " <pubDate>" . gmdate('D, d M Y H:i:s', (int) $row['postdate']) . " GMT</pubDate>\n";
        $output .= "
        </item>\n";
    }
    $output .= "
    </channel>\n</rss>\n";
    print $output;
}

function quotes_toast_redirect($message, $url, $type)
{
    $delay = max(0, (int) pluginGetVariable('quotes', 'redirect_delay')) * 1000;
    register_stylesheet(home . '/lib/notify.css');

    return '<script src="' . home . '/lib/notify.js"></script>'
        . '<script>(function(){'
        . 'if(typeof window.showToast==="function"){window.showToast('
        . json_encode($message, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) . ','
        . '{type:' . json_encode($type) . '});}'
        . 'window.setTimeout(function(){window.location.href='
        . json_encode($url, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) . ';},'
        . $delay . ');'
        . '})();</script>';
}
