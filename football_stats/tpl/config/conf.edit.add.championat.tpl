<script src="/engine/plugins/football_stats/tpl/js/jquery.dd.js" type="text/javascript"></script>
<script src="/engine/includes/js/ajax.js" type="text/javascript"></script>
<link rel="stylesheet" href="/engine/plugins/football_stats/tpl/css/jquery.dd.css" type="text/css" />
<script>
	$(function() {
		$( "#country" ).msDropDown();	
	});
	
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

<form method="post" enctype="multipart/form-data" action="admin.php?mod=football_stats&amp;action=modify-championat-save">
<div id="loading-layer" style="display:none; width:180px; height:40px; background:#fff; text-align:center; border:1px solid #eeeeef;"><img src="/templates/default/images/loading.gif" alt="" /></div>
<input type="hidden" name="id" value="{id}" />
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%" class="contentEntry1">{l_fs:list.championats.name}</td>
<td width="50%" class="contentEntry2"><input type="text" size="80" name="name" value="{name}" /></td>
</tr>
<tr>
<td width="50%" class="contentEntry1">{l_fs:list.countries.name}<br /><small>{l_fs:list.countries.name.tip}</small></td>
<td width="50%" class="contentEntry2"><select style="width:410px;" name="country" id="country" value="" />{countries}</select></td>
</tr>
<tr>
<td width="50%" class="contentEntry1">Старт чемпионата</td>
<td width="50%" class="contentEntry2"><input type="text" size="80" name="start" value="{start}" /></td>
</tr>
<tr>
<td width="50%" class="contentEntry1">Окончание чемпионата</td>
<td width="50%" class="contentEntry2"><input type="text" size="80" name="finish" value="{finish}" /></td>
</tr>
<tr>
<td width="50%" class="contentEntry1">Вид турнирной таблицы</td>
<td width="50%" class="contentEntry2">
	<select name="view-tournament-table" style="width:89%;">{view_tournament_table}</select>
</td>
</tr>
</table>

[edit-championat]
<br />
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td class="contentNav">Команды, выступающие в этом сезоне<a href="?mod=football_stats&amp;action=edit-championat&amp;id={id}&amp;subaction=add-teams" class="navbutton" style="float:right; margin-right:10px;">Добавить</a></td>
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
<form method="post" action="admin.php?mod=football_stats&amp;action=add-team-championat&amp;id={id}">
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