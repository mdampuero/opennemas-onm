{extends file="base/admin.tpl"}

{block name="content"}
<div class="wrapper-content">
   <form action="#" method="post" name="formulario" id="formulario" {$formAttrs}>

    {include file="agency_importer/europapress/menu.tpl"}

   <div id="{$category}">

        <table class="adminheading">
			<tr>
                <th align="left"><span {if $minutes > 10}class="red"{/if}>{t 1=$minutes}Last sync: %1 minutes ago{/t}</th>
				<th nowrap="nowrap" align="right">

					<label for="username">{t}Filter by title{/t}</label>
					<input id="username" name="filter[name]" onchange="$('action').value='list';this.form.submit();" value="{$smarty.request.filter.name}" />

					<label for="usergroup">{t}and group:{/t}</label>
					<select id="usergroup" name="filter[category]" onchange="$('action').value='list';this.form.submit();">
						{html_options options=$categories selected=$smarty.request.filter.group}
					</select>

					<input type="hidden" name="page" value="{$smarty.request.page}" />
					<input type="submit" value="{t}Search{/t}">
				</th>
			</tr>
		</table>

		<table class="adminlist" border=0>
            {if count($element) >0}
			<thead>
				<tr>
					<th  style='width:6%;' align="center">{t}Priority{/t}</th>
					<th  style='width:10%;'>{t}Title{/t}</th>
					<th  style='width:50%;'>{t}Content (50 chars){/t}</th>
					<th align="center" style="width:5%;">{t}Section{/t}</th>
					<th  style='width:6%;' align="center">{t}Date{/t}</th>
					<th  style='width:6%;' align="center">{t}Actions{/t}</th>
				</tr>
			</thead>
            {/if}

            {section name=c loop=$element}
            <tr {cycle values="class=row0,class=row1"}  style="cursor:pointer;" >
                <td style="font-size: 11px;width:4%;">
                    <input type="checkbox" class="minput"  id="selected_{$smarty.section.c.iteration}" name="selected_fld[]" value="{$comments[c]->id}"  style="cursor:pointer;" >
                </td>
                <td style="padding:2px; font-size: 11px;width:16%;" onmouseout="UnTip()" onmouseover="Tip('{$comments[c]->body|nl2br|regex_replace:"/[\r\t\n]/":" "|clearslash|regex_replace:"/'/":"\'"|escape:'html'}', SHADOW, true, ABOVE, true, WIDTH, 600)" onClick="javascript:document.getElementById('selected_{$smarty.section.c.iteration}').click();">
                    {$comments[c]->title|strip_tags|clearslash|truncate:50}
                </td>
                <td style="font-size: 11px;width:25%;" onmouseout="UnTip()" onmouseover="Tip('{$comments[c]->body|nl2br|regex_replace:"/[\r\t\n]/":" "|clearslash|regex_replace:"/'/":"\'"|escape:'html'}', SHADOW, true, ABOVE, true, WIDTH, 600)">
                    {$comments[c]->body|strip_tags|clearslash|truncate:50}
                </td>
                 {assign var=type value=$articles[c]->content_type}
                <td style="font-size: 11px;width:25%;" onmouseout="UnTip()" onmouseover="Tip('{$comments[c]->body|nl2br|regex_replace:"/[\r\t\n]/":" "|clearslash|regex_replace:"/'/":"\'"|escape:'html'}', SHADOW, true, ABOVE, true, WIDTH, 600)">
                     {$content_types[$type]}
                </td>
                <td style="padding:10px;font-size: 11px;width:25%;">
                    <a style="cursor:pointer;"  onClick="javascript:enviar(this, '_self', 'read', '{$comments[c]->pk_comment}');new Effect.BlindUp('edicion-contenido'); new Effect.BlindDown('article-info'); return false;">
                    {$articles[c]->title|strip_tags|clearslash}
                    </a>
                </td>
                <td style="width:6%;font-size: 11px;" align="center">
                        {$comments[c]->ip}
                </td>
                {if $category eq 'todos' || $category eq 'home'}
                <td style="width:6%;font-size: 11px;" align="center">
                    {$articles[c]->category_name} {if $articles[c]->content_type==4}Opini&oacute;n{/if}
                </td>
                {/if}
                <td style="font-size: 11px;width:100px;" align="center">
                    {if $category eq 'todos' || $comments[c]->content_status eq 0}
                        <a href="?id={$comments[c]->id}&amp;action=change_status&amp;status=1&amp;category={$category}&amp;comment_status={$comment_status}&amp;page={$paginacion->_currentPage}" title="Publicar">
                                <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicar" /></a>
                        <a href="?id={$comments[c]->id}&amp;action=change_status&amp;status=2&amp;category={$category}&amp;comment_status={$comment_status}&amp;page={$paginacion->_currentPage}" title="Rechazar">
                                <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Rechazar" /></a>
                    {elseif $comments[c]->content_status eq 2}
                            <a class="unpublishing" href="?id={$comments[c]->id}&amp;action=change_status&amp;status=1&amp;category={$category}&amp;comment_status={$comment_status}&amp;page={$paginacion->_currentPage}" title="Publicar">
                                   </a>
                    {else}
                            <a class="publishing" href="?id={$comments[c]->id}&amp;action=change_status&amp;status=2&amp;category={$category}&amp;comment_status={$comment_status}&amp;page={$paginacion->_currentPage}" title="Rechazar">
                                   </a>
                    {/if}
                </td>
                <td style="font-size: 11px;width:60px;" align="center">
                    <a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$comments[c]->id}');" title="Modificar">
                        <img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
                </td>
                <td style="font-size: 11px;width:60px;" align="center">
                    <a href="#" onClick="javascript:confirmar(this, '{$comments[c]->id}');" title="Eliminar">
                        <img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
                </td>
           </tr>
    
            {sectionelse}
            <tr>
                <td align="center" colspan=10>
                    <br><br>
                    <p>
                        <h2>
                            <b>{t}There is no elements to import{/t}</b>
                        </h2>
                        <p>{t}Try syncing from server by click over the "Sync with server" button above.{/t}</p>
                    </p>
                    <br><br>
                </td>
            </tr>
            {/section}
            <tfoot>
                 <tr class="pagination" >
                     <td colspan="13" align="center">{$paginacion->links}</td>
                 </tr>
            </tfoot>

	   </table>
   </div>

   <input type="hidden" id="action" name="action" value="" />
   <input type="hidden" name="id" id="id" value="{$id}" />
   </form>
</div>
{/block}
