<script>
function reload(id){
	location = document.getElementById(id).value;
}
</script>
<br />
<center><b>{league-name} {ch-start} / {ch-finish}</b><br /><select onchange="reload(this.id)" id="others-{type}">{others-cl}</select></center>
<br /><br />

<table width="97%" class="content" border="0" cellspacing="0" cellpadding="0" align="center">
<tr align="center" width="100%" class="contHead">
<td width="50%">{l_fs:list.cl.name}</td>
<td width="8%">{l_fs:action}</td>
</tr>
{entries}
<tr><td width="100%" colspan="3">&nbsp;</td></tr>
<tr>
<td width="100%" colspan="3" class="contentEdit" align="center">&nbsp;
<form method="post" action="admin.php?mod=football_stats&amp;action=add-league&amp;lid={lid}">
<input type="submit" value="{l_fs:button_add}" class="button" />
</form>
</td>
</tr>
</table>

<script type="text/javascript">
    function askDeleteLeague(id_league, name_league) {
        if (confirm("{l_fs:wont_delete} " + name_league + "?")) {
            location.href = '{admin_url}/admin.php?mod=football_stats&action=del-league&id=' + id_league;
        }
    }
</script>