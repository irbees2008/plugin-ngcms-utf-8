<table width="97%" class="content" border="0" cellspacing="0" cellpadding="0" align="center">
<tr align="center" width="100%" class="contHead">
<td width="40%">{l_boxers:list.organization.name}</td>
<td width="40%">{l_boxers:list.organization.db_name}</td>
<td width="8%">{l_boxers:action}</td>
</tr>
{entries}
<tr><td width="100%" colspan="3">&nbsp;</td></tr>
<tr>
<td width="100%" colspan="3" class="contentEdit" align="center">&nbsp;
<form method="post" action="admin.php?mod=extra-config&amp;plugin=boxers&amp;action=addorganization">
<input type="submit" value="{l_boxers:button_add}" class="button" />
</form>
</td>
</tr>
</table>