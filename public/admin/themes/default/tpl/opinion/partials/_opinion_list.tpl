<table class="adminheading">
	<tr>
	{if $type_opinion eq '0'}
		<th nowrap>{t}Opinion articles{/t}</th>
		<th> {t}Select an author{/t}
		<select name="autores" id="autores" class="" onChange='changeList(this.options[this.selectedIndex].value);'>
			<option value="0" {if $author eq "0"} selected {/if}> {t}All{/t} </option>
			{section name=as loop=$autores}
				<option value="{$autores[as]->pk_author}" {if $author eq $autores[as]->pk_author} selected {/if}>{$autores[as]->name}</option>
			{/section}
		</select>
		</th>
		<th  style="padding:10px;width:55%;"></th>
	{else}
		<th></th>
	{/if}
	</tr>
</table>
<table class="adminlist">
	<thead>
		<tr>
			<th class="title"  style="width:30px;"></th>
			{if  $type_opinion eq '0'}
			<th class="title"  style="width:150px;">{t}Author name{/t}</th> {/if}
			<th class="title">{t}Title{/t}</th>
			<th align="center" >{t}View{/t}</th>
			<th align="center" >{t}Ratings{/t}</th>
			<th align="center" >{t}Comments{/t}</th>
			<th align="center" >{t}Created in{/t}</th>
			<th align="center" >{t}In home{/t}</th>
			<th align="center" >{t}Published{/t}</th>
			<th align="center" >{t}Actions{/t}</th>
	  </tr>
	</thead>
	<tbody>
		{section name=c loop=$opinions}
		<tr {cycle values="class=row0,class=row1"}  style="cursor:pointer;" >
			<td style="padding:4px;width:30px;">
				<input type="checkbox" class="minput"  id="selected_{$smarty.section.c.iteration}" name="selected_fld[]" value="{$opinions[c]->id}"  style="cursor:pointer;">
			</td>
			 {if  $type_opinion eq '0'}
			<td style="padding:4px;width:150px;" onClick="javascript:document.getElementById('selected_{$smarty.section.c.iteration}').click();">
				<a href="controllers/opinion/author.php?action=read&id={$opinions[c]->fk_author}"><img src="{$params.IMAGE_DIR}author.png" border="0" alt="Publicado" alt='Editar autor' title='Editar autor'/></a>
				 {$names[c]}
			</td>
			{/if}
			  <td style="padding:4px;" onClick="javascript:document.getElementById('selected_{$smarty.section.c.iteration}').click();">
				{$opinions[c]->title|clearslash}
			</td>

			<td style="padding:4px;width:70px;" align="center">
				{$opinions[c]->views}
			</td>
			<td style="padding:4px;width:70px;" align="center">
				{$op_rating[c]}
			</td>
			<td style="padding:4px;width:70px;" align="center">
				{$op_comment[c]}
			</td>
			<td style="padding:4px;width:70px;" align="center">
					{$opinions[c]->created}
			</td>
			<td style="padding:4px;width:70px" align="center">
					{if $opinions[c]->in_home == 1}
					<a href="?id={$opinions[c]->id}&amp;action=inhome_status&amp;status=0&amp;page={$paginacion->_currentPage|default:0}" class="no_home" title="Sacar de portada" ></a>
					{else}
						<a href="?id={$opinions[c]->id}&amp;action=inhome_status&amp;status=1&amp;page={$paginacion->_currentPage|default:0}" class="go_home" title="Meter en portada" ></a>
					{/if}
			</td>
			<td style="padding:4px;width:70px;" align="center">
				{if $opinions[c]->content_status == 1}
					<a href="?id={$opinions[c]->id}&amp;action=change_status&amp;status=0&amp;page={$paginacion->_currentPage|default:0}" title="Publicado">
						<img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicado" /></a>
				{else}
					<a href="?id={$opinions[c]->id}&amp;action=change_status&amp;status=1&amp;page={$paginacion->_currentPage|default:0}" title="Pendiente">
						<img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Pendiente" /></a>
				{/if}
			</td>
			<td style="padding:4px;width:70px;" align="center">
				<a href="{$_SERVER['PHP_SELF']}?action=read&id={$opinions[c]->id}" title="Modificar">
					<img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
				&nbsp;
				<a href="#" onClick="javascript:delete_opinion('{$opinions[c]->id}',{$paginacion->_currentPage|default:0});" title="Eliminar">
					<img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
			</td>
		</tr>
		{/section}
	</tbody>
	<tfoot>
		<tr class="pagination">
			<td colspan="10" align="center">
				{$paginacion->links}
			</td>
		</tr>
	</tfoot>
</table>
