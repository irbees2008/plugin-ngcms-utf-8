<div class="container-fluid">
	<div class="row mb-2">
	  <div class="col-sm-6 d-none d-md-block ">
			<h1 class="m-0 text-dark">{{ plugin_name }}</h1>
	  </div><!-- /.col -->
	  <div class="col-sm-6">
		<ol class="breadcrumb float-sm-right">
			<li class="breadcrumb-item"><a href="admin.php"><i class="fa fa-home"></i></a></li>
			<li class="breadcrumb-item"><a href="admin.php?mod=extras">{{ lang['auth_social']['breadcrumb.plugins'] }}</a></li>
			<li class="breadcrumb-item active" aria-current="page">{{ plugin_name }} - {{ lang['auth_social']['breadcrumb.current'] }}</li>
		</ol>
	  </div><!-- /.col -->
	</div><!-- /.row -->
</div>

<div class="card plugin-settings-card">
	<h5 class="card-header d-flex justify-content-between align-items-center" data-plugin-settings-header>
		<span>{{ plugin_name }}</span>{{ documentation_button|raw }}
	</h5>
	<div class="card-body plugin-settings-content">{{ entries|raw }}</div>
</div>
{{ documentation_modal|raw }}
