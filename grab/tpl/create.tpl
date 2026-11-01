<script type="text/javascript">
	// Global variable: ID of current active input area
		{% if (flags.edit_split) %}var currentInputAreaID = 'ng_news_content_short';
		{% else %}var currentInputAreaID = 'ng_news_content';{% endif %}
</script>

<form name="form" method="post" action="" id="postForm">
<div class="card-body">

<div class="panel-body" style="font-family: Franklin Gothic Medium;text-transform: uppercase;color: #9f9f9f;">ТЕСТ</div>

<div class="panel-body">

	<table class="table table-striped table-bordered">
		<tr>
			<td>Заголовок:</td>
<td><input type="text" name="title" class="form-control" value="{{ grab_h1 }}"/></td>

		</tr>
		<tr>
			<td>Категория:</td>
			<td>{{ mastercat }}</td>
		</tr>
		<tr>
			<td>Описание:</td>
			<td>
			<textarea name="ng_news_content_short" id="ng_news_content_short" style="width:98%; height: 200px;" class="textarea">{{ grab_text }}</textarea></td>
		</tr>
	</table>		
</div>
</div>

<div class="card-footer text-center">

	<input type="submit" name="submit" value="сохранить" class="btn btn-success" />
	<input type="hidden" name="token" value="{{ token }}"/>
	<input type="hidden" name="mainpage" id="mainpage" value="1"/>
	<input type="hidden" name="approve" id="approve" value="1"/>
</div>
</form>