{extends file="base/admin.tpl"}

{block name="header-js" append}
    {script_tag src="/onm/jquery-functions.js" language="javascript"}

    {script_tag src="/utilsalbum.js" language="javascript"}

{/block}

{block name="footer-js" append}
    {script_tag src="/cropper.js" language="javascript"}
    {script_tag src="/utilsGallery.js" language="javascript"}
{/block}

{block name="content"}
<form action="#" method="get" name="formulario" id="formulario">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title">
                <h2>{t}Album manager{/t} :: {if $category eq 0}Widget Home{else}{$datos_cat[0]->title}{/if}</h2>
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

                {acl isAllowed="ALBUM_CREATE"}
                <li>
                    <a href="{$smarty.server.PHP_SELF}?action=new" title="{t}New album{/t}" >
                        <img src="{$params.IMAGE_DIR}/album.png" alt="{t}New album{/t}"><br />{t}New album{/t}
                    </a>
                </li>
                {/acl}
                {acl isAllowed="ALBUM_WIDGET"}
                     {if $category eq 'widget'}
                        <li class="separator"></li>
                        <li>
                            <a href="#" onClick="javascript:saveSortPositions('{$smarty.server.PHP_SELF}');" title="{t}Save positions{/t}">
                                <img src="{$params.IMAGE_DIR}save.png" alt="{t}Save positions{/t}"><br />{t}Save positions{/t}
                            </a>
                        </li>
                    {/if}
                {/acl}
                {acl isAllowed="ALBUM_SETTINGS"}
                <li class="separator"></li>
                    <li>
                        <a href="{$smarty.server.PHP_SELF}?action=config" title="{t}Config album module{/t}">
                            <img src="{$params.IMAGE_DIR}template_manager/configure48x48.png" alt="" /><br />
                            {t}Configurations{/t}
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
                <a href="{$smarty.server.SCRIPT_NAME}?action=list&amp;category=widget" {if $category=='widget'}class="active"{/if}>{t}WIDGET HOME{/t}</a>
            </li>
            <li>
                <a href="{$smarty.server.SCRIPT_NAME}?action=list&amp;category=all" {if $category==='all'}class="active"{/if} >{t}All categories{/t}</a>
            </li>
           {include file="menu_categories.tpl" home=$smarty.server.SCRIPT_NAME|cat:"?action=list"}
        </ul>

        {* MENSAJES DE AVISO GUARDAR POS******* *}
        <div id="warnings-validation"></div>

        <table class="listing-table">
            <thead>
                <tr>
                    <th style="width:15px;">
                        <input type="checkbox" id="toggleallcheckbox">
                    </th>
                    <th class="title">{t}Title{/t}</th>
                    <th class="center" style="width:40px"><img src="{$params.IMAGE_DIR}seeing.png" alt="{t}Views{/t}" title="{t}Views{/t}"></th>
                    {if $category=='widget' || $category=='all'}<th style="width:65px;" class="center">{t}Section{/t}</th>{/if}
                    <th class="center" style="width:100px;">Created</th>
                    <th class="center" style="width:35px;">{t}Published{/t}</th>
                    {if $category!='widget' && $category!='all'} <th class="center" style="width:35px;">{t}Favorite{/t}</th>{/if}
                    <th class="center" style="width:35px;">{t}Home{/t}</th>
                    <th class="center" style="width:35px;">{t}Actions{/t}</th>
                </tr>
            </thead>
            <tbody class="sortable">
            {section name=as loop=$albums}
            <tr data-id="{$albums[as]->pk_album}">
                <td class="center">
                    <input type="checkbox" class="minput"  id="selected_{$smarty.section.as.iteration}" name="selected_fld[]" value="{$albums[as]->id}"  style="cursor:pointer;" >
                </td>
                <td>
                    <a href="{$smarty.server.PHP_SELF}?action=read&amp;id={$albums[as]->pk_album}" title="{$albums[as]->title|clearslash}">
                        {$albums[as]->title|clearslash}
                    </a>
                </td>
                 <td class="center">
                    {$albums[as]->views}
                </td>
                {if $category=='widget' || $category=='all'}
                <td class="center">
                     {$albums[as]->category_title}
                </td>
                {/if}
                <td class="center">
                         {$albums[as]->created}
                </td>
                <td class="center">
                    {acl isAllowed="ALBUM_AVAILABLE"}
                        {if $albums[as]->available == 1}
                                <a href="{$smarty.server.PHP_SELF}?id={$albums[as]->pk_album}&amp;action=change_status&amp;status=0&amp;category={$category}&amp;page={$paginacion->_currentPage|default:0}" title="{t}Published{/t}">
                                        <img src="{$params.IMAGE_DIR}publish_g.png" alt="{t}Published{/t}" /></a>
                        {else}
                                <a href="{$smarty.server.PHP_SELF}?id={$albums[as]->pk_album}&amp;action=change_status&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage|default:0}" title="{t}Pending{/t}">
                                        <img src="{$params.IMAGE_DIR}publish_r.png" alt="{t}Pending{/t}"/></a>
                        {/if}
                    {/acl}
                </td>
                 {if $category!='widget' && $category!='all'}
                <td class="center">
                    {acl isAllowed="ALBUM_FAVORITE"}
                        {if $albums[as]->favorite == 1}
                           <a href="{$smarty.server.PHP_SELF}?id={$albums[as]->id}&amp;action=change_favorite&amp;status=0&amp;category={$category}&amp;page={$paginacion->_currentPage|default:0}" class="favourite_on" title="{t}Take out from frontpage{/t}"></a>
                        {else}
                            <a href="{$smarty.server.PHP_SELF}?id={$albums[as]->id}&amp;action=change_favorite&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage|default:0}" class="favourite_off" title="{t}Put in frontpage{/t}"></a>
                        {/if}
                    {/acl}
                </td>
                {/if}
                <td class="center">
                    {acl isAllowed="ALBUM_HOME"}
                        {if $albums[as]->in_home == 1}
                           <a href="{$smarty.server.PHP_SELF}?id={$albums[as]->id}&amp;action=change_inHome&amp;status=0&amp;category={$category}&amp;page={$page|default:0}" class="no_home" title="{t}Take out from home{/t}"></a>
                        {else}
                            <a href="{$smarty.server.PHP_SELF}?id={$albums[as]->id}&amp;action=change_inHome&amp;status=1&amp;category={$category}&amp;page={$page|default:0}" class="go_home" title="{t}Put in home{/t}"></a>
                        {/if}
                    {/acl}
                </td>
                <td class="center">
                    <ul class="action-buttons">
                        {acl isAllowed="ALBUM_UPDATE"}
                        <li>
                           <a href="{$smarty.server.PHP_SELF}?action=read&amp;id={$albums[as]->pk_album}" title="{t}Edit{/t}" >
                                <img src="{$params.IMAGE_DIR}edit.png" />
                            </a>
                        </li>
                        {/acl}
                        {acl isAllowed="ALBUM_DELETE"}
                        <li>
                            <a class="del" data-controls-modal="modal-from-dom" data-id="{$albums[as]->id}"
                               data-title="{$albums[as]->title|capitalize}" href="#" title="{t}Delete{/t}">
                               <img src="{$params.IMAGE_DIR}trash.png" />
                            </a>
                        </li>
                        {/acl}
                    </ul>
                </td>

            </tr>
            {sectionelse}
            <tr>
                <td class="empty" colspan=9>{t}There is no albums yet{/t}</td>
            </tr>
            {/section}
            </tbody>
            <tfoot>
              <td colspan="9">
                {$pagination|default:""}&nbsp;
              </td>
            </tfoot>
        </table>

        <input type="hidden" name="page" id="page" value="{$page|default:0}" />
        <input type="hidden" name="category" id="category" value="{$category}" />
        <input type="hidden" id="status" name="status" value="" />
        <input type="hidden" id="action" name="action" value="" />
        <input type="hidden" name="id" id="id" value="{$id|default:""}" />
    </div>
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

    {include file="album/modals/_modalDelete.tpl"}
    {include file="album/modals/_modalBatchDelete.tpl"}
    {include file="album/modals/_modalAccept.tpl"}
{/block}
