<form method="post" action="admin.php?mod=football_stats&amp;action=modify-country-save">
<input type="hidden" name="id" value="{id}" />
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%" class="contentEntry1">{l_fs:list.countries.name}<br /><small>{l_fs:list.countries.name.tip}</small></td>
<td width="50%" class="contentEntry2"><input type="text" size="80" name="name" value="{name}" /></td>
</tr>
<tr>
<td width="50%" class="contentEntry1">{l_fs:list.countries.alias}<br /><small>{l_fs:list.countries.alias.tip}</small></td>
<td width="50%" class="contentEntry2"><input type="text" size="80" name="alias" value="{alias}" /></td>
</tr>
</table>

<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td width="100%" colspan="2">&nbsp;</td></tr>
<tr>
<td width="100%" colspan="2" class="contentEdit" align="center">
<input type="submit" value="{button-action}" class="button" />
</td>
</tr>
</table>
</form>