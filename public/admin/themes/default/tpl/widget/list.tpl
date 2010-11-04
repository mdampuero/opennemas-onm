{literal}
<style>
div#pagina td, div#pagina th {
    font-size:11px;
    height:24px;
    padding:0 10px;
}
</style>
{/literal}

<div id="menu-acciones-admin">
<div style="float: left; margin-left: 10px; margin-top: 10px;"><h2>{t}Widget Manager{/t}</h2></div>
<ul>
    <li>
        <a href="#" class="admin_add" onClick="enviar($('formulario'), '_self', 'new', -1);"
           title="{t}New widget{/t}">
            <img border="0" src="{$params.IMAGE_DIR}list-add.png" title="" alt="" />
            <br />{t}New{/t}
        </a>
    </li>    
</ul>
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
        {if $widgets[wgt]->available == 1}
			<a href="?id={$widgets[wgt]->pk_widget}&amp;action=changeavailable" class="switchable" title="{t}Published{/t}">
				<img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="{t}Published{/t}" /></a>
		{else}
            <a href="?id={$widgets[wgt]->pk_widget}&amp;action=changeavailable" class="switchable" title="{t}Pending{/t}">
				<img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="{t}Pending{/t}" /></a>
		{/if}        
	</td>	
	
	<td width="24">
		<a href="#" onClick="javascript:enviar($('formulario'), '_self', 'read', '{$widgets[wgt]->pk_widget}');" title="{t}Edit{/t}">
			<img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
	</td>
	<td width="24">
		<a href="#" onClick="javascript:confirmar($('formulario'), '{$widgets[wgt]->pk_widget}');" title="{t}Delete{/t}">
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

{* Ajax button to change availability *}
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}switcher_flag.js"></script>
<script type="text/javascript" language="javascript">
/* <![CDATA[ */
{literal}
document.observe('dom:loaded', function() {
    $('pagina').select('a.switchable').each(function(item){
        new SwitcherFlag(item);
    });
});
{/literal}
</script>
