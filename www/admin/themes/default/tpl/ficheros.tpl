{include file="header.tpl"}

{* LISTADO ******************************************************************* *}
{if !isset($smarty.request.action) || $smarty.request.action eq "list"}

{*
<ul id="tabs">

{section name=as loop=$allcategorys}
    <li>
		 {assign var=ca value=`$allcategorys[as]->pk_content_category`}
		<a href="ficheros.php?action=list&category={$ca}" {if $category==$ca } style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}>{$allcategorys[as]->name}</a>
	</li>
{/section}
</ul>
*}


<ul class="tabs">
    <li>
        <a href="ficheros.php?action=list&category=0" {if $category==0} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}>GLOBAL</a>
    </li>
   {* <li>
        <a href="ficheros.php?action=list&category=8" {if $category=='8' } style="color:#000000; font-weight:bold; background-color:#BFD9BF"{/if}>
        PORTADAS</a>
    </li>*}
        {include file="menu_categorys.tpl" home="ficheros.php?action=list"}
</ul>
 <br />

<div id="{$category}">
	{if $category eq 0}
		<table class="adminheading" style="width:99%; margin-left:0;">
			<tr>
				<th width="300" class="title" align="left">T&iacute;tulo</th>		
				<th width="10%" align="left">Nº Ficheros</th>
			</tr>
        </table>
			<table class="adminlist" id="tabla"  style="width:99%;">
			  <tr><td colspan="2">
				  {section name=c loop=$categorys}
					  <table width="100%" cellpadding=0 cellspacing=0  id="{$categorys[c]->pk_content_category}">
						<tr {cycle values="class=row0,class=row1"}>
							<td style="padding: 0px 10px; height: 24px;font-size: 11px;width:300;">
								 <b> {$categorys[c]->title|clearslash|escape:"html"}</b>
							</td>				
							<td style="padding: 0px 10px; height: 24px;font-size: 11px;width:10%;" align="left">
								{$num_photos[c]}</a>
							</td>
						 </tr>	
						 	<tr><td colspan=2>								  
                                                                  {section name=su loop=$subcategorys[c]}
                                                                      <table width="100%" cellpadding=0 cellspacing=0 id="{$subcategorys[c][su]->pk_content_category}" class="tabla">
                                                                                <tr {cycle values="class=row0,class=row1"}>
                                                                                        <td style="padding: 0px 30px; height: 24px; font-size: 11px;width:300;">
                                                                                                 <b>{$subcategorys[c][su]->title} </blockquote></b>
                                                                                        </td>
                                                                                    <td style="padding: 0px 10px; height: 20px;font-size: 11px;width:10%;" align="left">
                                                                                                {$num_sub_photos[c][su]}</a>
                                                                                        </td>
                                                                                 </tr>
                                                                        </table>
                                                                {/section}
							</td></tr>
						</table>
					{/section}
				</tr>			
				<tr><td colspan="2">
				  {section name=c loop=$num_especials}
					  <table width="100%" cellpadding=0 cellspacing=0 >
						<tr {cycle values="class=row0,class=row1"}>
							<td style="padding: 0px 10px; height: 24px;font-size: 11px;width:300;">
								 <b> {$num_especials[c].title|upper|clearslash|escape:"html"}</b>
							</td>				
							<td style="padding: 0px 10px; height: 24px;font-size: 11px;width:10%;" align="left">
								{$num_especials[c].num}</a>
							</td>
						 </tr>	
						 	
						</table>
					{/section}
				</tr>					
			 </table>	
	{else}
	<a id="boton_subir" href="#" onclick="new Effect.toggle($('adjunt'),'blind')"> <img src='images/iconos/examinar.gif' border='0'> Subir nuevos Archivos </a>
		<div><h2 style="color:#BB1313">{$smarty.request.msg}</h2></div>
                        <table class="adminheading">
				<tr>
					<th nowrap>Ficheros relacionados con noticias</th>
				</tr>
				<tr>
			        <td colspan="2">
			  
				
			        </td>
			    </tr>	
			</table>
			<div id="adjunt" class="adjunt" style="display: none;">	
                                <iframe src="adjuntos.php?category={$category}&amp;desde=fich" width="500" height="220" align="center" frameborder="0" framespacing="0" scrolling="none" border="0">
                                </iframe>
			</div>
			<table class="adminlist">
                            <tr>
                                    <th class="title">Título</th>
                                    <th class="title">Ruta</th>
                                    <th align="center">Disponible</th>
                                    <th align="center">Modificar</th>
                                    <th align="center">Eliminar</th>
                              </tr>
			
                            {section name=c loop=$attaches}
                                <tr {cycle values="class=row0,class=row1"}>

                                        <td style="padding:10px;font-size: 11px;">
                                                {$attaches[c]->title|clearslash}
                                        </td>
                                        <td style="padding:10px;font-size: 11px;">
                                                 {$attaches[c]->path}
                                        </td>
                                        <td style="padding:10px;font-size: 11px;width: 84px;" align="center">
                                                {if $status[c] eq 1}
                                            <img src="{php}echo($this->image_dir);{/php}publish_g.png"  border="0" alt="Si"/>
                                        {else}
                                            <img src="{php}echo($this->image_dir);{/php}icon_aviso.gif" border="0" alt="No" />
                                        {/if}
                                        </td>
                                        <td style="padding:10px;font-size: 11px;width: 84px;" align="center">
                                                <a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$attaches[c]->id}');" title="Modificar">
                                                        <img src="{php}echo($this->image_dir);{/php}edit.png" border="0" /></a>
                                        </td>                                        
                                          <td style="padding:10px;font-size: 11px;width: 84px;" align="center">
                                                <a href="#" onClick="javascript:delete_fichero('{$attaches[c]->id}',1);" title="Eliminar">
                                                        <img src="{php}echo($this->image_dir);{/php}trash.png" border="0" /></a>
                                        </td>
                                </tr>

                            {sectionelse}
                            <tr>
                                    <td align="center" colspan="5">
                                    <h2 style="margin:50px">No hai ningún archivo guardado</h2>
                                </td>                            </tr>
			{/section}
			{if count($attaches) gt 0}
			
			<tr>
			    <td colspan="5" align="center">{$paginacion->links}</td>
			</tr>
			{/if}
			</table>
			
			    <div id="adjunto" class="adjunto"></div>
			
			</div>
			<br />	
							
		{/if}
{/if}

