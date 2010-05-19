{toolbar_route toolbar="toolbar-top"
    icon="new" route="category-create" text="New Category"}
    
<div id="menu-acciones-admin">
    <div style="float: left; margin-left: 10px; margin-top: 10px;">
        <h2>{t}Category Manager{/t}</h2>
    </div>
    {toolbar name="toolbar-top"}
</div>

<table class="adminheading">
    <tbody>
        <tr>
            <th>{t}Categories{/t}</th>
        </tr>
    </tbody>
</table>

<div id="pagina">
<table border="0" cellpadding="4" cellspacing="0" class="adminlist">    
<tbody>
<tr>
    <th class="title">{t}Category name{/t}</th>
    <th class="title">{t}Path{/t}</th>
    <th class="title">{t}Parent{/t}</th>
    <th>&nbsp;</th>
    <th>&nbsp;</th>
</tr>


{foreach name="list" from=$categories item="category"}
<tr class="row{cycle values="0,1"}">
    
	<td>
		{$category->title}
	</td>
    
    <td>
        /{implode glue="/" pieces=$catMgr->getPath($category->pk_category)}
    </td>
    
    <td>
        {$catMgr->getTitle($category->fk_category)|default:'-'}
    </td>
	
	<td width="24">
		<a href="{baseurl}/{url route="category-update" id=$category->pk_category}" title="{t}Edit{/t}">
			<img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
	</td>
	<td width="24">
		<a href="{baseurl}/{url route="category-delete" id=$category->pk_category}"
           class="deletable" style="display: none;" title="{t}Delete{/t}">
			<img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
	</td>
</tr>
{foreachelse}
<tr>
	<td align="center" colspan="5"><b>{t}No categories found{/t}.</b></td>
</tr>
{/foreach}
</tbody>

<tfoot>
    <tr>
        <td colspan="5" align="center">
            {$pager->links}
        </td>            
    </tr>
</tfoot>
</table>

</div>


<script type="text/javascript">
/* <![CDATA[ */
var confirmMsg = '{t}Confirm delete this elements and descendants?{/t}';

{literal}
jQuery(function() {
    jQuery('a.deletable').click(function(evt){                
        if( confirm(confirmMsg) ) {
            return true;
        }
        
        return false;
    }).css('display', '');
});
{/literal}
/* ]]> */
</script>
