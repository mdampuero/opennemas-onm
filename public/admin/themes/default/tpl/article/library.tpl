{extends file="base/admin.tpl"}

{block name="header-js" append}
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilsarticle.js"></script>
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}editables.js"></script>
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilsGallery.js"></script>

    {if $smarty.request.action == 'list_pendientes' || $smarty.request.action == 'list_agency'}
        <script type="text/javascript" language="javascript" src="{$params.JS_DIR}editables.js"></script>
    {/if}
{/block}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs|default:""}>
	<div class="top-action-bar">
		<div class="wrapper-content">
			<div class="title">
				<h2>{$titulo_barra} :: {if $category eq 'todos'}{$category|upper}{else}{$datos_cat[0]->title} {/if}</h2>
			</div>
			<ul class="old-button">
				<li>
					<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);" name="submit_mult" value="Eliminar" title="Eliminar">
						<img border="0" src="{$params.IMAGE_DIR}trash.png" title="Eliminar" alt="Eliminar" ><br />Eliminar
					</a>
				</li>
				<li>
					<button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; width: 95px;" onmouseover="return escape('<u>S</u>eleccionar todo');" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button');">
						<img id="select_button" class="icon" src="{$params.IMAGE_DIR}select_button.png" alt="Seleccionar Todo"  status="0">
					</button>
				</li>
				<li>
					<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'm_restore', 1);" onmouseover="return escape('Recuperar');" name="submit_mult" value="noFrontpage">
						<img border="0" src="{$params.IMAGE_DIR}archive_no.png" alt="recuperar"><br />Recuperar
					</a>
				</li>
				<li>
					<a title="Advanced Search" tabindex="1" accesskey="N" class="admin_add" href="{$smarty.const.SITE_URL}admin/controllers/search_advanced/search_advanced.php">
					<img border="0" alt="Advanced Search" title="Advanced Search" src="{$smarty.const.SITE_URL}/admin/themes/default/images/search.png"><br>Search
					</a>
				</li>
			</ul>
		</div>
	</div>

	<div class="wrapper-content">
	<ul class="tabs2" style="margin-bottom: 28px;">

		<li>
			<a href="article.php?action=list_hemeroteca&category=todos" id="link_todos" {if $category=='todos'} style="color:#000000; font-weight:bold; background-color:#BFD9BF"{/if}>TODOS</font></a>
		</li>
		  <script type="text/javascript">
					// <![CDATA[
						{literal}
							  Event.observe($('link_todos'), 'mouseover', function(event) {
								 $('menu_subcats').setOpacity(0);
								 e = setTimeout("show_subcat('{/literal}{$category}','{$home|urlencode}{literal}');$('menu_subcats').setOpacity(1);",1000);

								});

						{/literal}
					// ]]>
				</script>
		{include file="menu_categorys.tpl" home="article.php?action=list_hemeroteca"}
	</ul>

	{if $smarty.get.alert eq 'ok'}
	<script type="text/javascript" language="javascript">
		alert('{$smarty.get.msg}');
	</script>
	{/if}

	<div id="{$category}">
		<table class="adminheading">
			<tr>
				<td><strong>{t}Articles in library{/t}</strong></td>
			</tr>
		</table>

		<table class="adminlist">
			<thead>
				<th style="width:30px">&nbsp;</th>
				<th class="title">{t}Title{/t}</th>
				{if $category=='todos'}<th align="center">{t}Section{/t}</th>{/if}
				<th align="center">{t}Modification time{/t}</th>
				<th align="center">{t}Views{/t}</th>
				<th align="center">{t}Comments{/t}</th>
				<th align="center">Votaci&oacute;n</th>
				<th align="center">{t}Author{/t}</th>
				<th align="center">{t}Last Editor{/t}</th>
				<th align="center" style="width:100px;">{t}Actions{/t}</th>
			</thead>
			<tbody>
				<input type="hidden"  id="category" name="category" value="{$category}">

				{section name=c loop=$articles}
				<tr {cycle values="class=row0,class=row1"}>
					<td style="width:30px">
						 <input type="checkbox" class="minput" pos=1 id="selected_fld_des_{$smarty.section.c.iteration}" name="selected_fld[]" value="{$articles[c]->id}"  style="cursor:pointer;" >
					</td>
					<td style="width:50%;">
						<a href="{$smarty.server.PHP_SELF}?action=only_read&id={$articles[c]->id}" title="{$articles[c]->title|clearslash}"> {$articles[c]->title|clearslash}</a>
					</td>
					{if $category=='todos'}
						<td align="center">
							{$articles[c]->category_name}
						</td>
					{/if}
					<td align="center">
						{$articles[c]->changed}
					</td>
					<td>
						{$articles[c]->views}
					</td>
					<td align="center">
						{$articles[c]->comment}
					</td>
					<td align="center">
						{$articles[c]->rating}
					</td>
					<td>
						{$articles[c]->publisher}
					</td>
					<td >
						{$articles[c]->editor}
					</td>
					<td align="center">
						<ul class="action-buttons">
							<li>
								<a href="{$smarty.server.PHP_SELF}?id={$articles[c]->id}&amp;action=change_status&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage}" title="{t}Restore to available{/t}">
									<img src="{$params.IMAGE_DIR}archive_no2.png" border="0" alt="Publicar" />
								</a>
							</li>
							<li>
								<a href="{$smarty.const.SITE_URL}{generate_uri content_type='article' id=$articles[c]->id date=$articles[c]->created category_name=$articles[c]->category_name title=$articles[c]->title}"
								   target="_blank" accesskey="P" onmouseover="return escape('<u>P</u>revisualizar');"
								   onclick="UserVoice.PopIn.showPublic('{$smarty.const.SITE_URL}{generate_uri content_type='article' id=$articles[c]->id date=$articles[c]->created category_name=$articles[c]->category_name title=$articles[c]->title}');return false;" >
									<img border="0" src="{$params.IMAGE_DIR}preview_small.png" title="Previsualizar" alt="Previsualizar" />
								</a>
							</li>
							<li>
								<a href="{$smarty.server.PHP_SELF}?action=read&id={$articles[c]->id}" title="Editar">
									<img src="{$params.IMAGE_DIR}edit.png" border="0" alt="Editar" />
								</a>
							</li>
							<li>
								<a href="#" style="cursor:pointer" onClick="javascript:delete_article('{$articles[c]->id}','{$category}',0);" title="Eliminar">
									<img src="{$params.IMAGE_DIR}trash.png" border="0" />
								</a>
							</li>
						</ul>
					</td>
				</tr>
				{sectionelse}
				<tr>
					<td align="center" colspan=10><br><br><p><h2><strong>Ninguna noticia guardada</strong></h2></p><br><br></td>
				</tr>
				{/section}
				</tbody>

				<tfoot>
					<td class="pagination" colspan=10>{if count($articles) gt 0}{$paginacion->links}{/if}</td>
				</tfoot>

			</table>


            <input type="hidden" id="action" name="action" value="" />
            <input type="hidden" name="id" id="id" value="{$id}" />
        </div>
	</div>
</form>
{/block}
