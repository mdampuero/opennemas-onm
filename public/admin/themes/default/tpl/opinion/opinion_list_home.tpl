<div id="msg"></div>
    <table class="adminheading">
        <tr>
            <td>
                &nbsp;
            </td>
        </tr>
    </table>
<table class="adminlist" border=0>
	<tr>
		<th class="title"  style="padding:4px;width:40px;"></th>
		<th class="title"  style="width:180px;">Autor</th>
		<th class="title" style="">T&iacute;tulo</th>
		<th align="center" style="width:70px;">Visto</th>
		<th align="center" style="width:70px;">Votaci&oacute;n</th>
		<th align="center" style="width:74px;">Comentarios</th>
		<th align="center" style="width:70px;">Fecha</th>
		<th align="center" style="width:70px;">Home</th>
		<th align="center" style="width:70px;">Publicado</th>
		<th align="center" style="width:70px;">Modificar</th>
		<th align="center" style="width:70px;">Eliminar</th>
	  </tr>
	   <tr> <td colspan='11'>
	 	  <div style="float:left;width:100%;border:1px solid gray;">
		 {if $num_dir > 0}
		    <table width="100%" cellpadding=0 cellspacing=0  id="{$director[0]->id}" border=0 >
                        <tr {cycle values="class=row0,class=row1"}  style="cursor:pointer;" >
                            <td style="padding:4px;font-size: 11px;width:30px;">
                                    <input type="checkbox" class="minput"  id="selected_0" name="selected_fld[]" value="{$director[0]->id}"  style="cursor:pointer;" onClick="javascript:document.getElementById('selected_0').click();">
                            </td>
                            <td style="font-size: 11px;width:180px;" onClick="javascript:document.getElementById('selected_0').click();">
                                     Director
                            </td>
                            <td style="font-size: 11px;" onClick="javascript:document.getElementById('selected_0').click();">
                                    {$director[0]->title|clearslash}
                            </td>
                            <td style="font-size: 11px;width:70px;" align="center">
                                    {$director[0]->views}
                            </td>
                            <td style="width:70px;font-size: 11px;" align="center">
                                    {$director[0]->ratings}
                            </td>
                            <td style="width:74px;font-size: 11px;" align="center">
                                    {$director[0]->comments}
                            </td>
                            <td style="width:70px;font-size: 11px;" align="center">
                                    {$director[0]->created}
                            </td>
                            <td style="width:70px" align="center">
                                    {if $director[0]->in_home == 1}
                                            <a href="?id={$director[0]->id}&amp;action=inhome_status&amp;status=0&amp;category={$category}" class="no_home" title="Sacar de portada" ></a>
                                    {else}
                                            <a href="?id={$director[0]->id}&amp;action=inhome_status&amp;status=1&amp;category={$category}" class="go_home" title="Meter en portada" ></a>
                                    {/if}
                            </td>
                            <td style="font-size: 11px;width:70px;" align="center">
                                    {if $director[0]->content_status == 1}
                                            <a href="?id={$director[0]->id}&amp;action=change_status&amp;status=0&amp;page={$paginacion->_currentPage}" title="Publicado">
                                                    <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicado" /></a>
                                    {else}
                                            <a href="?id={$director[0]->id}&amp;action=change_status&amp;status=1&amp;page={$paginacion->_currentPage}" title="Pendiente">
                                                    <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Pendiente" /></a>
                                    {/if}
                            </td>
                            <td style="font-size: 11px;width:70px;" align="center">
                                    <a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$director[0]->id}');" title="Modificar">
                                            <img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
                            </td>
                            <td style="font-size: 11px;width:70px;" align="center">
                                    <a href="#" onClick="javascript:delete_opinion('{$director[0]->id}',{$paginacion->_currentPage|default:0});" title="Eliminar">
                                            <img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
                            </td>
                        </tr>
                    </table>
                {/if}
                </div>
            </td>
	  </tr>
	  <tr> <td colspan='11'>
		<div id="editoriales" class="seccion" style="float:left;width:100%;border:1px solid gray;"> <br>
			{assign var='cont' value=1}
			{section name=c loop=$editorial}
			 	<table width="100%" cellpadding=0 cellspacing=0  id="{$editorial[c]->id}" border=0 class="edits_sort">
					<tr {cycle values="class=row0,class=row1"}  style="cursor:pointer;" >
					    <td style="padding:4px;font-size: 11px;width:30px;">
						    <input type="checkbox" class="minput"  id="selected_{$cont}" name="selected_fld[]" value="{$editorial[c]->id}"  style="cursor:pointer;" >
					    </td>
					    <td style="font-size: 11px;width:180px;" onClick="javascript:document.getElementById('selected_{$cont}').click();">
						    Editorial
					    </td>
					      <td style="font-size: 11px;" onClick="javascript:document.getElementById('selected_{$cont}').click();">
						    {$editorial[c]->title|clearslash}
					    </td>
					    <td style="font-size: 11px;width:70px;" align="center">
						    {$editorial[c]->views}
					    </td>
					    <td style="width:70px;font-size: 11px;" align="center">
                                                    {$editorial[c]->ratings}
                                            </td>
					    <td style="width:74px;font-size: 11px;" align="center">
                                                    {$editorial[c]->comments}
                                            </td>
					    <td style="width:70px;font-size: 11px;" align="center">
                                                    {$editorial[c]->created}
					    </td>
					    <td style="width:70px" align="center">
                                                    {if $editorial[c]->in_home == 1}
                                                    <a href="?id={$editorial[c]->id}&amp;action=inhome_status&amp;status=0&amp;category={$category}" class="no_home" title="Sacar de portada" ></a>
                                                    {else}
                                                            <a href="?id={$editorial[c]->id}&amp;action=inhome_status&amp;status=1&amp;category={$category}" class="go_home" title="Meter en portada" ></a>
                                                    {/if}
                                            </td>
					    <td style="font-size: 11px;width:70px;" align="center">
						    {if $editorial[c]->content_status == 1}
							    <a href="?id={$editorial[c]->id}&amp;action=change_status&amp;status=0&amp;page={$paginacion->_currentPage}" title="Publicado">
								    <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicado" /></a>
						    {else}
							    <a href="?id={$editorial[c]->id}&amp;action=change_status&amp;status=1&amp;page={$paginacion->_currentPage}" title="Pendiente">
								    <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Pendiente" /></a>
						    {/if}
					    </td>
					    <td style="font-size: 11px;width:70px;" align="center">
						    <a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$editorial[c]->id}');" title="Modificar">
							    <img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
					    </td>
					    <td style="font-size: 11px;width:70px;" align="center">
						    <a href="#" onClick="javascript:delete_opinion('{$editorial[c]->id}',{$paginacion->_currentPage|default:0});" title="Eliminar">
							    <img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
					    </td>
					</tr>
			</table>
			{assign var='cont' value=$cont+1}
		 {/section}
		</div>
	   </td>
	</tr>
 	<tr> <td colspan='11'>
		<div id="cates" class="seccion" style="float:left;width:100%;border:1px solid gray;"> <br>
			{assign var='cont' value=$num_edit+1}
			{section name=c loop=$opinions}
			 <table width="100%" cellpadding=0 cellspacing=0  id="{$opinions[c]->id}" border=0 {if $opinions[c]->type_opinion==0}class="sortable"{/if}>
					<tr {cycle values="class=row0,class=row1"}  style="cursor:pointer;" >
					    <td style="padding:4px;font-size: 11px;width:30px;">
						   <input type="checkbox" class="minput"  id="selected_{$cont}" name="selected_fld[]" value="{$opinions[c]->id}"  style="cursor:pointer;" >
					    </td>
					     {if $type_opinion=='-1' ||  $type_opinion=='0'}
					    <td style="font-size: 11px;width:180px;" onClick="javascript:document.getElementById('selected_{$cont}').click();">
						    {if $opinions[c]->type_opinion==1} Editorial{elseif $opinions[c]->type_opinion==2} Director
						    {else} 	<a href="controllers/opinion/author.php?action=read&id={$opinions[c]->fk_author}"><img src="{$params.IMAGE_DIR}author.png" border="0" alt="Publicado" alt='Editar autor' title='Editar autor'/></a>
						    Opini&oacute;n: {$names[c]} 	{/if}
					    </td>
					    {/if}
					      <td style="font-size: 11px;" onClick="javascript:document.getElementById('selected_{$cont}').click();">
						    {$opinions[c]->title|clearslash}
					    </td>

					    <td style="font-size: 11px;width:70px;" align="center">
						    {$opinions[c]->views}
					    </td>
					    <td style="width:70px;font-size: 11px;" align="center">
                                                   {$op_rating[c]}
                                            </td>
					    <td style="width:74px;font-size: 11px;" align="center">
                                                   {$op_comment[c]}
                                            </td>
					    <td style="width:70px;font-size: 11px;" align="center">
						    {$opinions[c]->created}
					    </td>
					    <td style="width:70px" align="center">
							{if $opinions[c]->in_home == 1}
								<a href="?id={$opinions[c]->id}&amp;action=inhome_status&amp;status=0&amp;category={$category}" class="no_home" title="Sacar de portada" ></a>
							{else}
								<a href="?id={$opinions[c]->id}&amp;action=inhome_status&amp;status=1&amp;category={$category}" class="go_home" title="Meter en portada" ></a>
							{/if}
						</td>
					    <td style="font-size: 11px;width:70px;" align="center">
						    {if $opinions[c]->content_status == 1}
							    <a href="?id={$opinions[c]->id}&amp;action=change_status&amp;status=0&amp;page={$paginacion->_currentPage}" title="Publicado">
								    <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicado" /></a>
						    {else}
							    <a href="?id={$opinions[c]->id}&amp;action=change_status&amp;status=1&amp;page={$paginacion->_currentPage}" title="Pendiente">
								    <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Pendiente" /></a>
						    {/if}
					    </td>
					    <td style="font-size: 11px;width:70px;" align="center">
						    <a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$opinions[c]->id}');" title="Modificar">
							    <img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
					    </td>
					    <td style="font-size: 11px;width:70px;" align="center">
						    <a href="#" onClick="javascript:delete_opinion('{$opinions[c]->id}',{$paginacion->_currentPage|default:0});" title="Eliminar">
							    <img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
					    </td>
					</tr>
				</table>
				{assign var='cont' value=$cont+1}
			{/section}
	</div>
	  </td>
	</tr>
	<tfoot>
		<tr class="pagination">
			<td colspan="11" align="center">
				{$paginacion->links}
			</td>
		</tr>
	</tfoot>
   </table>

{if $type_opinion=='-1'}
<script type="text/javascript">
 // <![CDATA[
	Sortable.create('cates',{
		tag:'table',
		only:'sortable',
		dropOnEmpty: true,
		containment:["cates"],
		constraint:false
	});
	Sortable.create('editoriales',{
		tag:'table',
		only:'edits_sort',
		dropOnEmpty: true,
		containment:["editoriales"],
		constraint:false
	});
 // ]]>
</script>
{/if}
{if $type_opinion eq 0}
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
