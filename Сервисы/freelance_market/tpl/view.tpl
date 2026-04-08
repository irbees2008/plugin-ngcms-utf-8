<div class="container">
	<h1>{{ job.title|e }}</h1>
	<p>{{ job.description|raw }}</p>
	<p>
		<strong>Бюджет:</strong>
		{{ job.price }}
		₽</p>
	<p>
		<strong>Локация:</strong>
		{{ job.location|e }}</p>
	<p>
		<strong>Контакты:</strong>
		{{ contacts }}</p>
	{% if not canSeeContacts %}
		<p>
			<a class="btn btn-warning" href="/?plugin=freelance_market&handler=buy">Купить доступ, чтобы видеть контакты</a>
		</p>
	{% endif %}

	<hr>
	<h3>Отклики</h3>
	{% if bids and bids|length > 0 %}
		<div class="list-group">
			{% for b in bids %}
				<div class="list-group-item">
					<div>
						<strong>{{ b.user_name }}</strong>
						—
						{% if b.price_offer %}предложение:
							{{ b.price_offer }}
							₽{% else %}без цены
						{% endif %}
					</div>
					<div>{{ b.message|e }}</div>
				</div>
			{% endfor %}
		</div>
	{% else %}
		<p>Пока нет откликов.</p>
	{% endif %}

	{% if isLogged %}
		<h4>Откликнуться</h4>
		<form method="post" action="/?plugin=freelance_market&handler=bid">
			<input type="hidden" name="job_id" value="{{ job.id }}">
			<div class="form-group">
				<label>Сообщение</label>
				<textarea name="message" class="form-control" rows="4" required></textarea>
			</div>
			<div class="form-group">
				<label>Предложение, ₽ (необязательно)</label>
				<input type="number" step="0.01" name="price_offer" class="form-control">
			</div>
			<button class="btn btn-primary" type="submit">Отправить отклик</button>
		</form>
	{% else %}
		<p>Чтобы откликнуться, войдите в аккаунт.</p>
	{% endif %}
</div>
