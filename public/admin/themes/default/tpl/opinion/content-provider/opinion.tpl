<div data-content-id="{$content->id}" data-class="Opinion" class="content-provider-element {schedule_class item=$content} {suggested_class item=$content} clearfix">
    <div class="description">
        <input type="checkbox" class="action-button" name="selected-{$smarty.foreach.opinions_loop.index}">
        <div class="title">
            <span class="type">{t}Opinion{/t}</span>
            {$content->author->name} - {$content->title}
        </div>
    </div>
    <div class="content-action-buttons btn-group">
        <a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="#">
            <i class="icon-cog"></i>
            <span class="caret"></span>
        </a>
        <ul class="dropdown-menu">
            <li>
                <a title="{t 1=$content->title}Edit '%1'{/t}" href="/admin/controllers/opinion/opinion.php?action=read&amp;id={$content->id}">
                    <i class="icon-pencil"></i> {t}Edit{/t}
                </a>
            </li>
            <li>
                <a href="#" title="{t}Delete{/t}" class="drop-delete">
                    <i class="icon-trash"></i> {t}Remove{/t}
                </a>
            </li>
            <li>
                <a title="{t}Arquive{/t}" href="#" class="arquive">
                    <i class="icon-inbox"></i> {t}Arquive{/t}
                </a>
            </li>
            <li>
                {if !$params['home']}
                <a title="{t}Suggest to home{/t}" href="#" class="suggest-to-home">
                    <i class="icon-home"></i> {t}Suggest to home{/t}
                </a>
                {/if}
            </li>
            <li class="divider"></li>
            <li>
                <a href="{$smarty.server.PHP_SELF}?action=delete&amp;id={$content->id}&amp;category={$category}" title="{t}Delete{/t}" class="send-to-trash">
                    <i class="icon-trash"></i> {t}Send to trash{/t}
                </a>
            </li>
        </ul>
    </div>
</div>