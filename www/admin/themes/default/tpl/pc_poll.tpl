{include file="pc_header.tpl"}

{* LISTADO ******************************************************************* *}
{if !isset($smarty.request.action) || $smarty.request.action eq "list"}

<div>


{include file="pc_botonera_up.tpl"}
	
	
	
    <table class="adminheading">
	    <tr>
		    <th nowrap>Encuestas</th>
	    </tr>	
    </table>

    <table class="adminlist">
	<tr>  
		<th class="title"></th>	
		<th class="title">T&iacute;tulo</th>
		<th class="title">Subt&iacute;tulo</th>
		<th align="center">Votos</th>
		<th align="center">Visto</th>
		<th align="center">Fecha</th>
	{*	<th align="center">Favorito</th> *}
		<th align="center">Ver en columna</th>
		<th align="center">Publicado</th>	
		<th align="center">Archivar</th>
		<th align="center">Modificar</th>
		<th align="center">Eliminar</th>
	  </tr>

	{section name=c loop=$polls}
	<tr {cycle values="class=row0,class=row1"}  style="cursor:pointer;" >
	    <td style="padding:10px;font-size: 11px;">
		    <input type="checkbox" class="minput"  id="selected_{$smarty.section.c.iteration}" name="selected_fld[]" value="{$polls[c]->id}"  style="cursor:pointer;" >
	    </td>	    
	      <td style="padding:10px;font-size: 11px;width:40%;" onClick="javascript:document.getElementById('selected_{$smarty.section.c.iteration}').click();" >
		    {$polls[c]->title|clearslash}
	    </td>
	     <td style="padding:10px;font-size: 11px;width:20%;" onClick="javascript:document.getElementById('selected_{$smarty.section.c.iteration}').click();"  >
		    {$polls[c]->subtitle|clearslash}
	    </td>	    
	     <td style="padding:10px;font-size: 11px;width:10%;" align="center">
		     {$polls[c]->total_votes}
	    </td>
	  
	    <td style="padding:1px;font-size: 11px;width:10%;" align="center">
		    {$polls[c]->views}
	    </td>
	    <td style="padding:1px;width:10%;font-size: 11px;" align="center">		    
			    {$polls[c]->created}
	    </td>	 
	{*    <td style="padding:10px;font-size: 11px;width:6%;" align="center">
		{if $polls[c]->favorite == 1}
			<a href="?id={$polls[c]->id}&amp;action=change_favorite&amp;status=0&amp;page={$paginacion->_currentPage}" class="favourite_on" title="Publicado"></a>
		{else}
			<a href="?id={$polls[c]->id}&amp;action=change_favorite&amp;status=1&amp;page={$paginacion->_currentPage}" class="favourite_off" title="Pendiente"></a>
		{/if}
            </td>
            *}
             <td style="padding:10px;font-size: 11px;width:6%;" align="center">
                    {if $polls[c]->view_column == 1}
                            <a href="?id={$polls[c]->id}&amp;action=set_view_column&amp;status=0&amp;category={$category}" class="no_home" title="Publicado"></a>
                    {else}
                            <a href="?id={$polls[c]->id}&amp;action=set_view_column&amp;status=1&amp;page={$paginacion->_currentPage}" class="go_home" title="Pendiente"></a>
                    {/if}
            </td>
           
	    <td style="padding:10px;font-size: 11px;width:10%;" align="center">
		    {if $polls[c]->available == 1}
			    <a href="?id={$polls[c]->id}&amp;action=change_available&amp;status=0&amp;page={$paginacion->_currentPage}" title="Publicado">
				    <img src="{php}echo($this->image_dir);{/php}publish_g.png" border="0" alt="Publicado" /></a>
		    {else}
			    <a href="?id={$polls[c]->id}&amp;action=change_available&amp;status=1&amp;page={$paginacion->_currentPage}" title="Pendiente">
				    <img src="{php}echo($this->image_dir);{/php}publish_r.png" border="0" alt="Pendiente" /></a>
		    {/if}
	    </td>
	    	<td style="padding:1px;width:10%;font-size: 11px;" align="center">	
				<a href="?id={$polls[c]->id}&amp;action=change_status&amp;status=1&amp;page={$paginacion->_currentPage}" title="Archivar a Hemeroteca">
				<img src="{php}echo($this->image_dir);{/php}save_hemeroteca_icon.png" border="0" alt="Archivar a Hemeroteca" /></a>	
			</td>
	    <td style="padding:10px;font-size: 11px;width:10%;" align="center">
		    <a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$polls[c]->id}');" title="Modificar">
			    <img src="{php}echo($this->image_dir);{/php}edit.png" border="0" /></a>
	    </td>
	    <td style="padding:10px;font-size: 11px;width:10%;" align="center">
		    <a href="#" onClick="javascript:confirmar(this, '{$polls[c]->id}');" title="Eliminar">
			    <img src="{php}echo($this->image_dir);{/php}trash.png" border="0" /></a>
	    </td>
	</tr>

	{sectionelse}
	<tr>
		<td align="center" colspan=5><br><br><p><h2><b>Ninguna encuesta guardada</b></h2></p><br><br></td>
	</tr>
	{/section}

	{if count($polls) gt 0}
	  <tr>
	      <td colspan="9" align="center">{$paginacion->links}</td>
	  </tr>
	{/if}
    </table>
