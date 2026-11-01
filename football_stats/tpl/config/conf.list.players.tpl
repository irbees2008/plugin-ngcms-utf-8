<form method="post" action="admin.php?mod=football_stats&amp;action=add-player">
<input type="submit" value="{l_fs:button_add}" class="button" />
</form>
<input type="button" value="{l_fs:show_duplicate}" class="button" onmousedown="location.href='{admin_url}/admin.php?mod=football_stats&action=show-duplicate'"/>
<table width="97%" class="content" border="0" cellspacing="0" cellpadding="0" align="center">
<tr align="center" width="100%" class="contHead">
<td width="40%">{l_fs:list.players.name}</td>
<td width="40%">{l_fs:list.team.name}</td>
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
    function askDeletePlayer(id_player, name_player) {
        if (confirm("{l_fs:wont_delete} " + name_player + "?")) {
            location.href = '{admin_url}/admin.php?mod=football_stats&action=del-player&id=' + id_player;
        }
    }
</script>