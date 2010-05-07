<div id="menu-acciones-admin">
<ul>
    <li>
        <a href="#" class="admin_add" onClick="javascript:submitForm();"
           value="Guardar" title="Guardar">
            <img border="0" src="{$params.IMAGE_DIR}save.gif" title="{t}Save{/t}" alt="{t}Save{/t}" /><br />
            {t}Save{/t}
        </a>
    </li>
    <li>
        <a href="?action=list" class="admin_add" value="{t}Cancel{/t}" title="{t}Cancel{/t}">
            <img border="0" src="{$params.IMAGE_DIR}cancel.png" title="{t}Cancel{/t}" alt="{t}Cancel{/t}" /><br />
            {t}Cancel{/t}
        </a>
    </li>
</ul>
</div>

<div id="warnings-validation"></div>

<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo">
<tbody>
<tr>
    <td valign="top" align="right" style="padding:4px;" width="150px">
        <label for="title">{t}Name of widget{/t}:</label>
    </td>
    <td>
        <input type="text" id="title" name="title" title="Nombre del widget" value="{$widget->title}"
               class="required" size="30" maxlength="60" />						
    </td>
</tr>
<tr>
    <td valign="top" align="right" style="padding:4px;">
        <label for="renderlet">{t}Tipo de contenido{/t}:</label>
    </td>
    <td>
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
    <td>
        <textarea cols="80" rows="20" id="content" name="content">{$widget->content}</textarea>
    </td>
</tr>

<tr>
    <td valign="top" align="right" style="padding:4px;">
        <label for="available">{t}Publicado{/t}:</label>
    </td>
    <td>
        <select name="available" id="available">
            <option value="1" {if $widget->available == 1}selected="selected"{/if}>Si</option>
            <option value="0" {if $widget->available == 0}selected="selected"{/if}>No</option>
        </select>
    </td>
</tr>

<tr>
    <td valign="top" align="right" style="padding:4px;">
        <label for="metadata">{t}Metadata{/t}:</label>
    </td>
    <td>
        <input type="text" name="metadata" id="metadata" value="{$widget->metadata}" />            
    </td>
</tr>

<tr>
    <td valign="top" align="right" style="padding:4px;">
        <label for="description">{t}Descripción{/t}:</label>
    </td>
    <td>
        <textarea name="description" id="description" cols="40" rows="5">{$widget->description}</textarea>
    </td>
</tr>

</tbody>
</table>

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
/* ]]> */
</script>
{/literal}

{* editAreaLoader.execCommand('content', "change_syntax", '{$widget->renderlet|default:"html"}'); *}


