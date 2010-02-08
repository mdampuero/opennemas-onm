
<table border=0 cellpadding=0 cellspacing=0>
<tr><td valign='top' align='right'>
	<ul class="tabs">
            <li>
		<a href="article.php?action=list_hemeroteca&category=todos" {if $category=='todos' } style="color:#000000; font-weight:bold; background-color:#BFD9BF"{/if}>TODOS</font></a>
            </li>
	</ul>
</td><td>
	{include file="menu_categorys.tpl" home="article.php?action=list_hemeroteca"}
</td></tr></table>
<br><br>
 {if $smarty.get.alert eq 'ok'}

     <script type="text/javascript" language="javascript">
    {literal}
           alert('{/literal}{$smarty.get.msg}{literal}');
    {/literal}
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
	<tr>
<!--<th>ID</th>-->
		<th class="title">Título</th>
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
	  </tr>

<input type="hidden"  id="category" name="category" value="{$category}"> 

{section name=c loop=$articles}
<tr {cycle values="class=row0,class=row1"}>
	<td style="padding:10px;font-size: 11px;width:40%;">
		 <input type="checkbox" class="minput" pos=1 id="selected_fld_des_{$smarty.section.c.iteration}" name="selected_fld[]" value="{$articles[c]->id}"  style="cursor:pointer;" >
		<a href="#" onClick="javascript:enviar(this, '_self', 'only_read', '{$articles[c]->id}');" title="{$articles[c]->title|clearslash}"> {$articles[c]->title|clearslash}</a>
	</td>
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
                        <img src="{php}echo($this->image_dir);{/php}archive_no2.png" border="0" alt="Publicar" /></a>
	</td>
	<td style="padding:10px;width:10%;" align="center">
		{* <a href="#" onClick="javascript:enviar(this, '_self', 'only_read', '{$articles[c]->id}');" title="Consultar">
			<img src="{php}echo($this->image_dir);{/php}visualizar.png" border="0" /></a> *}
            
        {* Previsualizar *}
        <a href="{$articles[c]->permalink}" target="_blank" accesskey="P" onmouseover="return escape('<u>P</u>revisualizar');" onclick="UserVoice.PopIn.showPublic('{$articles[c]->permalink}');return false;" >            
            <img border="0" src="{php}echo($this->image_dir);{/php}preview_small.png" title="Previsualizar" alt="Previsualizar" /></a>
        
	</td>
	<td  align="center" style="text-align: center;width:5%;">
				<a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$articles[c]->id}');" title="Editar">
					<img src="{php}echo($this->image_dir);{/php}edit.png" border="0" alt="Editar" /></a>
			</td>
	<td style="padding:10px;width:70px;" align="center">
		<a href="#" style="cursor:pointer" onClick="javascript:delete_article('{$articles[c]->id}','{$category}',0);" title="Eliminar">
			<img src="{php}echo($this->image_dir);{/php}trash.png" border="0" /></a>
	</td>
</tr>
{sectionelse}
<tr>
	<td align="center" colspan=4><br><br><p><h2><b>Ninguna noticia guardada</b></h2></p><br><br></td>
</tr>
{/section}
{if count($articles) gt 0}
<tr>
<td colspan="10" style="padding:10px;font-size: 12px;" align="center"><br><br>{$paginacion->links}<br><br></td>
</tr>
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

