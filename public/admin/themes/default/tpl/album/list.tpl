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
<form action="#" method="post" name="formulario" id="formulario">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{$titulo_barra}::&nbsp; {if $category eq 0}Widget Home{else}{$datos_cat[0]->title}{/if}</h2></div>
            <ul class="old-button">
                {acl isAllowed="ALBUM_DELETE"}
                <li>
                    <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);" name="submit_mult" value="Eliminar" title="Eliminar">
                        <img border="0" src="{$params.IMAGE_DIR}trash.png" title="Eliminar" alt="Eliminar" ><br />{t}Eliminar{/t}
                    </a>
                </li>
                {/acl}
                {acl isAllowed="ALBUM_AVAILABLE"}
                <li>
                    <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 0);" name="submit_mult" value="noFrontpage" title="noFrontpage">
                        <img border="0" src="{$params.IMAGE_DIR}publish_no.gif" title="noFrontpage" alt="noFrontpage" ><br />{t}Unpublish{/t}
                    </a>
                </li>
                {/acl}
                {acl isAllowed="ALBUM_AVAILABLE"}
                <li>
                    <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 1);" name="submit_mult" value="Frontpage" title="Frontpage">
                        <img border="0" src="{$params.IMAGE_DIR}publish.gif" title="Publicar" alt="Publicar" ><br />{t}Publish{/t}
                    </a>
                </li>
                {/acl}
                {acl isAllowed="ALBUM_CREATE"}
                <li>
                    <a href="{$smarty.server.PHP_SELF}?action=new" onmouseover="return escape('<u>N</u>uevo Album');" accesskey="N" tabindex="1">
                        <img border="0" src="{$params.IMAGE_DIR}/album.png" title="Nuevo Album" alt="Nuevo Album"><br />{t}New album{/t}
                    </a>
                </li>
                {/acl}
                {acl isAllowed="ALBUM_WIDGET"}
                     {if $category eq 'widget'}
                        <li class="separator"></li>
                        <li>
                            <a href="#" class="admin_add" onClick="javascript:saveSortPositions('{$smarty.server.PHP_SELF}');" title="Guardar Positions" alt="Guardar Posiciones">
                                <img border="0" src="{$params.IMAGE_DIR}save.png" title="Guardar Cambios" alt="Guardar Posiciones"><br />{t}Save positions{/t}
                            </a>
                        </li>
                    {/if}
                {/acl}
                {acl isAllowed="ALBUM_SETTINGS"}
                <li class="separator"></li>
                    <li>
                        <a href="{$smarty.server.PHP_SELF}?action=config" class="admin_add" title="{t}Config album module{/t}">
                            <img border="0" src="{$params.IMAGE_DIR}template_manager/configure48x48.png" alt="" /><br />
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
                <a href="{$smarty.server.SCRIPT_NAME}?action=list&category=widget" {if $category=='widget'}class="active"{/if}>{t}WIDGET HOME{/t}</a>
            </li>
            <li>
                <a href="{$smarty.server.SCRIPT_NAME}?action=list&category=all" {if $category==='all'}class="active"{/if} >{t}All categories{/t}</a>
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
                    <a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$albums[as]->pk_album}');" title="{$albums[as]->title|clearslash}">
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
                                <a href="?id={$albums[as]->pk_album}&amp;action=change_status&amp;status=0&amp;category={$category}&amp;page={$paginacion->_currentPage|default:0}" title="{t}Published{/t}">
                                        <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="{t}Published{/t}" /></a>
                        {else}
                                <a href="?id={$albums[as]->pk_album}&amp;action=change_status&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage|default:0}" title="{t}Pending{/t}">
                                        <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="{t}Pending{/t}"/></a>
                        {/if}
                    {/acl}
                </td>
                 {if $category!='widget' && $category!='all'}
                <td class="center">
                    {acl isAllowed="ALBUM_FAVORITE"}
                        {if $albums[as]->favorite == 1}
                           <a href="?id={$albums[as]->id}&amp;action=change_favorite&amp;status=0&amp;category={$category}&amp;page={$paginacion->_currentPage|default:0}" class="favourite_on" title="{t}Take out from frontpage{/t}"></a>
                        {else}
                            <a href="?id={$albums[as]->id}&amp;action=change_favorite&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage|default:0}" class="favourite_off" title="{t}Put in frontpage{/t}"></a>
                        {/if}
                    {/acl}
                </td>
                {/if}
                <td class="center">
                    {acl isAllowed="ALBUM_HOME"}
                        {if $albums[as]->in_home == 1}
                           <a href="?id={$albums[as]->id}&amp;action=change_inHome&amp;status=0&amp;category={$category}&amp;page={$paginacion->_currentPage|default:0}" class="no_home" title="{t}Take out from home{/t}"></a>
                        {else}
                            <a href="?id={$albums[as]->id}&amp;action=change_inHome&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage|default:0}" class="go_home" title="{t}Put in home{/t}"></a>
                        {/if}
                    {/acl}
                </td>
                <td class="center">
                    <ul class="action-buttons">
                        {acl isAllowed="ALBUM_UPDATE"}
                        <li>
                           <a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$albums[as]->pk_album}');" title="{t}Edit{/t}" >
                                   <img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
                        </li>
                        {/acl}

                        {acl isAllowed="ALBUM_DELETE"}
                        <li>
                            <a href="#" onClick="javascript:delete_album('{$albums[as]->pk_album}','{$paginacion->_currentPage|default:0}');" title="{t}Delete{/t}">
                               <img src="{$params.IMAGE_DIR}trash.png" border="0" />
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
                {$paginacion->links|default:""}&nbsp;
              </td>
            </tfoot>
        </table>

        <input type="hidden" id="action" name="action" value="" />
        <input type="hidden" name="id" id="id" value="{$id|default:""}" />
    </div>
</form>
{if $category eq 'widget'}
        <script type="text/javascript">

        // <![CDATA[

            jQuery(document).ready(function() {
                makeSortable();
            });
        // ]]>
    </script>
{/if}
{/block}
