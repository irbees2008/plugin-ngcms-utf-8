<?php

// Protect against hack attempts
if (!defined('NGCMS')) {
    die ('HAL');
}

function create_urls()
{

    $ULIB = new urlLibrary();
    $ULIB->loadConfig();

    $ULIB->registerCommand(
        'eshop',
        '',
        array(
            'vars' =>
                array(
                    'alt' => array('matchRegex' => '.+?', 'descr' => array('russian' => 'Altname категории')),
                    'cat' => array('matchRegex' => '\d+', 'descr' => array('russian' => 'ID категории')),
                    'page' => array('matchRegex' => '\d{1,4}', 'descr' => array('russian' => 'Постраничная навигация')),
                ),
            'descr' => array('russian' => 'Главная страница'),
        )
    );

    $ULIB->registerCommand(
        'eshop',
        'show',
        array(
            'vars' =>
                array(
                    'alt' => array('matchRegex' => '.+?', 'descr' => array('russian' => 'Altname продукта')),
                    'id' => array('matchRegex' => '\d+', 'descr' => array('russian' => 'ID продукта')),
                ),
            'descr' => array('russian' => 'Ссылка на продукт'),
        )
    );

    $ULIB->registerCommand(
        'eshop',
        'search',
        array(
            'vars' =>
                array(
                    'page' => array('matchRegex' => '\d{1,4}', 'descr' => array('russian' => 'Постраничная навигация')),
                ),
            'descr' => array('russian' => 'Поиск по продукции'),
        )
    );

    $ULIB->registerCommand(
        'eshop',
        'stocks',
        array(
            'vars' =>
                array(
                    'page' => array('matchRegex' => '\d{1,4}', 'descr' => array('russian' => 'Постраничная навигация')),
                ),
            'descr' => array('russian' => 'Акции'),
        )
    );

    $ULIB->registerCommand(
        'eshop',
        'compare',
        array(
            'descr' => array('russian' => 'Сравнение продукции'),
        )
    );

    $ULIB->registerCommand(
        'eshop',
        'currency',
        array(
            'descr' => array('russian' => 'Валюты'),
        )
    );

    $ULIB->registerCommand(
        'eshop',
        'yml_export',
        array(
            'descr' => array('russian' => 'Экспорт XML'),
        )
    );

    $ULIB->registerCommand(
        'eshop',
        'ebasket_list',
        array(
            'descr' => array('russian' => 'Корзина'),
        )
    );

    $ULIB->registerCommand(
        'eshop',
        'order',
        array(
            'descr' => array('russian' => 'Заказы'),
        )
    );

    $ULIB->registerCommand(
        'eshop',
        'payment',
        array(
            'descr' => array('russian' => 'Оплата'),
        )
    );

    $ULIB->saveConfig();

    $UHANDLER = new urlHandler();
    $UHANDLER->loadConfig();

    $UHANDLER->registerHandler(
        0,
        array(
            'pluginName' => 'eshop',
            'handlerName' => '',
            'flagPrimary' => true,
            'flagFailContinue' => false,
            'flagDisabled' => false,
            'rstyle' =>
                array(
                    'rcmd' => '/[{alt}/][page/{page}/]',
                    'regex' => '#^/(.+?){0,1}(?:page/(\\d{1,4})/){0,1}$#',
                    'regexMap' =>
                        array(
                            1 => 'alt',
                            2 => 'page',
                        ),
                    'reqCheck' =>
                        array(),
                    'setVars' =>
                        array(),
                    'genrMAP' =>
                        array(
                            0 =>
                                array(
                                    0 => 0,
                                    1 => '/',
                                    2 => 0,
                                ),
                            1 =>
                                array(
                                    0 => 1,
                                    1 => 'alt',
                                    2 => 1,
                                ),
                            2 =>
                                array(
                                    0 => 0,
                                    1 => '/',
                                    2 => 1,
                                ),
                            3 =>
                                array(
                                    0 => 0,
                                    1 => 'page/',
                                    2 => 3,
                                ),
                            4 =>
                                array(
                                    0 => 1,
                                    1 => 'page',
                                    2 => 3,
                                ),
                            5 =>
                                array(
                                    0 => 0,
                                    1 => '/',
                                    2 => 3,
                                ),
                        ),
                ),
        )
    );

    $UHANDLER->registerHandler(
        0,
        array(
            'pluginName' => 'eshop',
            'handlerName' => 'show',
            'flagPrimary' => true,
            'flagFailContinue' => false,
            'flagDisabled' => false,
            'rstyle' =>
                array(
                    'rcmd' => '/{alt}.html',
                    'regex' => '#^/(.+?){0,1}.html$#',
                    'regexMap' =>
                        array(
                            1 => 'alt',
                        ),
                    'reqCheck' =>
                        array(),
                    'setVars' =>
                        array(),
                    'genrMAP' =>
                        array(
                            0 =>
                                array(
                                    0 => 0,
                                    1 => '/',
                                    2 => 0,
                                ),
                            1 =>
                                array(
                                    0 => 1,
                                    1 => 'alt',
                                    2 => 0,
                                ),
                            2 =>
                                array(
                                    0 => 0,
                                    1 => '.html',
                                    2 => 0,
                                ),
                        ),
                ),
        )
    );

    $UHANDLER->registerHandler(
        0,
        array(
            'pluginName' => 'eshop',
            'handlerName' => 'search',
            'flagPrimary' => true,
            'flagFailContinue' => false,
            'flagDisabled' => false,
            'rstyle' =>
                array(
                    'rcmd' => '/eshop/search/[page/{page}/]',
                    'regex' => '#^/eshop/search/(?:page/(\\d{1,4})/){0,1}$#',
                    'regexMap' =>
                        array(
                            1 => 'page',
                        ),
                    'reqCheck' =>
                        array(),
                    'setVars' =>
                        array(),
                    'genrMAP' =>
                        array(
                            0 =>
                                array(
                                    0 => 0,
                                    1 => '/eshop/search/',
                                    2 => 0,
                                ),
                            1 =>
                                array(
                                    0 => 0,
                                    1 => 'page/',
                                    2 => 1,
                                ),
                            2 =>
                                array(
                                    0 => 1,
                                    1 => 'page',
                                    2 => 1,
                                ),
                            3 =>
                                array(
                                    0 => 0,
                                    1 => '/',
                                    2 => 1,
                                ),
                        ),
                ),
        )
    );

    $UHANDLER->registerHandler(
        0,
        array(
            'pluginName' => 'eshop',
            'handlerName' => 'stocks',
            'flagPrimary' => true,
            'flagFailContinue' => false,
            'flagDisabled' => false,
            'rstyle' =>
                array(
                    'rcmd' => '/eshop/stocks/[page/{page}/]',
                    'regex' => '#^/eshop/stocks/(?:page/(\\d{1,4})/){0,1}$#',
                    'regexMap' =>
                        array(
                            1 => 'page',
                        ),
                    'reqCheck' =>
                        array(),
                    'setVars' =>
                        array(),
                    'genrMAP' =>
                        array(
                            0 =>
                                array(
                                    0 => 0,
                                    1 => '/eshop/stocks/',
                                    2 => 0,
                                ),
                            1 =>
                                array(
                                    0 => 0,
                                    1 => 'page/',
                                    2 => 1,
                                ),
                            2 =>
                                array(
                                    0 => 1,
                                    1 => 'page',
                                    2 => 1,
                                ),
                            3 =>
                                array(
                                    0 => 0,
                                    1 => '/',
                                    2 => 1,
                                ),
                        ),
                ),
        )
    );

    $UHANDLER->registerHandler(
        0,
        array(
            'pluginName' => 'eshop',
            'handlerName' => 'compare',
            'flagPrimary' => true,
            'flagFailContinue' => false,
            'flagDisabled' => false,
            'rstyle' =>
                array(
                    'rcmd' => '/eshop/compare/',
                    'regex' => '#^/eshop/compare/$#',
                    'regexMap' =>
                        array(
                            1 => 'page',
                        ),
                    'reqCheck' =>
                        array(),
                    'setVars' =>
                        array(),
                    'genrMAP' =>
                        array(
                            0 =>
                                array(
                                    0 => 0,
                                    1 => '/eshop/compare/',
                                    2 => 0,
                                ),
                        ),
                ),
        )
    );

    $UHANDLER->registerHandler(
        0,
        array(
            'pluginName' => 'eshop',
            'handlerName' => 'currency',
            'flagPrimary' => true,
            'flagFailContinue' => false,
            'flagDisabled' => false,
            'rstyle' =>
                array(
                    'rcmd' => '/eshop/currency/',
                    'regex' => '#^/eshop/currency/$#',
                    'regexMap' =>
                        array(
                            1 => 'page',
                        ),
                    'reqCheck' =>
                        array(),
                    'setVars' =>
                        array(),
                    'genrMAP' =>
                        array(
                            0 =>
                                array(
                                    0 => 0,
                                    1 => '/eshop/currency/',
                                    2 => 0,
                                ),
                        ),
                ),
        )
    );

    $UHANDLER->registerHandler(
        0,
        array(
            'pluginName' => 'eshop',
            'handlerName' => 'yml_export',
            'flagPrimary' => true,
            'flagFailContinue' => false,
            'flagDisabled' => false,
            'rstyle' =>
                array(
                    'rcmd' => '/eshop/yml_export/',
                    'regex' => '#^/eshop/yml_export/$#',
                    'regexMap' =>
                        array(
                            1 => 'page',
                        ),
                    'reqCheck' =>
                        array(),
                    'setVars' =>
                        array(),
                    'genrMAP' =>
                        array(
                            0 =>
                                array(
                                    0 => 0,
                                    1 => '/eshop/yml_export/',
                                    2 => 0,
                                ),
                        ),
                ),
        )
    );

    $UHANDLER->registerHandler(
        0,
        array(
            'pluginName' => 'eshop',
            'handlerName' => 'ebasket_list',
            'flagPrimary' => true,
            'flagFailContinue' => false,
            'flagDisabled' => false,
            'rstyle' =>
                array(
                    'rcmd' => '/eshop/ebasket_list/',
                    'regex' => '#^/eshop/ebasket_list/$#',
                    'regexMap' =>
                        array(
                            1 => 'page',
                        ),
                    'reqCheck' =>
                        array(),
                    'setVars' =>
                        array(),
                    'genrMAP' =>
                        array(
                            0 =>
                                array(
                                    0 => 0,
                                    1 => '/eshop/ebasket_list/',
                                    2 => 0,
                                ),
                        ),
                ),
        )
    );

    $UHANDLER->registerHandler(
        0,
        array(
            'pluginName' => 'eshop',
            'handlerName' => 'order',
            'flagPrimary' => true,
            'flagFailContinue' => false,
            'flagDisabled' => false,
            'rstyle' =>
                array(
                    'rcmd' => '/eshop/order/',
                    'regex' => '#^/eshop/order/$#',
                    'regexMap' =>
                        array(
                            1 => 'page',
                        ),
                    'reqCheck' =>
                        array(),
                    'setVars' =>
                        array(),
                    'genrMAP' =>
                        array(
                            0 =>
                                array(
                                    0 => 0,
                                    1 => '/eshop/order/',
                                    2 => 0,
                                ),
                        ),
                ),
        )
    );

    $UHANDLER->registerHandler(
        0,
        array(
            'pluginName' => 'eshop',
            'handlerName' => 'payment',
            'flagPrimary' => true,
            'flagFailContinue' => false,
            'flagDisabled' => false,
            'rstyle' =>
                array(
                    'rcmd' => '/eshop/payment/',
                    'regex' => '#^/eshop/payment/$#',
                    'regexMap' =>
                        array(
                            1 => 'page',
                        ),
                    'reqCheck' =>
                        array(),
                    'setVars' =>
                        array(),
                    'genrMAP' =>
                        array(
                            0 =>
                                array(
                                    0 => 0,
                                    1 => '/eshop/payment/',
                                    2 => 0,
                                ),
                        ),
                ),
        )
    );

    $UHANDLER->saveConfig();
}

