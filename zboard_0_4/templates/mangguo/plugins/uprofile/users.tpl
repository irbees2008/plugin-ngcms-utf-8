<div class="block-title">{{ lang.uprofile['profile_of'] }} {{ user.name }} {% if (user.flags.isOwnProfile) %}[ <a href="/profile.html">СЂРµРґР°РєС‚РёСЂРѕРІР°С‚СЊ</a> ]{% endif %}</div>
<div class="block-user-info">
	<div class="avatar">
		<img src="{{ user.avatar }}" alt=""/>
		{% if not (global.user.status == 0) %}
			{% if pluginIsActive('pm') %}<a href="/plugin/pm/?action=write&name={{ user.name }}">РЅР°РїРёСЃР°С‚СЊ Р›РЎ</a>{% endif %}
		{% endif %}
	</div>
	<div class="avatar">
		<img src="{{ user.photo_thumb }}" alt=""/>
		{% if (user.flags.hasPhoto) %}<a href="{{ user.photo }}" target="_blank">РЈРІРµР»РёС‡РёС‚СЊ С„РѕС‚Рѕ</a>{% endif %}
	</div>
	<div class="user-info">
		<table class="table" cellspacing="0" cellpadding="0">
			<tr>
				<td>РџРѕР»СЊР·РѕРІР°С‚РµР»СЊ:</td>
				<td class="second">{{ user.name }} [id: {{ user.id }}]</td>
			</tr>
			<tr>
				<td>{{ lang.uprofile['status'] }}:</td>
				<td class="second">{{ user.status }}</td>
			</tr>
			<tr>
				<td>{{ lang.uprofile['regdate'] }}:</td>
				<td class="second">{{ user.reg }}</td>
			</tr>
			<tr>
				<td>{{ lang.uprofile['last'] }}:</td>
				<td class="second">{{ user.last }}</td>
			</tr>
			<tr>
				<td>{{ lang.uprofile['from'] }}:</td>
				<td class="second">{{ user.from }}</td>
			</tr>
			<tr>
				<td>{{ lang.uprofile['about'] }}:</td>
				<td class="second">{{ user.info }}</td>
			</tr>
		</table>
	</div>
</div>
<div class="block-title-mini">РљРѕРЅС‚Р°РєС‚РЅС‹Рµ РґР°РЅРЅС‹Рµ</div>
<p>
	{{ lang.uprofile['icq'] }}: {{ user.icq }}<br>
	{{ lang.uprofile['site'] }}: {{ user.site }}
</p>
<div class="block-title-mini">РђРєС‚РёРІРЅРѕСЃС‚СЊ РїРѕР»СЊР·РѕРІР°С‚РµР»СЏ</div>
<p>
	{{ lang.uprofile['all_news'] }}: {{ user.news }}<br>
	{{ lang.uprofile['all_comments'] }}: {{ user.com }}
</p>