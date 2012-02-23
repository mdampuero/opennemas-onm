<div data-content-id="{$content->id}" data-class="Widget" class="content-provider-element clearfix">
    <div class="description">
        <input type="checkbox" class="action-button" name="selected-{$smarty.foreach.widget_loop.index}">
        <div class="title">
            <span class="type">{t}Widget{/t}</span>
            {$content->title}
        </div>
    </div>
    <div class="content-action-buttons btn-group">
        <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
            {t}Actions{/t}
        <span class="caret"></span>
        </a>
        <ul class="dropdown-menu">
            <li>
                <a href="#" title="{t}Delete{/t}" class="delete">
                    <i class="icon-trash"></i> {t}Delete{/t}
                </a>
            </li>
            <li>
                <a title="{t 1=$content->title}Edit '%1'{/t}" href="/admin/controllers/widget/widget.php?action=read&amp;id={$content->id}">
                    <i class="icon-pencil"></i> {t}Edit{/t}
                </a>
            </li>
            <li>
                {if !$params['home']}
                <a title="{t}Suggest to home{/t}" href="#">
                    <i class="icon-home"></i> {t}Suggest to home{/t}
                </a>
                {/if}
            </li>
            <li>
                <a title="{t}Arquive{/t}" href="#">
                    <i class="icon-inbox"></i> {t}Arquive{/t}
                </a>
            </li>
        </ul>
    </div>
</div>