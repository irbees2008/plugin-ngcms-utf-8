<div class="basecont">
	<div class="binner">
		<div class="heading">
			<h1>Пожалуйста, заполните форму!</h1>
		</div>
		<div class="maincont">
				<form method="post" action="{formurl}">
			<div>
        <br />Ваше имя: {changername}
        <br />Кому меняете:{username}
        <br />Действие: {action}
        <br />Изменено на: {url}
			</div>
			<div class="clr"></div>
		</div>
	</div>
		<div class="maincont" style="padding-bottom: 0;">
			<table class="tableform" width="100%" align="center">
			<tr class="fieldtr">
				<td align="center"><textarea name="comment" style="width: 98%;" height:70px" class="f_textarea"></textarea></td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<input type="submit" name="submit" class="fbutton" value="Изменить" />
					<input type="hidden" name="uid" value="{uid}" />
					<input type="hidden" name="url" value="{url}" />
					<input type="hidden" name="act" value="{act}" />
				</td>
			</tr>
			</table>
		</div>

				</form>

	</div>
	<div class="hsep"></div>
</div>