{extends file="base/admin.tpl"}

{block name="header-js" append}
    <script>
        var album_manager_urls = {
            batch_delete: '{url name=admin_album_batchdelete category=$category}'
        }
    </script>
    {script_tag src="/onm/jquery-functions.js" language="javascript"}

{/block}

{block name="content"}
<form action="#" method="get" name="formulario" id="formulario">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title">
                <h2>{t}Albums{/t} :: </h2>
                <div class="section-picker">
                    <div class="title-picker btn"><span class="text">{if $category== 'all'}{t}All categories{/t}{elseif $category == 'widget'}{t}WIDGET HOME{/t}{else}{$datos_cat[0]->title}{/if}</span> <span class="caret"></span></div>
                    <div class="options">
                        <h4>{t}Other{/t}</h4>
                        <a href="{url name=admin_albums_widget}" {if $category == 'widget'}class="active"{/if}>{t}WIDGET HOME{/t}</a>
                        {include file="common/drop_down_categories.tpl" home={url name=admin_albums l=1 status=$status}}
                    </div>
                </div>
            </div>
            <ul class="old-button">
                {acl isAllowed="ALBUM_DELETE"}
                <li>
                    <a class="delChecked" data-controls-modal="modal-album-batchDelete" href="#" title="{t}Delete{/t}">
                        <img src="{$params.IMAGE_DIR}trash.png" border="0"  title="{t}Delete{/t}" alt="{t}Delete{/t}" ><br />{t}Delete{/t}
                    </a>
                </li>
                {/acl}
                {acl isAllowed="ALBUM_AVAILABLE"}
                <li>
                    <button id="batch-publish" type="submit" name="status" value="0">
                       <img border="0" src="{$params.IMAGE_DIR}publish_no.gif" title="{t}Unpublish{/t}" alt="{t}Unpublish{/t}" ><br />{t}Unpublish{/t}
                   </button>
               </li>
               <li>
                   <button id="batch-unpublish" type="submit" name="status" value="1">
                       <img border="0" src="{$params.IMAGE_DIR}publish.gif" title="{t}Publish{/t}" alt="{t}Publish{/t}" ><br />{t}Publish{/t}
                   </button>
               </li>
                {/acl}

                {acl isAllowed="ALBUM_CREATE"}
                <li>
                    <a href="{url name=admin_album_create category=$category}" title="{t}New album{/t}" >
                        <img src="{$params.IMAGE_DIR}/album.png" alt="{t}New album{/t}"><br />{t}New album{/t}
                    </a>
                </li>
                {/acl}
                {acl isAllowed="ALBUM_WIDGET"}
                     {if $category eq 'widget'}
                        <li class="separator"></li>
                        <li>
                            <a href="#" onClick="javascript:saveSortPositions('{url name=admin_albums_savepositions}');" title="{t}Save positions{/t}">
                                <img src="{$params.IMAGE_DIR}save.png" alt="{t}Save positions{/t}"><br />{t}Save positions{/t}
                            </a>
                        </li>
                    {/if}
                {/acl}
                {acl isAllowed="ALBUM_SETTINGS"}
                <li class="separator"></li>
                    <li>
                        <a href="{url name=admin_albums_config}" title="{t}Config album module{/t}">
                            <img src="{$params.IMAGE_DIR}template_manager/configure48x48.png" alt="" /><br />
                            {t}Settings{/t}
                        </a>
                    </li>
                {/acl}
            </ul>
        </div>
    </div>
    <div class="wrapper-content">

        {render_messages}

        {* MENSAJES DE AVISO GUARDAR POS******* *}
        <div id="warnings-validation"></div>

        <table class="table table-hover table-condensed">
            <thead>
                <tr>
                    <th style="width:15px;">
                        <input type="checkbox" class="toggleallcheckbox">
                    </th>
                    <th></th>
                    <th class="title">{t}Title{/t}</th>
                    {if $category=='widget' || $category=='all'}<th style="width:65px;" class="left">{t}Section{/t}</th>{/if}
                    <th class="left nowrap" style="width:100px;">Created</th>
                    <!-- <th class="center" style="width:40px"><img src="{$params.IMAGE_DIR}seeing.png" alt="{t}Views{/t}" title="{t}Views{/t}"></th> -->
                    <th class="center" style="width:35px;">{t}Published{/t}</th>
                    {if $category!='widget'} <th class="center" style="width:35px;">{t}Favorite{/t}</th>{/if}
                    <th class="center" style="width:35px;">{t}Home{/t}</th>
                    <th class="right" style="width:110px;">{t}Actions{/t}</th>
                </tr>
            </thead>
            <tbody class="sortable">
            {foreach name=as from=$albums item=album}
            <tr data-id="{$album->pk_album}">
                <td class="center">
                    <input type="checkbox" class="minput"  id="selected_{$smarty.section.as.iteration}" name="selected_fld[]" value="{$album->id}"  style="cursor:pointer;" >
                </td>
                <td>
                    {if !empty($album->cover)}
                        <img src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$album->cover}" style="max-height:60px; max-width:80px;"/>
                    {else}
                        <img src="http://placehold.it/80x60" />
                    {/if}

                </td>
                <td>
                    <a href="{url name=admin_album_show id=$album->pk_album}" title="{$album->title|clearslash}">
                        {$album->title|clearslash}
                    </a>
                </td>
                {if $category=='widget' || $category=='all'}
                <td class="left">
                     {$album->category_name}
                </td>
                {/if}
                <td class="center nowrap">{$album->created}</td>
                <!-- <td class="center">{$album->views}</td> -->
                <td class="center">
                    {acl isAllowed="ALBUM_AVAILABLE"}
                        {if $album->available == 1}
                                <a href="{url name=admin_album_toggle_available id=$album->pk_album status=0 category=$category page=$paginacion->_currentPage|default:1}" title="{t}Published{/t}">
                                        <img src="{$params.IMAGE_DIR}publish_g.png" alt="{t}Published{/t}" /></a>
                        {else}
                                <a href="{url name=admin_album_toggle_available id=$album->pk_album status=1 category=$category page=$paginacion->_currentPage|default:1}" title="{t}Pending{/t}">
                                        <img src="{$params.IMAGE_DIR}publish_r.png" alt="{t}Pending{/t}"/></a>
                        {/if}
                    {/acl}
                </td>
                {if $category!='widget'}
                <td class="center">
                    {acl isAllowed="ALBUM_FAVORITE"}
                        {if $album->favorite == 1}
                           <a href="{url name=admin_album_toggle_favorite id=$album->pk_album status=0 category=$category page=$paginacion->_currentPage|default:1}" class="favourite_on" title="{t}Take out from frontpage{/t}"></a>
                        {else}
                            <a href="{url name=admin_album_toggle_favorite id=$album->pk_album status=1 category=$category page=$paginacion->_currentPage|default:1}" class="favourite_off" title="{t}Put in frontpage{/t}"></a>
                        {/if}
                    {/acl}
                </td>
                {/if}
                <td class="center">
                    {acl isAllowed="ALBUM_HOME"}
                        {if $album->in_home == 1}
                           <a href="{url name=admin_album_toggle_inhome id=$album->pk_album status=0 category=$category page=$paginacion->_currentPage|default:1}" class="no_home" title="{t}Take out from home{/t}"></a>
                        {else}
                            <a href="{url name=admin_album_toggle_inhome id=$album->pk_album status=1 category=$category page=$paginacion->_currentPage|default:1}" class="go_home" title="{t}Put in home{/t}"></a>
                        {/if}
                    {/acl}
                </td>
                <td class="right">
                    <div class="btn-group">
                        {acl isAllowed="ALBUM_UPDATE"}
                        <a class="btn" href="{url name=admin_album_show id=$album->id}" title="{t}Edit{/t}" >
                            <i class="icon-pencil"></i> {t}Edit{/t}
                        </a>
                        {/acl}

                        {acl isAllowed="ALBUM_DELETE"}
                        <a class="del btn btn-danger" data-controls-modal="modal-from-dom"
                           data-id="{$album->id}"
                           data-title="{$album->title|capitalize}"
                           data-url="{url name=admin_album_delete id=$album->id}"
                           href="{url name=admin_album_delete id=$album->id}" >
                            <i class="icon-trash icon-white"></i>
                        </a>
                        {/acl}
                    </div>
                </td>

            </tr>
            {foreachelse}
            <tr>
                <td class="empty" colspan=9>{t}There is no albums yet{/t}</td>
            </tr>
            {/foreach}
            </tbody>
            <tfoot>
              <td colspan="11" class="center">
                <div class="pagination">
                    {$pagination->links|default:""}
                </div>
              </td>
            </tfoot>
        </table>

        <input type="hidden" name="page" id="page" value="{$page}" />
    </div>
</form>
    <script>
    // <![CDATA[
        jQuery('#batch-publish').on('click', function(){
            jQuery('#formulario').attr('action', '{url name=admin_album_batchpublish}');
        });
        jQuery('#batch-unpublish').on('click', function(){
            jQuery('#formulario').attr('action', '{url name=admin_album_batchpublish}');
        });

        {if $category eq 'widget'}
            jQuery(document).ready(function() {
                makeSortable();
            });
        {/if}
    // ]]>
    </script>

    {include file="album/modals/_modalDelete.tpl"}
    {include file="album/modals/_modalBatchDelete.tpl"}
    {include file="album/modals/_modalAccept.tpl"}
{/block}
