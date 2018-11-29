<div data-content-id="{$content->id}" data-class="{get_class($content)}"
    {getProperty item=$content category=$params['category'] property='bgcolor, title' style='true'}
    data-bg ='{getProperty item=$content category=$params['category'] property='bgcolor'}'
    data-title='{getProperty item=$content category=$params['category'] property='title'}'
    data-format ='{getProperty item=$content category=$params['category'] property='format'}'
    class="content-provider-element {schedule_class item=$content} {suggested_class item=$content} clearfix">
    <div class="description">
        <div class="checkbox check-default">
          <input class="action-button" name="selected-{$content->id}" id="checkbox-{$content->id}" checklist-model="selected.contents" checklist-value="{$content->id}" type="checkbox">
          <label for="checkbox-{$content->id}"></label>
        </div>
        <div class="title">
            <span class="type">{$content->content_type_l10n_name}</span>
             {$content->title}
        </div>
    </div>
    <div class="content-action-buttons btn-group">
        <a href="#" class="btn btn-mini info">
            <i class="fa fa-info"></i>
        </a>
        <a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="#">
            <i class="fa fa-cog"></i>
            <span class="caret"></span>
        </a>
        <ul class="dropdown-menu pull-right dropdown-menu-right no-padding">
            <li>
                <a title="{t 1=$content->title}Edit '%1'{/t}" href="{url name=admin_opinion_show id=$content->id}">
                    <i class="fa fa-pencil"></i> {t}Edit{/t}
                </a>
            </li>
            <li>
                <a title="{t}Remove element from this frontpage{/t}" href="#" class="drop-element">
                    <i class="fa fa-times"></i> {t}Remove from this frontpage{/t}
                </a>
            </li>
            {is_module_activated name="ADVANCED_FRONTPAGE_MANAGER"}
            <li>
                <a title="{t}Customize in frontpage{/t}" href="#" class="change-color">
                    <i class="fa fa-cog"></i> {t}Customize content{/t}
                </a>
            </li>
            {/is_module_activated}
            <li>
                <a title="{t}Remove from all frontpages{/t}" href="#" class="arquive">
                    <i class="fa fa-inbox"></i> {t}Arquive{/t}
                </a>
            </li>
            <li class="divider"></li>
            <li>
                <a href="{url name=backend_ws_content_send_to_trash id=$content->id contentType=$content->content_type_name}" title="{t}Delete{/t}" class="send-to-trash">
                    <i class="fa fa-trash"></i> {t}Send to trash{/t}
                </a>
            </li>
        </ul>
    </div>
</div>
