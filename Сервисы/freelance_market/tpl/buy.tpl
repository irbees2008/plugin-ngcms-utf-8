<div class="container">
	<h1>Покупка доступа</h1>
	<p>Доступ позволяет видеть контакты заказчиков. Выберите срок:</p>
	<div class="row">
		<div class="col-md-6">
			<div class="panel panel-default">
				<div class="panel-heading">10 дней</div>
				<div class="panel-body">
					<p>
						<strong>{{ prices[10] }}
							₽</strong>
					</p>
					<a class="btn btn-success" href="/?plugin=freelance_market&handler=buy&days=10">Купить</a>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="panel panel-default">
				<div class="panel-heading">30 дней</div>
				<div class="panel-body">
					<p>
						<strong>{{ prices[30] }}
							₽</strong>
					</p>
					<a class="btn btn-success" href="/?plugin=freelance_market&handler=buy&days=30">Купить</a>
				</div>
			</div>
		</div>
	</div>
	{% if pay_form %}
		<div class="well">{{ pay_form|raw }}</div>
	{% endif %}
</div>
