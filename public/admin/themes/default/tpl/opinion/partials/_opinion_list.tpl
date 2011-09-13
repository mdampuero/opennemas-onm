{if $type_opinion eq '0'}
<table class="adminheading">
	<tr>
		<th align="right">
            {t}Select an author{/t}
            <select name="autores" id="autores" class="" onChange='changeList(this.options[this.selectedIndex].value);'>
                <option value="0" {if isset($author) && $author eq "0"} selected {/if}> {t}All{/t} </option>
                {section name=as loop=$autores}
                    <option value="{$autores[as]->pk_author}" {if isset($author) && $author eq $autores[as]->pk_author} selected {/if}>{$autores[as]->name}</option>
                {/section}
            </select>
		</th>
	</tr>
</table>
{/if}
<table class="listing-table">
	<thead>
		<tr>
			<th style="width:15px;"></th>
			{if  $type_opinion eq '0'}
			<th style="width:150px;">{t}Author name{/t}</th> {/if}
			<th>{t}Title{/t}</th>

            <th class="center" style="width:40px"><img src="{$params.IMAGE_DIR}seeing.png" alt="{t}Views{/t}" title="{t}Views{/t}"></th>
			<th style="width:40px;">{t}Ratings{/t}</th>
            <th class="center" style="width:40px"><img src="{$params.IMAGE_DIR}comments.png" alt="{t}Comments{/t}" title="{t}Comments{/t}"></th>
			<th class="center" style="width:110px;">{t}Created in{/t}</th>
			<th style="width:80px;">{t}In home{/t}</th>
			<th style="width:40px;">{t}Published{/t}</th>
			<th class="right" style="width:40px;">{t}Actions{/t}</th>
	  </tr>
	</thead>
	<tbody>
		{section name=c loop=$opinions}
		<tr>
			<td>
				<input type="checkbox" class="minput"  id="selected_{$smarty.section.c.iteration}" name="selected_fld[]" value="{$opinions[c]->id}">
			</td>
			 {if  $type_opinion eq '0'}
			<td  onClick="javascript:document.getElementById('selected_{$smarty.section.c.iteration}').click();">
                <a href="author.php?action=read&id={$opinions[c]->fk_author}">
                    {$names[c]}
                </a>

			</td>
			{/if}
			<td style="" onClick="javascript:document.getElementById('selected_{$smarty.section.c.iteration}').click();">
				<a href="{$smarty.server.PHP_SELF}?action=read&id={$opinions[c]->id}" title="Modificar">
					{$opinions[c]->title|clearslash}
                </a>
			</td>

			<td class="center">
				{$opinions[c]->views}
			</td>
			<td class="center">
				{$op_rating[c]|default:0}
			</td>
			<td class="center">
				{$op_comment[c]|default:0}
			</td>
			<td class="center">
				{$opinions[c]->created}
			</td>
			<td class="center">
                {if $opinions[c]->in_home == 1}
                <a href="?id={$opinions[c]->id}&amp;action=inhome_status&amp;status=0&amp;page={$paginacion->_currentPage|default:0}" class="no_home" title="Sacar de portada" ></a>
                {else}
                <a href="?id={$opinions[c]->id}&amp;action=inhome_status&amp;status=1&amp;page={$paginacion->_currentPage|default:0}" class="go_home" title="Meter en portada" ></a>
                {/if}
			</td>
			<td class="center">
				{if $opinions[c]->content_status == 1}
					<a href="?id={$opinions[c]->id}&amp;action=change_status&amp;status=0&amp;page={$paginacion->_currentPage|default:0}" title="Publicado">
						<img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicado" /></a>
				{else}
					<a href="?id={$opinions[c]->id}&amp;action=change_status&amp;status=1&amp;page={$paginacion->_currentPage|default:0}" title="Pendiente">
						<img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Pendiente" /></a>
				{/if}
			</td>
			<td class="right">
				<ul class="action-buttons">
					<li>
						<a href="{$smarty.server.PHP_SELF}?action=read&id={$opinions[c]->id}" title="Modificar">
							<img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
					</li>
					<li>
						<a href="#" onClick="javascript:delete_opinion('{$opinions[c]->id}',{$paginacion->_currentPage|default:0});" title="Eliminar">
							<img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
					</li>
				</ul>
			</td>
		</tr>
        {sectionelse}
        <tr>
            <td class="empty" colspan=10>
                {t}There is no opinions yet.{/t}
            </td>
        </tr>
		{/section}
	</tbody>
	<tfoot>
		<tr class="pagination">
			<td colspan="10">
				{$paginacion|default:""}&nbsp;
			</td>
		</tr>
	</tfoot>
</table>
