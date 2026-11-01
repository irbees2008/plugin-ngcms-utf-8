<table width="97%" class="content" border="0" cellspacing="0" cellpadding="0" align="center">
<tr align="center" width="100%" class="contHead">
<td width="50%">{l_fs:list.championats.name}</td>
<td width="30%">{l_fs:list.countries.name}</td>
<td width="8%">{l_fs:action}</td>
</tr>
{entries}

{pagination}
<tr><td width="100%" colspan="3">&nbsp;</td></tr>
<tr>
<td width="100%" colspan="3" class="contentEdit" align="center">&nbsp;
<form method="post" action="admin.php?mod=football_stats&amp;action=add-championat">
<input type="submit" value="{l_fs:button_add}" class="button" />
</form>
</td>
</tr>
</table>

<script type="text/javascript">
    function askDeleteChampo(id_champo, name_champo) {
        if (confirm("{l_fs:wont_delete} " + name_champo + "?")) {
            location.href = '{admin_url}/admin.php?mod=football_stats&action=del-championat&id=' + id_champo;
        }
    }
</script>