<section class="finance-account">
    <h2>{{ lang.title }}</h2>
    <p>{{ lang.balance }}: <strong>{{ balance }} {{ currency }}</strong></p>

    {% if can_topup %}
        <form method="post" action="{{ topup_url }}" class="finance-topup-form">
            <input type="hidden" name="mode" value="topup">
            <input type="hidden" name="token" value="{{ topup_token }}">
            <label for="finance-topup-amount">{{ lang.topup_amount }}</label>
            <input id="finance-topup-amount" class="form-control" type="number" name="amount" min="0.01" step="0.01" required>
            <small class="form-text text-muted">{{ lang.topup_help }}</small>
            <button class="btn btn-primary" type="submit">{{ lang.submit }}</button>
        </form>
    {% elseif payments_active and not currency_compatible %}
        <div class="alert alert-warning">{{ lang.currency_mismatch }}</div>
    {% elseif not transactions_available %}
        <div class="alert alert-warning">{{ lang.transactions_unsupported }}</div>
    {% else %}
        <div class="alert alert-info">{{ lang.payments_unavailable }}</div>
    {% endif %}

    <h3>{{ lang.history }}</h3>
    {% if history %}
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>{{ lang.date }}</th>
                        <th>{{ lang.operation }}</th>
                        <th>{{ lang.amount }}</th>
                        <th>{{ lang.description }}</th>
                    </tr>
                </thead>
                <tbody>
                    {% for entry in history %}
                        <tr>
                            <td>{{ entry.date }}</td>
                            <td>{{ entry.is_income ? lang.operation_income : lang.operation_expense }}</td>
                            <td>{{ entry.is_income ? '+' : '-' }}{{ entry.amount }} {{ currency }}</td>
                            <td>{{ entry.description }}</td>
                        </tr>
                    {% endfor %}
                </tbody>
            </table>
        </div>
    {% else %}
        <p>{{ lang.history_empty }}</p>
    {% endif %}
</section>
