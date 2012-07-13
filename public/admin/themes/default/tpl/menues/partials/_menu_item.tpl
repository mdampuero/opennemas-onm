<li class="menuItem"
    id="item_{$menuItem->pk_item}"
    data-item-id="{$menuItem->pk_item}"
    data-title="{$menuItem->title}"
    data-link="{$menuItem->link}"
    data-type="{$menuItem->type}"
    data-title="{t 1=$menuItem->title}Synchronized from %1{/t}">
    <div>
        {$menuItem->title}
        {if $menuItem->type == 'syncCategory'}
            <img src="{$params.IMAGE_DIR}sync-icon.png"
                 alt="{t}Sync{/t}">
        {/if}
        <div class="btn-group actions" style="float:right;">
            <a href="#" class="add-item"><i class="icon-plus"></i></a>
            <a href="#" class="edit-menu-item"><i class="icon-pencil"></i></a>
            <a href="#" class="delete-menu-item"><i class="icon-trash"></i></a>
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