<form method="post" action="admin.php?mod=extra-config&amp;plugin=avtek_callback&amp;mode=settings">
  <input type="hidden" name="mode" value="settings">
  <input type="hidden" name="token" value="{{ token }}">

  <div class="card mb-4">
    <div class="card-header"><b>Глобальные настройки уведомлений</b></div>
    <div class="card-body">
      <div class="mb-3 row">
        <label class="col-sm-4 col-form-label" for="avtekDefaultEmails">Email получателей по умолчанию</label>
        <div class="col-sm-8">
          <textarea id="avtekDefaultEmails" class="form-control" name="default_emails" rows="2">{{ default_emails|e('html') }}</textarea>
          <small class="form-text text-muted">Адреса через запятую. Используются, если для формы не заданы отдельные получатели.</small>
        </div>
      </div>
      <div class="mb-3 row">
        <label class="col-sm-4 col-form-label" for="avtekMailFrom">Адрес отправителя</label>
        <div class="col-sm-8">
          <input id="avtekMailFrom" class="form-control" type="text" name="mail_from" value="{{ mail_from|e('html_attr') }}">
        </div>
      </div>
      <div class="mb-3 row">
        <label class="col-sm-4 col-form-label" for="avtekTelegramToken">Telegram Bot Token</label>
        <div class="col-sm-8">
          <input id="avtekTelegramToken" class="form-control" type="text" name="telegram_token" autocomplete="off" value="{{ telegram_token|e('html_attr') }}">
        </div>
      </div>
      <div class="mb-3 row">
        <label class="col-sm-4 col-form-label" for="avtekTelegramChats">Telegram chat_id</label>
        <div class="col-sm-8">
          <textarea id="avtekTelegramChats" class="form-control" name="telegram_chat_ids" rows="2">{{ telegram_chat_ids|e('html') }}</textarea>
          <small class="form-text text-muted">Один или несколько идентификаторов через запятую.</small>
        </div>
      </div>
      <div class="mb-3 row">
        <label class="col-sm-4 col-form-label" for="avtekCrmType">CRM</label>
        <div class="col-sm-8">
          <input id="avtekCrmType" class="form-control" type="text" name="crm_type" value="{{ crm_type|e('html_attr') }}">
          <small class="form-text text-muted">Используйте <code>webhook</code> или <code>bitrix24</code>.</small>
        </div>
      </div>
      <div class="mb-3 row">
        <label class="col-sm-4 col-form-label" for="avtekCrmSettings">Настройки CRM (JSON)</label>
        <div class="col-sm-8">
          <textarea id="avtekCrmSettings" class="form-control" name="crm_settings" rows="6">{{ crm_settings|e('html') }}</textarea>
          <small class="form-text text-muted">Для webhook укажите url и при необходимости header_auth; для Bitrix24 — webhook_url.</small>
        </div>
      </div>
      <div class="row">
        <div class="offset-sm-4 col-sm-8">
          <pre class="bg-light border rounded p-3 mb-0">Webhook: {"url":"https://example.test/lead","header_auth":"Authorization: Bearer …"}
Bitrix24: {"webhook_url":"https://example.bitrix24.ru/rest/1/token/crm.lead.add.json"}</pre>
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header"><b>Автовставка в полную новость</b></div>
    <div class="card-body">
      <div class="mb-3 row">
        <label class="col-sm-4 col-form-label" for="avtekAutoFormId">ID формы</label>
        <div class="col-sm-8">
          <input id="avtekAutoFormId" class="form-control" type="number" min="0" name="auto_form_id" value="{{ auto_form_id|e('html_attr') }}">
        </div>
      </div>
      <div class="mb-3 row">
        <label class="col-sm-4 col-form-label" for="avtekAutoMode">Режим</label>
        <div class="col-sm-8">
          <select id="avtekAutoMode" class="form-control" name="auto_mode">
            <option value="embed" {% if auto_mode == 'embed' %}selected{% endif %}>Встроенная форма</option>
            <option value="modal" {% if auto_mode == 'modal' %}selected{% endif %}>Модальное окно</option>
          </select>
        </div>
      </div>
    </div>
  </div>

  <div class="text-center mb-4">
    <button class="btn btn-success" type="submit" name="save_settings" value="1">Сохранить настройки</button>
  </div>
</form>
