{extends file="base/admin.tpl"}

{block name="header-js" append}
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilsalbum.js"></script>

{/block}

{block name="footer-js" append}
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}cropper.js"></script>
     <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilsGallery.js"></script>
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
                        <img border="0" src="{$params.IMAGE_DIR}trash.png" title="Eliminar" alt="Eliminar" ><br />Eliminar
                    </a>
                </li>
                {/acl}
                {acl isAllowed="ALBUM_AVAILABLE"}
                <li>
                    <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 0);" name="submit_mult" value="noFrontpage" title="noFrontpage">
                        <img border="0" src="{$params.IMAGE_DIR}publish_no.gif" title="noFrontpage" alt="noFrontpage" ><br />Despublicar
                    </a>
                </li>
                {/acl}
                {acl isAllowed="ALBUM_AVAILABLE"}
                <li>
                    <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 1);" name="submit_mult" value="Frontpage" title="Frontpage">
                        <img border="0" src="{$params.IMAGE_DIR}publish.gif" title="Publicar" alt="Publicar" ><br />Publicar
                    </a>
                </li>
                {/acl}
                <li>
                    <button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; width: 95px;" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button');">
                        <img id="select_button" class="icon" src="{$params.IMAGE_DIR}select_button.png" title="Seleccionar Todo" alt="Seleccionar Todos" status="0">
                    </button>
                </li>
                {acl isAllowed="ALBUM_CREATE"}
                <li>
                    <a href="{$smarty.server.PHP_SELF}?action=new" onmouseover="return escape('<u>N</u>uevo Album');" accesskey="N" tabindex="1">
                        <img border="0" src="{$params.IMAGE_DIR}/album.png" title="Nuevo Album" alt="Nuevo Album"><br />Nuevo Album
                    </a>
                </li>
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

        <ul class="pills clearfix">
            <li>
                <a href="{$smarty.server.SCRIPT_NAME}?action=list&category=favorite" {if $category=='favorite'}class="active"{elseif $ca eq $datos_cat[0]->fk_content_category}class="active"{/if}>{t}WIDGET HOME{/t}</a>
            </li>
           {include file="menu_categories.tpl" home=$smarty.server.SCRIPT_NAME|cat:"?action=list"}
        </ul>

        {render_messages}

        <table class="listing-table">
            <thead>
                <tr>

                    <th style="width:15px;"><input type="checkbox" class="minput"  id="selected_{$smarty.section.as.iteration}" name="selected_fld[]" value="{$albums[as]->id}"  style="cursor:pointer;" ></th>
                    <th class="title">{t}Title{/t}</th>
                    <th class="center" style="width:35px;">{t}Views{/t}</th>
                    {if $category=='favorite'}<th style="width:65px;" class="center">{t}Section{/t}</th>{/if}
                    <th class="center" style="width:100px;">Created</th>
                    <th class="center" style="width:35px;">{t}Published{/t}</th>
                    <th class="center" style="width:35px;">{t}Favorite{/t}</th>
                    <th class="center" style="width:35px;">{t}Actions{/t}</th>
                </tr>
            </thead>

            {section name=as loop=$albums}
            <tr {cycle values="class=row0,class=row1"}>
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
                {if $category=='favorite'}
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
                                <a href="?id={$albums[as]->pk_album}&amp;action=change_status&amp;status=0&amp;category={$category}&amp;page={$paginacion->_currentPage|default:0}" title={t}"Published"{/t}>
                                        <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt={t}"Published"{/t} /></a>
                        {else}
                                <a href="?id={$albums[as]->pk_album}&amp;action=change_status&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage|default:0}" title={t}"Pending{/t}>
                                        <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt={t}"Pending{/t}/></a>
                        {/if}
                    {/acl}
                </td>

                <td class="center">
                    {acl isAllowed="ALBUM_FAVORITE"}
                        {if $albums[as]->favorite == 1}
                           <a href="?id={$albums[as]->id}&amp;action=change_favorite&amp;status=0&amp;category={$category}&amp;page={$paginacion->_currentPage|default:0}" class="favourite_on" title={t}"Take out from frontpage"{/t}></a>
                        {else}
                            <a href="?id={$albums[as]->id}&amp;action=change_favorite&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage|default:0}" class="favourite_off" title={t}"Put in frontpage"{/t}></a>
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
                           <a href="#" onClick="javascript:delete_album('{$albums[as]->pk_album}','{$paginacion->_currentPage|default:0}');" title={t}Delete{/t}>
                                   <img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
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
{/block}
