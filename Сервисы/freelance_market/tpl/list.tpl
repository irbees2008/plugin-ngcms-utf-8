<div class="container">
	<h1>Заявки</h1>
	{% if jobs|length == 0 %}
		<p>Пока нет заявок.</p>
	{% else %}
		<div class="list-group">
			{% for j in jobs %}
				<a class="list-group-item" href="/?plugin=freelance_market&handler=job&id={{ j.id }}">
					<h4 class="list-group-item-heading">{{ j.title|e }}</h4>
					<p class="list-group-item-text">{{ j.description|striptags|slice(0,160) }}
						{% if j.description|length > 160 %}…
						{% endif %}
					</p>
					<small>Автор:
						{{ j.author_name }}
						·
						{{ j.price }}
						₽ ·
						{{ j.location }}</small>
				</a>
			{% endfor %}
		</div>
	{% endif %}
	<p>
		<a class="btn btn-primary" href="/?plugin=freelance_market&handler=new">Оставить заявку</a>
		<a class="btn btn-default" href="/?plugin=freelance_market&handler=buy">Купить доступ</a>
	</p>
</div>
