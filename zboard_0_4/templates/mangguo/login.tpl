[error]
<div class="feed-me">
{l_login.error}
</div>
[/error]
[banned]
<div class="feed-me">
{l_login.banned}
</div>
[/banned]
[need.activate]
<div class="feed-me">
{l_login.need.activate}
</div>
[/need.activate]
<div class="block-title">{l_login.title}</div>
<form name="login" method="post" class="comment-form" action="{form_action}">
<input type="hidden" name="redirect" value="{redirect}"/>
	<div class="label pull-left">
		<label for="logn">Р›РѕРіРёРЅ:</label>
		<input type="text" class="form-control" type="text" name="username" class="input">
	</div><br/><br/>
	<div class="label pull-right">
		<label for="pass">РџР°СЂРѕР»СЊ:</label>
		<input type="password" type="password" name="password" class="input">
	</div>
	<div class="clearfix"></div>
	<div class="label"><br/>
		<input type="submit" value="Р’РѕР№С‚Рё" class="button">
	</div>
</form>
