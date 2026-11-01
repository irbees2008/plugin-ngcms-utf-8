<form method="post" action="admin.php?mod=extra-config&amp;plugin=boxers&amp;action=editorganization-save">
<input type="hidden" name="id" value="{db_name}" />
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%" class="contentEntry1">{l_boxers:list.organization.name}</td>
<td width="50%" class="contentEntry2"><input type="text" size="80" name="name" value="{name}" /></td>
</tr>
<tr>
<td width="50%" class="contentEntry1">{l_boxers:list.organization.db_name}<br /><small>{l_boxers:list.organization.db_name.tip}<small></td>
<td width="50%" class="contentEntry2"><input type="text" size="80" name="db_name" value="{db_name}" /></td>
</tr>
</table><br />

<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td width="100%" colspan="2">&nbsp;</td></tr>
<tr>
<td width="100%" colspan="2" class="contentEdit" align="center">
<input type="submit" value="{l_boxers:button_edit}" class="button" />
</td>
</tr>
</table>
</form>