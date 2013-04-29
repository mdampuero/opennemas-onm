<div data-content-id="{$content->id}" data-class="Article" {getProperty item=$content category=$params['category'] property='bgcolor' style="true"}
    data-title='{getProperty item=$content category=$params['category'] property='title'}'
    data-bg ='{getProperty item=$content category=$params['category'] property='bgcolor'}'
    data-format ='{getProperty item=$content category=$params['category'] property='format'}'
    class="content-provider-element {schedule_class item=$content} {suggested_class item=$content} {in_frontpage_class item=$content} clearfix">
    <div class="description">
        <input type="checkbox" class="action-button" name="selected-{$content->id}">
        <div class="title">
            {if $content->in_frontpage && ($params['home'] != true)}<span class="in_frontpage"></span>{/if}
            {if !($content->in_frontpage) && ($params['home'] != true)}<i class="icon-star content-icon-suggested"></i>{/if}
            <span class="type">{t}Article{/t}</span>
            {$content->title}

        </div>
    </div>
    <div class="content-action-buttons btn-group">
        <a href="#" class="btn btn-mini info">
            <i class="icon-info-sign"></i>
        </a>
        <a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="#">
            <i class="icon-cog"></i>
            <span class="caret"></span>
        </a>
        <ul class="dropdown-menu pull-right">
            <li>
                <a title="{t 1=$content->title}Edit '%1'{/t}" href="{url name=admin_article_show id=$content->id category=$category}">
                    <i class="icon-pencil"></i> {t}Edit{/t}
                </a>
            </li>
            <li>
                <a title="{t}Remove element from this frontpage{/t}" href="#" class="drop-element">
                    <i class="icon-remove"></i> {t}Remove from this frontpage{/t}
                </a>
            </li>
            {is_module_activated name="AVANCED_FRONTPAGE_MANAGER"}
            <li>
                <a title="{t}Customize in frontpage{/t}" href="#" class="change-color">
                    <i class="icon-cog"></i> {t}Customize content{/t}
                </a>
            </li>
            {/is_module_activated}
            <li>
                <a title="{t}Remove from all frontpages{/t}" href="#" class="arquive">
                    <i class="icon-inbox"></i> {t}Arquive{/t}
                </a>
            </li>
            <li>
                {if !$content->in_frontpage && ($params['home'] != true)}
                <a title="{t}Suggest this element to home{/t}" href="#" class="suggest-to-home">
                    <i class="icon-star"></i> {t}Suggest to home{/t}
                </a>
                <a title="{t}Unsuggest this element to home{/t}" href="#" class="remove suggest-to-home">
                    <i class="icon-star-empty"></i> {t}Unsuggest to home{/t}
                </a>
                {/if}
            </li>
            <li class="divider"></li>
            <li>
                <a href="{url name=admin_article_delete id=$content->id category=$category}" title="{t}Delete{/t}" class="send-to-trash">
                    <i class="icon-trash"></i> {t}Send to trash{/t}
                </a>
            </li>
        </ul>
    </div>
</div>