<br />
{include file="pc_botonera_down.tpl"}
</div>
{/if}

 {dialogo script="print"}
{* FORMULARIO PARA ENGADIR ************************************** *}

{if isset($smarty.request.action) && $smarty.request.action eq "new"}

{include file="pc_botonera_up.tpl"}

<!-- <div class="panel" id="edicion-contenido" style="width:720px">-->
<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="900">
<tbody>
    <tr>
        <td> </td><td > </td>
        <td rowspan="6" valign="top" style="padding:4px;border:0px;">
            <div align='center'>
                <table style='background-color:#F5F5F5; padding:18px; width:69%;' cellpadding="8">
                    <tr>
                        <td valign="top" align="right" style="padding:4px;" nowrap="nowrap">
                        <label for="title"> Disponible: </label>
                        </td>
                        <td valign="top" style="padding:4px;" nowrap="nowrap">
                            <select name="content_status" id="content_status" class="required">
                                <option value="0" >No</option>
                                <option value="1" selected>Si</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" align="right" style="padding:4px;" nowrap="nowrap">
                            <label for="title"> Favorito: </label>
                        </td>
                        <td valign="top" style="padding:4px;" nowrap="nowrap">
                            <select name="favorite" id="favorte" class="required">
                                <option value="0" selected>No</option>
                                <option value="1" >Si</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" align="right" style="padding:4px;" nowrap="nowrap">
                            <label for="title"> Ver en columna: </label>
                        </td>
                        <td valign="top" style="padding:4px;" nowrap="nowrap">
                            <select name="view_column" id="view_column" class="required">
                                <option value="0" selected>No</option>
                                <option value="1" >Si</option>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
    <tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="title">T&iacute;tulo:</label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
		<input type="text" id="title" name="title" title="Título" value=""  onBlur="javascript:get_tags(this.value);" class="required" size="80" />
			<input type="hidden" id="fk_pc_content_category" name="fk_pc_content_category" title="categoria" value="7">				
	</td>	
    </tr>
    <tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="title">Subtitulo:</label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
		<input type="text" id="subtitle" name="subtitle" title="subTitulo de la noticia" 
			value="" class="required" size="80" />		
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
	<td valign="top"  align="right" style="padding:4px;" nowrap="nowrap">						  
	</td>
	<td valign="top" style="padding:4px;" nowrap="nowrap">	
		<a onClick="add_item_poll(0)" style="cursor:pointer;"><img src="{php}echo($this->image_dir);{/php}add.png" border="0" /> Añadir </a> &nbsp;
		<a onclick="del_item_poll()" style="cursor:pointer;"><img src="{php}echo($this->image_dir);{/php}del.png" border="0"   /> Eliminar</a>
		<div id="items" name="items">		
		</div>		
	</td>
    </tr>

</tbody>
</table>

{/if}


{* FORMULARIO PARA ACTUALIZAR *********************************** *}
{if isset($smarty.request.action) && $smarty.request.action eq "read"}

{include file="pc_botonera_up.tpl"}

