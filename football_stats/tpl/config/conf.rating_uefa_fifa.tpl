<style>
    .rating_td_header {
        background-color: #d2d2d2;
        padding: 5px;
    }
    .rating_td {
        border-bottom: 1px solid #808080;
        padding: 5px;
    }
    #fifa_uefa tr:hover{
        background-color: #e4e4e4;
    }
</style>
<table style="width: 100%" cellpadding="0" cellspacing="0">
    <tr>
        <td style="text-align: center; padding-bottom: 10px;">
            <input type="button" onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=football_stats&amp;action=uefa_fifa&rating=rating_UEFA_clubs'" value="{l_fs:rating_UEFA_clubs}" class="{rating_UEFA_clubs}" />
            <input type="button" onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=football_stats&amp;action=uefa_fifa&rating=rating_UEFA_countries'" value="{l_fs:rating_UEFA_countries}" class="{rating_UEFA_countries}" />
            <input type="button" onmousedown="javascript:window.location.href='{admin_url}/admin.php?mod=football_stats&amp;action=uefa_fifa&rating=rating_FIFA_countries'" value="{l_fs:rating_FIFA_countries}" class="{rating_FIFA_countries}" />
        </td>
    </tr>
</table>
<a href="{on_site_page}" target="_blank">{l_fs:on_site_page}</a><br/><br/>
<form method="post" action="admin.php?mod=football_stats&amp;action=uefa_fifa&rating={rating_f}">
    <table style="width: 50%" cellpadding="0" cellspacing="0" id="fifa_uefa">
        <tr>
            <td class="rating_td_header">{l_fs:teams}</td>
            <td class="rating_td_header">{l_fs:points}</td>
            <td class="rating_td_header">{l_fs:position}</td>
        </tr>
        {rating}
    </table>
    <hr/>
    <input type="hidden" name="count_f" value="{count_f}" />
    <input type="submit" value="{l_fs:save}" class="button" style="float: right;" />
</form>