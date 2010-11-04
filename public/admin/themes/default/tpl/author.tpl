{include file="header.tpl"}

{* LISTADO ******************************************************************* *}
{if !isset($smarty.request.action) || $smarty.request.action eq "list"}

{include file="botonera_up.tpl"}
<table class="adminheading">
	<tr>
		 <th> Seleccione autor:
		    <select name="autores" id="autores" class="" onChange="window.location='author.php?action=read&id='+this.options[this.selectedIndex].value;">
		  	<option >   </option>
			{section name=as loop=$authors_list}
				<option value="{$authors_list[as]->pk_author}" >{$authors_list[as]->name}</option>
		    {/section}
	    </select>	
		    </th>
	</tr>	
</table>
<table border="0" cellpadding="4" cellspacing="0" class="adminlist" width="600">
<tr>
<th class="title" style="align:left;padding:10px;width:30%;">Nombre del Autor</th>
<th class="title" style="padding:10px;width:20%;">G&eacute;nero</th>
<th class="title" style="padding:10px;width:20%;">Tendencia</th>
<th class="title" style="padding:10px;width:20%;">Condici&oacute;n</th>
<th class="title" style="padding:10px;width:10%;">Foto(nº)</th>
<th class="title" style="padding:10px;width:10%;align:right;">Editar</th>
<th class="title" style="padding:10px;width:10%;align:right;">Eliminar</th>
</tr>
{section name=c loop=$authors}
<tr bgcolor="{cycle values="#eeeeee,#ffffff"}">
	<td style="padding:10px;">
		{$authors[c]->name}&nbsp;&nbsp;{*if $authors[c]->fk_user != 0}(usuario){/if*}
	</td>
	<td style="padding:10px;">
			{$authors[c]->gender}			
	</td>
	<td style="padding:10px;">
		{$authors[c]->politics}
	</td>	
	<td style="padding:10px;">
		{$authors[c]->condition}
	</td>	
	<td style="padding:10px;">
		{$authors[c]->num_photos}
	</td>
	<td style="padding:10px;align:right;">
		<a href="#" onClick="javascript:enviar(this, '_self', 'read', {$authors[c]->pk_author});" title="Modificar">
			<img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
	</td>
	<td style="padding:10px;align:right;">
		<a href="#" onClick="javascript:confirmar(this, {$authors[c]->pk_author});" title="Eliminar">
			<img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
	</td>
</tr>
{sectionelse}
<tr>
	<td align="center"><b>Ning&uacute;n author guardado.</b></td>
</tr>
{/section}
{if count($authors) gt 0}
<tr style="height: 50px;">
    <td colspan="7" align="center">{$paginacion->links}</td>
</tr>
{/if}
</table>
{/if}


