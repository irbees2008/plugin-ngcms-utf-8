<form action="{{ php_self }}?mod=extra-config&plugin=eshop&action=list_api" method="post">
    <table border="0" cellspacing="0" cellpadding="0" class="content" align="center">
        <tr class="contHead" align="left">
            <td width="55%">Токен</td>
            <td width="45%">Действие</td>
        </tr>
        {% for entry in entries %}
            <tr align="left">
                <td width="55%" class="contentEntry1"><a href="{{ entry.edit_link }}">{{ entry.token }}</a></td>
                <td width="45%" class="contentEntry1" style="text-align:center;vertical-align:middle"><a
                            href="{{ entry.del_link }}"/><img src="/engine/skins/default/images/delete.gif"></a></td>
            </tr>
        {% else %}
            <tr align="left">
                <td colspan="8" class="contentEntry1">По вашему запросу ничего не найдено.</td>
            </tr>
        {% endfor %}
        <tr>
            <td width="100%" colspan="8">&nbsp;</td>
        </tr>
        <tr align="center">
            <td colspan="8" class="contentEdit" align="right" valign="top">
                <div style="text-align: left;">

                    <input class="button" style="float:right; width: 105px;"
                           onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=eshop&action=add_api'"
                           value="Добавить токен"/>
                </div>
            </td>
        </tr>
    </table>
</form>
