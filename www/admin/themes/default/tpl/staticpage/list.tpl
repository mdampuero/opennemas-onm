{toolbar_route toolbar="toolbar-top"
    icon="new" route="staticpage-create" text="New Page"}
    
<div id="menu-acciones-admin">
    <div style="float: left; margin-left: 10px; margin-top: 10px;">
        <h2>{t}Static Pages Manager{/t}</h2>
    </div>
    {toolbar name="toolbar-top"}
</div>

<div style="padding: 5px 0;">
	<label>Título: <input type="text" name="filter[title]" value="{$smarty.request.filter.title}" /></label>
	<button type="submit">Filtrar</button>
</div>

<table border="0" cellpadding="4" cellspacing="0" class="adminlist" id="datagrid">  
{if count($staticpages)>0}
<thead>
<tr>
    <th>{t}Title{/t}</th>	
	<th>{t}Slug{/t}</th>	
	<th class="title">{t}Hits{/t}</th>	
	<th class="title">{t}Published{/t}</th>    
    <th>&nbsp;</th>
</tr>
</thead>
<tfoot>
    <tr>
        <td colspan="5" align="center">
            {$pager->links}
        </td>            
    </tr>
</tfoot>
{/if}

<tbody>
{section name=k loop=$staticpages}
<tr bgcolor="{cycle values="#eeeeee,#ffffff"}">
	<td>		
		{$staticpages[k]->title}
	</td>	
	<td>		
		{$staticpages[k]->slug}
	</td>
	
	<td width="44">
		{$staticpages[k]->views}
		&nbsp;&nbsp;
	</td>

	<td width="44">
        {if $staticpages[k]->status == 'AVAILABLE'}
            <a href="{baseurl}/{url route="staticpage-changestatus" pk_content=$staticpages[k]->pk_content status="PENDING"}" class="available">
				<img src="{$params.IMAGE_DIR}publish_g.png" border="0" title="Publicado" /></a>
		{else}
            <a href="{baseurl}/{url route="staticpage-changestatus" pk_content=$staticpages[k]->pk_content status="AVAILABLE"}" class="available">
				<img src="{$params.IMAGE_DIR}publish_r.png" border="0" title="Pendiente" /></a>
		{/if}        
	</td>
	
	<td width="64">
		<a href="{baseurl}/{url route="staticpage-update" pk_content=$staticpages[k]->pk_content}" title="{t}Update{/t}">
			<img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
		&nbsp;&nbsp;
		<a href="{baseurl}/{url route="staticpage-delete" pk_content=$staticpages[k]->pk_content}" title="{t}Delete{/t}">
			<img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
	</td> 
</tr>
{sectionelse}
<tr>
	<td align="center" colspan="5"><h2>{t}Pages not found{/t}</h2></td>
</tr>
{/section}
</tbody>
</table>

{if count($staticpages)>0}
<script type="text/javascript">
$(document).ready(function(){
    $('#datagrid').dataTable({
        "bJQueryUI": true
    });
});    
</script>
{/if}