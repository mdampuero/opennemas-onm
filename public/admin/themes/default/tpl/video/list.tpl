{extends file="base/admin.tpl"}

{block name="header-js" append}
    {script_tag src="/utilsVideo.js" language="javascript"}
{/block}

{block name="content"}
<form action="#" method="post" name="formulario">
	<div class="top-action-bar clearfix">
		<div class="wrapper-content">
			<div class="title"><h2>{t}Video Manager :: Listing videos{/t} {if $category eq 0}HOME{else}{$datos_cat[0]->title}{/if}</h2></div>
			<ul class="old-button">
				{acl isAllowed="VIDEO_DELETE"}
				<li>
					<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);" name="submit_mult" value="Eliminar" title="Eliminar">
						<img border="0" src="{$params.IMAGE_DIR}trash.png" title="Eliminar" alt="Eliminar" ><br />Eliminar
					</a>
				</li>
				{/acl}
				{acl isAllowed="VIDEO_AVAILABLE"}
				<li>
					<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 0);" name="submit_mult" value="noFrontpage" title="noFrontpage">
						<img border="0" src="{$params.IMAGE_DIR}publish_no.gif" title="noFrontpage" alt="noFrontpage" ><br />Despublicar
					</a>
				</li>
				<li>
					<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 1);" name="submit_mult" value="Frontpage" title="Frontpage">
						<img border="0" src="{$params.IMAGE_DIR}publish.gif" title="Publicar" alt="Publicar" ><br />Publicar
					</a>
				</li>
				{/acl}
				{acl isAllowed="VIDEO_CREATE"}
                <li class="separator"></li>
				<li>
					<a href="{$smarty.server.SCRIPT_NAME}?action=new&category={$category}" accesskey="N" tabindex="1">
						<img border="0" src="{$params.IMAGE_DIR}/video.png" title="Nuevo Video" alt="Nuevo Video"><br />Nuevo Video
					</a>
				</li>
				{/acl}
                 {acl isAllowed="ALBUM_SETTINGS"}
                <li class="separator"></li>
                    <li>
                        <a href="{$smarty.server.PHP_SELF}?action=config" class="admin_add" title="{t}Config video module{/t}">
                            <img border="0" src="{$params.IMAGE_DIR}template_manager/configure48x48.png" alt="" /><br />
                            {t}Configurations{/t}
                        </a>
                    </li>
                {/acl}
			</ul>
		</div>
	</div>

    <div class="wrapper-content">
        <ul class="pills clearfix">
            <li>
                <a href="{$smarty.server.SCRIPT_NAME}?action=list&category=favorite" {if $category=='favorite'}class="active"{elseif $ca eq $datos_cat[0]->fk_content_category}class="active"{/if} >WIDGET HOME</a>
            </li>

            {include file="menu_categories.tpl" home="video.php?action=list"}
        </ul>
    

        {render_messages}

        <table class="listing-table">
            <thead>
                <tr>
                    {if count($videos) > 0}
                    <th style="width:15px;">
                        <input type="checkbox" id="toggleallcheckbox">
                    </th>
                    <th>{t}Title{/t}</th>
                    <th class="center" style="width:35px;">{t}Views{/t}</th>
                    <th class="center">{t}Service{/t}</th>
                    <th class="center">Created</th>
                    {if $category=='favorite'}<th class="center">{t}Section{/t}</th>{/if}
                    <th class="center" style="width:35px;">{t}Published{/t}</th>
                    <th class="center" style="width:35px;">{t}Favorite{/t}</th>
                    <th class="center" style="width:35px;">{t}Actions{/t}</th>
                    {else}
                    <th class="center">
                        &nbsp;
                    </th>
                    {/if}
                </tr>
            </thead>
            <tbody>
                {section name=c loop=$videos}
                <tr {cycle values="class=row0,class=row1"}  style="cursor:pointer;">
                    <td>
                        <input type="checkbox" class="minput"  id="selected_{$smarty.section.c.iteration}" name="selected_fld[]" value="{$videos[c]->id}"  style="cursor:pointer;">
                    </td>
                    <td onClick="javascript:document.getElementById('selected_{$smarty.section.c.iteration}').click();">
                        {$videos[c]->title|clearslash}
                    </td>

                    <td class="center">
                        {$videos[c]->views}
                    </td>
                    <td class="center">
                        {$videos[c]->author_name}
                    </td>
                    <td class="center">
                        {$videos[c]->created}
                    </td class="center">
                    {if $category=='favorite'}
                        <td >
                             {$videos[c]->category_title}
                        </td>
                    {/if}
                    <td class="center">
                        {acl isAllowed="VIDEO_AVAILABLE"}
                            {if $videos[c]->available == 1}
                                <a href="?id={$videos[c]->id}&amp;action=change_status&amp;status=0&amp;category={$category}&amp;page={$paginacion->_currentPage|default:0}" title="Publicado">
                                        <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicado" /></a>
                            {else}
                                <a href="?id={$videos[c]->id}&amp;action=change_status&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage|default:0}" title="Pendiente">
                                        <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Pendiente" /></a>
                            {/if}
                        {/acl}
                    </td>
                    <td class="center">
                        {acl isAllowed="VIDEO_FAVORITE"}
                                {if $videos[c]->favorite == 1}
                                   <a href="?id={$videos[c]->id}&amp;action=change_favorite&amp;status=0&amp;category={$category}&amp;page={$paginacion->_currentPage|default:0}" class="favourite_on" title="Quitar de Portada"></a>
                                {else}
                                    <a href="?id={$videos[c]->id}&amp;action=change_favorite&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage|default:0}" class="favourite_off" title="Meter en Portada"></a>
                                {/if}
                         {/acl}
                    </td>
                    <td style="padding:1px; font-size:11px;" class="center">
                        <ul class="action-buttons">
                            {acl isAllowed="VIDEO_UPDATE"}
                            <li>
                                <a href="{$smarty.server.PHP_SELF}?action=read&id={$videos[c]->id}" title="{t}Edit{/t}" >
                                    <img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
                            </li>
                            {/acl}

                            {acl isAllowed="VIDEO_DELETE"}
                            <li>
                                <a href="#" onClick="javascript:delete_videos('{$videos[c]->id}','{$paginacion->_currentPage|default:""}');" title="Eliminar">
                                    <img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
                            </li>
                            {/acl}
                        </ul>
                    </td>
                </tr>
                {sectionelse}
                <tr>
                    <td class="empty" colspan="8">
                        {t}There is no videos yet.{/t}
                    </td>
                </tr>
                {/section}
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="10">
                        {$pagination|default:""}&nbsp;
                    </td>
                </tr>
            </tfoot>
        </table>

    </div>

    <input type="hidden" id="action" name="action" value="" />
    <input type="hidden" name="id" id="id" value="{$id|default:""}" />
</form>

{/block}
