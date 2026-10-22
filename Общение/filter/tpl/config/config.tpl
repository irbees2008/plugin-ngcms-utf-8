<div class="card">
    <div class="card-header">
        <h3>{{ title }}</h3>
    </div>
    <div class="card-body">
        <p>{{ description }}</p>

        {% if success %}
            <div class="alert alert-success" role="alert">{{ success_message }}</div>
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

        <form method="post" action="admin.php?mod=extra-config&amp;plugin=filter">
            <div class="mb-3">
                <label class="form-label" for="filter-replace">{{ replace_title }}</label>
                <textarea class="form-control" id="filter-replace" name="replace" rows="8">{{ replace }}</textarea>
                <div class="form-text">{{ replace_help }}</div>
            </div>

            <div class="mb-3">
                <label class="form-label" for="filter-block">{{ block_title }}</label>
                <textarea class="form-control" id="filter-block" name="block" rows="8">{{ block }}</textarea>
                <div class="form-text">{{ block_help }}</div>
            </div>

            <button type="submit" name="save" value="1" class="btn btn-primary">{{ save_label }}</button>
        </form>
    </div>
</div>
