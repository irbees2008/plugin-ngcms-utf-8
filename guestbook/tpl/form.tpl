<!-- PLUGIN: Guestbook -->
[textarea]
<form name="form" method="post" action="">
{bbcodes}<br />{smilies}<br />
{author}
Сообщение: <br/><textarea name="content" style="width: 95%;" rows="8"></textarea><br/><br/>
[captcha]
Проверочный код:<br/>
<input type="text" class="form-control" name="vcode" maxlength="5" size="30" /> <img src="{admin_url}/captcha.php" />
[/captcha]
<br/>
<input type="submit" value="Отправить"/>
<input type="hidden" name="ip" value="{ip}"/>
</form>
[/textarea]
<!-- END plugin: Guestbook -->
