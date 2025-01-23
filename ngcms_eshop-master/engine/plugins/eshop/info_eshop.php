<?php

if (!defined('NGCMS'))
    exit('HAL');
    
include_once(dirname(__FILE__).'/cache.php');

loadPluginLibrary('uprofile', 'lib');
LoadPluginLibrary('gsmg', 'common');

LoadPluginLang('eshop', 'main', '', '', '#');
add_act('core', 'eshop_infovars_show');
add_act('index', 'plugin_eshop_compare');
add_act('index', 'plugin_ebasket_total');
add_act('index', 'plugin_ebasket_notify');

register_htmlvar('js', admin_url.'/plugins/eshop/tpl/js/breeze.min.js');
register_htmlvar('js', admin_url.'/plugins/eshop/tpl/js/eshop.js');

function eshop_infovars_show()
{
global $CurrentHandler, $SYSTEM_FLAGS, $template, $lang, $mysql, $twig, $userROW, $ngCookieDomain;

    $SYSTEM_FLAGS["eshop"]["description_delivery"] = pluginGetVariable('eshop', 'description_delivery');
    $SYSTEM_FLAGS["eshop"]["description_order"] = pluginGetVariable('eshop', 'description_order');
    $SYSTEM_FLAGS["eshop"]["description_phones"] = pluginGetVariable('eshop', 'description_phones');

    $eshop_dir = get_plugcfg_dir('eshop');
    generate_currency_cache();
    
    if(file_exists($eshop_dir.'/cache_currency.php')){
        $currency_tEntry = unserialize(file_get_contents($eshop_dir.'/cache_currency.php'));
    } else {
        $currency_tEntry = array();
    }

/*
    $currency_link = checkLinkAvailable('eshop', 'currency')?
            generateLink('eshop', 'currency', array()):
            generateLink('core', 'plugin', array('plugin' => 'eshop', 'handler' => 'currency'), array());

    $currency_tEntry = array();
    foreach ($mysql->select("SELECT * FROM ".prefix."_eshop_currencies WHERE enabled = 1 ORDER BY position, id") as $row)
    {
        $row['currency_link'] = $currency_link."?id=".$row['id'];
        $currency_tEntry[] = $row;
    }
*/

    $SYSTEM_FLAGS["eshop"]["currency"] = $currency_tEntry;
    
    $current_currency = array();

    if (!isset($_COOKIE['ngCurrencyID'])) {
        $ngCurrencyID = $currency_tEntry[0]['id'];
        $current_currency = $currency_tEntry[0];
        @setcookie('ngCurrencyID', $ngCurrencyID, time()+86400*365, '/', $ngCookieDomain, 0, 1);
    } else {
        $ngCurrencyID = $_COOKIE['ngCurrencyID'];
        foreach ($currency_tEntry as $cc)
        {
            if($cc['id'] == $ngCurrencyID) {
                $current_currency = $cc;
            }
        }
        
        if(empty($current_currency)) {
            $current_currency = $currency_tEntry[0];
        }
        
    }
    
    $SYSTEM_FLAGS["eshop"]["current_currency"] = $current_currency;

    $filter = array();
    if (is_array($userROW)) {
        $filter []= '(user_id = '.db_squote($userROW['id']).')';
    }

    if (isset($_COOKIE['ngTrackID']) && ($_COOKIE['ngTrackID'] != '')) {
        $filter []= '(cookie = '.db_squote($_COOKIE['ngTrackID']).')';
    }

    $tCount = 0;
    $tEntries = array();
    foreach ($mysql->select("SELECT * FROM ".prefix."_eshop_compare WHERE ".join(" or ", $filter)."") as $row)
    {
        $tEntries[] = $row;
        $tCount += 1;
    }
    
    $compare_link = checkLinkAvailable('eshop', 'compare')?
        generateLink('eshop', 'compare', array()):
        generateLink('core', 'plugin', array('plugin' => 'eshop', 'handler' => 'compare'), array());

    $compare_tVars = array(
        'count' => $tCount,
        'link'  => $compare_link,
        'entries' => $tEntries,
    );

    $SYSTEM_FLAGS["eshop"]["compare"] = $compare_tVars;

    $tCount = 0;
    $tPrice = 0;
    $tEntries = array();
    foreach ($mysql->select("SELECT * FROM ".prefix."_eshop_ebasket WHERE ".join(" or ", $filter)."") as $row)
    {
        $tEntries[] = $row;
        $tCount += 1;
        $tPrice += $row['price']*$row['count'];
    }

    $basket_link = checkLinkAvailable('eshop', 'ebasket_list')?
            generateLink('eshop', 'ebasket_list', array()):
            generateLink('core', 'plugin', array('plugin' => 'eshop', 'handler' => 'ebasket_list'), array());

    $basket_tVars = array(
        'count' => $tCount,
        'price' => $tPrice,
        'entries' => $tEntries,
        'basket_link' => $basket_link,
    );

    $SYSTEM_FLAGS["eshop"]["basket"] = $basket_tVars;
    
}

