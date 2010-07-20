{toolbar_button toolbar="toolbar-top"
    icon="save" type="submit" text="Save"}

{toolbar_route toolbar="toolbar-top"
    icon="close" route="staticpage-index" text="Cancel"}    
    
<div id="menu-acciones-admin">
    <div style="float: left; margin-left: 10px; margin-top: 10px;">
        <h2>{t}Static Page Manager{/t}</h2>
    </div>
    {toolbar name="toolbar-top"}
</div>

<div style="float: left;">
<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo">
<tbody>
<tr>
    <td valign="top" align="right" style="padding:4px;" width="80">
        <label for="title">{t}Page title{/t}:</label>
    </td>
    <td valign="top">
        <input type="text" id="title" name="title" title="{t}Page title{/t}" value="{$staticpage->title}"
               class="required" size="30" maxlength="60" />						
    </td>
</tr>

<tr>
    <td valign="top" align="right" style="padding:4px;">
        <label>{t}Body{/t}:</label>        
    </td>
    <td valign="top">
        <textarea cols="80" rows="20" id="body" name="body">{$staticpage->body}</textarea>
    </td>
</tr>

</tbody>
</table>
</div>

<div style="float: right; width: 560px;">
    {ui_container title="Publishing"}
        {pane_publishing value=$staticpage}
    {/ui_container}
    
    {ui_container title="Categories"}
        {pane_categories value=$staticpage}
    {/ui_container}
    
    {ui_container title="SEO" hidden=true}
        {pane_seo value=$staticpage route_slugit="content-slugit" route_keywords="keyword-service"}
    {/ui_container}
    
    {ui_container title="Inner options" hidden=true}
        {pane_innerpage content=$staticpage}
    {/ui_container}
    
    {if ($request->getActionName() eq "update")}
        {ui_container title="Info" hidden=true}    
            {pane_info value=$staticpage}
        {/ui_container}
    {/if}
    
</div>

{if ($request->getActionName() eq "update")}
<input type="hidden" name="pk_content" value="{$staticpage->pk_content}" />
<input type="hidden" name="version" value="{$staticpage->version}" />
{/if}

<script type="text/javascript" src="{$params.JS_DIR}/tiny_mce/opennemas-config.js"></script>
<script type="text/javascript" language="javascript">
/* <![CDATA[ */
tinyMCE_GZ.init( OpenNeMas.tinyMceConfig.tinyMCE_GZ );

OpenNeMas.tinyMceConfig.advanced.elements = "body";	
tinyMCE.init( OpenNeMas.tinyMceConfig.advanced );

$(document).ready(function() {
    var headerOnClick = function(e) {        
        var jQ = ($(this).get(0).nodeName.toLowerCase() == 'a')? $(this).parent() : jQ = $(this);
        
        jQ.parent().find('div.ui-widget-content').toggleClass('ui-helper-hidden');
        
        if(jQ.hasClass('ui-corner-top')) {
            jQ.removeClass('ui-corner-top').addClass('ui-corner-all');
        } else {
            jQ.removeClass('ui-corner-all').addClass('ui-corner-top');
        }
                
        e.preventDefault();
        e.stopPropagation();
    };
    
    jQuery('div.ui-widget-header a').click(headerOnClick);
    jQuery('div.ui-widget-header').click(headerOnClick).css('cursor', 'pointer');    
});
/* ]]> */
</script>

{* Maintain previous filter *}
<input type="hidden" name="filter[title]" value="{$smarty.request.filter.title}" />