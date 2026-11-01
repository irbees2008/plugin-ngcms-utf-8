<div class="container-fluid">
  <div class="row mb-2">
    <div class="col-sm-6">
      <h1 class="m-0 text-dark" style="padding: 20px 0 0 0;">{{ plugin_title|e('html') }}</h1>
    </div>
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item"><a href="admin.php"><i class="fa fa-home"></i></a></li>
        <li class="breadcrumb-item"><a href="admin.php?mod=extras">{{ extras_label|e('html') }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ plugin_title|e('html') }} - {{ action|e('html') }}</li>
      </ol>
    </div>
  </div>
</div>
<div class="card plugin-settings-card">
  <h5 class="card-header d-flex justify-content-between align-items-center" data-plugin-settings-header>
    <span>{{ plugin_title|e('html') }}</span>{{ documentation_button|raw }}
  </h5>
  <div class="card-body plugin-settings-content">
    <ul class="nav nav-tabs nav-fill mb-3 d-md-flex d-block" role="tablist">
      <li class="nav-item">
        <a href="admin.php?mod=extra-config&amp;plugin=avtek_callback&amp;mode=forms" class="nav-link {{ tab_forms_active }}">Формы</a>
      </li>
      <li class="nav-item">
        <a href="admin.php?mod=extra-config&amp;plugin=avtek_callback&amp;mode=leads" class="nav-link {{ tab_leads_active }}">Заявки</a>
      </li>
      <li class="nav-item">
        <a href="admin.php?mod=extra-config&amp;plugin=avtek_callback&amp;mode=settings" class="nav-link {{ tab_settings_active }}">Настройки</a>
      </li>
    </ul>
    {{ entries|raw }}
  </div>
</div>
{{ documentation_modal|raw }}
