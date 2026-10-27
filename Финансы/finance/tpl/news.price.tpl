<tr>
	<th colspan="2">{{ title }}</th>
</tr>
{% for block in blocks %}
	<tr>
		<td valign="top">{{ block_label }} {{ block.number }} — {{ label }}:</td>
		<td valign="top">
			<input type="number" name="{{ block.field }}" id="{{ block.field }}" min="0" step="0.01" value="{{ block.price }}" />
			<small>{{ help }}</small>
		</td>
	</tr>
{% endfor %}
