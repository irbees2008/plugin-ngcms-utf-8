<?php

if (!defined('NGCMS')) {
    exit('HAL');
}

function payment_action($payment_name, $payment_options, $rData)
{
    global $config, $mysql, $SUPRESS_TEMPLATE_SHOW, $SUPRESS_MAINBLOCK_SHOW, $SYSTEM_FLAGS;

    $SUPRESS_TEMPLATE_SHOW = 1;
    $SUPRESS_MAINBLOCK_SHOW = 1;

    $current_time = time() + ($config['date_adjust'] * 60);
    $result = (int)$rData['result'];

    if (!empty($result)) {
        switch ($result) {
            case '1':
                // fail_url
                redirect_eshop(link_eshop());
                break;
            case '2':
                // result_url
                // РљРѕС€РµР»РµРє РїСЂРѕРґР°РІС†Р°, РЅР° РєРѕС‚РѕСЂС‹Р№ РїРѕРєСѓРїР°С‚РµР»СЊ СЃРѕРІРµСЂС€РёР» РїР»Р°С‚РµР¶. Р¤РѕСЂРјР°С‚ - Р±СѓРєРІР° Рё 12 С†РёС„СЂ.
                $merchant_purse = $rData['LMI_PAYEE_PURSE'];
                // РЎСѓРјРјР°, РєРѕС‚РѕСЂСѓСЋ Р·Р°РїР»Р°С‚РёР» РїРѕРєСѓРїР°С‚РµР»СЊ. Р”СЂРѕР±РЅР°СЏ С‡Р°СЃС‚СЊ РѕС‚РґРµР»СЏРµС‚СЃСЏ С‚РѕС‡РєРѕР№.
                $amount = $rData['OutSum'];
                // Р’РЅСѓС‚СЂРµРЅРЅРёР№ РЅРѕРјРµСЂ РїРѕРєСѓРїРєРё РїСЂРѕРґР°РІС†Р°
                // Р’ СЌС‚РѕРј РїРѕР»Рµ РїРµСЂРµРґР°РµС‚СЃСЏ id Р·Р°РєР°Р·Р° РІ РЅР°С€РµРј РјР°РіР°Р·РёРЅРµ.
                $order_id = (int)$rData['InvId'];
                // РљРѕРЅС‚СЂРѕР»СЊРЅР°СЏ РїРѕРґРїРёСЃСЊ
                $crc = strtoupper($rData['SignatureValue']);
                $mrh_pass2 = $payment_options['mrh_pass2'];

                // РџСЂРѕРІРµСЂСЏРµРј РєРѕРЅС‚СЂРѕР»СЊРЅСѓСЋ РїРѕРґРїРёСЃСЊ
                $my_crc = strtoupper(md5("$amount:$order_id:$mrh_pass2"));
                if ($my_crc !== $crc) {
                    die("bad sign\n");
                }

                $info = array(
                    'payment_name' => $payment_name,
                    'merchant_purse' => $merchant_purse,
                    'amount' => $amount,
                    'order_id' => $order_id,
                );

                $mysql->query(
                    'INSERT INTO '.prefix.'_eshop_purchases (dt, order_id, info)
                    VALUES
                    ('.db_squote($current_time).',
                        '.db_squote($order_id).',
                        '.db_squote(json_encode($info)).'
                    )
                '
                );

                $mysql->query(
                    'UPDATE '.prefix.'_eshop_orders SET
                    paid = 1
                    WHERE id = '.$order_id.'
                '
                );

                die("OK".$order_id."\n");

                break;
            case '3':
                // success_url
                redirect_eshop(link_eshop());
                break;
            default:
                break;
        }
    } else {

        $filter = array();
        $SQL = array();

        $order_id = filter_var($rData['order_id'], FILTER_SANITIZE_STRING);
        $uniqid = filter_var($rData['order_uniqid'], FILTER_SANITIZE_STRING);
        if (empty($order_id) || empty($uniqid)) {
            redirect_eshop(link_eshop());
        } else {
            $filter [] = '(id = '.db_squote($order_id).')';
            $filter [] = '(uniqid = '.db_squote($uniqid).')';
            $sqlQ = "SELECT * FROM ".prefix."_eshop_orders ".(count($filter) ? "WHERE ".implode(
                        " AND ",
                        $filter
                    ) : '')." LIMIT 1";
            $row = $mysql->record($sqlQ);

            if ($row['paid'] == 1) {
                redirect_eshop(link_eshop());
            } elseif (!empty($row)) {

                $mrh_login = $payment_options['mrh_login'];
                $mrh_pass1 = $payment_options['mrh_pass1'];
                $test_mode = $payment_options['test_mode'];

                $inv_id = $order_id;
                $inv_desc = 'РћРїР»Р°С‚Р° РїРѕ Р·Р°РєР°Р·Сѓ ID: '.$order_id;
                $out_summ = $row['total_price'];
                $OutSumCurrency = $SYSTEM_FLAGS['eshop']['currency'][0]['code'];
                $shp_item = 1;
                $in_curr = "";
                $culture = "ru";
                $IsTest = $test_mode;

                $crc = md5("$mrh_login:$out_summ:$inv_id:$OutSumCurrency:$mrh_pass1:Shp_item=$shp_item");

                // build URL
                $url = "https://auth.robokassa.ru/Merchant/Index.aspx?MrchLogin=$mrh_login&".
                    "OutSum=$out_summ&InvId=$inv_id&Desc=$inv_desc&OutSumCurrency=$OutSumCurrency&SignatureValue=$crc&IsTest=$IsTest";

                header('Location: '.$url.'');

                exit;
            } else {
                redirect_eshop(link_eshop());
            }

        }
    }
}
