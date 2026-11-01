<form method="post" action="admin.php?mod=football_stats&amp;action=modify-type-league-save">
<input type="hidden" name="id" value="{id}" />
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%" class="contentEntry1">Имя</td>
<td width="50%" class="contentEntry2"><input type="text" size="50" name="name" value="{name}" /></td>
</tr>
<tr>
<td width="50%" class="contentEntry1">Краткий синоним<br /><small>Например: для Лиги Чемпионов - cl<small></td>
<td width="50%" class="contentEntry2"><input type="text" size="50" name="alias" value="{alias}" /></td>
</tr>
<tr>
<td width="50%" class="contentEntry1">Тип турнира</td>
<td width="50%" class="contentEntry2">{type}</td>
</tr>
</table><br />

<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td width="100%" colspan="2">&nbsp;</td></tr>
<tr>
<td width="100%" colspan="2" class="contentEdit" align="center">
<input type="submit" value="{button-action}" class="button" />
</td>
</tr>
</table>
</form>