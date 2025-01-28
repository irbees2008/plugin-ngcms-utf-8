<script type="text/javascript" src="{{ scriptLibrary }}/ajax.js"></script>
<script type="text/javascript" src="{{ scriptLibrary }}/admin.js"></script>
<script type="text/javascript" src="{{ scriptLibrary }}/libsuggest.js"></script>
<script>
    function eshop_indication(status, text) {

        $('.message-error').remove();
        $('.message-succes').remove();
        var object = "";
        switch (status) {
            case 'success': {
                $('body').append('<div class="message-succes"></div>');
                object = $('.message-succes');
                break;
            }
            case 'error': {
                $('body').append('<div class="message-error"></div>');
                object = $('.message-error');
                break;
            }
            default: {
                $('body').append('<div class="message-error"></div>');
                object = $('.message-error');
                break;
            }
        }

        object.slideDown("fast");
        object.html(text);
        setTimeout(function () {
            object.remove();
        }, 3000);
    }
</script>
<style>
    .message-succes {
        display: none;
        padding: 30px;
        padding-bottom: 50px;
        border: 1px solid green;
        float: right;
        border-radius: 3px;
        -moz-border-radius: 3px;
        -webkit-border-radius: 3px;
        margin: 0;
        color: #fff;
        text-shadow: 1px 1px 1px #444;
        font-size: 16px;
        position: fixed;
        bottom: 0;
        right: 0;
        z-index: 105;
        background-color: #28BB1D;
        opacity: 0.9;
        filter: alpha(opacity=90);
    }

    .message-error {
        display: none;
        padding: 30px;
        padding-bottom: 50px;
        border: 1px solid #BA0A0A;
        float: right;
        border-radius: 3px;
        -moz-border-radius: 3px;
        -webkit-border-radius: 3px;
        margin: 0;
        color: #fff;
        text-shadow: 1px 1px 1px #444;
        bottom: 0;
        right: 0;
        position: fixed;
        z-index: 105;
        font-size: 16px;
        background-color: #BA0A0A;
        opacity: 0.9;
        filter: alpha(opacity=90);
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#EA3E3E', endColorstr='#FF1515', GradientType=0);
    }
</style>

<div style="text-align : left;">
    <table class="content" border="0" cellspacing="0" cellpadding="0" align="center">
        <tr>
            <td width="100%" colspan="2" class="contentHead"><img src="{{ skins_url }}/images/nav.gif" hspace="8"
                                                                  alt=""/><a href="{{ admin_url }}/admin.php?mod=extras"
                                                                             title="Управление плагинами">Управление
                    плагинами</a> &#8594; <a href="{{ admin_url }}/admin.php?mod=extra-config&plugin=eshop">Интернет
                    магазин</a> &#8594; {{ current_title }}</td>
        </tr>
    </table>

    <table border="0" cellspacing="0" cellpadding="0" width="100%">
        <tr align="center">
            <td width="100%" class="contentNav" align="center"
                style="background-repeat: no-repeat; background-position: left;">
                <input type="button" onmousedown="javascript:window.location.href='{{ plugin_url }}'" value="Продукция"
                       class="navbutton"/>
                <input type="button" onmousedown="javascript:window.location.href='{{ plugin_url }}&action=list_cat'"
                       value="Категории" class="navbutton"/>
                <input type="button"
                       onmousedown="javascript:window.location.href='{{ plugin_url }}&action=list_feature'"
                       value="Свойства" class="navbutton"/>
                <input type="button" onmousedown="javascript:window.location.href='{{ plugin_url }}&action=list_order'"
                       value="Заказы" class="navbutton"/>
                <input type="button" onmousedown="javascript:window.location.href='{{ plugin_url }}&action=options'"
                       value="Настройки" class="navbutton"/>
                <input type="button"
                       onmousedown="javascript:window.location.href='{{ plugin_url }}&action=list_currencies'"
                       value="Валюты" class="navbutton"/>
                <!--
                <input type="button" onmousedown="javascript:window.location.href='{{ plugin_url }}&action=list_delivery'" value="Доставка" class="navbutton" />
                <input type="button" onmousedown="javascript:window.location.href='{{ plugin_url }}&action=list_promocode'" value="Купоны" class="navbutton" />
                -->
                <input type="button"
                       onmousedown="javascript:window.location.href='{{ plugin_url }}&action=list_comment'"
                       value="Комментарии" class="navbutton"/>
                <input type="button" onmousedown="javascript:window.location.href='{{ plugin_url }}&action=automation'"
                       value="Автоматизация" class="navbutton"/>
                <input type="button"
                       onmousedown="javascript:window.location.href='{{ plugin_url }}&action=list_payment'"
                       value="Системы оплаты" class="navbutton"/>
                <input type="button" onmousedown="javascript:window.location.href='{{ plugin_url }}&action=list_payment_type'"
                       value="Способы оплаты" class="navbutton"/>
                <input type="button" onmousedown="javascript:window.location.href='{{ plugin_url }}&action=list_delivery_type'"
                       value="Способы доставки" class="navbutton"/>
                <input type="button" onmousedown="javascript:window.location.href='{{ plugin_url }}&action=urls'"
                       value="ЧПУ" class="navbutton"/>
                <input type="button" onmousedown="javascript:window.location.href='{{ plugin_url }}&action=list_api'"
                       value="API" class="navbutton"/>
            </td>
        </tr>
    </table>
    {{ entries }}
</div>
