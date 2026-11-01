
<form action="admin.php?mod=extra-config&amp;plugin=quotes&amp;action=modify" method="post" name="editquotes">
	<h4 class="mb-3">{{ quote_list_title }}</h4>
	<div class="table-responsive">
		<table class="table table-striped table-hover">
			<thead><tr>
				<th>{{ id_label }}</th><th>{{ date_label }}</th><th>{{ content_label }}</th><th>{{ active_label }}</th><th></th>
				<th><input class="check" type="checkbox" name="master_box" title="{{ select_all }}" onclick="javascript:check_uncheck_all(editquotes)"></th>
			</tr></thead>
			<tbody>{{ entries|raw }}</tbody>
		</table>
	</div>
	{{ pagesss|raw }}
	<div class="form-inline mt-3">
		<select class="form-control mr-2" name="subaction">
			<option value="">{{ choose_action }}</option>
			<option value="do_mass_delete">{{ delete_action }}</option>
			<option value="do_mass_approve">{{ approve_action }}</option>
			<option value="do_mass_forbidden">{{ forbid_action }}</option>
		</select>
		<button type="submit" class="btn btn-primary">{{ ok_button }}</button>
	</div>
</form>
