[TWIG]
<div class="content">
    <div class="frame-inside page-text">
        <div class="container">
            <div class="text-right">
            <h1>{{ news.title }}</h1>
                <div class="text">
                    <p>{{ news.short }}{{ news.full }}</p>
                    {% if (news.flags.hasPagination) %}
                        <div class="pagination">
                            <ul>
                                {{ news.pagination }}
                            </ul>
                        </div>
                    {% endif %}
                </div>
            </div>
        </div>
    </div>
</div>
[/TWIG]
