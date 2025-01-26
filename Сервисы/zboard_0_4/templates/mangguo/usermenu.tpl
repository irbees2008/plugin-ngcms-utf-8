{% if (global.flags.isLogged) %}
<div id="auth">
	{% if pluginIsActive('nsm') %}<a href="/plugin/nsm/" class="auth-add-news">Р”РѕР±Р°РІРёС‚СЊ РЅРѕРІРѕСЃС‚СЊ</a>{% endif %}
	<a href="#" class="auth-profile">РџСЂРѕС„РёР»СЊ</a>
	<div id="profile">
		<div class="profile-top-bg"></div>
		<div class="profile-block">
			<div class="title">РџСЂРѕС„РёР»СЊ</div>
			<ul>
				[if-have-perm]<li><a href="{{ admin_url }}" target="_blank"><b>РђРґРјРёРЅ-РїР°РЅРµР»СЊ</b></a></li>
				<li><a href="{{ addnews_link }}">Р”РѕР±Р°РІРёС‚СЊ РЅРѕРІРѕСЃС‚СЊ</a></li>[/if-have-perm]
				{% if pluginIsActive('uprofile') %}<li><a href="{{ profile_link }}">Р РµРґР°РєС‚РёСЂРѕРІР°С‚СЊ РїСЂРѕС„РёР»СЊ</a></li>{% endif %}
				{% if pluginIsActive('pm') %}<li><a href="{{ p.pm.link }}">Р›РёС‡РЅС‹Рµ СЃРѕРѕР±С‰РµРЅРёСЏ ({{ p.pm.new }})</a></li>{% endif %}
				<li><a href="{{ logout_link }}">Р—Р°РІРµСЂС€РёС‚СЊ СЃРµР°РЅСЃ</a></li>
			</ul>
		</div>
	</div>
</div>
{% else %}
<script language="javascript">
var set_login = 0;
var set_pass  = 0;
</script>
<!-- .modal -->
<div class="modal" id="auth-modal">
	<div class="modal-box">
		<div class="modal-clouse"></div>
		<div class="title">Р’С…РѕРґ</div>
		<div class="modal-content clearfix">
			<form name="login" method="post" action="{{ form_action }}" id="login">
				<input type="hidden" name="redirect" value="{{ redirect }}" />
				<div class="label">
					<label for="login">Р›РѕРіРёРЅ:</label>
					<input type="text" class="form-control" id="login" name="username" class="input">
				</div>
				<div class="label clearfix">
					<label for="password">РџР°СЂРѕР»СЊ:</label>
					<input type="password" id="password" name="password" class="input">
					<a href="{{ lost_link }}" class="pull-right">Р—Р°Р±С‹Р»Рё РїР°СЂРѕР»СЊ?</a>
				</div>
				<div class="label pull-left">
					<label><input type="checkbox"> Р·Р°РїРѕРјРЅРёС‚СЊ</label>
				</div>
				<div class="label pull-right">
					<input type="submit" value="Р’РѕР№С‚Рё" class="button">
				</div>
			</form>
		</div>
		{% if pluginIsActive('auth_loginza') %}
		<div class="modal-footer">
			Р’С…РѕРґ С‡РµСЂРµР· СЃРѕС†РёР°Р»СЊРЅС‹Рµ СЃРµС‚Рё: <br>
			<div class="social-in-modal">
				<script src="http://loginza.ru/js/widget.js" type="text/javascript"></script>
				<a href="https://loginza.ru/api/widget?token_url={home}/plugin/auth_loginza/" class="loginza"><img src="{{ tpl_url }}/img/social/fb.png" alt=""> Facebook</a>
				<a href="https://loginza.ru/api/widget?token_url={home}/plugin/auth_loginza/" class="loginza"><img src="{{ tpl_url }}/img/social/vk.png" alt=""> Р’РєРѕРЅС‚Р°РєС‚Рµ</a>
				<a href="https://loginza.ru/api/widget?token_url={home}/plugin/auth_loginza/" class="loginza"><img src="{{ tpl_url }}/img/social/tw.png" alt=""> Twitter</a>
			</div>
		</div>
		{% endif %}
	</div>
</div>
{% endif %}
