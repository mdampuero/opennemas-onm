<div id="id">
    <h1>{$content->title}</h1>
    <div>{$content->body|truncate:250|purify_html}</div>
</div>