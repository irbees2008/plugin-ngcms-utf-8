<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0">Формы обратной связи</h5>
  <a class="btn btn-primary" href="admin.php?mod=extra-config&amp;plugin=avtek_callback&amp;mode=form_edit">
    <i class="fa fa-plus" aria-hidden="true"></i> Новая форма
  </a>
</div>
<div class="table-responsive">
  <table class="table table-sm table-striped align-middle">
    <thead class="thead-dark">
      <tr>
        <th>ID</th>
        <th>Название</th>
        <th>Slug</th>
        <th>Email получателей</th>
        <th class="text-right">Действия</th>
      </tr>
    </thead>
    <tbody>
      {% for item in items %}
        <tr>
          <td>{{ item.id }}</td>
          <td>{{ item.title|e('html') }}</td>
          <td>{{ item.slug|e('html') }}</td>
          <td>{{ item.emails|e('html') }}</td>
          <td class="text-right text-nowrap">
            <a class="btn btn-sm btn-outline-primary" href="admin.php?mod=extra-config&amp;plugin=avtek_callback&amp;mode=form_edit&amp;id={{ item.id }}" title="Редактировать" aria-label="Редактировать">
              <i class="fa fa-pencil" aria-hidden="true"></i>
            </a>
            <a class="btn btn-sm btn-outline-secondary" href="admin.php?mod=extra-config&amp;plugin=avtek_callback&amp;mode=export&amp;form_id={{ item.id }}" title="Экспортировать заявки" aria-label="Экспортировать заявки">
              <i class="fa fa-download" aria-hidden="true"></i> XLS
            </a>
          </td>
        </tr>
      {% else %}
        <tr><td colspan="5" class="text-center text-muted">Формы ещё не созданы.</td></tr>
      {% endfor %}
    </tbody>
  </table>
</div>
