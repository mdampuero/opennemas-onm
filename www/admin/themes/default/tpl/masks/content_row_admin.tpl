{**
 *
 * $mask: Mask
 * $content: Content, also $mask->getContent()
 * $page: Page, also $mask->getPage()
 * $args: array, Associative array with custom properties
*}

<div class="ui-widget content-row" data-pk_content="{$content->pk_content}" data-mask="{$mask->getName()}">
    <div class="ui-widget-header ui-corner-top">
        {$content->title}                
    </div>
    
    <div class="ui-widget-content ui-corner-bottom">
        <div class="clearfix">{$args.preview}</div>
    </div>
</div>

{* <div class="content-row clearfix" data-pk_content="{$content->pk_content}" data-mask="{$mask->getName()}">
    
    <span class="title">{$content->title}</span>        
    
    <a href="{$content->pk_content}" title="{t}Edit{/t}">
        <img src="{$params.IMAGE_DIR}edit.png" border="0" alt="" /></a>
    
    <a href="{$content->pk_content}" title="{t}Send to Trash{/t}">
        <img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
    
    <div style="float: right">
        <select id="masks-{$content->pk_content}" class="masks">
            {mask_select item=$content page=$mask->getPage() selected=$mask->getName()}
        </select>
    </div>        
    
</div> *}
