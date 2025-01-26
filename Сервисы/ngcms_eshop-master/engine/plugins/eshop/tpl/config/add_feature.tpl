{{entries.error}}
<form method="post" action="">
<table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td width="50%" class="contentEntry1">Название<br /><small></small></td>
        <td width="50%" class="contentEntry2"><input type="text" size="80" name="name" value="{{entries.name}}" /></td>
    </tr>

    <tr>
        <td width="50%" class="contentEntry1">Категории<br /><small></small></td>
        <td width="50%" class="contentEntry2">
            <select multiple="" name="feature_categories[]">
                {{entries.catz}}
            </select>
        </td>
    </tr>

    <tr>
        <td width="50%" class="contentEntry1">Позиция<br /><small></small></td>
        <td width="50%" class="contentEntry2"><input type="text" size="80" name="position" value="{{entries.position}}" /></td>
    </tr>
    
</table>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td width="100%" colspan="2">&nbsp;</td></tr>
<tr>
<td width="100%" colspan="2" class="contentEdit" align="center">
<input type="submit" name="submit" value="Сохранить" class="button" />
</td>
</tr>
</table>
</form>
