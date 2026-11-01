<style type="text/css">
    .cell { 
        background-color: #f9f9f9;
        border-color: #e9e9e9;
        border-bottom-style: solid;
        border-bottom-width: 1px;
        border-left-style: solid;
        border-left-width: 1px;
        border-right-style: solid;
        border-right-width: 1px;
        border-top-style: solid;
        border-top-width: 1px;
        color: #000000;
        font-family: "tahoma", sans-serif;

        font-size: 11px;

        font-style: normal;
        font-variant: normal;
        line-height: normal;
        font-weight: 200;
        padding-bottom: 1px;
        padding-left: 3px;
        padding-right: 1px;
        padding-top: 1px;
        text-align: left;
    }
    
    .button_date{
        background-repeat: no-repeat;
        width: 25px;
        height: 25px;
        border: 0;
    }
</style>

<script type="text/javascript">

    $(document).ready(function () {
        var down = true;
        $('#interval').click(function () {
            $('#set_interval').animate({
                "opacity": "toggle"
            }, 1000);
            if (down == false) {
                $('#arrow_img').attr('src', '/engine/plugins/football_stats/tpl/images/icon-arrow-down.png');
                down = true;
            }
            else {
                $('#arrow_img').attr('src', '/engine/plugins/football_stats/tpl/images/icon-arrow-up.png');
                down = false;
            }
        });
    });

    function correctForm() {
        var date = document.getElementById('date');
        if (date.value == "") {
            alert("Виберіть дату!");
            date.focus();
            return false;
        }

        return true;
    }

    function correctInterval() {
        var interval_start = document.getElementById('interval_start');
        var interval_end = document.getElementById('interval_end');
        if (interval_start.value == "") {
            alert("Введіть часовий проміжок!");
            interval_start.focus();
            return false;
        }
        else if (interval_end.value == "") {
            alert("Введіть часовий проміжок!");
            interval_end.focus();
            return false;
        }
        else if($('#interval_start').val() >= $('#interval_end').val()){
            alert("Неправельно вказаний часовий проміжок!");
            return false;
        }
        return true;
    }
</script>

<div class="bt">
<div class="blt">
</div>
<div class="brt">
</div>
<div class="bct"> <center><b>Матчі дня</b> </center></div>
<div class="clr">
</div>
</div>
<div class="bc">
<div class="bcl">
<div class="bcr">
<div class="bcc">
<div class="bline">
{ads9}<br>

<form method="post" action="{action}" onsubmit="return correctForm();">
    <table style="width: 100%">
    <tr>
        <td style="width: 35%; text-align: right;">
            <input type="submit" name="prev_date" value="" style="background: url(/engine/plugins/football_stats/tpl/images/stock_mail-filters-apply-left.png); background-size: 100%;" class="button_date" />
        </td>
        <td style="width: 30%; text-align: center;">
            <input type="date" name="date" id="date" value="{date}" onchange="this.form.submit()" />
        </td>
        <td style="width: 35%; text-align: left;">
            <input type="submit" name="next_date" value="" style="background: url(/engine/plugins/football_stats/tpl/images/stock_mail-filters-apply.png); background-size: 100%;" class="button_date" />
        </td>
    </tr>
    </table>
</form>

<form method="post" action="{action}" onsubmit=" return correctInterval();">
    <table style="width: 100%">
        <tr>
            <td style="text-align: center; padding: 10px 0;">
                <span style="text-decoration: underline; cursor: pointer;" id="interval">Вказати часовий проміжок</span> <img src="/engine/plugins/football_stats/tpl/images/icon-arrow-down.png" id="arrow_img" width="10" height="8" />
            </td>
        </tr>
        <tr id="set_interval" style="display: {none};">
            <td style="text-align: center;">
                <input type="date" id="interval_start" name="interval_start" value="{interval_start}" /> - 
                <input type="date" id="interval_end" name="interval_end" value="{interval_end}" /><br/><br/>
                <input type="submit" value="Показати" />
            </td>
        </tr>
    </table>
</form>

{comments_left}<table style="width: 100%; margin-top: 10px;">
    <tr>
	<td class="cells" width="17%"><b>Дата</b></td>
    <td class="cells" width="3%"><b>Канал</b></td>
	<td class="cells" width="22%" style="text-align: right;"><b>Господарі</b></td>
	<td class="cells" width="6%" style="text-align: center;"><b>Рах</b></td>
    <td class="cells" width="22%" style="text-align: left;"><b>Гості</b></td>
	<td class="cells" width="5%">онлайн</td>
	<td class="cells" width="5%">фото</td>
	<td class="cells" width="5%">відео</td>
	<td class="cells" width="11%">статистика</td>
</tr>
    {matches}
</table>{comments_right}
<div style="text-align: center; margin-top: 20px;">{no_matches}</div>


</div> </div>
</div>
</div>
</div>
<div class="bb">
<div class="blb">
</div>
<div class="brb">
</div>
<div class="bcb">
</div>
<div class="clr">
</div>
</div>