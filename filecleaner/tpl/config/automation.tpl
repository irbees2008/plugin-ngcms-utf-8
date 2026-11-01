<ul class="nav nav-tabs mb-3" id="filecleaner-tabs" role="tablist">
    <li class="nav-item"><a class="nav-link{% if active_section == 'files' %} active{% endif %}" id="files-tab" data-toggle="tab" href="#files-pane" role="tab" aria-controls="files-pane" aria-selected="{{ active_section == 'files' ? 'true' : 'false' }}"><i class="fa fa-files-o"></i> {{ lang['filecleaner:tab_files'] }}</a></li>
    <li class="nav-item"><a class="nav-link{% if active_section == 'broken' %} active{% endif %}" id="broken-tab" data-toggle="tab" href="#broken-pane" role="tab" aria-controls="broken-pane" aria-selected="{{ active_section == 'broken' ? 'true' : 'false' }}"><i class="fa fa-chain-broken"></i> {{ lang['filecleaner:tab_broken'] }}</a></li>
    <li class="nav-item"><a class="nav-link" id="settings-tab" data-toggle="tab" href="#settings-pane" role="tab" aria-controls="settings-pane" aria-selected="false"><i class="fa fa-cog"></i> {{ lang['filecleaner:tab_settings'] }}</a></li>
    <li class="nav-item"><a class="nav-link" id="log-tab" data-toggle="tab" href="#log-pane" role="tab" aria-controls="log-pane" aria-selected="false"><i class="fa fa-history"></i> {{ lang['filecleaner:tab_log'] }}</a></li>
    <li class="nav-item"><a class="nav-link" id="operations-tab" data-toggle="tab" href="#operations-pane" role="tab" aria-controls="operations-pane" aria-selected="false"><i class="fa fa-list-alt"></i> {{ lang['filecleaner:tab_operations'] }}</a></li>
