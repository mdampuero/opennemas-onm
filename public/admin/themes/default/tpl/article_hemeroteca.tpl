
<ul class="tabs2" style="margin-bottom: 28px;">

    <li>
        <a href="article.php?action=list_hemeroteca&category=todos" id="link_todos" {if $category=='todos'} style="color:#000000; font-weight:bold; background-color:#BFD9BF"{/if}>TODOS</font></a>
    </li>
      <script type="text/javascript">
                // <![CDATA[
                    {literal}
                          Event.observe($('link_todos'), 'mouseover', function(event) {
                             $('menu_subcats').setOpacity(0);
                             e = setTimeout("show_subcat('{/literal}{$category}','{$home|urlencode}{literal}');$('menu_subcats').setOpacity(1);",1000);

                            });

                    {/literal}
                // ]]>
            </script>
    {include file="menu_categorys.tpl" home="article.php?action=list_hemeroteca"}
</ul>

<br style="clear: both;" />
 {if $smarty.get.alert eq 'ok'}

     <script type="text/javascript" language="javascript">
           alert('{$smarty.get.msg}');
    </script>
  {/if}
{include file="botonera_up.tpl"}
<div id="{$category}">
<table class="adminheading">
	<tr>
		<td><b>Art&iacute;culos en la hemeroteca</b></td><td style="font-size: 10px;" align="right"><em> </em></td>
	</tr>
</table>

<table class="adminlist">
	<thead>
<!--<th>ID</th>-->
		<th class="title">Título</th>
        {if $category=='todos'}
            <th align="center" style="width:70px;">Secci&oacute;n</th>
        {/if}
		<th align="center" style="width:70px;">Fecha de modificación</th>
		<th align="center" style="width:70px;">Visto</th>
		<th align="center" style="width:70px;">Comentarios</th>
		<th align="center" style="width:70px;">Votaci&oacute;n</th>
		<th style="width:70px;">Publisher</th>
		<th style="width:70px;">Last Editor</th>
		<th align="center" style="width:70px;">Recuperar</th>
		<th align="center" style="width:70px;">Visualizar</th>
		<th align="center" style="width:70px;">Editar</th>
		<th align="center" style="width:70px;">Eliminar</th>
	</thead>

    <input type="hidden"  id="category" name="category" value="{$category}">

{section name=c loop=$articles}
<tr {cycle values="class=row0,class=row1"}>
	<td style="padding:10px;font-size: 11px;width:40%;">
		 <input type="checkbox" class="minput" pos=1 id="selected_fld_des_{$smarty.section.c.iteration}" name="selected_fld[]" value="{$articles[c]->id}"  style="cursor:pointer;" >
		<a href="#" onClick="javascript:enviar(this, '_self', 'only_read', '{$articles[c]->id}');" title="{$articles[c]->title|clearslash}"> {$articles[c]->title|clearslash}</a>
	</td>
    {if $category=='todos'}
        <td style="padding:10px;font-size: 11px;width:10%;" align="center">
            {$articles[c]->category_name}
        </td>
    {/if}
    <td style="padding:10px;font-size: 11px;width:10%;" align="center">
		{$articles[c]->changed}
	</td>
	<td style="text-align: center;font-size: 11px;width:5%;">
		{$articles[c]->views}
	</td>
	<td style="padding:1px;width:5%;font-size: 11px;" align="center">
		{$articles[c]->comment}
	</td>
	<td style="padding:1px;width:5%;font-size: 11px;" align="center">
		{$articles[c]->rating}
	</td>
	<td style="padding:10px;font-size: 11px;width:10%;">
		{$articles[c]->publisher}
	</td>
	<td style="padding:10px;font-size: 11px;width:10%;">
		{$articles[c]->editor}
	</td>
	<td style="padding:10px;width:10%;" align="center">
                <a href="?id={$articles[c]->id}&amp;action=change_status&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage}" title="Publicado">
                        <img src="{$params.IMAGE_DIR}archive_no2.png" border="0" alt="Publicar" /></a>
	</td>
	<td style="padding:10px;width:10%;" align="center">
		{* <a href="#" onClick="javascript:enviar(this, '_self', 'only_read', '{$articles[c]->id}');" title="Consultar">
			<img src="{$params.IMAGE_DIR}visualizar.png" border="0" /></a> *}

        {* Previsualizar *}                
        <a href="{$smarty.const.SITE_URL}{generate_uri content_type='article' id=$articles[c]->id date=$articles[c]->created category_name=$articles[c]->category_name title=$articles[c]->title}" 
           target="_blank" accesskey="P" onmouseover="return escape('<u>P</u>revisualizar');" 
           onclick="UserVoice.PopIn.showPublic('{$smarty.const.SITE_URL}{generate_uri content_type='article' id=$articles[c]->id date=$articles[c]->created category_name=$articles[c]->category_name title=$articles[c]->title}');return false;" >
            <img border="0" src="{$params.IMAGE_DIR}preview_small.png" title="Previsualizar" alt="Previsualizar" /></a>

	</td>
	<td  align="center" style="text-align: center;width:5%;">
				<a href="{$smarty.server.PHP_SELF}?action=read&id={$articles[c]->id}" title="Editar">
					<img src="{$params.IMAGE_DIR}edit.png" border="0" alt="Editar" /></a>
			</td>
	<td style="padding:10px;width:70px;" align="center">
		<a href="#" style="cursor:pointer" onClick="javascript:delete_article('{$articles[c]->id}','{$category}',0);" title="Eliminar">
			<img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
	</td>
</tr>
{sectionelse}
<tr>
	<td align="center" colspan=4><br><br><p><h2><b>Ninguna noticia guardada</b></h2></p><br><br></td>
</tr>
{/section}
{if count($articles) gt 0}
<tfoot>
<td colspan="11" style="padding:10px;font-size: 12px;" align="center" class="pagination">{$paginacion->links}</td>
</tfoot>
{/if}
</table>

<table style="width:99%">
<tr><td> &nbsp;</td></tr>
<tr align='right'>
<td>


</td>
</tr>
</table>

</div>