function remove_urls()
{

    $ULIB = new urlLibrary();
    $ULIB->loadConfig();
    $ULIB->removeCommand('eshop', '');
    $ULIB->removeCommand('eshop', 'show');
    $ULIB->removeCommand('eshop', 'search');
    $ULIB->removeCommand('eshop', 'stocks');
    $ULIB->removeCommand('eshop', 'compare');
    $ULIB->removeCommand('eshop', 'currency');
    $ULIB->removeCommand('eshop', 'yml_export');
    $ULIB->removeCommand('eshop', 'ebasket_list');
    $ULIB->removeCommand('eshop', 'order');
    $ULIB->removeCommand('eshop', 'payment');
    $ULIB->saveConfig();

    $UHANDLER = new urlHandler();
    $UHANDLER->loadConfig();
    $UHANDLER->removePluginHandlers('eshop', '');
    $UHANDLER->removePluginHandlers('eshop', 'show');
    $UHANDLER->removePluginHandlers('eshop', 'search');
    $UHANDLER->removePluginHandlers('eshop', 'stocks');
    $UHANDLER->removePluginHandlers('eshop', 'compare');
    $UHANDLER->removePluginHandlers('eshop', 'currency');
    $UHANDLER->removePluginHandlers('eshop', 'yml_export');
    $UHANDLER->removePluginHandlers('eshop', 'ebasket_list');
    $UHANDLER->removePluginHandlers('eshop', 'order');
    $UHANDLER->removePluginHandlers('eshop', 'payment');
    $UHANDLER->saveConfig();
}

