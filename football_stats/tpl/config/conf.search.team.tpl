<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="//ajax.aspnetcdn.com/ajax/jquery.ui/1.10.3/jquery-ui.min.js"></script>
<link rel="stylesheet" href="http://ajax.aspnetcdn.com/ajax/jquery.ui/1.10.3/themes/base/jquery-ui.css">
<script type="text/javascript">
var teams = {array_teams};
    jQuery(document).ready(function ($) {
        $('#team_name').autocomplete({
            source: teams
        });
    });
</script>

<form method="post" action="{admin_url}/admin.php?mod=football_stats&amp;action=teams">
    <select name="first_letter_of_team" onchange="this.form.submit()">
        <option value="">{l_fs:select_letter}</option>
        {letters}
    </select>
</form>
{l_fs:or}
<form method="post" action="{admin_url}/admin.php?mod=football_stats&amp;action=teams">
    <select name="country_of_team" onchange="this.form.submit()">
        <option value="">{l_fs:select_country}</option>
        {countries}
    </select>
</form>
{l_fs:or}
<form method="post" action="{admin_url}/admin.php?mod=football_stats&amp;action=teams">
    <input type="text" style="width: 210px;" name="team_name" id="team_name" placeholder="{l_fs:Input_name_of_football_team}" />
    <input type="submit" value="{l_fs:find}" class='button' />
</form>
{result_search}