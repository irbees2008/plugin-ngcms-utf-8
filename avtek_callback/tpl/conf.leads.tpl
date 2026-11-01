<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0">Заявки — последние 200</h5>
  <a class="btn btn-outline-primary" href="admin.php?mod=extra-config&amp;plugin=avtek_callback&amp;mode=export">
    <i class="fa fa-download" aria-hidden="true"></i> Скачать XLS
  </a>
</div>
<div class="table-responsive">
  <table class="table table-sm table-striped align-middle">
    <thead class="thead-dark">
      <tr>
        <th>ID</th>
        <th>Дата</th>
        <th>Форма</th>
        <th>IP</th>
        <th>Страница</th>
        <th>Данные</th>
        <th>E-mail</th>
        <th>Почта</th>
        <th>Telegram</th>
      </tr>
    </thead>
    <tbody>
      {% for item in items %}
        <tr>
          <td>{{ item.id }}</td>
          <td class="text-nowrap">{{ item.created_at|e('html') }}</td>
          <td>{{ item.form_title|e('html') }}</td>
          <td>{{ item.ip|e('html') }}</td>
          <td style="max-width:260px;word-break:break-all">{{ item.page_url|e('html') }}</td>
          <td style="max-width:420px">
            {% for value in item.data %}
              <strong>{{ value.label|e('html') }}:</strong> {{ value.value|e('html') }}<br>
            {% else %}
              <span class="text-muted">Нет данных</span>
            {% endfor %}
          </td>
          <td>{{ item.email|e('html') }}</td>
          <td>
            {% if item.email_sent %}
              <span class="badge badge-success">Отправлено</span>
            {% else %}
              <span class="badge badge-light">Не отправлено</span>
            {% endif %}
          </td>
          <td>
            {% if item.telegram_sent %}
              <span class="badge badge-success">Отправлено</span>
            {% else %}
              <span class="badge badge-light">Не отправлено</span>
            {% endif %}
          </td>
        </tr>
      {% else %}
        <tr><td colspan="9" class="text-center text-muted">Заявок пока нет.</td></tr>
      {% endfor %}
    </tbody>
  </table>
</div>
