<tr>
<td>{{ id }}</td>
<td>{{ date }}</td>
<td>{{ content|raw }}</td>
<td>{{ active }}</td>
<td class="text-nowrap">
	<a class="btn btn-sm btn-outline-secondary" href="{{ edit_url|e('html_attr') }}" onclick="return confirm('{{ confirm_edit|e('js') }}')" title="{{ edit_label }}" aria-label="{{ edit_label }}"><i class="fa fa-pencil" aria-hidden="true"></i></a>
	<a class="btn btn-sm btn-outline-danger" href="{{ delete_url|e('html_attr') }}" onclick="return confirm('{{ confirm_delete|e('js') }}')" title="{{ delete_label }}" aria-label="{{ delete_label }}"><i class="fa fa-trash" aria-hidden="true"></i></a>
</td>
<td><input name="selected_news[]" value="{{ id }}" class="check" type="checkbox"></td>
</tr>
