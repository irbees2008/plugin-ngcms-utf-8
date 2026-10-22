<div class="card">
    <div class="card-header">
        <h3>{{ title }}</h3>
    </div>
    <div class="card-body">
        <p>{{ description }}</p>

        {% if success %}
            <div class="alert alert-success" role="alert">{{ saved_message }}</div>
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

        <form method="post" action="admin.php?mod=extra-config&amp;plugin=make_links">
            <fieldset class="mb-3">
                <legend>{{ group_title }}</legend>

                {% for field in fields %}
                    <div class="mb-3">
                        <label class="form-label" for="make-links-{{ field.name }}">{{ field.label }}</label>
                        {% if field.type == 'select' %}
                            <select class="form-select" id="make-links-{{ field.name }}" name="{{ field.name }}">
                                {% for option in field.options %}
                                    <option value="{{ option.value }}" {% if option.value == field.value %}selected{% endif %}>{{ option.label }}</option>
                                {% endfor %}
                            </select>
                        {% elseif field.type == 'textarea' %}
                            <textarea class="form-control" id="make-links-{{ field.name }}" name="{{ field.name }}" rows="8">{{ field.value }}</textarea>
                        {% else %}
                            <input class="form-control" id="make-links-{{ field.name }}" type="{{ field.type }}" name="{{ field.name }}" value="{{ field.value }}" min="0">
                        {% endif %}
                        {% if field.help %}
                            <div class="form-text">{{ field.help }}</div>
                        {% endif %}
                    </div>
                {% endfor %}
            </fieldset>

            <button type="submit" name="save" value="1" class="btn btn-primary">{{ save_label }}</button>
        </form>
    </div>
</div>
