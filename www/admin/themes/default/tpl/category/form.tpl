{toolbar_button toolbar="toolbar-top"
    icon="save" type="submit" text="Save"}

{toolbar_route toolbar="toolbar-top"
    icon="close" route="category-index" text="Cancel"}    
    
<div id="menu-acciones-admin">
    <div style="float: left; margin-left: 10px; margin-top: 10px;">
        <h2>{t}Category Manager{/t}</h2>
    </div>
    {toolbar name="toolbar-top"}
</div>

<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo">
<tbody>
<tr>
    <td valign="top" align="right" style="padding:4px;" width="150px">
        <label for="title">{t}Title of category{/t}:</label>
    </td>
    <td>
        <input type="text" id="title" name="title" title="{t}Title of category{/t}" value="{$category->title}"
               class="required" size="30" maxlength="60" />						
    </td>
</tr>

<tr>
    <td valign="top" align="right" style="padding:4px;">
        <label for="name">{t}Name of category{/t}:</label>
    </td>
    <td>
        <input type="text" id="name" name="name" title="{t}Name of category{/t}" value="{$category->name}"
               class="required" size="30" maxlength="60" />
    </td>
</tr>

<tr>
    <td valign="top" align="right" style="padding:4px;">
        <label for="fk_category">{t}Parent category{/t}:</label>
    </td>
    <td>
        {* category_select name="fk_category" selected=$category->pk_category *}
        <select name="fk_category" id="fk_category">
            <option value="0"> -- {t}root{/t} --</option>
            {category_select selected=$category->fk_category disableRecursive=$category->pk_category}                        
        </select>                
    </td>
</tr>

</tbody>
</table>

{if ($request->getActionName() eq "update")}
<input type="hidden" name="pk_category" value="{$category->pk_category}" />
{/if}

{literal}        
<script language="Javascript" type="text/javascript">
/* <![CDATA[ */    

/* ]]> */
</script>
{/literal}

{* editAreaLoader.execCommand('content', "change_syntax", '{$widget->renderlet|default:"html"}'); *}


