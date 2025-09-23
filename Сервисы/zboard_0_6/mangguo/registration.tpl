<script src="{{ tpl_url }}/js/registration.js"></script>
<div class="block-title">{{ lang.registration }}</div>
<form name="register" action="{{ form_action }}" method="post" class="comment-form">
	<input type="hidden" name="type" value="doregister"/>
	{% for entry in entries %}
		<div class="label label-table">
			<label for="{{entry.id}}">{{ entry.title }}:</label>
			<span class="input2">{{ entry.input }}</span>
		</div><br/><br/>
	{% endfor %}
	{% if flags.hasCaptcha %}
		<div class="label label-table captcha pull-left">
			<label for="reg_capcha">Введите код безопасности:</label>
			<input id="reg_capcha" type="text" name="vcode" class="input">
			<img src="{{ admin_url }}/captcha.php" onclick="reload_captcha();" id="img_captcha" style="cursor: pointer;" alt="Security code"/>
		</div>
	{% endif %}
	<div class="clearfix"></div><br/>
	<div class="label">
		<input type="submit" value="Зарегистрироваться" class="button pull-right">
	</div>
</form>
<script type="text/javascript">
	function validate() {
if (document.register.agree.checked == false) {
window.alert('{{ lang.theme['registration.check_rules'] }}');
return false;
}
return true;
}
var ADMIN_URL = "{{ admin_url }}";
function reload_captcha() {
let img = document.getElementById('img_captcha');
if (img) {
img.src = ADMIN_URL + '/captcha.php?id=registration&force=1&rand=' + Math.random();
}
}
</script>
