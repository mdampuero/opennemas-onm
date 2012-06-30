{extends file="base/admin.tpl"}

{block name="header-js" append}
    {script_tag src="/onm/jquery-functions.js" language="javascript"}
{/block}

{block name="footer-js" append}
    <script>
    var video_manager_urls = {
        batchDelete: '{url name=admin_video_batchdelete category=$category page=$page}',
        saveWidgetPositions: '{url name=admin_video_save_positions category=$category page=$page}'
    }
    </script>
    {script_tag src="/jquery-onm/jquery.video.js" language="javascript"}
{/block}

{block name="content"}
<form action="#" method="get" name="formulario" id="formulario">
	<div class="top-action-bar clearfix">
		<div class="wrapper-content">
			<div class="title"><h2>{t}Video Manager :: Listing videos{/t} {if $category eq 'all'}HOME{elseif $category eq 'widget'}WIDGET{else}{$datos_cat[0]->title}{/if}</h2></div>
			<ul class="old-button">
				{acl isAllowed="VIDEO_DELETE"}
				<li>
                    <button type="submit" class="delChecked" data-controls-modal="modal-video-batchDelete" href="#" title="{t}Delete{/t}">
                        <img src="{$params.IMAGE_DIR}trash.png" border="0"  title="{t}Delete{/t}" alt="{t}Delete{/t}" ><br />{t}Delete{/t}
                    </button>
				</li>
				{/acl}
				{acl isAllowed="VIDEO_AVAILABLE"}
                <li>
                    <button id="batch-unpublish" type="submit" name="status" value="0">
                       <img border="0" src="{$params.IMAGE_DIR}publish_no.gif" title="{t}Unpublish{/t}" alt="{t}Unpublish{/t}" ><br />{t}Unpublish{/t}
                   </button>
               </li>
               <li>
                   <button id="batch-publish" type="submit" name="status" value="1">
                       <img border="0" src="{$params.IMAGE_DIR}publish.gif" title="{t}Publish{/t}" alt="{t}Publish{/t}" ><br />{t}Publish{/t}
                   </button>
               </li>
				{/acl}
				{acl isAllowed="VIDEO_CREATE"}
                <li class="separator"></li>
				<li>
					<a href="{url name=admin_video_create category=$category}" accesskey="N" tabindex="1">
						<img border="0" src="{$params.IMAGE_DIR}/video.png" title="Nuevo Video" alt="Nuevo Video"><br />{t}New video{/t}
					</a>
				</li>
				{/acl}
                {acl isAllowed="VIDEO_WIDGET"}
                    {if $category eq 'widget'}
                        <li class="separator"></li>
                        <li>
                            <a href="#" id="save-widget-positions" title="{t}Save positions{/t}">
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
        <div id="warnings-validation"></div>

        <ul class="pills clearfix">
            <li>
                <a href="{url name=admin_videos_widget}" {if $category === 'widget'}class="active"{elseif $ca eq $datos_cat[0]->fk_content_category}{*class="active"*}{/if}>WIDGET HOME</a>
            </li>

            <li>
                <a href="{url name=admin_videos category=all}" {if $category==='all'}class="active"{/if} >{t}All categories{/t}</a>
            </li>

            {include file="menu_categories.tpl" home="{url name=admin_videos a=l}"}
        </ul>

        <table class="listing-table">
            <thead>
                <tr>
                    {if count($videos) > 0}
                    <th style="width:15px;">
                        <input type="checkbox" id="toggleallcheckbox">
                    </th>
                    <th>{t}Title{/t}</th>
                    {if $category=='widget' || $category=='all'}<th class="left">{t}Section{/t}</th>{/if}
                    <th class="center">{t}Service{/t}</th>
                    <th class="center">Created</th>
                    <th class="center" style="width:35px;">{t}Views{/t}</th>
                    <th class="center" style="width:35px;">{t}Published{/t}</th>
                    {if $category!='widget' && $category!='all'} <th class="center" style="width:35px;">{t}Favorite{/t}</th>{/if}
                    <th class="center" style="width:35px;">{t}Home{/t}</th>
                    <th class="center" style="width:100px;">{t}Actions{/t}</th>
                    {else}
                    <th class="center">
                        &nbsp;
                    </th>
                    {/if}
                </tr>
            </thead>
            <tbody class="sortable">
                {section name=c loop=$videos}
                <tr data-id="{$videos[c]->id}" style="cursor:pointer;">
                    <td>
                        <input type="checkbox" class="minput" id="selected_{$smarty.section.c.iteration}" name="selected_fld[]" value="{$videos[c]->id}"  style="cursor:pointer;">
                    </td>
                    <td onClick="javascript:document.getElementById('selected_{$smarty.section.c.iteration}').click();">
                        {$videos[c]->title|clearslash}
                    </td>
                    {if $category=='widget' || $category=='all'}
                    <td >
                         {$videos[c]->category_title}
                    </td>
                    {/if}
                    <td class="center">
                        {$videos[c]->author_name}
                    </td>
                    </td class="center">

                    <td class="center">
                        {$videos[c]->created}
                    </td>
                    <td class="center">
                        {$videos[c]->views}
                    </td>
                    <td class="center">
                        {acl isAllowed="VIDEO_AVAILABLE"}
                        {if $videos[c]->available == 1}
                            <a href="{url name=admin_video_toggle_availability id=$videos[c]->id status=0 category=$category page=$page|default:1}" title="{t}Published{/t}">
                                <img src="{$params.IMAGE_DIR}publish_g.png" alt="{t}Published{/t}" />
                            </a>
                        {else}
                            <a href="{url name=admin_video_toggle_availability id=$videos[c]->id status=1 category=$category page=$page|default:1}" title="{t}Pendiente{/t}">
                                <img src="{$params.IMAGE_DIR}publish_r.png" alt="{t}Pendiente{/t}" />
                            </a>
                        {/if}
                        {/acl}
                    </td>
                     {if $category!='widget' && $category != 'all'}
                    <td class="center">
                        {acl isAllowed="VIDEO_FAVORITE"}
                        {if $videos[c]->favorite == 1}
                           <a href="{url name=admin_video_toggle_favorite id=$videos[c]->id status=0 category=$category page=$page|default:1}" class="favourite_on" title="Quitar de Portada"></a>
                        {else}
                            <a href="{url name=admin_video_toggle_favorite id=$videos[c]->id status=1 category=$category page=$page|default:1}" class="favourite_off" title="Meter en Portada"></a>
                        {/if}
                        {/acl}
                    </td>
                    {/if}
                    <td class="center">
                    {acl isAllowed="VIDEO_HOME"}
                        {if $videos[c]->in_home == 1}
                           <a href="{url name=admin_video_toggle_inhome id=$videos[c]->id status=0 category=$category page=$page|default:1}" class="no_home" title="{t}Take out from home{/t}"></a>
                        {else}
                            <a href="{url name=admin_video_toggle_inhome id=$videos[c]->id status=1 category=$category page=$page|default:1}" class="go_home" title="{t}Put in home{/t}"></a>
                        {/if}
                    {/acl}
                    </td>
                    <td style="padding:1px; font-size:11px;" class="center">
                        <div class="btn-group">
                            {acl isAllowed="VIDEO_UPDATE"}
                            <a class="btn" href="{url name=admin_video_show id=$videos[c]->id}" title="{t}Edit{/t}" >
                                <i class="icon-pencil"></i> {t}Edit{/t}
                            </a>
                            {/acl}

                            {acl isAllowed="VIDEO_DELETE"}
                            <a class="del btn btn-danger" data-controls-modal="modal-from-dom"
                               data-id="{$videos[c]->id}"
                               data-title="{$videos[c]->title|capitalize}"
                               data-url="{url name=admin_video_delete id=$videos[c]->id}"
                               data-url-relations="{url name=admin_video_get_relations id=$videos[c]->id}"
                               href="{url name=admin_video_delete id=$videos[c]->id}" >
                                <i class="icon-trash icon-white"></i>
                            </a>
                            {/acl}
                        </div>
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
                    <td colspan="10" class="pagination">
                        {$pagination->links}&nbsp;
                    </td>
                </tr>
            </tfoot>
        </table>

    </div>
</form>
    <script>
    // <![CDATA[
        jQuery('#batch-publish').on('click', function(){
            jQuery('#formulario').attr('action', '{url name=admin_video_batchpublish}');
        });
        jQuery('#batch-unpublish').on('click', function(){
            jQuery('#formulario').attr('action', '{url name=admin_video_batchpublish}');
            e.preventDefault();
        });

        {if $category eq 'widget'}
            jQuery(document).ready(function() {
                makeSortable();
            });
        {/if}
    // ]]>
    </script>

    {include file="video/modals/_modalDelete.tpl"}
    {include file="video/modals/_modalBatchDelete.tpl"}
    {include file="video/modals/_modalAccept.tpl"}
{/block}
