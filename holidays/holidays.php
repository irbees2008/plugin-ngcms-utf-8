<?php

// Protect against hack attempts
if (!defined('NGCMS')) die('HAL');


add_act('index', 'holidays');

// Get content [ array - content and deferred elements ]
function holidays()
{
    global $template;


    // РљРѕРЅС„РёРіСѓСЂР°С†РёСЏ
    $klvmsg = "7";  // РЎРєРѕР»СЊРєРѕ РІС‹РІРѕРґРёС‚СЊ РґР°С‚?
    $klvdays = "30";  // РњР°РєСЃРёРјР°Р»СЊРЅРѕРµ СѓРґР°Р»С‘РЅРЅРѕРµ СЃРѕР±С‹С‚РёРµ, РґРЅРµР№
    $datafile = root . "plugins/holidays/dat_holidays/holidays.dat"; // Р�РјСЏ С„Р°Р№Р»Р° Р±Р°Р·С‹ РґР°РЅРЅС‹С…
    $months = array("", "СЏРЅРІР°СЂСЏ", "С„РµРІСЂР°Р»СЏ", "РјР°СЂС‚Р°", "Р°РїСЂРµР»СЏ", "РјР°СЏ", "РёСЋРЅСЏ", "РёСЋР»СЏ", "Р°РІРіСѓСЃС‚Р°", "СЃРµРЅС‚СЏР±СЂСЏ", "РѕРєС‚СЏР±СЂСЏ", "РЅРѕСЏР±СЂСЏ", "РґРµРєР°Р±СЂСЏ");
    $date = date("d " . $months[date('n')] . " Y"); // С‡РёСЃР»Рѕ.РјРµСЃСЏС†.РіРѕРґ
    $time = date("H:i:s"); // С‡Р°СЃС‹:РјРёРЅСѓС‚С‹:СЃРµРєСѓРЅРґС‹

    $holidays .= "";
    $day = $date = date("d"); // РґРµРЅСЊ
    $month = $date = date("n"); // РјРµСЃСЏС†
    $year = $date = date("Y"); // РіРѕРґ
    if ($month == 12) {
        $year++;
    } // Р§С‚РѕР±С‹ РІРµСЂРЅРѕ СЃС‡РёС‚Р°Р» СЏРЅРІР°СЂСЃРєРёРµ РїСЂР°Р·РґРЅРёРєРё
    $vchera = $day - 1;
    $klvchasov = $klvdays * 30;
    $lines = file($datafile);
    $itogo = count($lines);
    $i = 0;

    do {
        $dt = explode("|", $lines[$i]);

        $todaydate = date("d " . $months[date('n')] . " Y");
        $tekdt = mktime();

        $newdate = mktime(0, 0, 0, $dt[1], $dy[0], $year);
        $dayx = date("d " . $months[date('n')] . " Y", $newdate); // РєРѕРЅРІРµСЂРёСЂСѓРµРј РґРЅРё РґРѕ РїСЂР°Р·РґРЅРёРєР° РІ С‡РµР»РѕРІРµС‡РµСЃРєРёР№ С„РѕСЂРјР°С‚
        $hdate = ceil(($newdate - $tekdt) / 3600); // С‡РµСЂРµР· СЃРєРѕР»СЊРєРѕ Р§РђРЎРћР’ РЅР°СЃС‚СѓРїРёС‚ СЃРѕР±С‹С‚РёРµ
        $ddate = ceil($hdate / 24); // СЃС‡РёС‚Р°РµРј СЃРєРѕР»СЊРєРѕ РґРЅРµР№ РґРѕ СЃРѕР±С‹С‚РёСЏ

        // РїСЂРёРІРѕРґРёРј СЃР»РѕРІРѕ Р”Р•РќР¬/Р”РќРЇ/Р”РќР•Р™ Рє РЅСѓР¶РЅРѕРјСѓ С‚РёРїСѓ
        $dney = "РґРЅРµР№";
        if ($ddate == "1") {
            $dney = "РґРµРЅСЊ";
        }
        if ($ddate == "2" or $ddate == "3" or $ddate == "4") {
            $dney = "РґРЅСЏ";
        }

        if (($dt[0] == $vchera) and ($dt[1] == $month)) {
            $holidays .= "<IMG src='/engine/plugins/holidays/images/happy2.gif'> Р’С‡РµСЂР° Р±С‹Р» РїСЂР°Р·РґРЅРёРє:<IMG src='/engine/plugins/holidays/images/down.gif'> <strong>$dt[2]</strong>";
        }
        if (($dt[0] == $day) and ($dt[1] == $month)) {
            $holidays .= "<IMG src='/engine/plugins/holidays/images/happy.gif'> РЎРµРіРѕРґРЅСЏ РїСЂР°Р·РґРЅРёРє:<IMG src='/engine/plugins/holidays/images/down.gif'> <strong>$dt[2]</strong><br>";
        }
        if ($klvmsg > 1) {

            if (($hdate > 1) and ($hdate < $klvchasov)) {
                if (!isset($m1)) {
                    $holidays .= "<IMG src='/engine/plugins/holidays/images/info.gif'> Р’ Р±Р»РёР¶Р°Р№С‰РµРµ РІСЂРµРјСЏ РѕР¶РёРґР°СЋС‚СЃСЏ РїСЂР°Р·РґРЅРёРєРё:<DIV style='BORDER-BOTTOM: #515151 1px dashed'></DIV>";
                    $m1 = 1;
                }
                $klvmsg--;
                $holidays .= "<IMG src='/engine/plugins/holidays/images/data.gif'> <font color='#cc0017'><B>$dayx</B></font> <small>С‡РµСЂРµР· <B>$ddate</B> $dney</small><br><IMG src='/engine/plugins/holidays/images/down.gif'> $dt[2]<DIV style='BORDER-BOTTOM: #515151 1px dashed'></DIV>";
            }
        }

        $i++;
    } while ($i < $itogo);

    $holidays .= "";


    $output = $holidays;

    $template['vars']['plugin_holidays'] = $output;
}
