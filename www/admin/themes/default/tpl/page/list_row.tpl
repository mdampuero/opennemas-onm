<div data-pk_page="page-{$page->pk_page}">
    <img src="{$params.IMAGE_DIR}pages/{$page->type|lower}.png" border="0" alt="{$page->type}" />
    
    <span class="page-title" title="{$page->menu_title}">{$page->title}</span>
    
    <span class="">{$page->status}</span>
    
    &nbsp;&nbsp;&nbsp;
    
    <span class="page-actions">
        {if $page->type == 'STANDARD' || $page->type == 'NOT_IN_MENU'}
            <a href="{baseurl}/{url route="frontpage-edit" pk_page=$page->pk_page}">
                <img src="{$params.IMAGE_DIR}pages/layout_content.png" alt="" />
            </a>
        {/if}
        <a href="{baseurl}/{url route="page-update" id=$page->pk_page}">
            <img src="{$params.IMAGE_DIR}pages/page_white_edit.png" alt="" />
        </a>
        <a href="{baseurl}/{url route="page-delete" id=$page->pk_page}">
            <img src="{$params.IMAGE_DIR}pages/bin_closed.png" alt="" />
        </a>
    </span>
</div>