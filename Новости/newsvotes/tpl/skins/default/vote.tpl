<br/>
<div class="colorvote">
    <strong>{vote}</strong><br/>
    <form action="{self}" method="post" id="newsvoteForm">
        <input type="hidden" name="mode" value="vote" />
        <input type="hidden" name="voteid" value="{voteid}" />
        {answers}
        [is-rate]<br/><input type="submit" value="{vote_submit}" />[/is-rate]
    </form>
    {total_votes_label} <strong>{total}</strong><br/>
</div>
