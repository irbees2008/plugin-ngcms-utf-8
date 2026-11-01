<script src="/engine/plugins/football_stats/tpl/js/jquery.dd.js" type="text/javascript"></script>
<script src="/engine/plugins/football_stats/tpl/js/jquery.wysiwyg.js" type="text/javascript"></script>
<script src="/engine/plugins/football_stats/tpl/js/wysiwyg.link.js" type="text/javascript"></script>
<link rel="stylesheet" href="/engine/plugins/football_stats/tpl/css/jquery.dd.css" type="text/css" />
<link rel="stylesheet" href="/engine/plugins/football_stats/tpl/css/jquery.wysiwyg.css" type="text/css" />

<script>
	$(function() {
		$( "#country" ).msDropDown();	
		
		$('#description').wysiwyg( { 
			controls: {      
				html : { visible : true },
				insertImage : { visible : false },
				h1 : { visible : false },
				h2 : { visible : false },
				h3 : { visible : false },
				insertTable : { visible : false }
			}
		});	
		
	});
</script>

<form method="post" enctype="multipart/form-data" action="admin.php?mod=football_stats&amp;action=modify-team-save">
<input type="hidden" name="id" value="{id}" />
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%" class="contentEntry1">{l_fs:list.team.name}</td>
<td width="50%" class="contentEntry2"><input type="text" size="80" name="name" value="{name}" /></td>
</tr>
<tr>
<td width="50%" class="contentEntry1">Тип команды<br /></td>
<td width="50%" class="contentEntry2">{type}</td>
</tr>
<tr>
<td width="50%" class="contentEntry1">{l_fs:list.countries.name}<br /><small>{l_fs:list.countries.name.tip}</small></td>
<td width="50%" class="contentEntry2"><select style="width:410px;" name="country" id="country" value="" />{countries}</select></td>
</tr>
<tr>
<td width="50%" class="contentEntry1">{l_fs:team.photo}</td>
<td width="50%" class="contentEntry2"><input type="text" style="width:400px;" name="photo" value="{photo}" /><input type="button" value="{l_fs:button_upload}" class="button" onclick="javascript:ShowOrHide('uploadphoto');" /></td>													</tr>
</tr>
<tr id="uploadphoto" style="display : none;">
<td width="50%" class="contentEntry1"><input type="hidden" name="MAX_FILE_SIZE" value="3000000" />{l_fs:team.photo.upload}</td>
<td width="50%" class="contentEntry2"><input type="file" name="photofile" /></td>
</tr>
<tr>
<td width="50%" class="contentEntry1">{l_fs:list.team.alias}<br /><small>{l_fs:list.team.alias.tip}</small></td>
<td width="50%" class="contentEntry2"><input type="text" size="80" name="alias" value="{alias}" /></td>
</tr>
<tr>
<td width="50%" class="contentEntry1">Позиция в рейтинге УЕФА:</td>
<td width="50%" class="contentEntry2"><input type="text" size="80" name="uefa_position" value="{uefa_position}" /></td>
</tr>
<tr>
<td width="50%" class="contentEntry1">Очки в рейтинге УЕФА:</td>
<td width="50%" class="contentEntry2"><input type="text" size="80" name="uefa_rating" value="{uefa_rating}" /></td>
</tr>
<td width="50%" class="contentEntry1">Позиция в рейтинге ФИФА:</td>
<td width="50%" class="contentEntry2"><input type="text" size="80" name="fifa_position" value="{fifa_position}" /></td>
</tr>
<tr>
<td width="50%" class="contentEntry1">Очки в рейтинге ФИФА:</td>
<td width="50%" class="contentEntry2"><input type="text" size="80" name="fifa_rating" value="{fifa_rating}" /></td>
</tr>
{championats}
</table>
{link_on_site}

<fieldset>
<legend><b>{l_fs:team_description}</b></legend>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="100%" class="contentEntry1" align="center"><textarea name="description" id="description" cols="130" rows="20">{description}</textarea></td>
</tr>
</table>
</fieldset>

<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td width="100%" colspan="2">&nbsp;</td></tr>
<tr>
<td width="100%" colspan="2" class="contentEdit" align="center">
<input type="submit" value="{button-action}" class="button" />
</td>
</tr>
</table>
</form>