<div class="panel" id="edicion-contenido" style="width:95%;display:block;"> 
    <table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="700">
    <tbody>
        <tr>
            <td> </td><td > </td>
            <td rowspan="6" valign="top" style="padding:4px;border:0px;">
                <div align='center'>
                    <table style='background-color:#F5F5F5; padding:18px; width:69%;' cellpadding="8">
                        <tr>
                            <td valign="top" align="right" style="padding:4px;" nowrap="nowrap">
                                <label for="title"> Disponible: </label>
                            </td>
                            <td valign="top" style="padding:4px;" nowrap="nowrap">
                                <select name="content_status" id="content_status" class="required">
                                    <option value="0" {if $poll->content_status eq 0} selected {/if}>No</option>
                                    <option value="1" {if $poll->content_status eq 1} selected {/if}>Si</option>
                                </select>
                            </td>
			</tr> 	
			<tr>
                           <td valign="top" align="right" style="padding:4px;" nowrap="nowrap">
                               <label for="title"> Favorito: </label>
                            </td>
                            <td valign="top" style="padding:4px;" nowrap="nowrap">
                                <select name="favorite" id="favorite" class="required">
                                    <option value="0" {if $poll->favorite eq 0} selected {/if}>No</option>
                                    <option value="1" {if $poll->favorite eq 1} selected {/if}>Si</option>
                                </select>
                            </td>
			</tr> 	
			<tr>
                            <td valign="top" align="right" style="padding:4px;" nowrap="nowrap">
			    	<label for="title"> Ver en columna: </label>  
			    </td>
                            <td valign="top" style="padding:4px;" nowrap="nowrap">
                                <select name="view_column" id="view_column" class="required">
                                    <option value="0" {if $poll->view_column eq 0} selected {/if}>No</option>
                                    <option value="1" {if $poll->view_column eq 1} selected {/if}>Si</option>
                                </select>
                            </td>
			</tr> 		
                    </table>
                </div>
            </td>
        </tr>
        <tr>
            <td valign="top" align="right" style="padding:4px;" width="30%">
                    <label for="title">T&iacute;tulo:</label>
            </td>
            <td style="padding:4px;" nowrap="nowrap" width="70%">
                    <input type="text" id="title" name="title" title="Titulo de la noticia"
                            value="{$poll->title|clearslash|escape:"html"}" class="required" size="80" onChange="javascript:get_metadata(this.value);"  />
                    <input type="hidden" id="fk_pc_content_category" name="fk_pc_content_category" title="categoria" value="7">
            </td>
        </tr>
        <tr>
            <td valign="top" align="right" style="padding:4px;" width="30%">
                    <label for="title">Subtitulo:</label>
            </td>
            <td style="padding:4px;" nowrap="nowrap" width="70%">
                    <input type="text" id="subtitle" name="subtitle" title="subTitulo de la noticia"
                            value="{$poll->subtitle|clearslash}" class="required" size="80" />
            </td>
        </tr>
        <tr>
            <td valign="top" align="right" style="padding:4px;" width="30%">
                    <label for="title">Palabras Clave:</label>
            </td>
            <td style="padding:4px;" nowrap="nowrap" width="70%">
                    <input type="text" id="metadata" name="metadata" title="Titulo de la noticia"
                            value="{$poll->metadata|clearslash}" class="required" size="80" />
            </td>
        </tr>
        <tr>
            <td valign="top"  align="right" style="padding:4px;" nowrap="nowrap">
                    <label for="title"> Respuestas: </label>
            </td>
	    <td valign="top" style="padding:4px;" nowrap="nowrap">
	   	 
	   	   {assign var='num' value='0'}	  
		   {section name=i loop=$items}
			   <div id="item{$smarty.section.i.iteration}" class="marcoItem" style='display:inline;'>
			   <p style="font-weight: bold;" >Item #{$smarty.section.i.iteration}:  Votos:  {$items[i].votes} / {$poll->total_votes} </p> 
			   Item: <input type="text" name="item[{$smarty.section.i.iteration}]" value="{$items[i].item}" id="item[{$smarty.section.i.iteration}]" size="45"/> 			 
			    <input type="hidden" readonly name="votes[{$smarty.section.i.iteration}]" value="{$items[i].votes}" id="votes[{$smarty.section.i.iteration}]" size="8"/>
			   <a onclick="del_this_item('item{$smarty.section.i.iteration}')" style="cursor:pointer;"><img src="{php}echo($this->image_dir);{/php}del.png" border="0" /></a>
			   </div>
			   {assign var='num' value=`$smarty.section.i.iteration`}		   
		   {/section}
	   				
	   </td>
        </tr>
        <tr>
            <td valign="top"  align="right" style="padding:4px;" nowrap="nowrap">
            </td>
	    <td valign="top" style="padding:4px;" nowrap="nowrap">
		<a onClick="add_item_poll({$num})" style="cursor:pointer;"><img src="{php}echo($this->image_dir);{/php}add.png" border="0" /> Añadir </a> &nbsp;
		<a onclick="del_item_poll()" style="cursor:pointer;"><img src="{php}echo($this->image_dir);{/php}del.png" border="0"   /> Eliminar</a>
		<div id="items" name="items">		
		</div>		
            </td>
        </tr>
    </tbody>
    </table>
 </div> 

{/if}

{include file="footer.tpl"}