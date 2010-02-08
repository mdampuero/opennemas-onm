{include file="header.tpl"}

{* LISTADO ******************************************************************* *}
{if !isset($smarty.request.action) || $smarty.request.action eq "list"}

	{include file="botonera_up.tpl"}
	
<div id="{$category}">
<table class="adminheading">
	<tr>
		<th nowrap> Videos</th>
	</tr>	
</table>

<table class="adminlist">
	<tr>  
		<th class="title"></th>
	{*	<th class="title">Autor</th>*}
		<th >Título</th>
		<th align="center">Visto</th>
		<th align="center">Fecha</th>	
		<th align="center">Estado</th>	
		<th align="center">Modificar</th>
		<th align="center">Eliminar</th>
	  </tr>

{section name=c loop=$videos}
<tr {cycle values="class=row0,class=row1"}  style="cursor:pointer;">
	<td style="padding:10px;font-size: 11px;">
		<input type="checkbox" class="minput"  id="selected_{$smarty.section.c.iteration}" name="selected_fld[]" value="{$videos[c]->id}"  style="cursor:pointer;">
	</td>
	{*<td style="padding:10px;font-size: 11px;width:20%;">
		 {$authors[c]->name} 
	</td>*}
	<td style="padding:10px;font-size: 11px;width:50%;"  onClick="javascript:document.getElementById('selected_{$smarty.section.c.iteration}').click();">
		{$videos[c]->title|clearslash}
	</td>
	<td style="padding:1px;font-size: 11px;width:10%;" align="center">
		{$videos[c]->views}
	</td>
	<td style="padding:1px;width:10%;font-size: 11px;" align="center">	
			{$videos[c]->created}		
	</td>
	<td style="padding:10px;font-size: 11px;width:10%;" align="center">
		{if $videos[c]->content_status == 1}
			<a href="?id={$videos[c]->id}&amp;action=change_status&amp;status=0&amp;category={$category}&amp;page={$paginacion->_currentPage}" title="Publicado">
				<img src="{php}echo($this->image_dir);{/php}publish_g.png" border="0" alt="Publicado" /></a>
		{else}
			<a href="?id={$videos[c]->id}&amp;action=change_status&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage}" title="Pendiente">
				<img src="{php}echo($this->image_dir);{/php}publish_r.png" border="0" alt="Pendiente" /></a>
		{/if}
	</td>
	<td style="padding:10px;font-size: 11px;width:10%;" align="center">
		<a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$videos[c]->id}');" title="Modificar">
			<img src="{php}echo($this->image_dir);{/php}edit.png" border="0" /></a>
	</td>
	<td style="padding:10px;font-size: 11px;width:10%;" align="center">
		<a href="#" onClick="javascript:delete_videos('{$videos[c]->id}',{$paginacion->_currentPage});" title="Eliminar">
			<img src="{php}echo($this->image_dir);{/php}trash.png" border="0" /></a>
	</td>
</tr>

{sectionelse}
<tr>
	<td align="center" colspan=5><br><br><p><h2><b>Ningun video guardado</b></h2></p><br><br></td>
</tr>
{/section}
</table>
   {if $smarty.get.alert eq 'ok'}
         <script type="text/javascript" language="javascript">
            {literal}
                   alert('{/literal}{$smarty.get.msgdel}{literal}');
            {/literal}
            </script>
    {/if}
 
<table style="width:99%">
<tr><td> 

{if count($videos) gt 0}
<tr>
    <td colspan="3" align="center">{$paginacion->links}</td>
</tr>
{/if}

</td></tr>
<tr align='right'>
<td>

</td>
</tr>
</table>

</div>


{/if}

 {dialogo script="print"}
{* FORMULARIO PARA ENGADIR ************************************** *}

{if isset($smarty.request.action) && $smarty.request.action eq "new"}

{include file="botonera_up.tpl"}

