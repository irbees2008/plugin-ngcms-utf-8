<ul class="nav nav-tabs mb-3" id="quotes-tabs" role="tablist">
    <li class="nav-item"><a class="nav-link{% if active_tab == 'settings' %} active{% endif %}" id="quotes-settings-tab" data-toggle="tab" href="#quotes-settings-pane" role="tab" aria-controls="quotes-settings-pane" aria-selected="{{ active_tab == 'settings' ? 'true' : 'false' }}">{{ lang['quotes:config.tab_settings'] }}</a></li>
    <li class="nav-item"><a class="nav-link{% if active_tab == 'quotes' %} active{% endif %}" id="quotes-manage-tab" data-toggle="tab" href="#quotes-manage-pane" role="tab" aria-controls="quotes-manage-pane" aria-selected="{{ active_tab == 'quotes' ? 'true' : 'false' }}">{{ lang['quotes:config.tab_quotes'] }}</a></li>
    <li class="nav-item"><a class="nav-link{% if active_tab == 'add' %} active{% endif %}" id="quotes-add-tab" data-toggle="tab" href="#quotes-add-pane" role="tab" aria-controls="quotes-add-pane" aria-selected="{{ active_tab == 'add' ? 'true' : 'false' }}">{{ lang['quotes:config.tab_add'] }}</a></li>
    <li class="nav-item"><a class="nav-link{% if active_tab == 'import' %} active{% endif %}" id="quotes-import-tab" data-toggle="tab" href="#quotes-import-pane" role="tab" aria-controls="quotes-import-pane" aria-selected="{{ active_tab == 'import' ? 'true' : 'false' }}">{{ lang['quotes:config.tab_import'] }}</a></li>
