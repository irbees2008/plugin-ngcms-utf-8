<div class="posters-admin">
    <h2>{form_heading}</h2>
    <form method="post" action="{action_url}">
        <input type="hidden" name="id" value="{id}" />
        <div class="mb-3">
            <label class="form-label" for="poster-date">{date_label}</label>
            <input class="form-control" id="poster-date" type="text" name="date" value="{date}" />
        </div>
        <div class="mb-3">
            <label class="form-label" for="poster-title">{title_label}</label>
            <input class="form-control" id="poster-title" type="text" name="title" value="{title}" required />
        </div>
        <div class="mb-3">
            <label class="form-label" for="poster-text">{text_label}</label>
            <textarea class="form-control" id="poster-text" name="text" rows="8">{text}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label" for="poster-template">{template_label}</label>
            <select class="form-select" id="poster-template" name="tpl">{template_options}</select>
        </div>
        <div class="mb-3 form-check">
            <input class="form-check-input" id="poster-active" type="checkbox" name="active" value="1" {active} />
            <label class="form-check-label" for="poster-active">{active_label}</label>
        </div>
        <button class="btn btn-primary" type="submit">{save_label}</button>
        <a class="btn btn-secondary" href="admin.php?mod=poster">{cancel_label}</a>
    </form>
</div>
