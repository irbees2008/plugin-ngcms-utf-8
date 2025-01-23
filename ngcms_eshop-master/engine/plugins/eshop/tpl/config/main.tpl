<script type="text/javascript" src="{{home}}/engine/includes/js/ajax.js"></script>
<script type="text/javascript" src="{{home}}/engine/includes/js/admin.js"></script>
<script type="text/javascript" src="{{home}}/engine/includes/js/libsuggest.js"></script>
<div style="text-align : left;">
<table class="content" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td width="100%" colspan="2" class="contentHead"><img src="{{skins_url}}/images/nav.gif" hspace="8" alt="" /><a href="{{admin_url}}/admin.php?mod=extras" title="Управление плагинами">Управление плагинами</a> &#8594; <a href="{{admin_url}}/admin.php?mod=extra-config&plugin=eshop">Интернет магазин</a> &#8594; {{current_title}}</td>
</tr>
</table>

<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr align="center">
<td width="100%" class="contentNav" align="center" style="background-repeat: no-repeat; background-position: left;">
<input type="button" onmousedown="javascript:window.location.href='{{plugin_url}}'" value="Продукция" class="navbutton" />
<input type="button" onmousedown="javascript:window.location.href='{{plugin_url}}&action=list_cat'" value="Категории" class="navbutton" />
<input type="button" onmousedown="javascript:window.location.href='{{plugin_url}}&action=list_feature'" value="Свойства" class="navbutton" />
<input type="button" onmousedown="javascript:window.location.href='{{plugin_url}}&action=list_order'" value="Заказы" class="navbutton" />
<input type="button" onmousedown="javascript:window.location.href='{{plugin_url}}&action=options'" value="Настройки" class="navbutton" />
<input type="button" onmousedown="javascript:window.location.href='{{plugin_url}}&action=list_currencies'" value="Валюты" class="navbutton" />
<!--
<input type="button" onmousedown="javascript:window.location.href='{{plugin_url}}&action=list_delivery'" value="Доставка" class="navbutton" />
<input type="button" onmousedown="javascript:window.location.href='{{plugin_url}}&action=list_payment'" value="Оплата" class="navbutton" />
<input type="button" onmousedown="javascript:window.location.href='{{plugin_url}}&action=list_promocode'" value="Купоны" class="navbutton" />
-->
<input type="button" onmousedown="javascript:window.location.href='{{plugin_url}}&action=list_comment'" value="Комментарии" class="navbutton" />

<input type="button" onmousedown="javascript:window.location.href='{{plugin_url}}&action=automation'" value="Автоматизация" class="navbutton" />

<input type="button" onmousedown="javascript:window.location.href='{{plugin_url}}&action=urls'" value="ЧПУ" class="navbutton" />
</td>
</tr>
</table>
{{entries}}
</div>
