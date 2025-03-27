<div class="container-fluid">
	<div class="row mb-2">
		<div class="col-sm-6">
			<h1 class="m-0 text-dark" style="padding: 20px 0 0 0;">
				<a href="?mod=extra-config&plugin=ads_pro">grab</a>
			</h1>
		</div>
		<!-- /.col -->
		<div class="col-sm-6">
			<ol class="breadcrumb float-sm-right">
				<li class="breadcrumb-item">
					<a href="admin.php">
						<i class="fa fa-home"></i>
					</a>
				</li>
				<li class="breadcrumb-item">
					<a href="admin.php?mod=extras">{l_extras}</a>
				</li>
<li class="breadcrumb-item active" aria-current="page">grab -	{{ global }}</li>

			</ol>
		</div>
		<!-- /.col -->
	</div>
	<!-- /.row -->
</div><!-- /.container-fluid -->

<div class="card">
<h5 class="card-header">grab</h5>

	<ul class="nav nav-tabs mb-3 px-3">
		{% for tab in tabs %}
			<li class="nav-item">
				<a class="nav-link {% if tab.active %}active{% endif %}" href="{{ tab.url }}">
					{{ tab.title }}
				</a>
			</li>
		{% endfor %}
	</ul>

		{{ entries }}
	
</div>

<style>
.nav-tabs {
    border-bottom: 1px solid #dee2e6;
    padding-left: 15px;
}

.nav-tabs .nav-link {
    color: #495057;
    border: 1px solid transparent;
    border-top-left-radius: 0.25rem;
    border-top-right-radius: 0.25rem;
}

.nav-tabs .nav-link.active {
    color: #495057;
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
}
</style>