<?php
// Protect against hack attempts
if (!defined('NGCMS')) {
    die('HAL');
}
// Регистрация страниц плагина
register_plugin_page('freelance_market', '', 'fm_index', 0);
register_plugin_page('freelance_market', 'job', 'fm_view_job', 0);
register_plugin_page('freelance_market', 'new', 'fm_new_job', 0);
register_plugin_page('freelance_market', 'create', 'fm_create_job', 0);
register_plugin_page('freelance_market', 'buy', 'fm_buy_access', 0);
register_plugin_page('freelance_market', 'bid', 'fm_add_bid', 0);
register_plugin_page('freelance_market', 'result', 'fm_pay_result', 0);
register_plugin_page('freelance_market', 'success', 'fm_pay_success', 0);
register_plugin_page('freelance_market', 'fail', 'fm_pay_fail', 0);
register_plugin_page('freelance_market', 'my', 'fm_my_jobs', 0);
loadPluginLang('freelance_market', 'main', '', '', ':');
// Утилиты
function fm_has_access($uid)
{
    global $mysql;
    if (!$uid) {
        return false;
    }
    $row = $mysql->record('SELECT access_until FROM ' . prefix . '_freelance_user_access WHERE user_id = ' . db_squote($uid));
    if (!is_array($row)) {
        return false;
    }
    return (intval($row['access_until']) > time());
}
function fm_require_login()
{
    global $userROW, $lang;
    if (!is_array($userROW)) {
        msg(array('type' => 'error', 'text' => $lang['freelance_market:login_required']));
        return false;
    }
    return true;
}
function fm_render($tplName, $vars = [])
{
    global $twig, $template;
    $tpath = locatePluginTemplates([$tplName], 'freelance_market', pluginGetVariable('freelance_market', 'localsource'));
    $xt = $twig->loadTemplate($tpath[$tplName] . $tplName . '.tpl');
    $template['vars']['mainblock'] = $xt->render($vars);
}
// Список заданий
function fm_index()
{
    global $mysql, $lang, $SYSTEM_FLAGS;
    $SYSTEM_FLAGS['info']['title']['group'] = $lang['freelance_market:title'];
    $jobs = $mysql->select('SELECT j.*, u.name as author_name FROM ' . prefix . '_freelance_jobs j LEFT JOIN ' . uprefix . '_users u ON u.id=j.user_id WHERE j.status = 1 ORDER BY j.id DESC LIMIT 50');
    fm_render('list', ['jobs' => $jobs]);
}
// Просмотр задания
function fm_view_job()
{
    global $mysql, $userROW, $lang;
    $id = intval($_REQUEST['id']);
    if (!$id) {
        msg(array('type' => 'error', 'text' => $lang['freelance_market:job_not_found']));
        return fm_index();
    }
    $job = $mysql->record('SELECT j.*, u.name as author_name, u.id as author_id FROM ' . prefix . '_freelance_jobs j LEFT JOIN ' . uprefix . '_users u ON u.id=j.user_id WHERE j.id = ' . db_squote($id));
    if (!is_array($job)) {
        msg(array('type' => 'error', 'text' => $lang['freelance_market:job_not_found']));
        return fm_index();
    }
    // +1 просмотр
    $mysql->query('UPDATE ' . prefix . '_freelance_jobs SET views=views+1 WHERE id=' . db_squote($id));

    $canSeeContacts = fm_has_access($userROW['id'] ?? 0) || (($userROW['id'] ?? 0) == $job['user_id']);
    // Скрываем контакты, если нет доступа
    $contacts = $canSeeContacts ? $job['contacts'] : $lang['freelance_market:contacts_hidden'];
    // Отклики
    $bids = $mysql->select('SELECT b.*, u.name as user_name FROM ' . prefix . '_freelance_bids b LEFT JOIN ' . uprefix . '_users u ON u.id=b.user_id WHERE b.job_id = ' . db_squote($id) . ' ORDER BY b.id DESC');
    $isLogged = is_array($userROW) ? 1 : 0;
    fm_render('view', ['job' => $job, 'contacts' => $contacts, 'canSeeContacts' => $canSeeContacts, 'bids' => $bids, 'isLogged' => $isLogged]);
}
// Форма нового задания
function fm_new_job()
{
    global $lang;
    if (!fm_require_login()) {
        return;
    }
    fm_render('new', []);
}
// Создание задания (POST)
function fm_create_job()
{
    global $mysql, $userROW, $lang;
    if (!fm_require_login()) {
        return;
    }
    $title = trim($_REQUEST['title']);
    $desc = trim($_REQUEST['description']);
    $price = floatval($_REQUEST['price']);
    $location = trim($_REQUEST['location']);
    $contacts = trim($_REQUEST['contacts']);
    if ($title == '' || $desc == '' || $contacts == '') {
        msg(array('type' => 'error', 'text' => $lang['freelance_market:fill_required']));
        return fm_new_job();
    }
    $mysql->query('INSERT INTO ' . prefix . '_freelance_jobs (date,user_id,title,description,price,location,contacts,status,views) VALUES ('
        . db_squote(time()) . ', '
        . db_squote(intval($userROW['id'])) . ', '
        . db_squote($title) . ', '
        . db_squote($desc) . ', '
        . db_squote($price) . ', '
        . db_squote($location) . ', '
        . db_squote($contacts) . ', 1, 0)');
    msg(array('type' => 'info', 'text' => $lang['freelance_market:job_created']));
    return fm_index();
}
// Покупка доступа (страница выбора срока)
function fm_buy_access()
{
    global $userROW, $lang, $mysql;
    if (!fm_require_login()) {
        return;
    }
    $prices = [
        10 => floatval(pluginGetVariable('freelance_market', 'price_10') ?? 199),
        30 => floatval(pluginGetVariable('freelance_market', 'price_30') ?? 499),
    ];
    $days = intval($_REQUEST['days']);
    $payForm = '';
    if (fm_has_access($userROW['id'] ?? 0)) {
        $payForm .= '<div class="alert alert-info">' . $lang['freelance_market:already_active'] . '</div>';
    }
    if (in_array($days, [10, 30]) && $prices[$days] > 0) {
        $amount = number_format($prices[$days], 2, '.', '');
        $mrh_login = pluginGetVariable('freelance_market', 'robokassa_login');
        $pass1     = pluginGetVariable('freelance_market', 'robokassa_pass1');
        $isTest    = intval(pluginGetVariable('freelance_market', 'robokassa_is_test')) ? 1 : 0;
        // Создать заказ
        $orderId = time() . rand(1000, 9999);
        $mysql->query('INSERT INTO ' . prefix . '_freelance_pay_order (dt,user_id,merchant_id,order_id,amount,currency,description,paymode,status,test_mode) VALUES ('
            . db_squote(time()) . ', '
            . db_squote(intval($userROW['id'])) . ', '
            . db_squote($mrh_login) . ', '
            . db_squote($orderId) . ', '
            . db_squote($amount) . ', '
            . db_squote('RUB') . ', '
            . db_squote($days) . ', '
            . db_squote('robokassa') . ', '
            . db_squote('new') . ', '
            . db_squote($isTest) . ')');
        $shp = $days; // shpItem = 10|30
        $crc = strtoupper(md5($mrh_login . ':' . $amount . ':' . $orderId . ':' . $pass1 . ':shpItem=' . $shp));
        $baseUrl = 'https://auth.robokassa.ru/Merchant/Index.aspx';
        $params = [
            'MerchantLogin'  => $mrh_login,
            'OutSum'         => $amount,
            'InvId'          => $orderId,
            'Description'    => 'Access ' . $days . ' days',
            'SignatureValue' => $crc,
            'shpItem'        => $shp,
            'IsTest'         => $isTest,
            'Culture'        => 'ru',
        ];
        $q = [];
        foreach ($params as $k => $v) {
            $q[] = $k . '=' . rawurlencode($v);
        }
        $payUrl = $baseUrl . '?' . implode('&', $q);
        $payForm = '<a class="btn btn-success" href="' . $payUrl . '">Оплатить через Robokassa</a>';
    }
    fm_render('buy', ['prices' => $prices, 'pay_form' => $payForm]);
}

