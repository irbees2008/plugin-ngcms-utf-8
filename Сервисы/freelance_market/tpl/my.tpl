<div class="container">
	<h1>Мои заявки</h1>
	{% if jobs|length == 0 %}
		<p>У вас пока нет заявок.</p>
	{% else %}
		<table class="table table-striped">
			<thead>
				<tr>
					<th>ID</th>
					<th>Заголовок</th>
					<th>Статус</th>
					<th>Просмотры</th>
				</tr>
			</thead>
			<tbody>
				{% for j in jobs %}
					<tr>
						<td>{{ j.id }}</td>
						<td>
							<a href="/?plugin=freelance_market&handler=job&id={{ j.id }}">{{ j.title|e }}</a>
						</td>
						<td>{{ j.status }}</td>
						<td>{{ j.views }}</td>
					</tr>
				{% endfor %}
			</tbody>
		</table>
	{% endif %}
</div>
