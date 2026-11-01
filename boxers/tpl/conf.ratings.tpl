<form method="post" enctype="multipart/form-data" action="admin.php?mod=extra-config&amp;plugin=boxers&amp;action=ratings-save">
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%" class="contentEntry1">{l_boxers:ratings.count}</td>
<td width="50%" class="contentEntry2"><input type="text" style="width:410px;" size="80" name="ratings_count" value="{ratings.count}" /></td>
</tr>
</table><br />

{ratings}

<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td width="100%" colspan="2">&nbsp;</td></tr>
<tr>
<td width="100%" colspan="2" class="contentEdit" align="center">
<input type="submit" value="{l_boxers:button_save}" class="button" />
</form>
<form method="post" enctype="multipart/form-data" action="admin.php?mod=extra-config&amp;plugin=boxers&amp;action=history-ratings-save">
<input type="submit" value="{l_boxers:button_save_ratings}" class="button" />
</td>
</tr>
</table>
</form>

<br /> <br />

<form method="post" enctype="multipart/form-data" action="admin.php?mod=extra-config&amp;plugin=boxers&amp;action=history-ratings-delete">
<fieldset>
<legend><b>{l_boxers:history}</b></legend>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%" class="contentEntry1">{history}
<input type="submit" value="{l_boxers:button_dell}" class="button" />
</td>
</tr>
</table>
</fieldset>
</form>