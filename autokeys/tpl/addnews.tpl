<div class="form-row mb-3">
	<label class="col-lg-3 col-form-label">Автоматическая генерация ключевых слов</label>
	<div class="col-lg-9">
		<div class="form-check mb-3">
			<input type="checkbox" id="autokeys_generate" name="autokeys_generate" value="1" {% if (flags.checked) %} checked="checked" {% endif %} class="form-check-input"/>
			<label class="form-check-label" for="autokeys_generate">
				Генерировать keywords?
			</label>
		</div>

		<button type="button" id="autokeysButton" class="btn btn-outline-dark mb-2" onclick="autokeysAjaxUpdate(); return false;">
			Сгенерировать ключевые слова
		</button>
		<div id="autokeysStatus" role="status" aria-live="polite"></div>
	</div>
</div>

<script language="javascript">
	// Функция для очистки текста от HTML-тегов и лишних пробелов
var cleanText = function (text) {
if (! text || typeof text !== 'string') {
return ''; // Возвращаем пустую строку, если текст пустой или некорректный
}
return text.replace(/<[^>]*>/g, '').trim(); // Удаляем HTML-теги и лишние пробелы
};

var autokeysAjaxUpdate = function () {
	var $button = $("#autokeysButton");
	var $status = $("#autokeysStatus");
	var $keywords = $("textarea[name='keywords']");
	if (!$keywords.length) {
		$status.text("Поле ключевых слов не найдено.");
		return;
	}

	var title = cleanText($("#newsTitle").val() || "");
	var content = cleanText(
		($("#ng_news_content_short").val() || "") + " " +
		($("#ng_news_content_full").val() || "") + " " +
		($("#ng_news_content").val() || "")
	);
	if (!title && !content) {
		$status.text("Сначала заполните заголовок или текст новости.");
		return;
	}

	$button.prop("disabled", true).text("Генерация...");
	$status.text("Ключевые слова генерируются...");
	ngShowLoading();

	$.post("/engine/rpc.php", {
		json: 1,
		methodName: "plugin.autokeys.generate",
		rndval: new Date().getTime(),
		params: JSON.stringify({ title: title, content: content })
	}, null, "text")
		.done(function (data) {
			var response;
			try {
				response = JSON.parse(data);
			} catch (error) {
				$status.text("Ошибка обработки ответа сервера.");
				ngNotifySticker("Ошибка при обработке ответа генератора.", {
					className: "stickers-danger",
					sticked: "true",
					closeBTN: true
				});
				return;
			}

			if (!response || !response.status) {
				var errorText = response && response.errorText
					? response.errorText
					: "Не удалось сгенерировать ключевые слова.";
				$status.text(errorText);
				ngNotifySticker(errorText, {
					className: "stickers-danger",
					sticked: "true",
					closeBTN: true
				});
				return;
			}

			var keywords = typeof response.data === "string" ? response.data.trim() : "";
			if (!keywords || keywords.indexOf("undefined") !== -1) {
				$status.text("Генератор не вернул ключевые слова.");
				return;
			}

			$keywords.val(keywords).trigger("input").trigger("change");
			$status.text("Ключевые слова сразу подставлены в поле.");
		})
		.fail(function () {
			$status.text("Не удалось выполнить запрос генерации.");
			ngNotifySticker("HTTP ошибка при запросе генерации ключевых слов.", {
				className: "stickers-danger",
				sticked: "true",
				closeBTN: true
			});
		})
		.always(function () {
			ngHideLoading();
			$button.prop("disabled", false).text("Сгенерировать ключевые слова");
		});
};
</script>