<!-- <div class="panel" id="edicion-contenido" style="width:720px">-->
<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="700">
<tbody>

<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="title">T&iacute;tulo:</label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
		<input type="text" id="title" name="title" title="Título" onChange="javascript:get_metadata(this.value);"
			value="" class="required" size="100" />			
		
	</td>
</tr>
<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="metadata">Palabras clave: </label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
		<input type="text" id="metadata" name="metadata" size="70" title="Metadatos" value="" />
		<sub>Separadas por comas</sub>
	</td>
</tr>
<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="title">Autor:</label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
		<input type="text" id="author_name" name="author_name" title="Título"
			value=""  size="70" />

	</td>
</tr>
<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="title">Video ID:</label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
		<input type="text" id="videoid" name="videoid" size="70" title="Video ID" class="required" value="" />
	</td>
</tr>
<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="title">Código HTML:</label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
		<textarea name="htmlcode" id="htmlcode" value=""
		title="Código HTML" style="width:98%; height:6em;"></textarea>
	</td>
</tr>
<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="title">Descripcion:</label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">		
		<textarea name="description" id="description" class="required" value=" "
			title="Resumen de la noticia" style="width:98%; height:6em;">{$video->description|clearslash}</textarea>
	</td>
</tr>

</tbody>
</table>
<!-- </div> -->

{/if}


{* FORMULARIO PARA ACTUALIZAR *********************************** *}
{if isset($smarty.request.action) && $smarty.request.action eq "read"}

{include file="botonera_up.tpl"}
	
<!-- <div class="panel" id="edicion-contenido" style="width:720px"> -->
<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="700">
<tbody>
<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="title">T&iacute;tulo:</label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
		<input type="text" id="title" name="title" title="Título de la noticia"  onChange="javascript:get_metadata(this.value);"
			value="{$video->title|clearslash|escape:"html"}" class="required" size="100" />
		<input type="hidden" id="available" name="available" title="available"  
			value="{$video->available}" size="100" />	
	</td>
</tr>
<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="metadata">Palabras clave: </label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
		<input type="text" id="metadata" name="metadata" size="70" title="Metadatos" value="{$video->metadata}" />
		<sub>Separadas por comas</sub>
	</td>
</tr>
<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="title">Autor:</label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
		<input type="text" id="author_name" name="author_name" title="Título"
			value="{$video->author_name}"  size="70" />

	</td>
</tr>
<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
	
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
		<div id="imgcc"> 	  
		    <div id="nifty" style="width:520px;">
  			    <b class="rtop"><b class="r1"></b><b class="r2"></b><b class="r3"></b><b class="r4"></b></b>
		 		<table border='0' width='90%'>
					<tr>
					  <td align="center"> 
					       				    
						 <div id="ejep">							 
							<object width="416" height="150">
								<param name="movie" value="http://www.youtube.com/v/{$video->videoid}&hl=es&fs=1"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param>
								<embed src="http://www.youtube.com/v/{$video->videoid}&hl=es&fs=1" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="416" height="150"></embed></object>							
						</div>	
						<br>	
						
					 	</td></tr></table>	
				
		     </div>
		   </div>	 	

	</td>
</tr>
<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="title">Video ID:</label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
		<input type="text" id="videoid" name="videoid" value="{$video->videoid}" size="70" title="Video ID" class="required" value="" />
	</td>
</tr>
<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="title">Código HTML:</label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
		<textarea name="htmlcode" id="htmlcode" title="Código HTML" style="width:98%; height:6em;">{$video->htmlcode|clearslash}
		</textarea>
	</td>
</tr>

<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="title">Descripcion:</label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">		
		<textarea name="description" id="description" class="required" value=" "
			title="Resumen de la noticia" style="width:98%; height:6em;">{$video->description|clearslash}</textarea>
	</td>
</tr>

</tbody>
</table>
<!-- </div> -->

{/if}

{include file="footer.tpl"}