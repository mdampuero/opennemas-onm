<li class="menuItem"
    id="item_{$menuItem->pk_item}"
    data-item-id="{$menuItem->pk_item}"
    data-title="{$menuItem->title}"
    data-link="{$menuItem->link}"
    data-type="{$menuItem->type}"
    data-title="{t 1=$menuItem->title}Synchronized from %1{/t}">
    <div>
        <span class="type">
            {if $menuItem->type == 'external'}
                {t}External link{/t}:
            {elseif $menuItem->type == 'internal'}
                {t}Module{/t}:
            {elseif $menuItem->type == 'blog-category'}
                {t}Category blog{/t}:
            {elseif $menuItem->type == 'category'}
                {t}Frontpage{/t}:
            {elseif $menuItem->type == 'albumCategory'}
                {t}Album category{/t}:
            {elseif $menuItem->type == 'pollCategory'}
                {t}Poll category{/t}:
            {elseif $menuItem->type == 'videoCategory'}
                {t}Video Category{/t}:
            {elseif $menuItem->type == 'static'}
                {t}Static Page{/t}:
            {elseif $menuItem->type == 'syncCategory'}
                {t}Sync category{/t}:
            {elseif $menuItem->type == 'syncBlogCategory'}
                {t}Sync blog category{/t}:
            {/if}
        </span>
        <span class="menu-title">
            {$menuItem->title}
        </span>
        {if $menuItem->type == 'syncCategory' || $menuItem->type == 'syncBlogCategory'}
            <img src="{$params.IMAGE_DIR}sync-icon.png"
                 alt="{t}Sync{/t}">
        {/if}
        <div class="btn-group actions" style="float:right;">
            <a href="#" class="add-item"><i class="icon-plus"></i></a>
            <a href="#" class="edit-menu-item"><i class="icon-pencil"></i></a>
            <a href="#" class="delete-menu-item"><i class="icon-trash"></i></a>
        </div>

        <div class="form-horizontal edit-menu-form">
            <fieldset>
            <div class="control-group">
                <label for="title{$menuItem->pk_item}" class="control-label">{t}Title{/t}</label>
                <div class="controls">
                    <input type="text" id="title{$menuItem->pk_item}" class="title" value="{$menuItem->title}" placeholder="{t}Menu title...{/t}">
                </div>
            </div>
            {if $menuItem->type == "external"}
            <div class="control-group">
                <label for="link{$menuItem->pk_item}" class="control-label">{t}Link{/t}</label>
                <div class="controls">
                    <input type="text" id="link{$menuItem->pk_item}" class="link" value="{$menuItem->link}" placeholder="{t}Menu link...{/t}">
                </div>
            </div>
            {/if}
            <div class="send-button-wrapper">
                <button type="submit" class="btn save-menuitem-button">{t}Update{/t}</button>
            </div>
            </fieldset>
        </div>
    </div>
    {if count($menuItem->submenu) > 0}
    <ol>
        {foreach from=$menuItem->submenu item=subMenuItem}
        {include file="menues/partials/_menu_item.tpl" menuItem=$subMenuItem}
        {/foreach}
    </ol>
    {/if}
</li>
