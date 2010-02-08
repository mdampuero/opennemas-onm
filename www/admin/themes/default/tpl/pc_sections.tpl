{include file="header.tpl"}

{* LISTADO ******************************************************************* *}
{if !isset($smarty.request.action) || $smarty.request.action eq "list"}


    <table width="100%">
        <tr>
            <td style="padding:10px;font-size: 12px;">
                {include file="pc_botonera_up.tpl"}
            </td>
        </tr>
    </table>

    <table class="adminlist" border=0 id="tabla"  width="100%">
        <tr>

            <th class="title"  width="40%">T&iacute;tulo</th>
            <th align="left" width="30%">Descripci&oacute;n</th>
            <th align="center" width="10%">Disponible</th>
            <th align="center" width="10%">Modificar</th>
            <th align="center" width="10%">Eliminar</th>
        </tr>
        <tr>
            <td colspan="5">
                <div id="cates" class="seccion" style="float:left;width:100%;border:1px solid gray;"> <br>
                    {foreach key=eltype from=$categorys item=categorys}
                        <table width="100%" id="{$eltype}" class="tabla">
                            <tr>
                                <td><b>{$eltype}</b></td>
                            </tr>
                            {section name=a loop=$categorys}
                                <tr {cycle values="class=row0,class=row1"} style="cursor:pointer;">
                                    <td style="padding:10px;font-size: 11px;width:40%;">
                                         {$categorys[a]->title}
                                    </td>
                                    <td style="padding:10px;font-size: 11px;width:30%;">
                                         {$categorys[a]->description|clearslash}</a>
                                    </td>

                                        <td style="padding:10px;font-size: 11px;width:10%;" align="center">
                                            {if $categorys[a]->available == 1}
                                                <a href="?id={$categorys[a]->pk_content_category}&amp;action=change_available&amp;status=0" title="Publicado">
                                                        <img src="{php}echo($this->image_dir);{/php}publish_g.png" border="0" alt="Publicado" /></a>
                                            {else}
                                                <a href="?id={$categorys[a]->pk_content_category}&amp;action=change_available&amp;status=1" title="Pendiente">
                                                        <img src="{php}echo($this->image_dir);{/php}publish_r.png" border="0" alt="Pendiente" /></a>
                                            {/if}
                                        </td>
                                         <td style="padding:10px;width:75px;width:10%;" align="center">
                                             <a href="#" onClick="javascript:enviar(this, '_self', 'read', {$categorys[a]->pk_content_category});" title="Modificar">
                                                 <img src="{php}echo($this->image_dir);{/php}edit.png" border="0" /></a>
                                        </td>
                                        <td style="padding:10px;width:75px;width:10%;" align="center">
                                             <a href="#" onClick="javascript:confirmar(this, {$categorys[a]->pk_content_category});" title="Eliminar">
                                                 <img src="{php}echo($this->image_dir);{/php}trash.png" border="0" /></a>
                                        </td>
                                   
                                </tr>
                            {/section}
                        </table>
                    {/foreach}
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="5" style="padding:10px;font-size: 12px;" align="center">
                    {include file="pc_botonera_up.tpl"}
            </td>
        </tr>
    </table>

{/if}

 {dialogo script="print"}
{* FORMULARIO PARA ENGADIR ************************************** *}

{if isset($smarty.request.action) && $smarty.request.action eq "new"}

	{include file="pc_botonera_up.tpl"}


<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="99%">
<tbody>
<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="title">T&iacute;tulo:</label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
		<input type="text" id="title" name="title" title="Título"
			value="" class="required" size="80" />
	</td>
</tr>
<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="summary">Tipo:</label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
		 <select name="fk_content_type" class="required">
			{foreach key=k from=$alltypes item=i}
				<option value="{$k}">{$i}</option>
	   		 {/foreach}
                 </select>
                <label for="summary">Disponible:</label>
                <select name="available" class="required">
                    <option value="1" selected="selected">Si</option>
                    <option value="0" >No</option>
                 </select>

	</td>
</tr>
<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="title">Descripcion:</label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
		<textarea name="description" id="description" class="required"
			title="Resumen del Categoria" style="width:500px; height:6em;"></textarea>

	</td>
</tr>

</tbody>
</table>
<!-- </div> -->

{/if}


{* FORMULARIO PARA ACTUALIZAR *********************************** *}
{if isset($smarty.request.action) && $smarty.request.action eq "read"}

	{include file="pc_botonera_up.tpl"}

<!-- <div class="panel" id="edicion-contenido" style="width:720px"> -->
<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="99%">
<tbody>
<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="title">T&iacute;tulo:</label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
		<input type="text" id="title" name="title" title="Título de la categoria"
			value="{$category->title|clearslash}" class="required" size="80" />
		
	</td>
</tr>
<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="summary">Tipo:</label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
		 <select name="fk_content_type" class="required">
			{foreach key=k from=$alltypes item=i}
				<option value="{$k}">{$i}</option>
	   		 {/foreach}
                 </select>
                <label for="summary">Disponible:</label>
                <select name="available" class="required">
                    <option value="1"  {if $category->available eq 1} selected {/if}>Si</option>
                    <option value="0" {if $category->available eq 0} selected {/if}>No</option>
                 </select>

	</td>
</tr>
<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="title">Decripcion:</label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
		<textarea name="description" id="description" class="required"
			title="Resumen de la noticia" style="width:500px; height:6em;">{$category->description|clearslash}</textarea>

	</td>
</tr>

 
</tbody>
</table>
<!-- </div> -->
	
{/if}

{include file="footer.tpl"}