<form method="post" action="admin.php?mod=extra-config&plugin=eshop&action=edit_payment&id={{ entries.name }}">
    <fieldset class="admGroup">
        <legend class="title">Настройки Privat24</legend>
        <table width="100%" border="0" class="content">
            <tr>
                <td class="contentEntry1" valign=top>Merchant ID<br/></td>
                <td class="contentEntry2" valign=top><input name="merchantid" type="text" size="100"
                                                            value="{{ entries.options.merchantid }}"/></td>
            </tr>
            <tr>
                <td class="contentEntry1" valign=top>Пароль<br/></td>
                <td class="contentEntry2" valign=top><input name="merchantpass" type="text" size="100"
                                                            value="{{ entries.options.merchantpass }}"/></td>
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
