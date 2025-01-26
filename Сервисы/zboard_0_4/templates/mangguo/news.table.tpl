{% for entry in data %}
{{ entry }}
{% else %}
<div class="alert alert-info">
	<strong>Р�РЅС„РѕСЂРјР°С†РёСЏ</strong>
	{{ lang['msgi_no_news'] }}
</div>
{% endfor %}
{{ pagination }}