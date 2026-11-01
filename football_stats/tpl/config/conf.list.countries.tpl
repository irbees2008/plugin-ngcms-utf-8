<table width="97%" class="content" border="0" cellspacing="0" cellpadding="0" align="center">
<tr align="center" width="100%" class="contHead">
<td width="40%">{l_fs:list.countries.name}</td>
<td width="40%">{l_fs:list.countries.alias}</td>
<td width="8%">{l_fs:action}</td>
</tr>
{entries}

{pagination}
<tr><td width="100%" colspan="3">&nbsp;</td></tr>
<tr>
<td width="100%" colspan="3" class="contentEdit" align="center">&nbsp;
<form method="post" action="admin.php?mod=football_stats&amp;action=add-country">
<input type="submit" value="{l_fs:button_add}" class="button" />
</form>
</td>
</tr>
</table>

<script type="text/javascript">
    function askDeleteCountry(id_country, name_country) {
        if (confirm("{l_fs:wont_delete} " + name_country + "?")) {
            location.href = '{admin_url}/admin.php?mod=football_stats&action=del-country&id=' + id_country;
        }
    }
</script>