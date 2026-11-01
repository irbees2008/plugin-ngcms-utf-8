<style>
    .scorers_td_header {
        background-color: #d2d2d2;
        padding: 5px;
    }
    .scorers_td {
        border-bottom: 1px solid #808080;
        padding: 5px;
    }
</style>
<table style="width: 100%" cellpadding="0" cellspacing="0">
    <tr>
        <td style="text-align: center; padding-bottom: 10px;">
            <input type="button" onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=football_stats&amp;action=scorers&alias_champo=ukraine'" value="{l_fs:upl}" class="{ukraine}" />
            <input type="button" onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=football_stats&amp;action=scorers&alias_champo=championsleague'" value="{l_fs:championsleague}" class="{championsleague}" />
            <input type="button" onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=football_stats&amp;action=scorers&alias_champo=europaleague'" value="{l_fs:europaleague}" class="{europaleague}" />
            <input type="button" onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=football_stats&amp;action=scorers&alias_champo=russia'" value="{l_fs:russia}" class="{russia}" />
            <input type="button" onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=football_stats&amp;action=scorers&alias_champo=england'" value="{l_fs:england}" class="{england}" />
            <input type="button" onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=football_stats&amp;action=scorers&alias_champo=germany'" value="{l_fs:germany}" class="{germany}" />
            <input type="button" onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=football_stats&amp;action=scorers&alias_champo=spain'" value="{l_fs:spain}" class="{spain}" />
            <input type="button" onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=football_stats&amp;action=scorers&alias_champo=france'" value="{l_fs:france}" class="{france}" />
<input type="button" onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=football_stats&amp;action=scorers&alias_champo=italy'" value="{l_fs:italy}" class="{italy}" />
<input type="button" onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=football_stats&amp;action=scorers&alias_champo=worldcup'" value="Чемпіонат світу" class="{europaleague}" />
<input type="button" onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=football_stats&amp;action=scorers&alias_champo=euro'" value="Євро" class="{europaleague}" />
<input type="button" onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=football_stats&amp;action=scorers&alias_champo=uacub'" value="Кубок України" class="{europaleague}" />
</td>
    </tr>
</table>
<a href="{on_site_page}" target="_blank">{l_fs:on_site_page}</a><br/><br/>
<form method="post" action="admin.php?mod=football_stats&amp;action=scorers">
    <input type="hidden" name="alias" value="{alias}" />
    <select name="year" onchange="this.form.submit()">
        {years}
    </select>
</form>
<form method="post" action="admin.php?mod=football_stats&amp;action=scorers">
    <input type="hidden" name="alias" value="{alias}" />
    <input type="hidden" name="year" value="{start_year}" />
    <input type="hidden" name="reload" value="1" />
    <input type="submit" value="{l_fs:reload}" class="navbutton" />
</form>
<form method="post" action="admin.php?mod=football_stats&amp;action=scorers" onsubmit="return deleteScorers()">
    <input type="hidden" name="alias" value="{alias}" />
    <input type="hidden" name="year" value="{start_year}" />
    <input type="hidden" name="delete" value="1" />
    <input type="submit" value="{l_fs:delete}" class="navbutton" />
</form>
<form method="post" action="admin.php?mod=football_stats&amp;action=update-position-scorers" >
<table style="width: 50%" cellpadding="0" cellspacing="0">
    <tr>
        <td colspan="5" style="text-align: center;"><h2>{result_year}</h2></td>
    </tr>
    <tr>
        <td class="scorers_td_header">{l_fs:number_sc}</td>
        <td class="scorers_td_header">{l_fs:players}</td>
        <td class="scorers_td_header">{l_fs:championat}</td>
        <td class="scorers_td_header">{l_fs:golas}</td>
        <td class="scorers_td_header">{l_fs:position}</td>
    </tr>
    {scorers}
</table>
    <input type="hidden" name="count_scorers" value="{count_scorers}" />
    <input type="hidden" name="alias" value="{alias}" />
    <hr/>
    <input type="submit" value="{l_fs:save}" class="button" style="float: right;" />
</form>

<script type="text/javascript">
    function deleteScorers() {
        if (confirm("You really want to delete?")) {
            return true;
        }
        else {
            return false;
        }
    }
</script>