// Добавление отклика
function fm_add_bid()
{
    global $mysql, $userROW, $lang;
    if (!fm_require_login()) {
        return;
    }
    $job_id = intval($_REQUEST['job_id']);
    $message = trim($_REQUEST['message']);
    $price_offer = floatval($_REQUEST['price_offer']);
    if (!$job_id || $message == '') {
        msg(array('type' => 'error', 'text' => $lang['freelance_market:bid_fill_required']));
        header('Location: /?plugin=freelance_market&handler=job&id=' . $job_id);
        return;
    }
    // Проверим существование задания
    $j = $mysql->record('SELECT id FROM ' . prefix . '_freelance_jobs WHERE id=' . db_squote($job_id));
    if (!is_array($j)) {
        msg(array('type' => 'error', 'text' => $lang['freelance_market:job_not_found']));
        header('Location: /?plugin=freelance_market');
        return;
    }
    $mysql->query('INSERT INTO ' . prefix . '_freelance_bids (job_id,user_id,message,price_offer,date,status) VALUES ('
        . db_squote($job_id) . ', '
        . db_squote(intval($userROW['id'])) . ', '
        . db_squote($message) . ', '
        . db_squote($price_offer) . ', '
        . db_squote(time()) . ', 0)');
    msg(array('type' => 'info', 'text' => $lang['freelance_market:bid_added']));
    header('Location: /?plugin=freelance_market&handler=job&id=' . $job_id);
}
// Робокасса: формирование подписи
function fm_robo_signature($merchantLogin, $amount, $invoiceId, $password1, $shp)
{
    $crc = md5($merchantLogin . ':' . $amount . ':' . $invoiceId . ':' . $password1 . ':shpItem=' . $shp);
    return $crc;
}
// Робокасса: обработка результата
function fm_pay_result()
{
    global $mysql;
    $outSum = $_REQUEST['OutSum'];
    $invId  = $_REQUEST['InvId'];
    $crc    = strtoupper($_REQUEST['SignatureValue']);
    $shp    = $_REQUEST['shpItem'];
    $mrh_login = pluginGetVariable('freelance_market', 'robokassa_login');
    $pass2     = pluginGetVariable('freelance_market', 'robokassa_pass2');
    $my_crc    = strtoupper(md5($outSum . ':' . $invId . ':' . $pass2 . ':shpItem=' . $shp));
    if ($my_crc != $crc) {
        echo 'bad sign';
        return;
    }
    // Найти заказ
    $order = $mysql->record('SELECT * FROM ' . prefix . '_freelance_pay_order WHERE order_id = ' . db_squote($invId));
    if (!is_array($order)) {
        echo 'no order';
        return;
    }
    // Проставить успешный статус
    $mysql->query('UPDATE ' . prefix . '_freelance_pay_order SET status=' . db_squote('success') . ', trans_id=' . db_squote($_REQUEST['InvId']) . ' WHERE id=' . db_squote($order['id']));
    // Продлить доступ
    $days = intval($order['description']);
    $delta = $days * 86400;
    $row = $mysql->record('SELECT * FROM ' . prefix . '_freelance_user_access WHERE user_id = ' . db_squote($order['user_id']));
    $now = time();
    if (is_array($row)) {
        $newUntil = max(intval($row['access_until']), $now) + $delta;
        $mysql->query('UPDATE ' . prefix . '_freelance_user_access SET access_until = ' . db_squote($newUntil) . ' WHERE user_id = ' . db_squote($order['user_id']));
    } else {
        $mysql->query('INSERT INTO ' . prefix . '_freelance_user_access (user_id, access_until) VALUES (' . db_squote($order['user_id']) . ', ' . db_squote($now + $delta) . ')');
    }
    echo 'OK' . $invId;
}
function fm_pay_success()
{
    global $template, $lang;
    $template['vars']['mainblock'] = '<div class="alert alert-success">' . $lang['freelance_market:pay_success'] . '</div>';
}
function fm_pay_fail()
{
    global $template, $lang;
    $template['vars']['mainblock'] = '<div class="alert alert-danger">' . $lang['freelance_market:pay_fail'] . '</div>';
}
// Страница Мои задания
function fm_my_jobs()
{
    global $mysql, $userROW, $lang;
    if (!fm_require_login()) {
        return;
    }
    $jobs = $mysql->select('SELECT * FROM ' . prefix . '_freelance_jobs WHERE user_id = ' . db_squote(intval($userROW['id'])) . ' ORDER BY id DESC');
    fm_render('my', ['jobs' => $jobs]);
}