function check_php_str($ext_image)
{
    $ext_image = secure_html(trim($ext_image));
    $extensions = array_map('trim', explode(',', $ext_image));
    $ext_image = array_filter(
        $extensions,
        function ($value) {
            if (strstr($value, 'php') !== false) {
                return false;
            }

            return true;
        }
    );

    return implode(', ', $ext_image);

}

function import_yml($yml_url)
{
    global $mysql, $parse;

    include_once(__DIR__.'/import.class.php');

    $file = file_get_contents($yml_url);
    $xml = new SimpleXMLElement($file);
    unset($file);
    unset(
        $_SESSION['cats'],
        $_SESSION['cats_uf_ids'],
        $_SESSION['update'],
        $_SESSION['offers'],
        $_SESSION['IDS'],
        $_SESSION['j'],
        $_SESSION['page'],
        $_SESSION['work']
    );

    foreach ($xml->shop->offers->offer as $key => $offer) {
        $_SESSION['work'][] = (int)$offer->attributes()->id;
    }

    $ctg = new YMLCategory();
    $ctg->GetFromSite();
    $ctg->GetFromXML($xml->shop->categories->category);

    $ofs = new YMLOffer();

    foreach ($xml->shop->offers->offer as $key => $offer) {
        $oif = (int)$offer->attributes()->id;

        $name = iconv('utf-8', 'windows-1251', (string)$offer->name);

        if ($name == "") {
            $name = iconv('utf-8', 'windows-1251', trim((string)$offer->model." ".(string)$offer->barcode));
        }

        if ($name != "") {
            $url = strtolower($parse->translit($name, 1, 1));
            $url = str_replace("/", "-", $url);
        }

        if ($url) {
            $vendorCode = iconv('utf-8', 'windows-1251', (string)$offer->vendorCode);
        
            if ($vendorCode){        
                $prd_row = $mysql->record(
                    "SELECT * FROM ".prefix."_eshop_products WHERE code = ".db_squote($vendorCode)." LIMIT 1"
                );
            }
            else{
                $prd_row = $mysql->record(
                    "SELECT * FROM ".prefix."_eshop_products WHERE url = ".db_squote($url)." LIMIT 1"
                );
            }
            if (!is_array($prd_row)) {
                $oid = $ofs->Add($offer, $name, $url);
                echo 'Добавлен товар: '.$name.'<br>';
            } else {
                $oid = $ofs->Update($prd_row['id'], $offer, $name);
                echo 'Обновлен товар: '.$name.'<br>';
            }
            
        }
    }

    generate_catz_cache(true);
    generate_features_cache(true);

}

