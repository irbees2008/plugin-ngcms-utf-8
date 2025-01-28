<form method="post" action="admin.php?mod=extra-config&plugin=eshop&action=edit_payment&id={{ entries.name }}">
    <fieldset class="admGroup">
        <legend class="title">Настройки LiqPay</legend>
        <table width="100%" border="0" class="content">
            <tr>
                <td class="contentEntry1" valign=top>Публичный ключ<br/></td>
                <td class="contentEntry2" valign=top><input name="public_key" type="text" title="Публичный ключ"
                                                            size="100" value="{{ entries.options.public_key }}"/></td>
            </tr>
            <tr>
                <td class="contentEntry1" valign=top>Приватный ключ<br/></td>
                <td class="contentEntry2" valign=top><input name="private_key" type="text" title="Приватный ключ"
                                                            size="100" value="{{ entries.options.private_key }}"/></td>
            </tr>
            <tr>
                <td class="contentEntry1" valign=top>Тестовый режим?<br/></td>
                <td class="contentEntry2" valign=top><select name="sandbox">
                        <option value="0" {% if entries.options.sandbox == 0 %}selected{% endif %}>Нет</option>
                        <option value="1" {% if entries.options.sandbox == 1 %}selected{% endif %}>Да</option>
                    </select></td>
            </tr>
        </table>
    </fieldset>

    <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td width="100%" colspan="2">&nbsp;</td>
        </tr>
        <tr>
            <td width="100%" colspan="2" class="contentEdit" align="center">
                <input name="submit" type="submit" value="Сохранить" class="button"/>
            </td>
        </tr>
    </table>
</form>