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
            </ul>
        </div>
    </div>
    <div class="wrapper-content">

        <ul class="tabs2" style="margin-bottom: 28px;">
            <li>
                <a href="{$smarty.server.SCRIPT_NAME}?action=list&category=favorite" {if $category=='favorite'} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {else}{if $ca eq $datos_cat[0]->fk_content_category}style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}{/if} >WIDGET HOME</a>
            </li>
           {include file="menu_categorys.tpl" home=$smarty.server.SCRIPT_NAME|cat:"?action=list"}
        </ul>

        <div id="messageBoard"></div>

        {if (!empty($msg) || !empty($msgdel) || !empty($errors) )}
            <script type="text/javascript">
                showMsgContainer({ 'warn':['  {$msg} , {$msgdel}, {$errors} '] },'inline','messageBoard');
            </script>
        {/if}
        {render_messages}

        <div id="{$category}">
            <table class="adminheading">
                <tr>
                    <th nowrap>{t}Albums{/t}</th>
                </tr>
            </table>
            <table class="adminlist">
                <thead>
                    <tr>
                        <th class="title" style="width:35px;"></th>
                        <th>{t}Title{/t}</th>
                        <th>{t}Created{/t}</th>
                        <th align="center" style="width:35px;">{t}Views{/t}</th>
                        {if $category=='favorite'}<th align="center">{t}Section{/t}</th> {/if}
                        <th align="center">{t}Published{/t}</th>
                        <th align="center" style="width:35px;">{t}Favorite{/t}</th>
                        <th align="center" style="width:35px;">{t}Edit{/t}</th>
                        <th align="center" style="width:35px;">{t}Delete{/t}</th>
                    </tr>
                </thead>

                {section name=as loop=$albums}
                <tr {cycle values="class=row0,class=row1"}>
                    <td align="center">
                            <input type="checkbox" class="minput"  id="selected_{$smarty.section.as.iteration}" name="selected_fld[]" value="{$albums[as]->id}"  style="cursor:pointer;" >
                    </td>
                    <td>
                            <a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$albums[as]->pk_album}');" title="{$albums[as]->title|clearslash}">
                             {$albums[as]->title|clearslash}</a>
                    </td>
                    <td align="center">
                             {$albums[as]->created}
                    </td>
                     <td align="center">
                             {$albums[as]->views}
                    </td>
                    {if $category=='favorite'}
                            <td align="center">
                                 {$albums[as]->category_title}
                            </td>
                    {/if}
                    <td align="center">
                        {acl isAllowed="ALBUM_AVAILABLE"}
                            {if $albums[as]->available == 1}
                                    <a href="?id={$albums[as]->pk_album}&amp;action=change_status&amp;status=0&amp;page={$paginacion->_currentPage|default:0}" title={t}"Published"{/t}>
                                            <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt={t}"Published"{/t} /></a>
                            {else}
                                    <a href="?id={$albums[as]->pk_album}&amp;action=change_status&amp;status=1&amp;page={$paginacion->_currentPage|default:0}" title={t}"Pending{/t}>
                                            <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt={t}"Pending{/t}/></a>
                            {/if}
                        {/acl}
                    </td>

                    <td align="center">
                        {acl isAllowed="ALBUM_FAVORITE"}
                            {if $albums[as]->favorite == 1}
                               <a href="?id={$albums[as]->id}&amp;action=change_favorite&amp;status=0&amp;category={$category}&amp;page={$paginacion->_currentPage|default:0}" class="favourite_on" title={t}"Take out from frontpage"{/t}></a>
                            {else}
                                <a href="?id={$albums[as]->id}&amp;action=change_favorite&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage|default:0}" class="favourite_off" title={t}"Put in frontpage"{/t}></a>
                            {/if}
                        {/acl}
                    </td>
                    <td align="center">
                        {acl isAllowed="ALBUM_UPDATE"}
                            <a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$albums[as]->pk_album}');" title={t}"Edit"{/t}>
                                    <img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
                        {/acl}
                    </td>
                    <td align="center">
                        {acl isAllowed="ALBUM_DELETE"}
                            <a href="#" onClick="javascript:delete_album('{$albums[as]->pk_album}','{$paginacion->_currentPage|default:0}');" title={t}Delete{/t}>
                                    <img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
                        {/acl}
                    </td>

                </tr>
                {sectionelse}
                <tr>
                        <td align="center" colspan=9><br><br><h2>{t}No album saved{/t}</h2><br><br></td>
                </tr>
            {/section}
                <tfoot>
                  <td colspan="9"> {if !empty($pagination)} {$paginacion->links|default:""} {/if}</td>
                </tfoot>
            </table>
        </div>

        <input type="hidden" id="action" name="action" value="" />
        <input type="hidden" name="id" id="id" value="{$id|default:""}" />
    </div>
</form>
{/block}
