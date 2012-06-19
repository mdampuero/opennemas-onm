{extends file="base/admin.tpl"}

{block name="header-js" append}
    {script_tag src="/utilsVideo.js" language="javascript"}
    {script_tag src="/onm/jquery-functions.js" language="javascript"}
{/block}

{block name="content"}
<form action="#" method="get" name="formulario">
	<div class="top-action-bar clearfix">
		<div class="wrapper-content">
			<div class="title"><h2>{t}Video Manager :: Listing videos{/t} {if $category eq 0}HOME{else}{$datos_cat[0]->title}{/if}</h2></div>
			<ul class="old-button">
				{acl isAllowed="VIDEO_DELETE"}
				<li>
                    <a class="delChecked" data-controls-modal="modal-video-batchDelete" href="#" title="{t}Delete{/t}">
                        <img src="{$params.IMAGE_DIR}trash.png" border="0"  title="{t}Delete{/t}" alt="{t}Delete{/t}" ><br />{t}Delete{/t}
                    </a>
				</li>
				{/acl}
				{acl isAllowed="VIDEO_AVAILABLE"}
                <li>
                    <button value="batchnoFrontpage" name="buton-batchnoFrontpage" id="buton-batchnoFrontpage" type="submit">
                       <img border="0" src="{$params.IMAGE_DIR}publish_no.gif" title="{t}Unpublish{/t}" alt="{t}Unpublish{/t}" ><br />{t}Unpublish{/t}
                   </button>
               </li>
               <li>
                   <button value="batchFrontpage" name="buton-batchFrontpage" id="buton-batchFrontpage" type="submit">
                       <img border="0" src="{$params.IMAGE_DIR}publish.gif" title="{t}Publish{/t}" alt="{t}Publish{/t}" ><br />{t}Publish{/t}
                   </button>
               </li>
				{/acl}
				{acl isAllowed="VIDEO_CREATE"}
                <li class="separator"></li>
				<li>
					<a href="{url name=admin_videos_create category=$category}" accesskey="N" tabindex="1">
						<img border="0" src="{$params.IMAGE_DIR}/video.png" title="Nuevo Video" alt="Nuevo Video"><br />{t}New video{/t}
					</a>
				</li>
				{/acl}
                {acl isAllowed="VIDEO_WIDGET"}
                    {if $category eq 'widget'}
                        <li class="separator"></li>
                        <li>
                            <a href="#" onClick="javascript:saveSortPositions('{$smarty.server.PHP_SELF}');" title="{t}Save positions{/t}">
                                <img src="{$params.IMAGE_DIR}save.png" alt="{t}Save positions{/t}"><br />{t}Save positions{/t}
                            </a>
                        </li>
                    {/if}
                {/acl}
                {acl isAllowed="VIDEO_SETTINGS"}
                <li class="separator"></li>
                    <li>
                        <a href="{url name=admin_videos_config}" class="admin_add" title="{t}Config video module{/t}">
                            <img border="0" src="{$params.IMAGE_DIR}template_manager/configure48x48.png" alt="" /><br />
                            {t}Settings{/t}
                        </a>
                    </li>
                {/acl}
			</ul>
		</div>
	</div>

    <div class="wrapper-content">

        {render_messages}

        <ul class="pills clearfix">
            <li>
                <a href="{url name=admin_videos_widget}" {if $category === 'widget'}class="active"{elseif $ca eq $datos_cat[0]->fk_content_category}{*class="active"*}{/if}>WIDGET HOME</a>
            </li>

            <li>
                <a href="{url name=admin_videos category=all}" {if $category==='all'}class="active"{/if} >{t}All categories{/t}</a>
            </li>

            {include file="menu_categories.tpl" home="{url name=admin_videos a=l}"}
        </ul>

        <div id="warnings-validation"></div>

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
                    {if $category=='widget' || $category=='all'}<th class="center">{t}Section{/t}</th>{/if}
                    <th class="center" style="width:35px;">{t}Published{/t}</th>
                    {if $category!='widget' && $category!='all'} <th class="center" style="width:35px;">{t}Favorite{/t}</th>{/if}
                    <th class="center" style="width:35px;">{t}Home{/t}</th>
                    <th class="center" style="width:35px;">{t}Actions{/t}</th>
                    {else}
                    <th class="center">
                        &nbsp;
                    </th>
                    {/if}
                </tr>
            </thead>
            <tbody class="sortable">
                {section name=c loop=$videos}
                <tr data-id="{$videos[c]->pk_album}" style="cursor:pointer;">
                    <td>
                        <input type="checkbox" class="minput" id="selected_{$smarty.section.c.iteration}" name="selected_fld[]" value="{$videos[c]->id}"  style="cursor:pointer;">
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
                    </td>
                    </td class="center">
                    {if $category=='widget' || $category=='all'}
                        <td >
                             {$videos[c]->category_title}
                        </td>
                    {/if}
                    <td class="center">
                        {acl isAllowed="VIDEO_AVAILABLE"}
                            {if $videos[c]->available == 1}
                                <a href="?id={$videos[c]->id}&amp;action=change_status&amp;status=0&amp;category={$category}&amp;page={$page|default:0}" title="Publicado">
                                        <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicado" /></a>
                            {else}
                                <a href="?id={$videos[c]->id}&amp;action=change_status&amp;status=1&amp;category={$category}&amp;page={$page|default:0}" title="Pendiente">
                                        <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Pendiente" /></a>
                            {/if}
                        {/acl}
                    </td>
                     {if $category!='widget' && $category!='all'}
                    <td class="center">
                        {acl isAllowed="VIDEO_FAVORITE"}
                                {if $videos[c]->favorite == 1}
                                   <a href="?id={$videos[c]->id}&amp;action=change_favorite&amp;status=0&amp;category={$category}&amp;page={$page|default:0}" class="favourite_on" title="Quitar de Portada"></a>
                                {else}
                                    <a href="?id={$videos[c]->id}&amp;action=change_favorite&amp;status=1&amp;category={$category}&amp;page={$page|default:0}" class="favourite_off" title="Meter en Portada"></a>
                                {/if}
                         {/acl}
                    </td>
                    {/if}
                    <td class="center">
                    {acl isAllowed="VIDEO_HOME"}
                        {if $videos[c]->in_home == 1}
                           <a href="{$smarty.server.PHP_SELF}?id={$videos[c]->id}&amp;action=change_inHome&amp;status=0&amp;category={$category}&amp;page={$page|default:0}" class="no_home" title="{t}Take out from home{/t}"></a>
                        {else}
                            <a href="{$smarty.server.PHP_SELF}?id={$videos[c]->id}&amp;action=change_inHome&amp;status=1&amp;category={$category}&amp;page={$page|default:0}" class="go_home" title="{t}Put in home{/t}"></a>
                        {/if}
                    {/acl}
                    </td>
                    <td style="padding:1px; font-size:11px;" class="center">
                        <ul class="action-buttons">
                            {acl isAllowed="VIDEO_UPDATE"}
                            <li>
                                <a href="{url name=admin_videos_show id=$videos[c]->id}" title="{t}Edit{/t}" >
                                    <img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
                            </li>
                            {/acl}

                            {acl isAllowed="VIDEO_DELETE"}
                            <li>
                             <a class="del" data-controls-modal="modal-from-dom"
                               data-id="{$videos[c]->id}"
                               data-title="{$videos[c]->title|capitalize}" href="#" >
                            <img src="{$params.IMAGE_DIR}trash.png" border="0" />
                            </a>
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
    <input type="hidden" name="page" id="page" value="{$page|default:0}" />
    <input type="hidden" name="category" id="category" value="{$category}" />
    <input type="hidden" id="status" name="status" value="" />
    <input type="hidden" id="action" name="action" value="" />
    <input type="hidden" name="id" id="id" value="{$id|default:""}" />
</form>
     <script>
        // <![CDATA[
        jQuery('#buton-batchnoFrontpage').on('click', function(){
            jQuery('#action').attr('value', "batchFrontpage");
            jQuery('#status').attr('value', "0");
            jQuery('#formulario').submit();
            e.preventDefault();
        });
        jQuery('#buton-batchFrontpage').on('click', function(){
            jQuery('#action').attr('value', "batchFrontpage");
            jQuery('#status').attr('value', "1");
            jQuery('#formulario').submit();
            e.preventDefault();
        });

        {if $category eq 'widget'}
            jQuery(document).ready(function() {
                makeSortable();
            });
        // ]]>
        {/if}
    </script>

    {include file="video/modals/_modalDelete.tpl"}
    {include file="video/modals/_modalBatchDelete.tpl"}
    {include file="video/modals/_modalAccept.tpl"}
{/block}
