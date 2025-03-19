<style>
	.ui-progressbar {
		position: relative;
	}

	.progress-label {
		position: absolute;
		left: 50%;
		top: 0;
		font-weight: bold;
		text-shadow: 1px 1px 0 #fff;
	}

	/* Стиль для сообщений */
	.message {
		margin-top: 10px;
		padding: 10px;
		border-radius: 4px;
		display: none; /* Сообщение скрыто по умолчанию */
	}
	.message.success {
		background-color: #d4edda;
		color: #155724;
		border: 1px solid #c3e6cb;
	}
	.message.error {
		background-color: #f8d7da;
		color: #721c24;
		border: 1px solid #f5c6cb;
	}
</style>

<div class="row mt-2">
	<div class="col-sm">
		<form action="" method="post" name="parse_rss_news">
			<input type="hidden" name="actionName" value="generate_news">
			<div class="card">
				<div class="card-header">Новости из RSS</div>
				<div class="card-body">
					<div class="list">
						URL RSS-канала:
						<input type="text" class="form-control" name="rss_url" value="{{ rss_url }}" required>
					</div>
					<div class="list">
						Количество новостей:
						<input type="number" class="form-control" name="count" value="{{ rss_limit }}" min="1" max="1000">
					</div>
					<div class="list">
						<div class="progressbar">
							<div class="progress-label"></div>
						</div>
					</div>
					<!-- Добавляем блок для сообщений -->
					<div class="message"></div>
				</div>
				<div class="card-footer">
					<input type="submit" name="submit" value="Парсить RSS!" class="btn btn-outline-primary">
				</div>
			</div>
		</form>
	</div>
</div>

<!-- Подключение jQuery и jQuery UI -->
<script src="{{ home }}/lib/jq/jquery.min.js"></script>
<script src="{{ home }}/lib/jqueryui/core/jquery-ui.min.js"></script>
<link rel="stylesheet" href="{{ home }}/lib/jqueryui/core/jquery-ui.min.css">

<script>
	$(document).ready(function () {
let progressbar,
progressLabel,
button,
message;

$('form').on('submit', function (event) {
event.preventDefault();

// Определяем текущую форму
const form = $(this);
const actionName = form.find('input[name="actionName"]').val();
const rssUrl = form.find('input[name="rss_url"]').val();
const count = parseInt(form.find('input[name="count"]').val(), 10);

if (! rssUrl || rssUrl.trim() === '') {
alert('Введите корректный URL RSS-канала');
return;
}

if (isNaN(count) || count < 1 || count > 1000) {
alert('Введите корректное количество (от 1 до 1000)');
return;
}

// Находим блок сообщений только в текущей форме
message = form.find('.message');
message.hide().removeClass('success error').text('');

startAjaxProcess(form, actionName, rssUrl, count);
});

function startAjaxProcess(form, actionName, rssUrl, count) {
const chunkSize = 100; // Размер одного чанка
const chunkCount = Math.ceil(count / chunkSize);
let currentChunk = 1;

progressbar = form.find(".progressbar");
progressLabel = form.find(".progress-label");
button = form.find('input[type="submit"]');

button.hide();
progressbar.show().progressbar({
value: false,
change: function () {
progressLabel.text(`${
Math.round(progressbar.progressbar("value"))
}%`);
},
complete: function () {
progressLabel.text("Готово!");
}
});

processChunk(currentChunk, chunkCount, actionName, rssUrl, count, chunkSize);
}

function processChunk(currentChunk, chunkCount, actionName, rssUrl, count, chunkSize) {
$.ajax({
method: "POST",
cache: false,
url: '/plugin/content_parser/', // Убедитесь, что путь правильный
data: {
actionName,
rss_url: rssUrl,
real_count: Math.min(chunkSize, count - chunkSize * (currentChunk - 1))
},
success: function (response) {
console.log(`Чанк ${currentChunk} из ${chunkCount} завершен`);
progressbar.progressbar("value", (100 / chunkCount) * currentChunk);
},
error: function (xhr, status, error) {
console.error("Ошибка AJAX:", error);
showMessage('error', `Произошла ошибка: ${error}`);
},
complete: function () {
if (currentChunk < chunkCount) {
processChunk(currentChunk + 1, chunkCount, actionName, rssUrl, count, chunkSize);
} else {
finishProcess();
showMessage('success', 'Парсинг завершен!');
}
}
});
}

function finishProcess() {
button.show();
progressbar.hide();
}

// Функция для отображения сообщений
function showMessage(type, text) {
message.text(text).addClass(type).fadeIn();
setTimeout(() => {
message.fadeOut();
}, 5000); // Сообщение исчезает через 5 секунд
}
});
</script>
