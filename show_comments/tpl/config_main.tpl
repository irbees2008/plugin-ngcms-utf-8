<div class="card">
    <div class="card-header">
        <h3>{{ lang['show_comments#config_title'] }}</h3>
    </div>
    <div class="card-body">
        <p>{{ lang['show_comments#config_description'] }}</p>
        {% if saved %}
            <div class="alert alert-success">{{ lang['show_comments#config_saved'] }}</div>
        {% endif %}
        {% if error %}
            <div class="alert alert-danger">{{ error }}</div>
        {% endif %}
        <form method="post" action="admin.php?mod=extra-config&amp;plugin=show_comments">
            <input type="hidden" name="action" value="commit">
            <input type="hidden" name="token" value="{{ token }}">
            <div class="mb-3">
                <label class="form-label" for="show-comments-perpage">{{ lang['show_comments#perpage_title'] }}</label>
                <input class="form-control" id="show-comments-perpage" type="number" name="perpage" min="1" value="{{ perpage }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="show-comments-length">{{ lang['show_comments#comm_length_title'] }}</label>
                <input class="form-control" id="show-comments-length" type="number" name="comm_length" min="10" max="5000" value="{{ comm_length }}" required>
                <div class="form-text">{{ lang['show_comments#comm_length_description'] }}</div>
            </div>
            <div class="mb-3">
                <label class="form-label" for="show-comments-order">{{ lang['show_comments#order_title'] }}</label>
                <select class="form-select" id="show-comments-order" name="order">
                    <option value="asc"{% if order == 'asc' %} selected{% endif %}>{{ lang['show_comments#order_asc'] }}</option>
                    <option value="desc"{% if order == 'desc' %} selected{% endif %}>{{ lang['show_comments#order_desc'] }}</option>
                </select>
                <div class="form-text">{{ lang['show_comments#order_description'] }}</div>
            </div>
            <button class="btn btn-primary" type="submit" name="save_config" value="1">{{ lang['show_comments#config_save'] }}</button>
        </form>
    </div>
</div>