function update_currency()
{
    global $mysql, $SYSTEM_FLAGS;

    $rates_array = array();
    foreach ($SYSTEM_FLAGS['eshop']['currency'] as $currency) {
        if ($currency['code'] != "USD") {
            $code = $currency['code'];
            $q = $code."_USD";
            $q_url = "http://free.currencyconverterapi.com/api/v3/convert?q=".$q."&compact=ultra";
            $get_rate = (array)json_decode(file_get_contents($q_url));
            $mysql->query(
                'UPDATE '.prefix.'_eshop_currencies SET rate_from = '.db_squote(
                    $get_rate[$q]
                ).' WHERE code ='.db_squote($code).' '
            );

            $rates_array[$q] = $get_rate[$q];
        }
    }

    generate_currency_cache(true);

    $rates_str = "";
    foreach ($rates_array as $k => $v) {
        $rates_str .= $k.' = '.$v."<br/><br/>";
    }

    return $rates_str;

}

function update_prices($change_price_type, $change_price_qnt)
{
    global $mysql;

    if ($change_price_type == 1) {
        $newprice = ((100 + $change_price_qnt) / 100);
    } else {
        $newprice = ((100 - $change_price_qnt) / 100);
    }

    $mysql->query(
        'UPDATE '.prefix.'_eshop_variants SET price = price*'.$newprice.', compare_price = compare_price*'.$newprice.' '
    );
    generate_catz_cache(true);

}

