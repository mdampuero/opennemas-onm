<div data-content-id="{$content->id}" data-class="Album" {getProperty item=$content category=$params['category'] property='bgcolor' style="true"}
    data-title='{getProperty item=$content category=$params['category'] property='title'}'
    data-bg ='{getProperty item=$content category=$params['category'] property='bgcolor'}'
    class="content-provider-element {schedule_class item=$content} {suggested_class item=$content} clearfix">
    <div class="description">
        <input type="checkbox" class="action-button" name="selected-{$smarty.foreach.album_loop.index}">
        <div class="title">
            <span class="type">Album</span>
            {$content->title}
        </div>
    </div>
    <div class="content-action-buttons btn-group">
        <a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="#">
            <i class="fa fa-cog"></i>
            <span class="caret"></span>
        </a>
        <ul class="dropdown-menu pull-right">
            <li>
                <a title="{t 1=$content->title}Edit '%1'{/t}" href="{url name=admin_album_show id=$content->id}">
                    <i class="fa fa-pencil"></i> {t}Edit{/t}
                </a>
            </li>
            <li>
                <a title="{t}Remove element from this frontpage{/t}" href="#" class="drop-element">
                    <i class="fa fa-times"></i> {t}Remove from this frontpage{/t}
                </a>
            </li>
            {is_module_activated name="AVANCED_FRONTPAGE_MANAGER"}
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
                <a href="{url name=admin_album_delete id=$content->id category=$category}" title="{t}Delete{/t}" class="send-to-trash">
                    <i class="fa fa-trash-o"></i> {t}Send to trash{/t}
                </a>
            </li>
        </ul>
    </div>
</div>


