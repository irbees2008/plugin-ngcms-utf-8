<form action="admin.php" method="get" class="row align-items-end mb-3">
    <input type="hidden" name="mod" value="extra-config">
    <input type="hidden" name="plugin" value="quotes">
    <input type="hidden" name="action" value="showlist">
    <div class="col-md-3 form-group">
        <label for="quotes-sort">{{ sort_label }}</label>
        <select class="form-control" id="quotes-sort" name="sort">{{ sort|raw }}</select>
    </div>
    <div class="col-md-3 form-group">
        <label for="quotes-month">{{ month_label }}</label>
        <select class="form-control" id="quotes-month" name="postdate"><option value="">{{ all_months }}</option>{{ selectdate|raw }}</select>
    </div>
    <div class="col-md-2 form-group">
        <label for="quotes-author">{{ author_label }}</label>
        <select class="form-control" id="quotes-author" name="author">{{ author|raw }}</select>
    </div>
    <div class="col-md-2 form-group">
        <label for="quotes-status">{{ status_label }}</label>
        <select class="form-control" id="quotes-status" name="status">{{ status|raw }}</select>
    </div>
    <div class="col-md-2 form-group"><button type="submit" class="btn btn-outline-primary">{{ show_button }}</button></div>
</form>
