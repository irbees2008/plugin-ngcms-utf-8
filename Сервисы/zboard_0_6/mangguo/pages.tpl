<div class="pagenavi clearfix">
	{% if (flags.previous_page) %}
		{{ previous_page }}
	{% endif %}
	{{ pages }}
	{% if (flags.next_page) %}
		{{ next_page }}
	{% endif %}
</div>
