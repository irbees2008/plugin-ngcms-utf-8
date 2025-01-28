<form method="post" action="admin.php?mod=extra-config&plugin=eshop&action=edit_payment&id={{ entries.name }}">
    <fieldset class="admGroup">
        <legend class="title">Настройки Robokassa</legend>
        <table width="100%" border="0" class="content">
            <tr>
                <td class="contentEntry1" valign=top>Логин<br/></td>
                <td class="contentEntry2" valign=top><input name="mrh_login" type="text" size="100"
                                                            value="{{ entries.options.mrh_login }}"/></td>
            </tr>
            <tr>
                <td class="contentEntry1" valign=top>Пароль1<br/></td>
                <td class="contentEntry2" valign=top><input name="mrh_pass1" type="text" size="100"
                                                            value="{{ entries.options.mrh_pass1 }}"/></td>
            </tr>
            <tr>
                <td class="contentEntry1" valign=top>Пароль2<br/></td>
                <td class="contentEntry2" valign=top><input name="mrh_pass2" type="text" size="100"
                                                            value="{{ entries.options.mrh_pass2 }}"/></td>
            </tr>
            <tr>
                <td class="contentEntry1" valign=top>Тестовый режим?<br/></td>
                <td class="contentEntry2" valign=top><select name="test_mode">
                        <option value="0" {% if entries.options.test_mode == 0 %}selected{% endif %}>Нет</option>
                        <option value="1" {% if entries.options.test_mode == 1 %}selected{% endif %}>Да</option>
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