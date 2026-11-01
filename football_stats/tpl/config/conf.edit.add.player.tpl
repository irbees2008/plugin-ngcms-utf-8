<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script src="//ajax.aspnetcdn.com/ajax/jquery.ui/1.10.3/jquery-ui.min.js"></script>
<link rel="stylesheet" href="http://ajax.aspnetcdn.com/ajax/jquery.ui/1.10.3/themes/base/jquery-ui.css">

<script type="text/javascript">
var players = {array_players};

    $(document).ready(function () {
        $('#player_name').autocomplete({
            source: players
        });


{teams_ajax}
        $('#id_champo').change(function () {
            $('#id_champo option:selected').each(function () {
                get_teams_ajax($(this).val(), '#id_team');
            });
        });
    });


function get_teams_ajax(id_champo, selector) {
            jQuery.ajax({
                type: "POST",
                url: "/engine/plugins/football_stats/tpl/config/get_teams.php",
                dataType: "text",
                data: { id_champo: encodeURIComponent(id_champo) },
                success: function (response) {
                    $(selector).html(response);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(thrownError);
                }
            });
        }
</script>

<script src="/engine/plugins/football_stats/tpl/js/jquery.wysiwyg.js" type="text/javascript"></script>
<script src="/engine/plugins/football_stats/tpl/js/wysiwyg.link.js" type="text/javascript"></script>
<link rel="stylesheet" href="/engine/plugins/football_stats/tpl/css/jquery.wysiwyg.css" type="text/css" />

<script type="text/javascript">

    $(document).ready(function () {
		$('#biography').wysiwyg( { 
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

<form method="post" enctype="multipart/form-data" action="admin.php?mod=football_stats&amp;action=modify-player-save">
<input type="hidden" name="id" value="{id}" />
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%" class="contentEntry1">{l_fs:list.players.name}</td>
<td width="50%" class="contentEntry2"><input type="text" size="80" id="player_name" name="name" value="{name}" /></td>
</tr>
<tr>
<td width="50%" class="contentEntry1">Чемпіонат</td>
<td width="50%" class="contentEntry2"><select name="id_champo" id="id_champo" style="width: 200px;"><option value='0'>-</option>{championats}</select></td>
</tr>
<tr>
<td width="50%" class="contentEntry1">Команда</td>
<td width="50%" class="contentEntry2"><select name="id_team" id="id_team" style="width: 200px; margin-right: 10px;">{team-options}</select> <input type="text" size="35" name="name_team" /></td>
</tr>
<tr>
<td width="50%" class="contentEntry1">Национальная команда</td>
<td width="50%" class="contentEntry2"><select name="id_team_country" style="width: 200px;">{team-country-options}</select></td>
</tr>
<tr>
<td width="50%" class="contentEntry1">Статус</td>
<td width="50%" class="contentEntry2">
    <select name="status" style="width: 200px;">{statuses}</select>
</td>
</tr>
</table>
<br/>
{link_player_on_site}
<br/><br/>
<fieldset>
<legend><b>{l_fs:player_bio}</b></legend>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="100%" class="contentEntry1" align="center"><textarea name="bio" id="biography" cols="130" rows="20">{bio}</textarea></td>
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