</ul>
<style>
.filecleaner-preview-wrap { position: relative; display: inline-block; }
.filecleaner-preview { display: none; position: absolute; left: 0; top: 1.5rem; z-index: 20; padding: .25rem; background: #fff; border: 1px solid #dee2e6; box-shadow: 0 .25rem .75rem rgba(0,0,0,.15); }
.filecleaner-preview-wrap:hover .filecleaner-preview { display: block; }
.filecleaner-preview img { display: block; max-width: 280px; max-height: 220px; width: auto; height: auto; }
.filecleaner-delete-loading { opacity: .7; pointer-events: none; }
</style>
<script>
function filecleanerFormatSize(bytes) {
    var units = ['B', 'KB', 'MB', 'GB', 'TB'], unit = 0, value = bytes;
    while (value >= 1024 && unit < units.length - 1) { value /= 1024; unit++; }
    return (unit ? value.toFixed(2) : Math.round(value)) + ' ' + units[unit];
}
function filecleanerConfirmDelete(form) {
    if (form.dataset.deleteLoading === '1') return false;
    if (form.dataset.singleAction === '1') {
        filecleanerStartDelete(form);
        return true;
    }
    var checked = form.querySelectorAll('input[name="files[]"]:checked'), total = 0;
    checked.forEach(function (input) { total += parseInt(input.getAttribute('data-size') || '0', 10); });
    if (!checked.length) return confirm('{{ lang['filecleaner:no_selected_files']|e('js') }}');
    var message = '{{ lang['filecleaner:confirm_delete_summary']|e('js') }}'
        .replace('%count%', checked.length)
        .replace('%size%', filecleanerFormatSize(total));
    var confirmed = confirm(message);
    if (confirmed) filecleanerStartDelete(form);
    return confirmed;
}
function filecleanerStartDelete(form) {
    if (form.dataset.deleteLoading === '1') return;
    form.dataset.deleteLoading = '1';
    form.classList.add('filecleaner-delete-loading');
    form.querySelectorAll('button[type="submit"]').forEach(function (button) {
        button.disabled = true;
        button.innerHTML = '<i class="fa fa-spinner fa-spin" aria-hidden="true"></i> {{ lang['filecleaner:deleting']|e('js') }}';
    });
}
function filecleanerSingleDelete(form) {
    if (!confirm('{{ lang['filecleaner:confirm_delete_single']|e('js') }}')) return false;
    form.dataset.singleAction = '1';
    return true;
}
</script>
<div class="tab-content" id="filecleaner-tabs-content">
<div class="tab-pane fade{% if active_section == 'files' %} show active{% endif %}" id="files-pane" role="tabpanel" aria-labelledby="files-tab">
<div class="row mb-3">
    <div class="col-md-3 mb-3 mb-md-0"><div class="card h-100"><div class="card-body"><b>{{ lang['filecleaner:stat_all'] }} :</b> {{ stats.all }}</div></div></div>
    <div class="col-md-3 mb-3 mb-md-0"><div class="card h-100"><div class="card-body"><b>{{ lang['filecleaner:stat_used'] }} :</b> {{ stats.used }}</div></div></div>
    <div class="col-md-3 mb-3 mb-md-0"><div class="card h-100"><div class="card-body"><b>{{ lang['filecleaner:stat_unused'] }} :</b> {{ stats.unused }}</div></div></div>
    <div class="col-md-3"><div class="card h-100"><div class="card-body"><b>{{ lang['filecleaner:stat_trash_size'] }} :</b> {{ stats.trash_label }}</div></div></div>
</div>
{% if toast_json and toast_json != '[]' %}<script>(function () {
    var toasts = {{ toast_json|raw }};
    if (typeof window.showToast !== 'function') return;
    toasts.forEach(function (toast) {
        window.showToast(toast.message, {type: toast.type, title: '{{ lang['filecleaner:title']|e('js') }}'});
    });
})();</script>{% endif %}
<div class="mb-4">
    <form method="post" action="admin.php?mod=extra-config&amp;plugin=filecleaner&amp;action=scan" style="display:inline"><input type="hidden" name="token" value="{{ token }}"><button class="btn btn-primary"><i class="fa fa-refresh"></i> {{ lang['filecleaner:button_scan'] }}</button></form>
    <span class="text-muted">  {{ lang['filecleaner:last_scan'] }}: {{ scanned_at }}</span>
</div>
<form method="get" action="admin.php">
    <input type="hidden" name="mod" value="extra-config"><input type="hidden" name="plugin" value="filecleaner">
    <input class="form-control" style="display:inline-block;width:28%;" type="text" name="q" value="{{ query }}" placeholder="{{ lang['filecleaner:search_placeholder'] }}">
    <select class="form-control" style="display:inline-block;width:14%;" name="status"><option value="">{{ lang['filecleaner:status_all'] }}</option><option value="unused"{% if filter == 'unused' %} selected{% endif %}>{{ lang['filecleaner:status_unused'] }}</option><option value="protected"{% if filter == 'protected' %} selected{% endif %}>{{ lang['filecleaner:status_protected'] }}</option><option value="used"{% if filter == 'used' %} selected{% endif %}>{{ lang['filecleaner:status_used'] }}</option><option value="registered"{% if filter == 'registered' %} selected{% endif %}>{{ lang['filecleaner:status_registered'] }}</option><option value="insufficient"{% if filter == 'insufficient' %} selected{% endif %}>{{ lang['filecleaner:status_insufficient'] }}</option></select>
    <select class="form-control" style="display:inline-block;width:9%;" name="type"><option value="">{{ lang['filecleaner:type_all'] }}</option>{% for type in ['image', 'document', 'archive', 'other'] %}<option value="{{ type }}"{% if type_filter == type %} selected{% endif %}>{{ lang['filecleaner:type_' ~ type] }}</option>{% endfor %}</select>
    <select class="form-control" style="display:inline-block;width:10%;" name="size"><option value="">{{ lang['filecleaner:size_all'] }}</option>{% for size in size_ranges %}<option value="{{ size }}"{% if size_filter == size %} selected{% endif %}>{{ lang['filecleaner:size_' ~ size] }}</option>{% endfor %}</select>
    <select class="form-control" style="display:inline-block;width:9%;" name="sort"><option value="name"{% if sort == 'name' %} selected{% endif %}>{{ lang['filecleaner:sort_name'] }}</option><option value="size"{% if sort == 'size' %} selected{% endif %}>{{ lang['filecleaner:sort_size'] }}</option><option value="date"{% if sort == 'date' %} selected{% endif %}>{{ lang['filecleaner:sort_date'] }}</option><option value="age"{% if sort == 'age' %} selected{% endif %}>{{ lang['filecleaner:sort_age'] }}</option></select>
    <select class="form-control" style="display:inline-block;width:11%;" name="order"><option value="asc"{% if order == 'asc' %} selected{% endif %}>{{ lang['filecleaner:order_asc'] }}</option><option value="desc"{% if order == 'desc' %} selected{% endif %}>{{ lang['filecleaner:order_desc'] }}</option></select>
    <select class="form-control" style="display:inline-block;width:10%;" name="per_page" aria-label="{{ lang['filecleaner:per_page'] }}">{% for option in per_page_options %}<option value="{{ option }}"{% if per_page == option %} selected{% endif %}>{{ option }} {{ lang['filecleaner:per_page_suffix'] }}</option>{% endfor %}</select>
    <button class="btn btn-outline-secondary">{{ lang['filecleaner:button_filter'] }}</button>
</form>
<form method="post" action="admin.php?mod=extra-config&amp;plugin=filecleaner&amp;action=delete" onsubmit="return filecleanerConfirmDelete(this)"><input type="hidden" name="token" value="{{ token }}"><button class="btn btn-danger mt-2">{{ lang['filecleaner:button_delete_selected'] }}</button>
    <div class="table-responsive mt-2" style="display:block;width:100%;max-width:100%;overflow-x:auto;-webkit-overflow-scrolling:touch;"><table class="table table-striped mb-0" style="width:100%;min-width:760px;table-layout:fixed;"><thead><tr><th style="width:42px;"><input type="checkbox" onclick="document.querySelectorAll('[name=files\\[\\]]').forEach(function(x){x.checked=this.checked}, this)"></th><th style="width:40%;">{{ lang['filecleaner:column_file'] }}</th><th>{{ lang['filecleaner:column_size'] }}</th><th>{{ lang['filecleaner:column_date'] }}</th><th>{{ lang['filecleaner:column_status'] }}</th><th>{{ lang['filecleaner:column_action'] }}</th></tr></thead><tbody>
    {% for file in files %}<tr><td>{% if file.can_delete %}<input type="checkbox" name="files[]" value="{{ file.path }}" data-size="{{ file.size }}">{% endif %}</td><td style="word-break:break-all;overflow-wrap:anywhere;"><span class="filecleaner-preview-wrap">{% if file.is_image %}<span title="{{ lang['filecleaner:preview'] }}"><i class="fa fa-eye"></i><span class="filecleaner-preview"><img src="{{ file.url }}" alt="{{ file.path }}"></span></span>{% endif %}</span> {{ file.path }}</td><td>{{ file.size_label }}</td><td>{{ file.date_label }}</td><td>{{ lang['filecleaner:status_' ~ file.status] }}</td><td><a href="{{ file.url }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">{{ lang['filecleaner:open_file'] }}</a> {% if file.can_delete %}<button type="submit" class="btn btn-sm btn-outline-danger" name="single" value="{{ file.path }}" onclick="return filecleanerSingleDelete(this.form)" title="{{ lang['filecleaner:button_delete'] }}" aria-label="{{ lang['filecleaner:button_delete'] }}"><i class="fa fa-trash" aria-hidden="true"></i><span class="sr-only">{{ lang['filecleaner:button_delete'] }}</span></button>{% endif %}</td></tr>{% else %}<tr><td colspan="6">{{ lang['filecleaner:no_files'] }}</td></tr>{% endfor %}
    </tbody></table></div>
    {% if page_count > 1 %}<div class="mt-2">{{ pagelist|raw }}</div>{% endif %}
</form>
</div>
<div class="tab-pane fade{% if active_section == 'broken' %} show active{% endif %}" id="broken-pane" role="tabpanel" aria-labelledby="broken-tab">
    <div class="mb-3">
        <p>{{ lang['filecleaner:broken_intro'] }}</p>
        <form id="filecleaner-broken-scan-form" method="post" action="admin.php?mod=extra-config&amp;plugin=filecleaner&amp;section=broken">
            <input type="hidden" name="token" value="{{ token }}">
            <label for="filecleaner-broken-batch">{{ lang['filecleaner:broken_batch_size'] }}</label>
            <select class="form-control" id="filecleaner-broken-batch" name="batch_size" style="display:inline-block;width:auto;">
                <option value="20" selected>20</option><option value="50">50</option><option value="100">100</option>
            </select>
            <button type="button" class="btn btn-primary" onclick="filecleanerRunBrokenScan(this.form)"><i class="fa fa-search"></i> {{ lang['filecleaner:broken_scan_button'] }}</button>
            <span id="filecleaner-broken-scan-status" class="ml-2 text-muted">
                {% if broken_progress.processed is defined %}{{ lang['filecleaner:broken_resume'] }} {{ broken_progress.processed }}{% elseif broken_scanned_at %}{{ lang['filecleaner:broken_last_scan'] }}: {{ broken_scanned_at }}{% endif %}
            </span>
        </form>
    </div>
    <script>
    async function filecleanerRunBrokenScan(form) {
        var button = form.querySelector('button[type="button"]');
        var status = document.getElementById('filecleaner-broken-scan-status');
        var maxRetries = 5;
        button.disabled = true;
        try {
            while (true) {
                var data = new FormData(form);
                data.set('action', 'broken_scan_step');
                var result;
                var retryCount = 0;
                while (true) {
                    try {
                        var response = await fetch(form.action, {method: 'POST', body: data, credentials: 'same-origin'});
                        if (response.redirected || response.status === 401 || response.status === 403) {
                            throw new Error('{{ lang['filecleaner:broken_session_error']|e('js') }}');
                        }
                        var body = await response.text();
                        try { result = JSON.parse(body); }
                        catch (parseError) {
                            if (!response.ok && response.status < 500 && response.status !== 429) {
                                throw new Error('{{ lang['filecleaner:broken_scan_error']|e('js') }}');
                            }
                            var parseRetryError = new Error('{{ lang['filecleaner:broken_scan_error']|e('js') }}');
                            parseRetryError.retryable = true;
                            throw parseRetryError;
                        }
                        if (result.message === 'invalid_token') {
                            throw new Error('{{ lang['filecleaner:broken_session_error']|e('js') }}');
                        }
                        if (!response.ok || !result.ok) {
                            var requestError = new Error('{{ lang['filecleaner:broken_scan_error']|e('js') }}');
                            if (response.status >= 500 || response.status === 429 || result.message === 'database_error' || result.message === 'scan_error') {
                                requestError.retryable = true;
                            }
                            throw requestError;
                        }
                        break;
                    } catch (requestError) {
                        if (requestError.message === '{{ lang['filecleaner:broken_session_error']|e('js') }}') throw requestError;
                        if (!requestError.retryable && requestError.name !== 'TypeError') throw requestError;
                        if (retryCount >= maxRetries) throw new Error('{{ lang['filecleaner:broken_scan_error']|e('js') }}');
                        retryCount++;
                        status.textContent = '{{ lang['filecleaner:broken_retrying']|e('js') }}'.replace('%attempt%', retryCount).replace('%max%', maxRetries);
                        await new Promise(function(resolve) {
                            window.setTimeout(resolve, Math.min(1000 * Math.pow(2, retryCount - 1), 15000));
                        });
                    }
                }
                status.textContent = '{{ lang['filecleaner:broken_progress']|e('js') }}'.replace('%count%', result.processed);
                if (result.complete) {
                    window.location.href = 'admin.php?mod=extra-config&plugin=filecleaner&section=broken';
                    return;
                }
            }
        } catch (error) {
            status.textContent = error.message || '{{ lang['filecleaner:broken_scan_error']|e('js') }}';
            button.disabled = false;
        }
    }
    </script>
    <div class="mb-2"><b>{{ lang['filecleaner:broken_total'] }}:</b> {{ broken_issue_count }}</div>
    <form method="post" action="admin.php?mod=extra-config&amp;plugin=filecleaner&amp;action=clean_broken&amp;section=broken" onsubmit="return confirm('{{ lang['filecleaner:broken_confirm_clean']|e('js') }}')">
        <input type="hidden" name="token" value="{{ token }}">
        <button class="btn btn-danger mb-2">{{ lang['filecleaner:broken_clean_selected'] }}</button>
        <div class="table-responsive"><table class="table table-striped">
            <thead><tr><th><input type="checkbox" aria-label="{{ lang['filecleaner:broken_select_all'] }}" onchange="document.querySelectorAll('input[name^=broken_issues]').forEach(function(input){input.checked=this.checked}, this)"></th><th>{{ lang['filecleaner:broken_news'] }}</th><th>{{ lang['filecleaner:broken_field'] }}</th><th>{{ lang['filecleaner:broken_path'] }}</th><th>{{ lang['filecleaner:broken_tag'] }}</th></tr></thead>
            <tbody>
            {% for issue in broken_issues %}<tr>
                <td><input type="checkbox" name="broken_issues[]" value="{{ issue.key }}"></td>
                <td>#{{ issue.news_id }} {{ issue.title }}</td>
                <td>{{ issue.field }}</td><td style="word-break:break-all;overflow-wrap:anywhere;">{{ issue.path }}</td>
                <td><code>{{ issue.tag }}</code></td>
            </tr>{% else %}<tr><td colspan="5">{{ lang['filecleaner:broken_empty'] }}</td></tr>{% endfor %}
            </tbody>
        </table></div>
        {% if broken_page_count > 1 %}<div class="mt-2">{{ broken_pagelist|raw }}</div>{% endif %}
    </form>
</div>
<div class="tab-pane fade" id="settings-pane" role="tabpanel" aria-labelledby="settings-tab">
<div class="card mt-2"><div class="card-header">{{ lang['filecleaner:settings_title'] }}</div><div class="card-body"><form method="post" action="admin.php?mod=extra-config&amp;plugin=filecleaner&amp;action=settings"><input type="hidden" name="token" value="{{ token }}">
    <label>{{ lang['filecleaner:scan_dirs'] }}</label>
    <div class="border rounded p-2 mb-3" style="max-height:260px;overflow-y:auto;">
        {% for path, label in available_dirs %}<div class="form-check"><input class="form-check-input" type="checkbox" name="selected_dirs[]" value="{{ path }}" id="filecleaner-dir-{{ loop.index }}"{% if path in config.selected_dirs %} checked{% endif %}><label class="form-check-label" for="filecleaner-dir-{{ loop.index }}">{{ label }}</label></div>{% else %}<span class="text-muted">{{ lang['filecleaner:no_dirs'] }}</span>{% endfor %}
    </div>
    <label>{{ lang['filecleaner:protect_days'] }}</label><select class="form-control" name="protect_days">{% for days in protect_days %}<option value="{{ days }}"{% if config.protect_days == days %} selected{% endif %}>{{ days }} {{ lang['filecleaner:days'] }}</option>{% endfor %}</select>
    <label class="mt-2">{{ lang['filecleaner:excluded_dirs'] }}</label><textarea class="form-control" name="excluded_dirs" rows="4">{{ config.excluded_dirs|join('\n') }}</textarea>
    <label class="mt-2">{{ lang['filecleaner:excluded_masks'] }}</label>
    <div class="border rounded p-2 mb-2">{% for mask in config.mask_options %}<div class="form-check"><input class="form-check-input" type="checkbox" name="selected_masks[]" value="{{ mask }}" id="filecleaner-mask-{{ loop.index }}"{% if mask in config.selected_masks %} checked{% endif %}><label class="form-check-label" for="filecleaner-mask-{{ loop.index }}">{{ mask }}</label></div>{% endfor %}</div>
    <label class="mt-2">{{ lang['filecleaner:custom_masks'] }}</label><textarea class="form-control" name="custom_masks" rows="3" placeholder="*.cache&#10;preview-*.jpg">{{ config.custom_masks|join('\n') }}</textarea>
    <fieldset class="border rounded p-3 mt-3"><legend class="h6 w-auto px-2 mb-0">{{ lang['filecleaner:cron_title'] }}</legend>
        <div class="form-check"><input class="form-check-input" type="checkbox" name="cron_enabled" value="1" id="filecleaner-cron-enabled"{% if cron.enabled %} checked{% endif %}><label class="form-check-label" for="filecleaner-cron-enabled">{{ lang['filecleaner:cron_enabled'] }}</label></div>
        <div class="form-row mt-2"><div class="col-sm-6"><label for="filecleaner-cron-hour">{{ lang['filecleaner:cron_hour'] }}</label><select class="form-control" name="cron_hour" id="filecleaner-cron-hour">{% for hour in 0..23 %}<option value="{{ hour }}"{% if cron.hour == hour %} selected{% endif %}>{{ '%02d'|format(hour) }}:00</option>{% endfor %}</select></div><div class="col-sm-6"><label for="filecleaner-cron-minute">{{ lang['filecleaner:cron_minute'] }}</label><select class="form-control" name="cron_minute" id="filecleaner-cron-minute">{% for minute in [0, 15, 30, 45] %}<option value="{{ minute }}"{% if cron.minute == minute %} selected{% endif %}>{{ '%02d'|format(minute) }}</option>{% endfor %}</select></div></div>
        <small class="form-text text-muted">{{ lang['filecleaner:cron_daily'] }}</small>
    </fieldset>
    <button class="btn btn-success mt-2">{{ lang['filecleaner:button_save'] }}</button>
</form></div></div>
</div>
<div class="tab-pane fade" id="log-pane" role="tabpanel" aria-labelledby="log-tab">
    <form method="post" action="admin.php?mod=extra-config&amp;plugin=filecleaner&amp;action=clear_log" onsubmit="return confirm('{{ lang['filecleaner:confirm_clear_log']|e('js') }}')"><input type="hidden" name="token" value="{{ token }}"><button type="submit" class="btn btn-outline-danger btn-sm"><i class="fa fa-trash"></i> {{ lang['filecleaner:clear_log'] }}</button></form>
    <div class="table-responsive mt-2"><table class="table table-striped"><thead><tr><th>{{ lang['filecleaner:log_date'] }}</th><th>{{ lang['filecleaner:log_file'] }}</th><th>{{ lang['filecleaner:log_size'] }}</th></tr></thead><tbody>
    {% for entry in deletion_log %}<tr><td>{{ entry.date }}</td><td style="word-break:break-all;overflow-wrap:anywhere;">{{ entry.path }}</td><td>{{ entry.size_label }}</td></tr>{% else %}<tr><td colspan="3">{{ lang['filecleaner:log_empty'] }}</td></tr>{% endfor %}
    </tbody></table></div>{% if log_page_count > 1 %}<div class="mt-2">{{ log_pagelist|raw }}</div>{% endif %}
</div>
<div class="tab-pane fade" id="operations-pane" role="tabpanel" aria-labelledby="operations-tab">
    <form method="post" action="admin.php?mod=extra-config&amp;plugin=filecleaner&amp;action=clear_operations_log" onsubmit="return confirm('{{ lang['filecleaner:confirm_clear_operations_log']|e('js') }}')"><input type="hidden" name="token" value="{{ token }}"><button type="submit" class="btn btn-outline-danger btn-sm"><i class="fa fa-trash"></i> {{ lang['filecleaner:clear_operations_log'] }}</button></form>
    <div class="table-responsive mt-2"><table class="table table-sm table-striped"><thead><tr><th>{{ lang['filecleaner:operation_date'] }}</th><th>{{ lang['filecleaner:operation_action'] }}</th><th>{{ lang['filecleaner:operation_status'] }}</th><th>{{ lang['filecleaner:operation_details'] }}</th></tr></thead><tbody>
    {% for entry in operations_log %}<tr><td>{{ entry.date }}</td><td>{{ entry.action }}</td><td>{{ entry.status }}</td><td style="word-break:break-all;overflow-wrap:anywhere;">{{ entry.details }}</td></tr>{% else %}<tr><td colspan="4">{{ lang['filecleaner:log_empty'] }}</td></tr>{% endfor %}
    </tbody></table></div>{% if operation_page_count > 1 %}<div class="mt-2">{{ operation_pagelist|raw }}</div>{% endif %}
</div>
</div>
