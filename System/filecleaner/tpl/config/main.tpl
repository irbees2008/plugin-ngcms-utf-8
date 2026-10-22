<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6"><h1 class="m-0 text-dark" style="padding:20px 0 0 0;">{{ plugin_name }}</h1></div>
        <div class="col-sm-6"><ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="admin.php"><i class="fa fa-home"></i></a></li>
            <li class="breadcrumb-item"><a href="admin.php?mod=extras">{{ l_extras }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">filecleaner - {{ current_title }}</li>
        </ol></div>
    </div>
</div>
<div class="card plugin-settings-card">
    <h5 class="card-header d-flex justify-content-between align-items-center" data-plugin-settings-header>
        <span>{{ plugin_name }}</span>{{ documentation_button|raw }}
    </h5>
    <div class="card-body plugin-settings-content">{{ entries|raw }}</div>
</div>
{{ documentation_modal|raw }}
