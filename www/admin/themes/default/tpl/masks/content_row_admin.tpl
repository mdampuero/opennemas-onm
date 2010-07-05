{**
 * content_row_admin.tpl
 *
 * $mask: Mask
 * $content: Content, also $mask->getContent()
 * $contentBox: ContentBox, alse $mask->getContentBox()
 * $page: Page, also $mask->getPage()
 * $args: array, Associative array with custom properties
*}

<div class="ui-widget content-box" data-pk_content="{$content->pk_content}" data-mask="{$mask->getName()}">
    <div class="ui-widget-header ui-corner-top ui-helper-clearfix">
        <span>{$content->title}</span>
        
        <ul class="ui-helper-reset">
            <li><span data-action="toggle" class="ui-icon ui-icon-minus"></span></li>       
            <li><span data-action="repaint" class="ui-icon ui-icon-newwin"></span></li>
            <li><span data-action="drop" class="ui-icon ui-icon-circle-close"></span></li>
        </ul>
        
        <div class="content-box-masks ui-corner-all">{mask_list page=$page item=$content}</div>
    </div>
    
    <div class="ui-widget-content ui-corner-bottom">
        <div class="clearfix">{$args.preview}</div>
    </div>
</div>
