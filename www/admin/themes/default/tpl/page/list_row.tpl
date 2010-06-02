<span class="page-title">{$page->title}</span>

<span>{$page->menu_title}</span>
<span>{$page->status}</span>
<span>{$page->type}</span>

<span class="page-actions">
    {if $page->type == 'STANDARD' || $page->type == 'NOT_IN_MENU'}
        <a href="{baseurl}/{url route="frontpage-edit" pk_page=$page->pk_page}">{t}Edit frontpage{/t}</a>
    {/if}
    <a href="{baseurl}/{url route="page-update" id=$page->pk_page}">{t}Update{/t}</a>
    <a href="{baseurl}/{url route="page-delete" id=$page->pk_page}">{t}Delete{/t}</a>
</span>