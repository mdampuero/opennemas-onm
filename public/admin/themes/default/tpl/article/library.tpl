
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

<br style="clear: both;" />
 {if $smarty.get.alert eq 'ok'}

     <script type="text/javascript" language="javascript">
           alert('{$smarty.get.msg}');
    </script>
  {/if}
{include file="article/partials/_menu.tpl"}
<br>

<div id="{$category}">
	<table class="adminheading">
		<tr>
			<td><b>Art&iacute;culos en la hemeroteca</b></td><td style="font-size: 10px;" align="right"><em> </em></td>
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
			<th align="center">{t}Publisher{/t}</th>
			<th align="center">{t}Last Editor{/t}</th>
			<th align="center" style="width:100px;">{t}Actions{/t}</th>
		</thead>

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
				<a href="{$smarty.server.PHP_SELF}?id={$articles[c]->id}&amp;action=change_status&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage}" title="{t}Restore to available{/t}">
					<img src="{$params.IMAGE_DIR}archive_no2.png" border="0" alt="Publicar" />
				</a>
				&nbsp;
				<a href="{$smarty.const.SITE_URL}{generate_uri content_type='article' id=$articles[c]->id date=$articles[c]->created category_name=$articles[c]->category_name title=$articles[c]->title}"
				   target="_blank" accesskey="P" onmouseover="return escape('<u>P</u>revisualizar');"
				   onclick="UserVoice.PopIn.showPublic('{$smarty.const.SITE_URL}{generate_uri content_type='article' id=$articles[c]->id date=$articles[c]->created category_name=$articles[c]->category_name title=$articles[c]->title}');return false;" >
					<img border="0" src="{$params.IMAGE_DIR}preview_small.png" title="Previsualizar" alt="Previsualizar" />
				</a>
				&nbsp;
				<a href="{$smarty.server.PHP_SELF}?action=read&id={$articles[c]->id}" title="Editar">
					<img src="{$params.IMAGE_DIR}edit.png" border="0" alt="Editar" />
				</a>
				&nbsp;
				<a href="#" style="cursor:pointer" onClick="javascript:delete_article('{$articles[c]->id}','{$category}',0);" title="Eliminar">
					<img src="{$params.IMAGE_DIR}trash.png" border="0" />
				</a>
			</td>
		</tr>
		{sectionelse}
		<tr>
			<td align="center" colspan=10><br><br><p><h2><b>Ninguna noticia guardada</b></h2></p><br><br></td>
		</tr>
		{/section}

		<tfoot>
			<td class="pagination" colspan=10>{if count($articles) gt 0}{$paginacion->links}{/if}</td>
		</tfoot>

		</table>
	<br>


</div>
