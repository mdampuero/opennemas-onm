<div id="id">
    <h1>{$content->title}</h1>
    <div>{$content->body|truncate:250|purify_html}</div>
    <a href="{baseurl}/{url route="staticpage-read" slug=$content->slug pk_content=$content->pk_content}">{t}More{/t} &raquo;</a>
</div>