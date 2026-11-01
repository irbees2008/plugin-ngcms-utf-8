<form method="post" action="">
<div class="card-body">

	<div class="panel-body" style="font-family: Franklin Gothic Medium;text-transform: uppercase;color: #9f9f9f;">Настройки плагина</div>
	<div class="table-responsive">
	<table class="table table-striped">
      <tr>
        <td class="col-xs-6 col-sm-6 col-md-7">
		  <h6 class="media-heading text-semibold">Введите урл для прасинга?</h6>
		  <span class="text-muted text-size-small hidden-xs">указывается страница сайта</span>
		</td>
        <td class="col-xs-6 col-sm-6 col-md-5">
			<input type="text" name="url" value="{{ url }}" class="form-control">
        </td>
      </tr> 
      <tr>
        <td class="col-xs-6 col-sm-6 col-md-7">
		  <h6 class="media-heading text-semibold">Обработка заголовка</h6>
		  <span class="text-muted text-size-small hidden-xs">нужно задать класс заголовка, откуда будут браться данные</span>
		</td>
        <td class="col-xs-6 col-sm-6 col-md-5">
			<div class="input-group">
				<input type="text" name="header" value="{{ header }}" class="form-control">
				<div class="input-group-append">
				<a class="btn btn-outline-primary" data-toggle="popover" data-placement="top" data-trigger="focus" data-html="true" title="" data-content="Обычно на странице заголовок указывается тегом h1, в большинстве случаяв указывается он" tabindex="0" data-original-title="ЗАГОЛОВОК">
					<i class="fa fa-question"></i>
				</a>
				</div>
			</div>	
        </td>
      </tr> 
      <tr>
        <td class="col-xs-6 col-sm-6 col-md-7">
		  <h6 class="media-heading text-semibold">Обработка текста</h6>
		  <span class="text-muted text-size-small hidden-xs">нужно задать класс текста, откуда будут браться данные</span>
		</td>
        <td class="col-xs-6 col-sm-6 col-md-5">
			<div class="input-group">
				<input type="text" name="content" value="{{ content }}" class="form-control">
				<div class="input-group-append">
				<a class="btn btn-outline-primary" data-toggle="popover" data-placement="top" data-trigger="focus" data-html="true" title="" data-content="Пример нахождения описания в обвертке div class=text и получим html-содержимое. В строке для парсинга пишем например: .text" tabindex="0" data-original-title="КОНТЕНТ">
					<i class="fa fa-question"></i>
				</a>
				</div>
			</div>	
        </td>
      </tr> 
	</table>
	</div>
</div>

<div class="card-footer text-center">
	<button type="submit" class="btn btn-outline-success">Сохранить изменения</button>
</div>

</form>