</ul>
<div class="tab-content" id="quotes-tabs-content">
    <section class="tab-pane fade{% if active_tab == 'settings' %} show active{% endif %}" id="quotes-settings-pane" role="tabpanel" aria-labelledby="quotes-settings-tab">
        <form method="post" action="admin.php?mod=extra-config&amp;plugin=quotes">
            <input type="hidden" name="action" value="commit">
            <input type="hidden" name="token" value="{{ token }}">
            <div class="row">
                <div class="col-lg-6">
                    <h5>{{ lang['quotes:config.page_settings'] }}</h5>
                    <div class="form-group">
                        <label for="quotes-template-source">{{ lang['quotes:config.template_source'] }}</label>
                        <select class="form-control" id="quotes-template-source" name="localsource">
                            <option value="0"{% if settings.localsource == 0 %} selected{% endif %}>{{ lang['quotes:config.site_template'] }}</option>
                            <option value="1"{% if settings.localsource == 1 %} selected{% endif %}>{{ lang['quotes:config.plugin_template'] }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="quotes-max-char">{{ lang['quotes:config.max_char'] }}</label>
                        <input class="form-control" type="number" min="0" id="quotes-max-char" name="max_char" value="{{ settings.max_char }}">
                        <small class="form-text text-muted">{{ lang['quotes:config.max_char_desc']|raw }}</small>
                    </div>
                    <div class="form-group">
                        <label for="quotes-count">{{ lang['quotes:config.quotes_per_page'] }}</label>
                        <input class="form-control" type="number" min="1" id="quotes-count" name="count" value="{{ settings.count }}">
                    </div>
                    <div class="form-group">
                        <label for="quotes-redirect-delay">{{ lang['quotes:config.redirect_delay'] }}</label>
                        <input class="form-control" type="number" min="0" id="quotes-redirect-delay" name="redirect_delay" value="{{ settings.redirect_delay }}">
                        <small class="form-text text-muted">{{ lang['quotes:config.redirect_delay_desc'] }}</small>
                    </div>
                    <div class="form-group">
                        <label for="quotes-date">{{ lang['quotes:config.date_format'] }}</label>
                        <input class="form-control" type="text" id="quotes-date" name="date" value="{{ settings.date|e }}">
                    </div>
                    <div class="form-group">
                        <label for="quotes-description">{{ lang['quotes:config.description'] }}</label>
                        <input class="form-control" type="text" id="quotes-description" name="description" value="{{ settings.description|e }}">
                    </div>
                    <div class="form-group">
                        <label for="quotes-keywords">{{ lang['quotes:config.keywords'] }}</label>
                        <input class="form-control" type="text" id="quotes-keywords" name="keywords" value="{{ settings.keywords|e }}">
                    </div>
                </div>
                <div class="col-lg-6">
                    <h5>{{ lang['quotes:config.block_settings'] }}</h5>
                    <div class="form-group">
                        <label for="quotes-random-mode">{{ lang['quotes:config.random_mode'] }}</label>
                        <select class="form-control" id="quotes-random-mode" name="rand">
                            <option value="0"{% if settings.rand == 0 %} selected{% endif %}>{{ lang['quotes:config.light'] }}</option>
                            <option value="1"{% if settings.rand == 1 %} selected{% endif %}>{{ lang['quotes:config.heavy'] }}</option>
                        </select>
                        <small class="form-text text-muted">{{ lang['quotes:config.random_mode_desc']|raw }}</small>
                    </div>
                    <h5>{{ lang['quotes:config.admin_settings'] }}</h5>
                    <div class="form-group">
                        <label for="quotes-adm-count">{{ lang['quotes:config.admin_quotes_per_page'] }}</label>
                        <input class="form-control" type="number" min="1" id="quotes-adm-count" name="adm_count" value="{{ settings.adm_count }}">
                    </div>
                    <h5>{{ lang['quotes:config.cache_settings'] }}</h5>
                    <div class="form-group">
                        <label for="quotes-cache">{{ lang['quotes:config.cache_title'] }}</label>
                        <select class="form-control" id="quotes-cache" name="cache">
                            <option value="1"{% if settings.cache == 1 %} selected{% endif %}>{{ lang['quotes:config.yes'] }}</option>
                            <option value="0"{% if settings.cache == 0 %} selected{% endif %}>{{ lang['quotes:config.no'] }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="quotes-cache-expire">{{ lang['quotes:config.cache_expire'] }}</label>
                        <input class="form-control" type="number" min="1" id="quotes-cache-expire" name="cacheExpire" value="{{ settings.cacheExpire }}">
                    </div>
                </div>
            </div>
            <button class="btn btn-primary" type="submit">{{ lang['quotes:config.save_settings'] }}</button>
        </form>
    </section>
    <section class="tab-pane fade{% if active_tab == 'quotes' %} show active{% endif %}" id="quotes-manage-pane" role="tabpanel" aria-labelledby="quotes-manage-tab">
        {{ work_html|raw }}
    </section>
    <section class="tab-pane fade{% if active_tab == 'add' %} show active{% endif %}" id="quotes-add-pane" role="tabpanel" aria-labelledby="quotes-add-tab">
        {{ add_html|raw }}
    </section>
    <section class="tab-pane fade{% if active_tab == 'import' %} show active{% endif %}" id="quotes-import-pane" role="tabpanel" aria-labelledby="quotes-import-tab">
        {% if import_result %}<div class="alert alert-{{ import_result.type }}" role="alert">{{ import_result.message }}</div>{% endif %}
        <h5>{{ lang['quotes:config.import_title'] }}</h5>
        <form method="post" action="admin.php?mod=extra-config&amp;plugin=quotes&amp;action=import" enctype="multipart/form-data" accept-charset="UTF-8">
            <input type="hidden" name="token" value="{{ token }}">
            <div class="form-group">
                <label for="quotes-import-file">{{ lang['quotes:config.import_file'] }}</label>
                <input class="form-control-file" type="file" id="quotes-import-file" name="quote_file" accept=".txt,text/plain" required>
                <small class="form-text text-muted">{{ lang['quotes:config.import_help'] }}</small>
            </div>
            <button class="btn btn-primary" type="submit">{{ lang['quotes:config.import_submit'] }}</button>
        </form>
    </section>
</div>
