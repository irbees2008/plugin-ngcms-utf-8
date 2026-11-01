<article class="quotes-entry mb-4">
    <p class="text-muted"><strong>Дата:</strong> {{ date }} | <strong>Оставил:</strong> <a href="{{ author_url|e('html_attr') }}">{{ author_name }}</a> | <a href="{{ show_url|e('html_attr') }}">Ссылка на цитату</a></p>
    <div class="quotes-entry-content">{{ content|raw }}</div>
</article>
