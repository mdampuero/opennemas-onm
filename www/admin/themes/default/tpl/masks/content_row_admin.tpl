<div class="content-row clearfix" data-pk_content="{$item->pk_content}" data-mask="{$mask}">
    
    <span class="title">{$item->title}</span>        
    
    <a href="{$item->id}" title="{t}Edit{/t}">
        <img src="{$params.IMAGE_DIR}edit.png" border="0" alt="" /></a>
    
    <a href="{$item->id}" title="{t}Send to Trash{/t}">
        <img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
    
    <div style="float: right">
        <select id="masks-{$item->pk_content}" class="masks">
            {mask_select item=$item page=$args.page selected=$mask}
        </select>
    </div>
    
</div>