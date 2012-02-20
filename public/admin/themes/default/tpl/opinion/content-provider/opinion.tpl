<div data-content-id="{$content->id}" data-class="Opinion" class="content-provider-element clearfix">
    <div class="description">
        <input type="checkbox" class="action-button" name="selected-{$smarty.foreach.opinions_loop.index}">
        <div class="title">
            <span class="type">{t}Opinion{/t}</span>
            {$content->author->name} - {$content->title}
        </div>
    </div>
    <ul class="action-buttons">
        <li>
            <a href="/admin/controllers/opinion/opinion.php?action=delete&id={$content->id}" title="Eliminar" class="action-button delete-button"></a>
        </li>
        <li>
            <a title="{t}Edit{/t} '{$content->title}'" href="/admin/controllers/opinion/opinion.php?action=read&id={$content->id}&category={$smarty.request.category}" class="action-button edit-button"></a>
        </li>

    </ul>
    <div class="selectButton"></div>
</div>