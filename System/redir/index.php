<!DOCTYPE>
<html lang="ru">
<head>
 <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <title>РџРѕРґРѕР¶РґРёС‚Рµ.</title>
        <meta http-equiv="refresh" content="6; url=<?=$_SERVER['QUERY_STRING']?>">

    <script type="text/javascript">
    //<![CDATA[
    // Fix Mozilla bug: 209020
    if ( navigator.product == 'Gecko' )
    {
        navstring = navigator.userAgent.toLowerCase();
        geckonum  = navstring.replace( /.*gecko\/(\d+)/, "$1" );

        setTimeout("moz_redirect()",5500);
    }

    function moz_redirect()
    {
        var url_bit     = "<?=$_SERVER['QUERY_STRING']?>";
        window.location = url_bit.replace( new RegExp( "&amp;", "g" ) , '&' );
    }
    //>
    </script>
    </head>
    <body>
        <div id="redirectwrap">
        <center>
        <noindex>
            <h4><b>РҐР°РєРё Рё РЎРєСЂРёРїС‚С‹</b></h4>
        <p>Р’СЃРµ С‡С‚Рѕ РґР°Р»СЊС€Рµ СЌС‚РѕР№ СЃС‚СЂР°РЅРёС†С‹ - РЅРµ РЅР°С€ СЃР°Р№С‚, РѕС‚РІРµС‚СЃС‚РІРµРЅРЅРѕСЃС‚Рё Р·Р° С„Р°Р№Р»С‹ РјС‹ РЅРµ РЅРµСЃРµРј</p>
            <p>Р’С‹ РїРµСЂРµС€Р»Рё РїРѕ РІРЅРµС€РЅРµР№ СЃСЃС‹Р»РєРµ, РІРѕР·РјРѕР¶РЅРѕ РІС‹ СЃРєР°С‡РёРІР°РµС‚Рµ С„Р°Р№Р». РџРѕРґРѕР¶РґРёС‚Рµ 5 СЃРµРєСѓРЅРґС‹ РёР»Рё : </p>
            <p class="redirectfoot">(<a href="<?=$_SERVER['QUERY_STRING']?>">РЅР°Р¶РјРёС‚Рµ СЃСЋРґР°, РµСЃР»Рё РЅРµ С…РѕС‚РёС‚Рµ Р¶РґР°С‚СЊ</a>)</p>
            </noindex>
            </center>
        </div>
    </body>
    </html>