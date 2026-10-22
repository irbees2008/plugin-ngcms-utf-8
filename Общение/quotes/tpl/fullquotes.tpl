<article class="quotes-entry">
    <h3>{{ title }}</h3>
    <p class="text-muted" ><strong>Дата:</strong> {{ date }} | <strong>Оставил:</strong> <a href="{{ author_url|e('html_attr') }}">{{ author_name }}</a></p>
    <div class="quotes-entry-content">{{ content|raw }}</div>
</article>
