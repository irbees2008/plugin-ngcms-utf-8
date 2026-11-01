<script>
function NumChar() {
	p_msg = document.form.content.value.toString();
	if ({{ max_char }} > 0 && p_msg.length > {{ max_char }}) {
		alert({{ max_char_alert|raw }});
		document.form.content.focus();
		return false;
	}
	return true;
}
</script>
{{ error|raw }}
<h4 class="mb-3">{{ edit_form_title }}</h4>
<form name="form" method="post" action="" accept-charset="UTF-8" onSubmit="return NumChar();">
    <div class="form-group">
        <label for="content">{{ content_label }}</label>
        <div>{{ smilies|raw }}<br>{{ bbcodes|raw }}</div>
        <textarea class="form-control" id="content" name="content" rows="6">{{ content }}</textarea>
    </div>
    <div class="form-group">
        <label for="quote-active">{{ active_label }}</label>
        <select class="form-control" id="quote-active" name="active">{{ active_options|raw }}</select>
    </div>
    <button class="btn btn-primary" type="submit" name="actions" value="1">{{ save_button }}</button>
</form>
