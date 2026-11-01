<script src="/engine/includes/js/ajax.js" type="text/javascript"></script>
<script>
function add_fine(id_team, id_championat, url){
		var ajaxAddFine = new sack();
		ajaxAddFine.onShow(""); 
		ajaxAddFine.onComplete = function (){
		};
		ajaxAddFine.setVar("actions", 'add-fine');
		ajaxAddFine.setVar("id_team", id_team);
		ajaxAddFine.setVar("id_championat", id_championat);
		ajaxAddFine.setVar("fine", document.getElementById('fine_' + id_team).value);
		ajaxAddFine.setVar("fine1", document.getElementById('fine1_' + id_team).value);
		ajaxAddFine.setVar("ajax", true);
		ajaxAddFine.requestFile = url;
		ajaxAddFine.method ="GET";
		ajaxAddFine.runAJAX();
	}
</script>

<form method="post" enctype="multipart/form-data" action="admin.php?mod=football_stats&amp;action=modify-league-save">
<div id="loading-layer" style="display:none; width:180px; height:40px; background:#fff; text-align:center; border:1px solid #eeeeef;"><img src="/templates/default/images/loading.gif" alt="" /></div>
<input type="hidden" name="id" value="{id}" />
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%" class="contentEntry1">Турнир</td>
<td width="50%" class="contentEntry2">{league-type}</td>
</tr>
<tr>
<td width="50%" class="contentEntry1">{l_fs:list.cl.name}<br /><small>{l_fs:cl.name.tip}</small></td>
<td width="50%" class="contentEntry2"><input type="text" size="80" name="name" value="{name}" /></td>
</tr>
<tr>
<td width="50%" class="contentEntry1">Тип этапа<br /><small>Групповой - строим таблицу<br />Обычный - набор отборочных или финальных игр</small></td>
<td width="50%" class="contentEntry2">{add-type}</td>
</tr>
<tr>
<td width="50%" class="contentEntry1">Старт</td>
<td width="50%" class="contentEntry2"><input type="text" size="80" name="start" value="{start}" /></td>
</tr>
<tr>
<td width="50%" class="contentEntry1">Окончание</td>
<td width="50%" class="contentEntry2"><input type="text" size="80" name="finish" value="{finish}" /></td>
</tr>
</table>

[edit-championat]
<br />
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td class="contentNav">Команды, принимающие участие <a href="?mod=football_stats&amp;action=edit-league&amp;id={id}&amp;subaction=add-teams" class="navbutton" style="float:right; margin-right:10px;">Добавить</a></td>
</tr>
{teams}
</table>
[/edit-championat]

<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td width="100%" colspan="2">&nbsp;</td></tr>
<tr>
<td width="100%" colspan="2" class="contentEdit" align="center">
<input type="submit" value="{button-action}" class="button" />
</td>
</tr>
</table>
</form>

[add-teams]
<form method="post" action="admin.php?mod=football_stats&amp;action=add-team-league&amp;id={id}">
<br />
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td class="contentNav">Выберите команды, которых вы хотите добавить</td>
</tr>
<tr>
	<td class="contentEntry1">{add-teams}</td>
</tr>
<tr><td width="100%">&nbsp;</td></tr>
<tr>
<td width="100%" class="contentEdit" align="center">
<input type="submit" value="Добавить" class="button" />
</td>
</tr>
</table>
</form>
[/add-teams]