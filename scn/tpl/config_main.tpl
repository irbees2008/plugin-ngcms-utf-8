<div class="card">
    <div class="card-header">
        <h3>{{ lang['scn#config_title'] }}</h3>
    </div>
    <div class="card-body">
        <p>{{ lang['scn#config_description'] }}</p>
        {% if saved %}
            <div class="alert alert-success">{{ lang['scn#config_saved'] }}</div>
        {% endif %}
        {% if error %}
            <div class="alert alert-danger">{{ error }}</div>
        {% endif %}
        <form method="post" action="admin.php?mod=extra-config&plugin=scn">
            <div class="mb-3">
                <label class="form-label" for="scn-number">{{ lang['scn#config_number'] }}</label>
                <input class="form-control" id="scn-number" type="number" name="number" min="1" value="{{ number }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="scn-orderby">{{ lang['scn#config_orderby'] }}</label>
                <select class="form-select" id="scn-orderby" name="orderby">
                    {% for option in order_options %}
                        <option value="{{ option.value }}"{% if option.value == orderby %} selected{% endif %}>{{ option.label }}</option>
                    {% endfor %}
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label" for="scn-mantemplate">{{ lang['scn#config_template_mode'] }}</label>
                <select class="form-select" id="scn-mantemplate" name="mantemplate">
                    {% for option in template_options %}
                        <option value="{{ option.value }}"{% if option.value == mantemplate %} selected{% endif %}>{{ option.label }}</option>
                    {% endfor %}
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label" for="scn-template">{{ lang['scn#config_template'] }}</label>
                <textarea class="form-control" id="scn-template" name="template" rows="8">{{ template|e }}</textarea>
                <div class="form-text">{{ lang['scn#config_template_help'] }}</div>
            </div>
            <button class="btn btn-primary" type="submit" name="submit" value="1">{{ lang['scn#config_save'] }}</button>
        </form>
    </div>
</div>