// Generate page list for admin panel
// * current - number of current page
// * count   - total count of pages
// * url     - URL of page, %page% will be replaced by page number
// * maxNavigations - max number of navigation links
function generateLP($param)
{
    global $tpl, $twig;

    if ($param['count'] < 2) {
        return '';
    }

    templateLoadVariables(true, 1);
    $nav = LoadVariables_eshop();

    if ($param['current'] > 1) {
        $prev = $param['current'] - 1;
        $prev_link = str_replace(
            '%page%',
            "$1",
            str_replace('%link%', str_replace('%page%', $prev, $param['url']), $nav['prevlink'])
        );
    } else {
        $prev_link = "";
        $no_prev = true;
    }

    // ===[ TO PUT INTO CONFIG ]===
    $pages = '';
    if (isset($param['maxNavigations']) && ($param['maxNavigations'] > 3) && ($param['maxNavigations'] < 500)) {
        $maxNavigations = intval($param['maxNavigations']);
    } else {
        $maxNavigations = 10;
    }

    $sectionSize = floor($maxNavigations / 3);
    if ($param['count'] > $maxNavigations) {
        // We have more than 10 pages. Let's generate 3 parts
        // Situation #1: 1,2,3,4,[5],6 ... 128
        if ($param['current'] < ($sectionSize * 2)) {
            $pages .= generateNavi($param['current'], 1, $sectionSize * 2, $param['url'], $nav);
            $pages .= $nav['dots'];
            $pages .= generateNavi(
                $param['current'],
                $param['count'] - $sectionSize,
                $param['count'],
                $param['url'],
                $nav
            );
        } elseif ($param['current'] > ($param['count'] - $sectionSize * 2 + 1)) {
            $pages .= generateNavi($param['current'], 1, $sectionSize, $param['url'], $nav);
            $pages .= $nav['dots'];
            $pages .= generateNavi(
                $param['current'],
                $param['count'] - $sectionSize * 2 + 1,
                $param['count'],
                $param['url'],
                $nav
            );
        } else {
            $pages .= generateNavi($param['current'], 1, $sectionSize, $param['url'], $nav);
            $pages .= $nav['dots'];
            $pages .= generateNavi(
                $param['current'],
                $param['current'] - 1,
                $param['current'] + 1,
                $param['url'],
                $nav
            );
            $pages .= $nav['dots'];
            $pages .= generateNavi(
                $param['current'],
                $param['count'] - $sectionSize,
                $param['count'],
                $param['url'],
                $nav
            );
        }
    } else {
        // If we have less then 10 pages
        $pages .= generateNavi($param['current'], 1, $param['count'], $param['url'], $nav);
    }

    $tvars['vars']['pages'] = $pages;
    if ($prev + 2 <= $param['count']) {
        $next = $prev + 2;
        $next_link = str_replace(
            '%page%',
            "$1",
            str_replace('%link%', str_replace('%page%', $next, $param['url']), $nav['nextlink'])
        );
    } else {
        $next_link = "";
        $no_next = true;
    }

    $tpl_name = $param['tpl'];
    $tpath = locatePluginTemplates(array($tpl_name), 'eshop', pluginGetVariable('eshop', 'localsource'));
    $xt = $twig->loadTemplate($tpath[$tpl_name].$tpl_name.'.tpl');

    $tpl->template($tpl_name, $tpath[$tpl_name]);
    $tVars = array(
        'current' => $param['current'],
        'prev' => $param['current'] - 1,
        'prev_link' => $prev_link,
        'pages' => $pages,
        'next' => $prev + 2,
        'next_link' => $next_link,
        'no_prev' => $no_prev,
        'no_next' => $no_next,
    );

    return $xt->render($tVars);
}

