<br />
<center><b>{ch-name} {ch-season} {ch-country-name}</b></center>
<br /><br /><br />

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td class="contentNav" colspan="9">Матчи в этом чемпионате: <a href="?mod=football_stats&amp;action=add-league-game&amp;cid={id}" class="navbutton" style="float:right; margin-right:10px;">Добавить</a></td>
</tr>
<tr>
	<td class="contentEntry1" width="50%"><b>Матч</b></td>
	<td class="contentEntry1" width="15%"><b>Счет</b></td>
	<td class="contentEntry1" width="15%"><b>Дата</b></td>
	<td class="contentEntry1" width="15%"><b>Канал</b></td>
	<td class="contentEntry1" width="15%"><b>Фото</b></td>
	<td class="contentEntry1" width="15%"><b>Відео</b></td>
	<td class="contentEntry1" width="15%"><b>Статистика</b></td>
	<td class="contentEntry1" width="10%">&nbsp;</td>
	<td class="contentEntry1" width="10%">&nbsp;</td>
</tr>
{entries}
</table>

<script type="text/javascript">
    function askDeleteGameInLigue(id, cid, name) {
        if (confirm("{l_fs:wont_delete} " + name + "?")) {
            location.href = '{admin_url}/admin.php?mod=football_stats&action=del-game-league&id=' + id + '&cid=' + cid;
        }
    }
</script>