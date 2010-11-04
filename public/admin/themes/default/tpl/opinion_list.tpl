
{if $type_opinion eq '0'}
    <table class="adminheading">
	    <tr>
		    <th nowrap>Articulos de Opini&oacute;n</th> 
		    <th> Seleccione autor:
		    <select name="autores" id="autores" class="" onChange='changeList(this.options[this.selectedIndex].value);'>
		  	<option value="0" {if $author eq "0"} selected {/if}> Todos </option>
			{section name=as loop=$autores}
				<option value="{$autores[as]->pk_author}" {if $author eq $autores[as]->pk_author} selected {/if}>{$autores[as]->name}</option>
		    {/section}
	    </select>	
		    </th>
		    <th style="padding:10px;width:55%;"></th>
	    </tr>	
    </table>
{/if}
    <table class="adminlist">
	<tr>  
		<th class="title"  style="width:30px;"></th>
		{if  $type_opinion eq '0'}
                    <th class="title"  style="width:150px;">Autor</th> {/if}
		<th class="title">T&iacute;tulo</th>	
		<th align="center" style="width:70px;">Visto</th>		
		<th align="center" style="width:70px;">Votaci&oacute;n</th>
		<th align="center" style="width:70px;">Comentarios</th>		
		<th align="center" style="width:70px;">Fecha</th>
		<th align="center" style="width:70px;">Home</th>
		<th align="center" style="width:70px;">Publicado</th>
		<th align="center" style="width:70px;">Modificar</th>
		<th align="center" style="width:70px;">Eliminar</th>
	  </tr>	  
	 
 <tr> <td colspan='11'>
		<div id="cates" class="seccion" style="float:left;width:100%;border:1px solid gray;"> 
		{section name=c loop=$opinions}
			 <table width="100%" cellpadding=0 cellspacing=0  id="{$opinions[c]->id}" border=0 {if $opinions[c]->type_opinion==0}class="sortable"{/if}>
					<tr {cycle values="class=row0,class=row1"}  style="cursor:pointer;" >
					    <td style="padding:4px;font-size: 11px;width:30px;">
						    <input type="checkbox" class="minput"  id="selected_{$smarty.section.c.iteration}" name="selected_fld[]" value="{$opinions[c]->id}"  style="cursor:pointer;">
					    </td>
					     {if  $type_opinion eq '0'} 
					    <td style="padding:4px;font-size: 11px;width:150px;" onClick="javascript:document.getElementById('selected_{$smarty.section.c.iteration}').click();">
					    	<a href="author.php?action=read&id={$opinions[c]->fk_author}"><img src="{$params.IMAGE_DIR}author.png" border="0" alt="Publicado" alt='Editar autor' title='Editar autor'/></a>					    					    
						     {$names[c]} 					    
					    </td>
					    {/if}
					      <td style="padding:4px;font-size: 11px;" onClick="javascript:document.getElementById('selected_{$smarty.section.c.iteration}').click();">
						    {$opinions[c]->title|clearslash}
					    </td>	    

					    <td style="padding:4px;font-size: 11px;width:70px;" align="center">
						    {$opinions[c]->views}
					    </td>
					    <td style="padding:4px;width:70px;font-size: 11px;" align="center">		
							{$op_rating[c]}				
						</td>
					    <td style="padding:4px;width:70px;font-size: 11px;" align="center">		
							{$op_comment[c]}				
						</td>	
					    <td style="padding:4px;width:70px;font-size: 11px;" align="center">		    
							    {$opinions[c]->created}
					    </td>
					    <td style="padding:4px;width:70px" align="center">
								{if $opinions[c]->in_home == 1}
								<a href="?id={$opinions[c]->id}&amp;action=inhome_status&amp;status=0&amp;page={$paginacion->_currentPage|default:0}" class="no_home" title="Sacar de portada" ></a>
								{else}
									<a href="?id={$opinions[c]->id}&amp;action=inhome_status&amp;status=1&amp;page={$paginacion->_currentPage|default:0}" class="go_home" title="Meter en portada" ></a>
								{/if}
						</td>
					    <td style="padding:4px;font-size: 11px;width:70px;" align="center">
						    {if $opinions[c]->content_status == 1}
							    <a href="?id={$opinions[c]->id}&amp;action=change_status&amp;status=0&amp;page={$paginacion->_currentPage|default:0}" title="Publicado">
								    <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicado" /></a>
						    {else}
							    <a href="?id={$opinions[c]->id}&amp;action=change_status&amp;status=1&amp;page={$paginacion->_currentPage|default:0}" title="Pendiente">
								    <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Pendiente" /></a>
						    {/if}
					    </td>
					    <td style="padding:4px;font-size: 11px;width:70px;" align="center">
						    <a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$opinions[c]->id}');" title="Modificar">
							    <img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
					    </td>
					    <td style="padding:4px;font-size: 11px;width:70px;" align="center">
						    <a href="#" onClick="javascript:delete_opinion('{$opinions[c]->id}',{$paginacion->_currentPage|default:0});" title="Eliminar">
							    <img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
					    </td>
					</tr>
			</table>		 
		{/section}
		</div>
		
		</td>
	</tr>		
	
	{if count($opinions) gt 0}
	  <tr>
	      <td colspan='11' align="center">{$paginacion}</td>
	  </tr>
	{/if}
   </table>


{if $type_opinion eq '0'}
    <table class="adminheading">
	    <tr>
		    <th nowrap>Articulos de Opini&oacute;n</th> 
		    <th> Seleccione autor:
		    <select name="autores" id="autores" class="" onChange='changeList(this.options[this.selectedIndex].value);'>
		  	<option value="0" {if $author eq "0"} selected {/if}> Todos </option>
			{section name=as loop=$autores}
				<option value="{$autores[as]->pk_author}" {if $author eq $autores[as]->pk_author} selected {/if}>{$autores[as]->name}</option>
		    {/section}
	    </select>	
		    </th>
		    <th  style="padding:10px;width:55%;"></th>
	    </tr>	
    </table>
{/if}
