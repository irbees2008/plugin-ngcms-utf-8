<form method="post" action="admin.php?mod=extra-config&amp;plugin=avtek_callback&amp;mode=form_edit">
  <input type="hidden" name="mode" value="form_edit">
  <input type="hidden" name="id" value="{{ id }}">
  <input type="hidden" name="token" value="{{ token }}">

  <div class="card mb-4">
    <div class="card-header"><b>{{ is_existing ? 'Параметры формы' : 'Новая форма' }}</b></div>
    <div class="card-body">
      <div class="mb-3 row">
        <label class="col-sm-4 col-form-label" for="avtekFormTitle">Название</label>
        <div class="col-sm-8">
          <input id="avtekFormTitle" class="form-control" type="text" name="title" maxlength="200" required value="{{ title|e('html_attr') }}">
        </div>
      </div>
      <div class="mb-3 row">
        <label class="col-sm-4 col-form-label" for="avtekFormSlug">Slug</label>
        <div class="col-sm-8">
          <input id="avtekFormSlug" class="form-control" type="text" name="slug" maxlength="190" value="{{ slug|e('html_attr') }}">
          <small class="form-text text-muted">Можно оставить пустым: он будет создан из названия формы.</small>
        </div>
      </div>
      <div class="mb-0 row">
        <label class="col-sm-4 col-form-label" for="avtekFormEmails">Email получателей</label>
        <div class="col-sm-8">
          <textarea id="avtekFormEmails" class="form-control" name="emails" rows="2">{{ emails|e('html') }}</textarea>
          <small class="form-text text-muted">Адреса через запятую. Если пусто, будут использованы глобальные настройки.</small>
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header"><b>Поля формы</b></div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-sm table-striped table-bordered align-middle">
          <thead class="thead-dark">
            <tr>
              <th>Поле</th>
              <th>Включено</th>
              <th>Обязательное</th>
              <th>Название</th>
              <th>Placeholder</th>
            </tr>
          </thead>
          <tbody>
            {% for field in fields %}
              <tr>
                <td>{{ field.title|e('html') }}</td>
                <td>
                  <div class="custom-control custom-checkbox">
                    <input id="avtekFieldEnabled{{ field.key }}" class="custom-control-input" type="checkbox" name="f_{{ field.key }}" value="1" {% if field.enabled %}checked{% endif %}>
                    <label class="custom-control-label" for="avtekFieldEnabled{{ field.key }}"></label>
                  </div>
                </td>
                <td>
                  <div class="custom-control custom-checkbox">
                    <input id="avtekFieldRequired{{ field.key }}" class="custom-control-input" type="checkbox" name="r_{{ field.key }}" value="1" {% if field.required %}checked{% endif %}>
                    <label class="custom-control-label" for="avtekFieldRequired{{ field.key }}"></label>
                  </div>
                </td>
                <td><input class="form-control" type="text" name="label_{{ field.key }}" value="{{ field.label|e('html_attr') }}"></td>
                <td><input class="form-control" type="text" name="ph_{{ field.key }}" value="{{ field.placeholder|e('html_attr') }}"></td>
              </tr>
            {% endfor %}
          </tbody>
        </table>
      </div>
      <div class="mb-3">
        <label for="avtekVariants">Варианты ответа</label>
        <textarea id="avtekVariants" class="form-control" name="variants_list" rows="4" placeholder="Каждый вариант — с новой строки">{{ variants_list|e('html') }}</textarea>
        <small class="form-text text-muted">Используется, если включено поле «Варианты ответа».</small>
      </div>
      <div class="mb-3">
        <label for="avtekCustomFields">Дополнительные поля</label>
        <textarea id="avtekCustomFields" class="form-control" name="custom_fields" rows="5" placeholder="Компания|text|1|&#10;Тема|select|0|Сервис,Продажи,Другое">{{ custom_fields|e('html') }}</textarea>
        <small class="form-text text-muted">Каждая строка: <code>Название|type|required(0/1)|options</code>. Варианты select разделяются запятыми.</small>
      </div>
      <div class="alert alert-info mb-0">
        Подписи полей используются в Email, Telegram и списке заявок. Дата передаётся в формате <code>YYYY-MM-DD</code>.
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header"><b>Поведение и уведомления</b></div>
    <div class="card-body">
      <div class="mb-3 row">
        <label class="col-sm-4 col-form-label" for="avtekButtonText">Текст кнопки</label>
        <div class="col-sm-8">
          <input id="avtekButtonText" class="form-control" type="text" name="button_text" value="{{ form_settings.button_text|e('html_attr') }}">
        </div>
      </div>
      <div class="mb-3 row">
        <label class="col-sm-4 col-form-label" for="avtekSuccessText">Сообщение после отправки</label>
        <div class="col-sm-8">
          <input id="avtekSuccessText" class="form-control" type="text" name="success_text" value="{{ form_settings.success_text|e('html_attr') }}">
        </div>
      </div>
      <div class="mb-3 row">
        <label class="col-sm-4 col-form-label" for="avtekSuccessMode">После успешной отправки</label>
        <div class="col-sm-8">
          <select id="avtekSuccessMode" class="form-control" name="success_mode">
            <option value="inline" {% if form_settings.success_mode == 'inline' %}selected{% endif %}>Показать сообщение в форме</option>
            <option value="redirect" {% if form_settings.success_mode == 'redirect' %}selected{% endif %}>Перейти по URL</option>
          </select>
        </div>
      </div>
      <div class="mb-3 row">
        <label class="col-sm-4 col-form-label" for="avtekRedirectUrl">URL редиректа</label>
        <div class="col-sm-8">
          <input id="avtekRedirectUrl" class="form-control" type="text" name="success_redirect_url" value="{{ form_settings.success_redirect_url|e('html_attr') }}">
        </div>
      </div>
      <div class="mb-3 row">
        <label class="col-sm-4 col-form-label" for="avtekFormTelegramChats">Telegram chat_id</label>
        <div class="col-sm-8">
          <textarea id="avtekFormTelegramChats" class="form-control" name="telegram_chat_ids_form" rows="2">{{ form_settings.telegram_chat_ids|e('html') }}</textarea>
          <small class="form-text text-muted">Необязательно. Если пусто, будут использованы глобальные chat_id.</small>
        </div>
      </div>
      <div class="mb-3 row">
        <label class="col-sm-4 col-form-label" for="avtekMailSubject">Тема письма</label>
        <div class="col-sm-8">
          <input id="avtekMailSubject" class="form-control" type="text" name="mail_subject" value="{{ form_settings.mail_subject|e('html_attr') }}">
        </div>
      </div>
      <div class="mb-3">
        <label for="avtekConversionJs">Google Ads conversion JS</label>
        <textarea id="avtekConversionJs" class="form-control" name="conversion_js" rows="4" placeholder="gtag('event','conversion',{send_to:'AW-XXXXX/YYY'});">{{ form_settings.conversion_js|e('html') }}</textarea>
        <small class="form-text text-muted">Выполняется в браузере после успешной отправки.</small>
      </div>
      <div class="mb-0 row">
        <label class="col-sm-4 col-form-label" for="avtekSkinEmbed">Шаблон встроенной формы</label>
        <div class="col-sm-8">
          <input id="avtekSkinEmbed" class="form-control" type="text" name="skin_embed" value="{{ form_settings.skin_embed|e('html_attr') }}">
          <small class="form-text text-muted">Имя PHP-файла из каталога <code>/tpl</code>, например <code>skin_embed.php</code>.</small>
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header"><b>Интеграция с CRM</b></div>
    <div class="card-body">
      <div class="mb-3 row">
        <label class="col-sm-4 col-form-label" for="avtekFormCrmType">Тип CRM</label>
        <div class="col-sm-8">
          <input id="avtekFormCrmType" class="form-control" type="text" name="crm_type_form" value="{{ form_settings.crm_type|e('html_attr') }}">
          <small class="form-text text-muted">Поддерживаются <code>webhook</code> и <code>bitrix24</code>. Если поле пустое, используются глобальные настройки.</small>
        </div>
      </div>
      <div class="mb-0 row">
        <label class="col-sm-4 col-form-label" for="avtekFormCrmSettings">Настройки CRM (JSON)</label>
        <div class="col-sm-8">
          <textarea id="avtekFormCrmSettings" class="form-control" name="crm_settings_form" rows="5">{{ form_settings.crm_settings|e('html') }}</textarea>
        </div>
      </div>
    </div>
  </div>

  <div class="text-center mb-4">
    <button class="btn btn-success" type="submit" name="save_form" value="1">{{ is_existing ? 'Сохранить форму' : 'Добавить форму' }}</button>
    <a class="btn btn-secondary" href="admin.php?mod=extra-config&amp;plugin=avtek_callback&amp;mode=forms">Отмена</a>
  </div>
</form>

{% if is_existing %}
  <div class="card mb-4">
    <div class="card-header"><b>Вставка формы на сайт</b></div>
    <div class="card-body">
      {{ snippet_box|raw }}
    </div>
  </div>
  <form method="post" action="admin.php?mod=extra-config&amp;plugin=avtek_callback" onsubmit="return confirm('{{ delete_confirm|e('js') }}');">
    <input type="hidden" name="mode" value="form_delete">
    <input type="hidden" name="id" value="{{ id }}">
    <input type="hidden" name="token" value="{{ token }}">
    <button class="btn btn-outline-danger mb-4" type="submit"><i class="fa fa-trash" aria-hidden="true"></i> Удалить форму</button>
  </form>
{% endif %}
