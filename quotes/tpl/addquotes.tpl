{{ error|raw }}
{% if max_char > 0 %}
<script>
function NumChar() {
	var content = document.forms.quoteForm.content.value;
	if (content.length > {{ max_char }}) {
		alert({{ max_char_alert|raw }});
		document.forms.quoteForm.content.focus();
		return false;
	}
	return true;
}
</script>
{% endif %}
<form name="quoteForm" method="post" action="" accept-charset="UTF-8"{% if max_char > 0 %} onsubmit="return NumChar();"{% endif %}>
	<div class="form-group">
		<div>{{ bbcodes|raw }}<br>{{ smilies|raw }}</div>
		<textarea class="form-control" name="content" id="content" rows="10">{{ content }}</textarea>
	</div>
	<button type="submit" name="addpost" value="1" class="btn btn-primary">Добавить цитату</button>
</form>
