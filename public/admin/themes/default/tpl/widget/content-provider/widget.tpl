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
                <a href="#" title="{t}Delete{/t}"  class="delete">{t}Delete{/t}</a>
            </li>
            <li>
                <a title="{t 1=$content->title}Edit '%1'{/t}" href="/admin/controllers/widget/widget.php?action=read&amp;id={$content->id}">
                    {t}Edit{/t}
                </a>
            </li>
            <li>
                <a title="{t}Suggest to home{/t}" href="#">
                    {t}Suggest to home{/t}
                </a>
            </li>
            <li class="divider"></li>
            <li>
                <a title="{t}Settings{/t}" href="#">
                    {t}Settings{/t}
                </a>
            </li>
        </ul>
    </div>
</div>