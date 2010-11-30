{extends file="base/admin.tpl"}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs}>

{* LISTADO ******************************************************************* *}
{if !isset($smarty.request.action) || $smarty.request.action eq "list"}
   <ul class="tabs2" style="margin-bottom: 28px;">
        {include file="menu_categorys.tpl" home="poll.php?action=list"}
    </ul>

    {include file="botonera_up.tpl"}

<br>
<div id="{$category}">

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
	 	<th align="center">Favorito</th>
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
	     <td style="padding:10px;font-size: 11px;width:6%;" align="center">
		{if $polls[c]->favorite == 1}
			<a href="?id={$polls[c]->id}&amp;action=change_favorite&amp;status=0&amp;page={$paginacion->_currentPage}" class="favourite_on" title="Publicado"></a>
		{else}
			<a href="?id={$polls[c]->id}&amp;action=change_favorite&amp;status=1&amp;page={$paginacion->_currentPage}" class="favourite_off" title="Pendiente"></a>
		{/if}
            </td>

	    <td style="padding:10px;font-size: 11px;width:10%;" align="center">
		    {if $polls[c]->available == 1}
			    <a href="?id={$polls[c]->id}&amp;action=change_status&amp;status=0&amp;page={$paginacion->_currentPage}" title="Publicado">
				    <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicado" /></a>
		    {else}
			    <a href="?id={$polls[c]->id}&amp;action=change_status&amp;status=1&amp;page={$paginacion->_currentPage}" title="Pendiente">
				    <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Pendiente" /></a>
		    {/if}

	    </td>
	    	<td style="padding:1px;width:10%;font-size: 11px;" align="center">
				<a href="?id={$polls[c]->id}&amp;action=change_status&amp;status=1&amp;page={$paginacion->_currentPage}" title="Archivar a Hemeroteca">
				<img src="{$params.IMAGE_DIR}save_hemeroteca_icon.png" border="0" alt="Archivar a Hemeroteca" /></a>
			</td>
	    <td style="padding:10px;font-size: 11px;width:10%;" align="center">
		    <a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$polls[c]->id}');" title="Modificar">
			    <img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
	    </td>
	    <td style="padding:10px;font-size: 11px;width:10%;" align="center">
		    <a href="#" onClick="javascript:confirmar(this, '{$polls[c]->id}');" title="Eliminar">
			    <img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
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

</div>
{/if}

 {dialogo script="print"}
{* FORMULARIO PARA ENGADIR ************************************** *}

{if isset($smarty.request.action) && ($smarty.request.action eq "new"  || $smarty.request.action eq "read")}

{include file="botonera_up.tpl"}

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
                                <select name="available" id="available" class="required">
                                    <option value="0" {if $poll->available eq 0} selected {/if}>No</option>
                                    <option value="1" {if $poll->available eq 1} selected {/if}>Si</option>
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
                               <label for="title"> Visualización: </label>
                            </td>
                            <td valign="top" style="padding:4px;" nowrap="nowrap">
                                <select name="visualization" id="visualization" class="required">
                                    <option value="0" {if $poll->visualization eq 0} selected {/if}>Circular</option>
                                    <option value="1" {if $poll->visualization eq 1} selected {/if}>Barras</option>
                                </select>
                            </td>
			</tr>
			<tr>
                            <td valign="top" align="right" style="padding:4px;" nowrap="nowrap">
			    	<label for="title"> Sección: </label>
			    </td>
                            <td valign="top" style="padding:4px;" nowrap="nowrap">
                                 <select name="category" id="category"  >
                                    {section name=as loop=$allcategorys}
                                        <option value="{$allcategorys[as]->pk_content_category}" {if $video->category eq $allcategorys[as]->pk_content_category || $category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$allcategorys[as]->title}" >{$allcategorys[as]->title}</option>
                                        {section name=su loop=$subcat[as]}
                                            <option value="{$subcat[as][su]->pk_content_category}" {if $video->category eq $subcat[as][su]->pk_content_category || $category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$subcat[as][su]->title}">&nbsp;&nbsp;&nbsp;&nbsp;{$subcat[as][su]->title}</option>
                                        {/section}
                                    {/section}
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
			   <a onclick="del_this_item('item{$smarty.section.i.iteration}')" style="cursor:pointer;"><img src="{$params.IMAGE_DIR}del.png" border="0" />Eliminar item</a>
			   </div>
			   {assign var='num' value=$smarty.section.i.iteration}
		   {/section}

	   </td>
        </tr>
        <tr>
            <td valign="top"  align="right" style="padding:4px;" nowrap="nowrap">
            </td>
	    <td valign="top" style="padding:4px;" nowrap="nowrap">
		<a onClick="add_item_poll({$num})" style="cursor:pointer;"><img src="{$params.IMAGE_DIR}add.png" border="0" /> Añadir </a> &nbsp;
		<a onclick="del_item_poll()" style="cursor:pointer;"><img src="{$params.IMAGE_DIR}del.png" border="0"   /> Eliminar</a>
		<div id="items" name="items">
		</div>
            </td>
        </tr>
    </tbody>
    </table>

 </div>

{/if}

<input type="hidden" id="action" name="action" value="" />
<input type="hidden" name="id" id="id" value="{$id}" />
</form>
{/block}
