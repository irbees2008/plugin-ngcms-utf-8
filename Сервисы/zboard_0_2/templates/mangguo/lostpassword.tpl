<div class="block-title">Восстановление пароля</div>
<form name="lostpassword" action="{form_action}" class="comment-form" method="post">
<input type="hidden" name="type" value="send" />
	{entries}
	[captcha]
		<div class="label label-table captcha pull-left">
			<label>Введите код безопасности:</label>
			<input type="text" class="form-control" name="vcode" class="input">
			<img src="{admin_url}/captcha.php" onclick="reload_captcha();" id="img_captcha" style="cursor: pointer;" alt="Security code"/>
		</div>
		<div class="clearfix"></div><br/>
		<div class="label">
			<input type="submit" value="Восстановить" class="button">
		</div>
	[/captcha]
</form>
<script type="text/javascript">
	function reload_captcha() {
		var captc = document.getElementById('img_captcha');
		if (captc != null) {
			captc.src = "{admin_url}/captcha.php?rand="+Math.random();
		}
	}
</script>
