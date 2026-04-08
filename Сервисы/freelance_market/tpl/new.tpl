<div class="container">
	<h1>Новая заявка</h1>
	<form method="post" action="/?plugin=freelance_market&handler=create">
		<div class="form-group">
			<label>Заголовок</label>
			<input type="text" name="title" class="form-control" required>
		</div>
		<div class="form-group">
			<label>Описание</label>
			<textarea name="description" class="form-control" rows="8" required></textarea>
		</div>
		<div class="form-group">
			<label>Бюджет, ₽</label>
			<input type="number" step="0.01" name="price" class="form-control">
		</div>
		<div class="form-group">
			<label>Локация</label>
			<input type="text" name="location" class="form-control">
		</div>
		<div class="form-group">
			<label>Контакты (телефон/почта)</label>
			<input type="text" name="contacts" class="form-control" required>
		</div>
		<button class="btn btn-primary" type="submit">Опубликовать</button>
	</form>
</div>
