<table width="97%" class="content" border="0" cellspacing="0" cellpadding="0" align="center">
<tr align="center" width="100%" class="contHead">
<td width="80%">{l_boxers:list.boxers.full_name}</td>
<td width="8%">{l_boxers:action}</td>
</tr>
{entries}

{pagination}
<tr><td width="100%" colspan="2">&nbsp;</td></tr>
<tr>
<td width="100%" colspan="2" class="contentEdit" align="center">&nbsp;
<form method="post" action="admin.php?mod=extra-config&amp;plugin=boxers&amp;action=addboxer">
<input type="submit" value="{l_boxers:button_add}" class="button" />
</form>
</td>
</tr>
</table>