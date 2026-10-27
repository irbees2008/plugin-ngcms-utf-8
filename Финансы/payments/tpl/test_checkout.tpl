<section class="payments-test-checkout">
	<h2>{{ lang.test_title }} #{{ order.id }}</h2>
	<div class="alert alert-warning">{{ lang.test_warning }}</div>
	<p>{{ lang.pay_amount }} <strong>{{ order.total }} {{ currency }}</strong></p>
	<form method="post" action="{{ action_url }}">
		<input type="hidden" name="action" value="confirm_test_payment">
		<input type="hidden" name="token" value="{{ token }}">
		<button type="submit" class="btn btn-warning">{{ lang.test_confirm }}</button>
	</form>
	<a href="{{ cancel_url }}">{{ lang.test_cancel }}</a>
</section>
