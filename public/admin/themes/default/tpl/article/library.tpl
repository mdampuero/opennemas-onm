{extends file="base/admin.tpl"}

{block name="header-js" append}
    {script_tag src="/utilsarticle.js" language="javascript"}
    {script_tag src="/editables.js" language="javascript"}
    {script_tag src="/utilsGallery.js" language="javascript"}
{/block}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs|default:""}>
	<div class="top-action-bar">
		<div class="wrapper-content">
			<div class="title">
				<h2>{t}Library{/t} :: {if $category eq 'todos'}{$category|upper}{else}{$datos_cat[0]->title} {/if}</h2>
			</div>
			<ul class="old-button">
                {acl isAllowed="ARTICLE_DELETE"}
				<li>
					<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);" name="submit_mult" value="Eliminar" title="Eliminar">
						<img border="0" src="{$params.IMAGE_DIR}trash.png" title="Eliminar" alt="Eliminar" ><br />Eliminar
					</a>
				</li>
                {/acl}
                {acl isAllowed="ARTICLE_UPDATE"}
				<li>
					<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'm_restore', 1);" onmouseover="return escape('Recuperar');" name="submit_mult" value="noFrontpage">
						<img border="0" src="{$params.IMAGE_DIR}archive_no.png" alt="recuperar"><br />Recuperar
					</a>
				</li>
                {/acl}
                <li class="separator"></li>
				<li>
					<a title="Advanced Search" tabindex="1" accesskey="N" class="admin_add" href="{$smarty.const.SITE_URL}admin/controllers/search_advanced/search_advanced.php">
					<img border="0" alt="Advanced Search" title="Advanced Search" src="{$smarty.const.SITE_URL}/admin/themes/default/images/search.png"><br>Search
					</a>
				</li>
			</ul>
		</div>
	</div>

	<div class="wrapper-content">
	<ul class="pills">
		<li>
			<a href="article.php?action=list_hemeroteca&category=todos" id="link_todos" {if $category=='todos'}class="active"{/if}>TODOS</font></a>
		</li>
		{include file="menu_categories.tpl" home="article.php?action=list_hemeroteca"}
	</ul>

	{if isset($smarty.get.alert) && ($smarty.get.alert eq 'ok')}
	<script type="text/javascript" language="javascript">
		alert('{$smarty.get.msg}');
	</script>
	{/if}

	<div id="{$category}">

		<table class="listing-table">
			<thead>
				<th style="width:30px">
                    <input type="checkbox" id="toggleallcheckbox">
                </th>
				<th class="title">{t}Title{/t}</th>
				{if $category=='todos'}<th align="center">{t}Section{/t}</th>{/if}
				<th align="center" style="width:130px">{t}Modification time{/t}</th>

                <th class="center" style="width:40px"><img src="{$params.IMAGE_DIR}seeing.png" alt="{t}Views{/t}" title="{t}Views{/t}"></th>
                <th class="center" style="width:40px"><img src="{$params.IMAGE_DIR}comments.png" alt="{t}Comments{/t}" title="{t}Comments{/t}"></th>
				<th align="center" style="width:80px">{t}Votes{/t}</th>
				<th align="center" style="width:80px">{t}Author{/t}</th>
				<th align="center" style="width:80px">{t}Last Editor{/t}</th>
				<th align="right" style="width:100px;">{t}Actions{/t}</th>
			</thead>
			<tbody>
				<input type="hidden"  id="category" name="category" value="{$category}">

				{section name=c loop=$articles}
				<tr {cycle values="class=row0,class=row1"}>
					<td>
						 <input type="checkbox" class="minput" pos=1 id="selected_fld_des_{$smarty.section.c.iteration}" name="selected_fld[]" value="{$articles[c]->id}"  style="cursor:pointer;" >
					</td>
					<td >
						<a href="{$smarty.server.PHP_SELF}?action=only_read&id={$articles[c]->id}" title="{$articles[c]->title|clearslash}"> {$articles[c]->title|clearslash}</a>
					</td>
					{if $category=='todos'}
						<td class="center">
							{$articles[c]->category_name}
						</td>
					{/if}
					<td class="center">
						{$articles[c]->changed}
					</td>
					<td class="center">
						{$articles[c]->views}
					</td>
					<td class="center">
						{$articles[c]->comment}
					</td>
					<td class="center">
						{$articles[c]->rating}
					</td>
					<td class="center">
						{$articles[c]->publisher}
					</td>
					<td class="center">
						{$articles[c]->editor}
					</td>
					<td class="center">
						<ul class="action-buttons">
                            {acl isAllowed="ARTICLE_AVAILABLE"}
							<li>
								<a href="{$smarty.server.PHP_SELF}?id={$articles[c]->id}&amp;action=change_status&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage}" title="{t}Restore to available{/t}">
									<img src="{$params.IMAGE_DIR}archive_no2.png" border="0" alt="Publicar" />
								</a>
							</li>
                            {/acl}
							<li>
								<a href="{$smarty.server.PHP_SELF}?action=read&amp;id={$articles[c]->id}" title="Editar">
									<img src="{$params.IMAGE_DIR}edit.png" border="0" alt="Editar" />
								</a>
							</li>
                            {acl isAllowed="ARTICLE_DELETE"}
							<li>
								<a href="#" style="cursor:pointer" onClick="javascript:delete_article('{$articles[c]->id}','{$category}',0);" title="Eliminar">
									<img src="{$params.IMAGE_DIR}trash.png" border="0" />
								</a>
							</li>
                            {/acl}
						</ul>
					</td>
				</tr>
				{sectionelse}
				<tr>
					<td class="empty" colspan=10>{t}There is no article saved{/t}</td>
				</tr>
				{/section}
				</tbody>

				<tfoot>
					<td class="pagination" colspan=10>{if count($articles) gt 0}{$paginacion->links}{/if}&nbsp;</td>
				</tfoot>

			</table>


            <input type="hidden" id="action" name="action" value="" />
            <input type="hidden" name="id" id="id" value="{$id|default:""}" />
        </div>
	</div>
</form>
{/block}
