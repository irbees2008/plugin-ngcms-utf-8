<script src="{{ tpl_url }}/js/registration.js"></script>
<div class="block-title">Р РµРіРёСЃС‚СЂР°С†РёСЏ РЅРѕРІРѕРіРѕ РїРѕР»СЊР·РѕРІР°С‚РµР»СЏ</div>
<form name="register" action="{{ form_action }}" method="post" class="comment-form" >
<input type="hidden" name="type" value="doregister" />
	{% for entry in entries %}
		<div class="label label-table">
			<label for="{{entry.id}}">{{ entry.title }}:</label>
			<span class="input2">{{ entry.input }}</span>
		</div><br/><br/>
	{% endfor %}
	{% if flags.hasCaptcha %}
	<div class="label label-table captcha pull-left">
		<label for="reg_capcha">Р’РІРµРґРёС‚Рµ РєРѕРґ Р±РµР·РѕРїР°СЃРЅРѕСЃС‚Рё:</label>
		<input id="reg_capcha" type="text" name="vcode" class="input">
		<img src="{{ admin_url }}/captcha.php" onclick="reload_captcha();" id="img_captcha" style="cursor: pointer;" alt="Security code"/>
	</div>
	{% endif %}
	<div class="clearfix"></div><br/>
	<div class="label">
		<input type="submit" value="Р—Р°СЂРµРіРµСЃС‚СЂРёСЂРѕРІР°С‚СЊСЃСЏ" class="button pull-right">
	</div>
</form>
<script type="text/javascript">
	function reload_captcha() {
		var captc = document.getElementById('img_captcha');
		if (captc != null) {
			captc.src = "{{ admin_url }}/captcha.php?rand="+Math.random();
		}
	}   
</script>