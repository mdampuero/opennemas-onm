{literal}
<style>
div#pagina td, div#pagina th {
    font-size:11px;
    height:24px;
    padding:0 10px;
}
</style>
{/literal}

{toolbar_route toolbar="toolbar-top"
    icon="new" route="widget-create" text="New Widget"}
    
<div id="menu-acciones-admin">
    <div style="float: left; margin-left: 10px; margin-top: 10px;">
        <h2>{t}Widget Manager{/t}</h2>
    </div>
    {toolbar name="toolbar-top"}
</div>

<table class="adminheading">
    <tbody>
        <tr>
            <th>{t}Widgets{/t}</th>
        </tr>
    </tbody>
</table>

<div id="pagina">
<table border="0" cellpadding="4" cellspacing="0" class="adminlist">    
<tbody>
<tr>
    <th class="title">{t}Name{/t}</th>
    <th class="title">{t}Type{/t}</th>
    <th class="title" align="center">{t}Published{/t}</th>
    <th>&nbsp;</th>
    <th>&nbsp;</th>
</tr>


{section name=wgt loop=$widgets}
<tr bgcolor="{cycle values="#eeeeee,#ffffff"}">
	<td>
		{$widgets[wgt]->title}
	</td>
    
    <td width="240">
        {$widgets[wgt]->renderlet|upper}
    </td>

	<td width="100" align="center">		
        {if $widgets[wgt]->status == "AVAILABLE"}
			<a href="{baseurl}/{url route="widget-changestatus" id=$widgets[wgt]->pk_widget status="PENDING"}"
               class="switchable" title="{t}Published{/t}">
				<img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="{t}Published{/t}" /></a>
		{else}
            <a href="{baseurl}/{url route="widget-changestatus" id=$widgets[wgt]->pk_widget status="AVAILABLE"}"
               class="switchable" title="{t}Pending{/t}">
				<img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="{t}Pending{/t}" /></a>
		{/if}        
	</td>	
	
	<td width="24">
		<a href="{baseurl}/{url route="widget-update" id=$widgets[wgt]->pk_widget}" title="{t}Edit{/t}">
			<img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
	</td>
	<td width="24">
		<a href="{baseurl}/{url route="widget-delete" id=$widgets[wgt]->pk_widget}"
           onclick="" title="{t}Delete{/t}">
			<img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
	</td>
</tr>
{sectionelse}
<tr>
	<td align="center" colspan="5"><b>{t}No widget found{/t}.</b></td>
</tr>
{/section}
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
