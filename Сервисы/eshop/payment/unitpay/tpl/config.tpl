<form method="post" action="admin.php?mod=extra-config&plugin=eshop&action=edit_payment&id={{ entries.name }}">
    <fieldset class="admGroup">
        <legend class="title">Настройки UnitPay</legend>
        <table width="100%" border="0" class="content">
            <tr>
                <td class="contentEntry1" valign=top>ID вашего проекта в системе Unitpay<br/></td>
                <td class="contentEntry2" valign=top><input name="projectId" type="text" size="100"
                                                            value="{{ entries.options.projectId }}"/></td>
            </tr>
            <tr>
                <td class="contentEntry1" valign=top>Секретный ключ, доступен в настройках проекта<br/></td>
                <td class="contentEntry2" valign=top><input name="secretKey" type="text" size="100"
                                                            value="{{ entries.options.secretKey }}"/></td>
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