function generateNavi($current, $start, $stop, $link, $navigations)
{
    $result = '';
    //print "call generateAdminNavigations(current=".$current.", start=".$start.", stop=".$stop.")<br>\n";
    //print "Navigations: <pre>"; var_dump($navigations); print "</pre>";
    for ($j = $start; $j <= $stop; $j++) {
        if ($j == $current) {
            $result .= str_replace('%page%', $j, $navigations['current_page']);
        } else {
            $row['page'] = $j;
            $result .= str_replace(
                '%page%',
                $j,
                str_replace('%link%', str_replace('%page%', $j, $link), $navigations['link_page'])
            );
        }
    }

    return $result;
}

function LoadVariables_eshop()
{
    $tpath = locatePluginTemplates(array(':'), 'eshop', pluginGetVariable('eshop', 'localsource'));

    return parse_ini_file($tpath[':'].'/main_variables.ini', true);
}

function getUploadsDir()
{
    global $config;
    $uploadsDir = dirname($config['images_dir']);

    return $uploadsDir;
}

function makeUploadsDirs($dir, $thumb = true)
{
    global $config;
    $uploadsDir = dirname($config['images_dir']);

    @mkdir($uploadsDir.$dir, 0777);
    if ($thumb) {
        @mkdir($uploadsDir.$dir.'/thumb', 0777);
    }
}

function moveFromTemp($qid, $path, $img, $iname)
{
    global $config;
    $uploadsDir = dirname($config['images_dir']);

    $temp_name = $uploadsDir.$path.'temp/'.$img;
    $current_name = $uploadsDir.$path.$qid.'/'.$iname;
    rename($temp_name, $current_name);

    $temp_name = $uploadsDir.$path.'temp/thumb/'.$img;
    $current_name = $uploadsDir.$path.$qid.'/thumb/'.$iname;
    rename($temp_name, $current_name);

}

function getPaymentDir($id)
{
    $eshop_dir = get_plugcfg_dir('eshop');

    return $eshop_dir.'/payment/'.$id.'/';
}

function getEntityRow($table, $id)
{
    global $mysql;

    $row = $mysql->record('SELECT * FROM '.$table.' WHERE id = '.(int)$id);

    return $row;
}

function getDeliveryTypes()
{
    global $mysql;

    $deliveryTypes = [];
    foreach ($mysql->select(
        'SELECT * FROM '.prefix.'_eshop_delivery_type WHERE active = 1 ORDER BY position, id'
    ) as $row) {
        $deliveryTypes[] = $row;
    }

    return $deliveryTypes;
}

function getPaymentTypes()
{
    global $mysql;

    $paymentTypes = [];
    foreach ($mysql->select(
        'SELECT * FROM '.prefix.'_eshop_payment_type WHERE active = 1 ORDER BY position, id'
    ) as $row) {
        $paymentTypes[] = $row;
    }

    return $paymentTypes;
}