{* FORMULARIO PARA ACTUALIZAR *********************************** *}
{if isset($smarty.request.action) && $smarty.request.action eq "read"}

<!-- <div class="panel" id="edicion-contenido" style="width:720px"> -->

<br>	
<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="700">
<tbody>
<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="title">T&iacute;tulo:</label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
		<input type="text" id="title" name="title" title="Título de la noticia"
			value="{$attaches->title|clearslash}" class="required" size="100" />
		<input type="hidden" id="category" name="category" title="Fichero"
			value="{$attaches->category}" />
			<input type="hidden" id="fich" name="fich" title="Fichero"
			value="{$attaches->pk_attachment}" />
			
	</td>
</tr>
<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="title">Ruta:</label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
		<input type="text" id="path" name="path" title="path" readonly
			value="{$attaches->path|clearslash}" class="required" size="100" />
	</td>
</tr>

<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="title">Metadata:</label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
		<input type="text" id="metadata" name="metadata" title="path" 
			value="{$attaches->metadata|clearslash}" class="required" size="100" />
	</td>
</tr>
<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="title">Descripcion:</label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
		<input type="text" id="description" name="description" title="path" 
			value="{$attaches->description|clearslash}" class="required" size="100" />
	</td>
</tr>

</tbody>
</table>
<!-- </div> -->


<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="600">
<tbody>
<tr>
	<td colspan="2" align="right">
		<a href="#" onClick="javascript:enviar(this, '_self', 'update', '{$attaches->pk_attachment}');">
			<img src="{php}echo($this->image_dir);{/php}btn_guardar.gif" border="0" /></a>&nbsp;&nbsp;
	</td>
</tr>
</tbody>
</table>

{/if}

{include file="footer.tpl"}