//
// Отображение общей информации о сравнеии продукции
function plugin_eshop_compare() {
    global $mysql, $twig, $userROW, $template, $SYSTEM_FLAGS;

    $tpath = locatePluginTemplates(array('compare_block_eshop'), 'eshop', pluginGetVariable('eshop', 'localsource'));
    $xt = $twig->loadTemplate($tpath['compare_block_eshop'].'compare_block_eshop.tpl');
    $template['vars']['eshop_compare'] = $xt->render($SYSTEM_FLAGS["eshop"]["compare"]);
}

//
// Отображение общей информации/остатков в корзине
function plugin_ebasket_total() {
    global $mysql, $twig, $userROW, $template, $SYSTEM_FLAGS;

    $tpath = locatePluginTemplates(array('ebasket/total'), 'eshop', pluginGetVariable('eshop', 'localsource'));
    $xt = $twig->loadTemplate($tpath['ebasket/total'].'ebasket/'.'total.tpl');
    $template['vars']['eshop_ebasket'] = $xt->render($SYSTEM_FLAGS["eshop"]["basket"]);
}

//
// Отображение блока нотификации при добавлении продукта в корзину
function plugin_ebasket_notify() {
    global $mysql, $twig, $userROW, $template;

    // Выводим шаблон
    $tpath = locatePluginTemplates(array('ebasket/notify'), 'eshop', pluginGetVariable('eshop', 'localsource'));

    $basket_link = checkLinkAvailable('eshop', 'ebasket_list')?
            generateLink('eshop', 'ebasket_list', array()):
            generateLink('core', 'plugin', array('plugin' => 'eshop', 'handler' => 'ebasket_list'), array());

    // Готовим переменные
    $tVars = array(
        'basket_link' => $basket_link,
    );

    $xt = $twig->loadTemplate($tpath['ebasket/notify'].'ebasket/'.'notify.tpl');
    $template['vars']['eshop_ebasket_notify'] = $xt->render($tVars);
}

if (class_exists('p_uprofileFilter')) {
    class uOrderFilter extends p_uprofileFilter {
        function editProfileForm($userID, $SQLrow, &$tvars) 
        {
            global $mysql, $twig, $userROW, $template;
      
            $conditions = array();

            if ($userID) {
                array_push($conditions, "author_id = ".db_squote($userID));
            }

            $fSort = " ORDER BY id DESC";
            $sqlQPart = "FROM ".prefix."_eshop_orders ".(count($conditions)?"WHERE ".implode(" AND ", $conditions):'').$fSort;
            $sqlQ = "SELECT * ".$sqlQPart;

            foreach ($mysql->select($sqlQ) as $row)
            {

                $order_link = checkLinkAvailable('eshop', 'order')?
                generateLink('eshop', 'order', array(), array('id' => $row['id'], 'uniqid' => $row['uniqid'])):
                generateLink('core', 'plugin', array('plugin' => 'eshop', 'handler' => 'order'), array(), array('id' => $row['id'],'uniqid' => $row['uniqid']));
                
                $row['order_link'] = $order_link;
                $tEntry[] = $row;
                
            }
            
            $tvars['eshop']['orders'] = $tEntry;
        }
    }

    register_filter('plugin.uprofile','orders', new uOrderFilter);
}

if (class_exists('gsmgFilter')) {
    class gShopFilter extends gsmgFilter {
        function onShow(&$output) {
            global $userROW, $mysql, $twig;
    
            if(pluginGetVariable('eshop', 'integrate_gsmg') == "1") {
                $lm = $mysql->record("select date(from_unixtime(max(date))) as pd from ".prefix."_eshop_products");
                foreach ($mysql->select("SELECT * FROM ".prefix."_eshop_categories ORDER BY position, id") as $rows)
                {
                    $cat_link = checkLinkAvailable('eshop', '')?
                            generateLink('eshop', '', array('alt' => $rows['url'])):
                            generateLink('core', 'plugin', array('plugin' => 'eshop'), array('alt' => $rows['url']));
                    $new_output.= "<url>";
                    $new_output.= "<loc>".home.$cat_link."</loc>";
                    $new_output.= "<priority>".floatval(pluginGetVariable('gsmg', 'catp_pr'))."</priority>";
                    $new_output.= "<lastmod>".$lm['pd']."</lastmod>";
                    $new_output.= "<changefreq>daily</changefreq>";
                    $new_output.= "</url>";
                }
                
                $query = "select * from ".prefix."_eshop_products where active = 1 order by id desc";

                foreach ($mysql->select($query) as $rec) {
                    $link = checkLinkAvailable('eshop', 'show')?
                            generateLink('eshop', 'show', array('alt' => $rec['url'])):
                            generateLink('core', 'plugin', array('plugin' => 'eshop', 'handler' => 'show'), array('alt' => $rec['url']));;
                    $new_output.= "<url>";
                    $new_output.= "<loc>".home.$link."</loc>";
                    $new_output.= "<priority>".floatval(pluginGetVariable('gsmg', 'news_pr'))."</priority>";
                    $new_output.= "<lastmod>".strftime("%Y-%m-%d", max($rec['editdate'], $rec['date']))."</lastmod>";
                    $new_output.= "<changefreq>daily</changefreq>";
                    $new_output.= "</url>";
                }
                
                $output = $output.$new_output;
                
            }
            
        }
    }
    
    register_filter('gsmg','eshop', new gShopFilter);
}
