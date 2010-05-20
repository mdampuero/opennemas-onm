{toolbar_button toolbar="toolbar-top"
    icon="save" type="submit" text="Save"}

{toolbar_route toolbar="toolbar-top"
    icon="close" route="widget-index" text="Cancel"}    
    
<div id="menu-acciones-admin">
    <div style="float: left; margin-left: 10px; margin-top: 10px;">
        <h2>{t}Widget Manager{/t}</h2>
    </div>
    {toolbar name="toolbar-top"}
</div>

<div style="float: left;">
<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo">
<tbody>
<tr>
    <td valign="top" align="right" style="padding:4px;" width="150">
        <label for="title">{t}Name of widget{/t}:</label>
    </td>
    <td valign="top">
        <input type="text" id="title" name="title" title="Nombre del widget" value="{$widget->title}"
               class="required" size="30" maxlength="60" />						
    </td>
</tr>

<tr>
    <td valign="top" align="right" style="padding:4px;">
        <label for="renderlet">{t}Tipo de contenido{/t}:</label>
    </td>
    <td valign="top">
        <select name="renderlet" id="renderlet">
            <option value="html" {if $widget->renderlet == 'html'}selected="selected"{/if}>HTML</option>
            <option value="php" {if $widget->renderlet == 'php'}selected="selected"{/if}>PHP</option>
            <option value="smarty" {if $widget->renderlet == 'smarty'}selected="selected"{/if}>Smarty</option>            
        </select>
    </td>
</tr>

<tr>
    <td valign="top" align="right" style="padding:4px;">
        <label>{t}Contenido{/t}:</label>        
    </td>
    <td valign="top">
        <textarea cols="80" rows="20" id="content" name="content">{$widget->content}</textarea>
    </td>
</tr>

</tbody>
</table>
</div>

<div style="float: right; width: 520px;">
    {ui_container title="Categories"}
        {pane_categories content=$widget}
    {/ui_container}
    
    {ui_container title="Publishing" hidden=true}
        {pane_publishing content=$widget}
    {/ui_container}        
    
    {ui_container title="SEO" hidden=true}
        {pane_seo content=$widget}
    {/ui_container}
    
    {ui_container title="Information" hidden=true}
        {pane_info content=$widget}
    {/ui_container}
</div>

{if ($request->getActionName() eq "update")}
<input type="hidden" name="pk_content" value="{$widget->pk_content}" />
<input type="hidden" name="version" value="{$widget->version}" />
{/if}

{literal}        
<script language="Javascript" type="text/javascript">
/* <![CDATA[ */    
// Init editArea
editAreaLoader.init({
    id: 'content',	// id of the textarea to transform		
    start_highlight: true,	// if start with highlight
    allow_resize: "both",
    allow_toggle: true,
    word_wrap: true,
    toolbar: "search,go_to_line,|,undo,redo,|,select_font,|,syntax_selection,|,change_smooth_selection,highlight, reset_highlight",
    language: "es",
    syntax: "{/literal}{$widget->renderlet|default:"html"}{literal}",
    syntax_selection_allow: "html,php,smarty"
});

// FIXME: fix toolbar
submitForm = function() {
    $('#content').get(0).value = editAreaLoader.getValue('content');
    enviar($('formulario'), '_self', 'save', $('id').value);
};

// TODO: interesa¿?¿? 
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
{/literal}

{* editAreaLoader.execCommand('content', "change_syntax", '{$widget->renderlet|default:"html"}'); *}