{* FORMULARIO PARA ENGADIR OU MODIFICAR  ************************************** *}
{if isset($smarty.request.action) && (($smarty.request.action eq "new") || ($smarty.request.action eq "read"))}

	{include file="botonera_up.tpl"}

	<table  border="0" cellpadding="4" cellspacing="0" class="fuente_cuerpo" width="1000">
	<tr><td valign="top">
			<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo">
			<tbody>
			
			<!-- Nome -->
			<tr>
				<td valign="top" align="right" style="padding:4px;" width="40%">
					<label for="name">Nombre:</label>
				</td>
				<td style="padding:4px;" nowrap="nowrap" width="60%">
					<input type="text" id="name" name="name" title="Nombre del usuario"
						value="{$author->name}" class="required"  size="50"/>						
				</td>
			</tr>
			<tr> 
						<td valign="top" align="right" style="padding:4px;">
							<label for="phone">Condici&oacute;n:</label>
						</td>
						<td style="padding:4px;" nowrap="nowrap">
							<input type="text" id="condition" name="condition" title="Condicion"
								value="{$author->condition}"  size="50"/>
						</td>
					</tr>	
			<tr>
			<tr> 
						<td valign="top" align="right" style="padding:4px;">
							<label for="phone">Tendencia Pol&iacute;tica:</label>
						</td>
						<td style="padding:4px;" nowrap="nowrap">							
								<select name="politics" id="politics" class="required" title="Tendencia politica">
									<option value="Progresista" {if $author->politics eq 'Progresista'} selected {/if}>Progresista</option>
									<option value="Conservador" {if $author->politics eq 'Conservador'} selected {/if}>Conservador</option>
									<option value="Izquierdas" {if $author->politics eq 'Izquierdas'} selected {/if}>Izquierdas</option>
									<option value="Derechas" {if $author->politics eq 'Derechas'} selected {/if}>Derechas</option>
									<option value="Centro" {if $author->politics eq 'Centro'} selected {/if}>Centro</option>
									<option value="Comunista" {if $author->politics eq 'Comunista'} selected {/if}>Comunista</option>
							 </select>
						</td>
					</tr>	
			<tr>
						<td valign="top" align="right" style="padding:4px;">
							<label for="title">Sexo:</label>
						</td>
						<td style="padding:4px;" nowrap="nowrap">					    	
						     <select name="gender" id="gender" class="required">
								<option value="Mujer" {if $author->gender eq 'Mujer'} selected {/if}>Mujer</option>
								<option value="Hombre" {if $author->gender eq 'Hombre'} selected {/if}>Hombre</option>
							</select>
					</td>
					</tr>	
					
					<tr>
						<td valign="top" align="right" style="padding:4px;" >
							<label for="phone">Fecha de Nacimiento:</label>
						</td>
						<td style="padding:4px;" nowrap="nowrap">				
									<input type="text" id="date_nac" name="date_nac" size="18" title="Fecha de nacimiento"
										value="{$author->date_nac}" /><button id="triggerend">...</button>
						</td>
					</tr>	
			
			
			</tbody>
			</table>
	</td>
	<td> <!-- Si es de opinion necesita datos autor -->
		<div id="photograph">
				<table border=0>
					
					<tr> 
					   <td> </td>
						<td valign="top" style="padding:4px;border:1px solid #CCCCCC">
						<b>Fotos autor:</b><br>
						  	<div id="contenedor" name="contenedor" style="display:none; width:99%;"> </div>
							<div class="photos" style="width:650px; padding:8px; ">
									 <ul id='thelist'  class="gallery_list" style="width:600px;"> 
										{section name=as loop=$photos}
										<li id='{$photos[as]->pk_img}'>	<div style="float: left;width:90px;"> <a>
												<img src="{$MEDIA_IMG_PATH_URL}{$photos[as]->path_img}" id="{$photos[as]->pk_img}" width="67"  border="1" /></a> 
												<br>{$photos[as]->description}</div>												
													<a href="#" onclick="javascript:del_photo('{$photos[as]->pk_img}');" title="Eliminar foto">
												     <img src="{$params.IMAGE_DIR}iconos/eliminar.gif" border="0" align="absmiddle" /></a>&nbsp;
												
										</li>
										{/section}									
									 </ul>
							</div>
							<input type="hidden" id="del_img" name="del_img" value="">
						
								<input type="hidden" id="fk_author_img" name="fk_author_img"
										value="" />	
							</td>
						</tr>
						<tr><td nowrap valign="top"></td>
						    <td style="padding:4px;border:1px solid #CCCCCC">
						      	<div id="iframe" style="display: inline;">	
									<iframe src="newPhoto.php?nameCat=authors&category=7" width="670" height="300" align="center" frameborder="0" framespacing="0" scrolling="none" border="0">
									</iframe>
								</div>											      
						    </td>
						</tr>
					</table>
			</div>
	</td></tr>
	</table>
</div>

{literal}

{/literal}
{dhtml_calendar inputField="date_nac" button="triggerend" singleClick=true ifFormat="%Y-%m-%d" firstDay=1 align="CR"}
{/if}

{include file="footer.tpl"}