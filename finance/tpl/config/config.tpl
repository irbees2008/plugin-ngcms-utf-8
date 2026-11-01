<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark" style="padding: 20px 0 0 0;">{{ plugin_id }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="admin.php"><i class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item"><a href="admin.php?mod=extras">{{ extras_label }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ plugin_id }} - {{ action }}</li>
            </ol>
        </div>
    </div>
</div>

<div class="card plugin-settings-card">
    <h5 class="card-header d-flex justify-content-between align-items-center" data-plugin-settings-header>
        <span>{{ lang.config_title }}</span>{{ documentation_button|raw }}
    </h5>
    <div class="card-body plugin-settings-content">
        <p>{{ lang.config_description }}</p>
        <form method="post" action="admin.php?mod=extra-config&amp;plugin=finance">
            <input type="hidden" name="action" value="commit">
            <input type="hidden" name="token" value="{{ token }}">
            <input type="hidden" name="operation" value="save_settings">

            <div class="form-group row">
                <label class="col-sm-3 col-form-label" for="finance-currency">{{ lang.currency }}</label>
                <div class="col-sm-9">
                    <select class="form-control" id="finance-currency" name="currency">
                        <option value="RUR"{% if settings.currency == 'RUR' %} selected{% endif %}>RUR</option>
                        <option value="EUR"{% if settings.currency == 'EUR' %} selected{% endif %}>EUR</option>
                        <option value="USD"{% if settings.currency == 'USD' %} selected{% endif %}>USD</option>
                    </select>
                    <small class="form-text text-muted">{{ lang.currency_help }}</small>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-3 col-form-label" for="finance-news-access-duration">{{ lang.news_access_duration }}</label>
                <div class="col-sm-9">
                    <select class="form-control" id="finance-news-access-duration" name="news_access_duration">
                        <option value="permanent"{% if settings.news_access_duration == 'permanent' %} selected{% endif %}>{{ lang.duration_permanent }}</option>
                        <option value="1h"{% if settings.news_access_duration == '1h' %} selected{% endif %}>{{ lang.duration_1h }}</option>
                        <option value="2h"{% if settings.news_access_duration == '2h' %} selected{% endif %}>{{ lang.duration_2h }}</option>
                        <option value="3h"{% if settings.news_access_duration == '3h' %} selected{% endif %}>{{ lang.duration_3h }}</option>
                        <option value="page"{% if settings.news_access_duration == 'page' %} selected{% endif %}>{{ lang.duration_page }}</option>
                        <option value="session"{% if settings.news_access_duration == 'session' %} selected{% endif %}>{{ lang.duration_session }}</option>
                    </select>
                    <small class="form-text text-muted">{{ lang.duration_help }}</small>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-3 col-form-label" for="finance-news-block-count">{{ lang.news_block_count }}</label>
                <div class="col-sm-9">
                    <select class="form-control" id="finance-news-block-count" name="news_block_count">
                        {% for number in 1..5 %}
                            <option value="{{ number }}"{% if settings.news_block_count == number %} selected{% endif %}>{{ number }}</option>
                        {% endfor %}
                    </select>
                    <small class="form-text text-muted">{{ lang.news_block_count_help }}</small>
                </div>
            </div>

            <fieldset class="border rounded p-3 mb-3">
                <legend class="w-auto px-2">{{ lang.protection_header }}</legend>
                {% for key, label in {
                    'copy_dots': lang.protect_copy_dots,
                    'copy_watermark': lang.protect_copy_watermark,
                    'disable_selection': lang.protect_disable_selection,
                    'visible_watermark': lang.protect_visible_watermark
                } %}
                    <div class="form-check mb-2">
                        <input type="hidden" name="protect_{{ key }}" value="0">
                        <input class="form-check-input" type="checkbox" id="finance-protect-{{ key }}" name="protect_{{ key }}" value="1"{% if settings.protection[key] %} checked{% endif %}>
                        <label class="form-check-label" for="finance-protect-{{ key }}">{{ label }}</label>
                    </div>
                {% endfor %}
                <div class="form-group row mb-0">
                    <label class="col-sm-3 col-form-label" for="finance-watermark-text">{{ lang.watermark_text }}</label>
                    <div class="col-sm-9">
                        <input class="form-control" id="finance-watermark-text" name="protect_watermark_text" maxlength="200" value="{{ settings.protection.watermark_text }}">
                        <small class="form-text text-muted">{{ lang.watermark_text_help }}</small>
                    </div>
                </div>
            </fieldset>

            <div class="form-group row">
                <label class="col-sm-3 col-form-label" for="finance-max-file-downloads">{{ lang.max_file_downloads }}</label>
                <div class="col-sm-9">
                    <input class="form-control" type="number" min="1" max="10" step="1" id="finance-max-file-downloads" name="max_file_downloads" value="{{ settings.max_file_downloads }}">
                    <small class="form-text text-muted">{{ lang.max_file_downloads_help }}</small>
                </div>
            </div>

            {% for number, balance in settings.balances %}
                <fieldset class="border rounded p-3 mb-3">
                    <legend class="w-auto px-2">{{ lang.balance_header }} {{ number }}</legend>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label" for="balance-{{ number }}-monetary">{{ lang.monetary }}</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="balance-{{ number }}-monetary" name="balance{{ number }}_monetary">
                                <option value="1"{% if balance.monetary %} selected{% endif %}>{{ lang.monetary_yes }}</option>
                                <option value="0"{% if not balance.monetary %} selected{% endif %}>{{ lang.monetary_no }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label" for="balance-{{ number }}-type">{{ lang.type }}</label>
                        <div class="col-sm-9">
                            <input class="form-control" id="balance-{{ number }}-type" name="balance{{ number }}_type" maxlength="30" value="{{ balance.type }}">
                        </div>
                    </div>
                    <div class="form-group row mb-0">
                        <label class="col-sm-3 col-form-label" for="balance-{{ number }}-description">{{ lang.description }}</label>
                        <div class="col-sm-9">
                            <textarea class="form-control" id="balance-{{ number }}-description" name="balance{{ number }}_description" rows="2" maxlength="4000">{{ balance.description }}</textarea>
                        </div>
                    </div>
                </fieldset>
            {% endfor %}

            <button type="submit" class="btn btn-primary">{{ lang.save }}</button>
        </form>
    </div>
</div>

{% if toast_json and toast_json != '[]' %}
<script>
(function () {
    var toasts = {{ toast_json|raw }};
    if (typeof window.showToast !== 'function') return;
    toasts.forEach(function (toast) {
        window.showToast(toast.message, {
            type: toast.type,
            title: '{{ lang.config_title|e('js') }}'
        });
    });
})();
</script>
{% endif %}

{{ documentation_modal|raw }}
