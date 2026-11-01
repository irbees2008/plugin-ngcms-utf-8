<div class="finance-locked-content alert alert-info">
    <p>{{ lang.news_access }} {{ lang['news.block'] }} {{ block_number }}. {{ lang.news_price }}: <strong>{{ price }} {{ currency }}</strong></p>
    <p>{{ lang.balance }}: {{ ubalance }} {{ currency }}</p>
    <form method="post" action="{{ pay_url }}">
        <input type="hidden" name="access_element_id" value="{{ newsid }}">
        <input type="hidden" name="block_number" value="{{ block_number }}">
        <input type="hidden" name="token" value="{{ token }}">
        <button class="btn btn-primary" type="submit">{{ lang.news_pay }}</button>
    </form>
</div>
