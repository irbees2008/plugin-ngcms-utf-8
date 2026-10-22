<div class="social-plugin-config">
    <h2>{{ lang['social#config_title'] }}</h2>

    {% if notice %}
        <div class="alert alert-success" role="status">{{ notice }}</div>
    {% endif %}
    {% if error %}
        <div class="alert alert-danger" role="alert">{{ error }}</div>
    {% endif %}

    <section class="card mb-4">
        <div class="card-header"><h3>{{ lang['social#settings_title'] }}</h3></div>
        <div class="card-body">
            <form method="post" action="admin.php?mod=extra-config&amp;plugin=social">
                <input type="hidden" name="action" value="commit">
                <input type="hidden" name="token" value="{{ token }}">
                <input type="hidden" name="operation" value="save_settings">
                <div class="mb-3">
                    <label class="form-label" for="social-localsource">{{ lang['social#localsource'] }}</label>
                    <select class="form-select" id="social-localsource" name="localsource">
                        <option value="0"{% if settings.localsource == 0 %} selected{% endif %}>{{ lang['social#localsource_site'] }}</option>
                        <option value="1"{% if settings.localsource == 1 %} selected{% endif %}>{{ lang['social#localsource_plugin'] }}</option>
                    </select>
                    <div class="form-text">{{ lang['social#localsource_help'] }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="social-skin">{{ lang['social#skin'] }}</label>
                    <select class="form-select" id="social-skin" name="skin">
                        {% for option in skin_options %}
                            <option value="{{ option.value }}"{% if option.value == settings.skin %} selected{% endif %}>{{ option.label }}</option>
                        {% endfor %}
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="social-cache">{{ lang['social#cache'] }}</label>
                    <select class="form-select" id="social-cache" name="cache">
                        <option value="0"{% if settings.cache == 0 %} selected{% endif %}>{{ lang['social#option_no'] }}</option>
                        <option value="1"{% if settings.cache == 1 %} selected{% endif %}>{{ lang['social#option_yes'] }}</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="social-cache-expire">{{ lang['social#cache_expire'] }}</label>
                    <input class="form-control" id="social-cache-expire" type="number" name="cacheExpire" min="1" value="{{ settings.cacheExpire }}" required>
                </div>
                <button class="btn btn-primary" type="submit">{{ lang['social#save_settings'] }}</button>
            </form>
            <form class="mt-3" method="post" action="admin.php?mod=extra-config&amp;plugin=social">
                <input type="hidden" name="action" value="commit">
                <input type="hidden" name="token" value="{{ token }}">
                <input type="hidden" name="operation" value="clear_cache">
                <button class="btn btn-secondary" type="submit">{{ lang['social#clear_cache'] }}</button>
            </form>
        </div>
    </section>

    <section class="card mb-4">
        <div class="card-header"><h3>{{ lang['social#services_title'] }}</h3></div>
        <div class="card-body">
            {% if services %}
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>{{ lang['social#service_name'] }}</th>
                                <th>{{ lang['social#service_description'] }}</th>
                                <th>{{ lang['social#service_state'] }}</th>
                                <th>{{ lang['social#service_actions'] }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {% for item in services %}
                                <tr>
                                    <td>{{ item.name }}</td>
                                    <td>{{ item.title }}</td>
                                    <td>{% if item.active %}{{ lang['social#enabled'] }}{% else %}{{ lang['social#disabled'] }}{% endif %}</td>
                                    <td class="text-nowrap">
                                        <a class="btn btn-sm btn-outline-primary" href="admin.php?mod=extra-config&amp;plugin=social&amp;id={{ item.id|url_encode }}">{{ lang['social#edit'] }}</a>
                                        <form class="d-inline" method="post" action="admin.php?mod=extra-config&amp;plugin=social">
                                            <input type="hidden" name="action" value="commit">
                                            <input type="hidden" name="token" value="{{ token }}">
                                            <input type="hidden" name="operation" value="move_service">
                                            <input type="hidden" name="id" value="{{ item.id }}">
                                            <input type="hidden" name="direction" value="up">
                                            <button class="btn btn-sm btn-outline-secondary" type="submit" aria-label="{{ lang['social#move_up'] }}" {% if item.is_first %}disabled{% endif %}>↑</button>
                                        </form>
                                        <form class="d-inline" method="post" action="admin.php?mod=extra-config&amp;plugin=social">
                                            <input type="hidden" name="action" value="commit">
                                            <input type="hidden" name="token" value="{{ token }}">
                                            <input type="hidden" name="operation" value="move_service">
                                            <input type="hidden" name="id" value="{{ item.id }}">
                                            <input type="hidden" name="direction" value="down">
                                            <button class="btn btn-sm btn-outline-secondary" type="submit" aria-label="{{ lang['social#move_down'] }}" {% if item.is_last %}disabled{% endif %}>↓</button>
                                        </form>
                                        <form class="d-inline" method="post" action="admin.php?mod=extra-config&amp;plugin=social" onsubmit="return confirm('{{ lang['social#confirm_delete']|e('js') }}');">
                                            <input type="hidden" name="action" value="commit">
                                            <input type="hidden" name="token" value="{{ token }}">
                                            <input type="hidden" name="operation" value="delete_service">
                                            <input type="hidden" name="id" value="{{ item.id }}">
                                            <button class="btn btn-sm btn-outline-danger" type="submit">{{ lang['social#delete'] }}</button>
                                        </form>
                                    </td>
                                </tr>
                            {% endfor %}
                        </tbody>
                    </table>
                </div>
            {% else %}
                <p>{{ lang['social#no_services'] }}</p>
            {% endif %}
            <form class="mt-3" method="post" action="admin.php?mod=extra-config&amp;plugin=social" onsubmit="return confirm('{{ lang['social#confirm_restore']|e('js') }}');">
                <input type="hidden" name="action" value="commit">
                <input type="hidden" name="token" value="{{ token }}">
                <input type="hidden" name="operation" value="restore_services">
                <button class="btn btn-outline-secondary" type="submit">{{ lang['social#restore_services'] }}</button>
            </form>
        </div>
    </section>

    <section class="card">
        <div class="card-header"><h3>{% if editing %}{{ lang['social#edit_service'] }}{% else %}{{ lang['social#add_service'] }}{% endif %}</h3></div>
        <div class="card-body">
            <form method="post" action="admin.php?mod=extra-config&amp;plugin=social">
                <input type="hidden" name="action" value="commit">
                <input type="hidden" name="token" value="{{ token }}">
                <input type="hidden" name="operation" value="save_service">
                <input type="hidden" name="id" value="{{ service_id }}">
                <div class="mb-3">
                    <label class="form-label" for="social-service-name">{{ lang['social#service_name'] }}</label>
                    <input class="form-control" id="social-service-name" type="text" name="name" maxlength="100" value="{{ service.name }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="social-service-title">{{ lang['social#service_description'] }}</label>
                    <input class="form-control" id="social-service-title" type="text" name="title" maxlength="255" value="{{ service.title }}">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="social-service-link">{{ lang['social#service_url'] }}</label>
                    <input class="form-control" id="social-service-link" type="url" name="link" maxlength="2048" value="{{ service.link }}" required>
                    <div class="form-text">{{ lang['social#service_url_help'] }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="social-service-image">{{ lang['social#service_image'] }}</label>
                    <select class="form-select" id="social-service-image" name="img">
                        {% for option in image_options %}
                            <option value="{{ option.value }}"{% if option.value == service.img %} selected{% endif %}>{{ option.label }}</option>
                        {% endfor %}
                    </select>
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="social-service-active" name="active" value="1"{% if service.active %} checked{% endif %}>
                    <label class="form-check-label" for="social-service-active">{{ lang['social#service_enabled'] }}</label>
                </div>
                <button class="btn btn-primary" type="submit">{% if editing %}{{ lang['social#save_service'] }}{% else %}{{ lang['social#add_service'] }}{% endif %}</button>
                {% if editing %}
                    <a class="btn btn-secondary" href="admin.php?mod=extra-config&amp;plugin=social">{{ lang['social#cancel_edit'] }}</a>
                {% endif %}
            </form>
        </div>
    </section>
</div>
