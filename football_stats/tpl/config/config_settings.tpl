<div class="card">
    <div class="card-header">
        <h3>Настройки футбольной статистики</h3>
    </div>
    <div class="card-body">
        {% if success %}
            <div class="alert alert-success" role="alert">Настройки сохранены.</div>
        {% endif %}

        {% if errors %}
            <div class="alert alert-danger" role="alert">
                <ul>
                    {% for error in errors %}
                        <li>{{ error }}</li>
                    {% endfor %}
                </ul>
            </div>
        {% endif %}

        <form method="post" action="admin.php?mod=extra-config&amp;plugin=football_stats">
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="football-stats-cache" name="cache" value="1" {% if cache_value %}checked{% endif %}>
                <label class="form-check-label" for="football-stats-cache">{{ cache_label }}</label>
            </div>

            {% for field in fields %}
                <div class="mb-3">
                    <label class="form-label" for="football-stats-{{ field.name }}">{{ field.label }}</label>
                    {% if field.type == 'json' %}
                        <textarea class="form-control font-monospace" id="football-stats-{{ field.name }}" name="{{ field.name }}" rows="8">{{ field.value }}</textarea>
                    {% else %}
                        <input class="form-control" id="football-stats-{{ field.name }}" type="{{ field.type }}" name="{{ field.name }}" value="{{ field.value }}">
                    {% endif %}
                    {% if field.help %}
                        <div class="form-text">{{ field.help }}</div>
                    {% endif %}
                </div>
            {% endfor %}

            <button type="submit" name="save" value="1" class="btn btn-primary">{{ save_label }}</button>
        </form>
    </div>
</div>
