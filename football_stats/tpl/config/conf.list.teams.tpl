<form method="post" action="admin.php?mod=football_stats&amp;action=add-team">
<input type="submit" value="{l_fs:button_add}" class="button" />
</form>
<table width="97%" class="content" border="0" cellspacing="0" cellpadding="0" align="center">
<tr align="center" width="100%" class="contHead">
<td width="50%">{l_fs:list.team.name}</td>
<td width="30%">{l_fs:list.countries.name}</td>
<td width="8%">{l_fs:action}</td>
</tr>
{entries}

{pagination}
<tr><td width="100%" colspan="3">&nbsp;</td></tr>
<tr>
<td width="100%" colspan="3" class="contentEdit" align="center">&nbsp;
</td>
</tr>
</table>

<script type="text/javascript">
    function askDeleteTeam(id_team, name_team) {
        name_team = name_team.replace(/\"/gi, "");
        if (confirm("{l_fs:wont_delete} " + name_team + "?")) {
            location.href = '{admin_url}/admin.php?mod=football_stats&action=del-team&id=' + id_team;
        }
    }
</script>