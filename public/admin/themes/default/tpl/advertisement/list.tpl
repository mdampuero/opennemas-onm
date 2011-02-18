
<ul class="tabs2" style="margin-bottom: 28px;">
        <li>
        <a href="{$smarty.server.SCRIPT_NAME}?action=list&category=0" id="link_home" {if $category==0} style="color:#000000; font-weight:bold; background-color:#BFD9BF"{/if}>HOME</font></a>
        </li>
        <li>
        <a href="{$smarty.server.SCRIPT_NAME}?action=list&category=4" id="link_opinion"  {if $category==4} style="color:#000000; font-weight:bold; background-color:#BFD9BF"{/if}>OPINIÓN</font></a>
        </li>
        <li>
        <a href="{$smarty.server.SCRIPT_NAME}?action=list&category=3" id="link_gallery"  {if $category==3} style="color:#000000; font-weight:bold; background-color:#BFD9BF"{/if}>GALERÍAS</font></a>
        </li>
        <script defer="defer" type="text/javascript">
            // <![CDATA[
            Event.observe($('link_home'), 'mouseover', function(event) {
               $('menu_subcats').setOpacity(0);
               e = setTimeout("show_subcat('{$category}','{$home|urlencode}');$('menu_subcats').setOpacity(1);",1000);
              });
            Event.observe($('link_opinion'), 'mouseover', function(event) {
               $('menu_subcats').setOpacity(0);
               e = setTimeout("show_subcat('{$category}','{$home|urlencode}');$('menu_subcats').setOpacity(1);",1000);
              });
            Event.observe($('link_gallery'), 'mouseover', function(event) {
               $('menu_subcats').setOpacity(0);
               e = setTimeout("show_subcat('{$category}','{$home|urlencode}');$('menu_subcats').setOpacity(1);",1000);
              });
            // ]]>
        </script>
    {include file="menu_categorys.tpl" home=$smarty.server.SCRIPT_NAME|cat:"?action=list"}
</ul>
<br style="clear: both;" />

<div id="{$category}">

{include file="botonera_up.tpl"}

<script type="text/javascript">

    function submitFilters(frm) {
        $('action').value='list';
        $('page').value = 1;

        frm.submit();
    }

</script>

<table class="adminheading">
	<tr>
		<th nowrap="nowrap" align="right">
            <label>Tipo de banner:
            <select name="filter[type_advertisement]" onchange="submitFilters(this.form);">
                {html_options options=$filter_options.type_advertisement
                              selected=$smarty.request.filter.type_advertisement}
            </select></label>
            &nbsp;&nbsp;&nbsp;

            <label>Estado:
            <select name="filter[available]" onchange="submitFilters(this.form);">
                {html_options options=$filter_options.available
                              selected=$smarty.request.filter.available}
            </select></label>
            &nbsp;&nbsp;&nbsp;

            <label>Tipo:
            <select name="filter[type]" onchange="submitFilters(this.form);">
                {html_options options=$filter_options.type
                              selected=$smarty.request.filter.type}
            </select></label>

            {* $_REQUEST['page'] => $_POST['page'] is more important that $_GET['page'], see also php.ini - variables_order *}
            <input type="hidden" id="page" name="page" value="{$smarty.request.page|default:"1"}" />
        </th>
	</tr>
</table>

<table class="adminlist">
<thead>
<tr>
    <th></th>
    <th class="title">Tipo</th>
    <th>Título</th>
    <th align="center">Permanencia</th>
    <th align="center">Clicks</th>
    <th align="center">Visto</th>
    <th align="center">Tipo</th>
    <th align="center">Publicado</th>
    <th align="center">Modificar</th>
    <th align="center">Eliminar</th>
</tr>
</thead>

<tbody>
{section name=c loop=$advertisements}
<tr {cycle values="class=row0,class=row1"}>
    <td style="text-align:center;font-size: 11px;width:5%;">
        <input type="checkbox" class="minput" id="selected_{$smarty.section.c.iteration}" name="selected_fld[]"
            value="{$advertisements[c]->pk_advertisement}" />
    </td>
	<td style="font-size: 11px;">
        <label for="title">
            {assign var="type_advertisement" value=$advertisements[c]->type_advertisement}
            {$map.$type_advertisement}
        </label>
	</td>
	<td style="font-size: 11px;">
		{$advertisements[c]->title|clearslash}
	</td>

    <td style="text-align:center;font-size: 11px;width:80px;" align="center">
		{if $advertisements[c]->type_medida == 'NULL'} Indefinida {/if}
		{if $advertisements[c]->type_medida == 'CLIC'} Clicks: {$advertisements[c]->num_clic} {/if}
		{if $advertisements[c]->type_medida == 'VIEW'} Visionados: {$advertisements[c]->num_view} {/if}
		{if $advertisements[c]->type_medida == 'DATE'}
            Fecha: {$advertisements[c]->starttime|date_format:"%d:%m:%Y"}-{$advertisements[c]->endtime|date_format:"%d:%m:%Y"}
        {/if}
	</td>

	<td style="text-align:center;font-size: 11px;width:105px;" align="right">
		{$advertisements[c]->num_clic_count}
	</td>
	<td style="text-align:center;font-size: 11px;width:40px;" align="right">
		 {$advertisements[c]->views}
	</td>
    <td style="text-align:center;font-size: 11px;width:70px;" align="center">
        {if $advertisements[c]->with_script == 1}
            <img src="{$params.IMAGE_DIR}iconos/script_code_red.png" border="0"
                 alt="Javascript" title="Javascript" />
        {else}
            <img src="{$params.IMAGE_DIR}iconos/picture.png" border="0" alt="Multimedia"
                 title="Elemento multimedia (flash, imagen, gif animado)" />
        {/if}
    </td>
	<td style="text-align:center;width:70px;" align="center">
		{if $advertisements[c]->available == 1}
			<a href="?id={$advertisements[c]->id}&amp;action=available_status&amp;category={$category}&amp;status=0&amp;&amp;page={$paginacion->_currentPage}&amp;{$query_string}"
                title="Publicado">
				<img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicado" /></a>
		{else}
			<a href="?id={$advertisements[c]->id}&amp;action=available_status&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage}&amp;{$query_string}"
                title="Pendiente">
				<img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Pendiente" /></a>
		{/if}
	</td>

	<td style="text-align:center;width:70px;" align="center">
		<a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$advertisements[c]->id}');" title="Modificar">
			<img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
	</td>

	<td style="text-align:center;width:70px;" align="center">
		<a href="#" onClick="javascript:confirmar(this, '{$advertisements[c]->id}');" title="Eliminar">
			<img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
	</td>

</tr>
{sectionelse}
<tr>
	<td align="center" colspan="10">
        <h2>No hay ninguna publicidad guardada en esta sección</h2>
    </td>
</tr>
{/section}
</tbody>

<tfoot >
    <tr>
        <td colspan="10" style="font-size: 12px;" align="right" class="pagination">
            {if count($advertisements) gt 0}
                {$paginacion->links}
            {/if}
        </td>
    </tr>
</tfoot>

</table>

