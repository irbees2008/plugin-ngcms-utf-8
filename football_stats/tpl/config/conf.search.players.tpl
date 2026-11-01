<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="//ajax.aspnetcdn.com/ajax/jquery.ui/1.10.3/jquery-ui.min.js"></script>
<link rel="stylesheet" href="http://ajax.aspnetcdn.com/ajax/jquery.ui/1.10.3/themes/base/jquery-ui.css">
<script type="text/javascript">
var players = {array_players};
    jQuery(document).ready(function ($) {
        $('#player_name').autocomplete({
            source: players
        });
    });
</script>

<form method="post" action="{admin_url}/admin.php?mod=football_stats&amp;action=players">
    <select name="first_letter" onchange="this.form.submit()">
        <option value="">{l_fs:select_letter}</option>
        {letters}
    </select>
</form>
{l_fs:or}
<form method="post" action="{admin_url}/admin.php?mod=football_stats&amp;action=players">
    <select name="team_name" onchange="this.form.submit()">
        <option value="">{l_fs:select_team}</option>
        {teams}
    </select>
</form>
{l_fs:or}
<form method="post" action="{admin_url}/admin.php?mod=football_stats&amp;action=players">
    <input type="text" style="width:210px;" name="player_name" id="player_name" placeholder="{l_fs:Input_name_of_football_player}" />
    <input type="submit" value="{l_fs:find}" class="button" />
</form>
{result_search}