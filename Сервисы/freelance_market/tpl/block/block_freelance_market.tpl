<div class="panel panel-default">
	<div class="panel-heading">Последние заявки</div>
	<ul class="list-group">
		{% for e in entries %}
			<li class="list-group-item">
				<a href="/?plugin=freelance_market&handler=job&id={{ e.id }}">{{ e.title|e }}</a>
				{% if e.price %}
					<span class="pull-right">{{ e.price }}
						₽</span>
				{% endif %}
			</li>
		{% else %}
			<li class="list-group-item">Пока пусто</li>
		{% endfor %}
	</ul>
</div>
