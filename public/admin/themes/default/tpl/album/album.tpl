{extends file="base/admin.tpl"}

{block name="footer-js" append}
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilsalbum.js"></script>
{/block}




{block name="content"}
    <form action="#" method="post" name="formulario" id="formulario" {$formAttrs}
         style="max-width:70% !important; margin: 0 auto; display:block;">

    {* LISTADO ******************************************************************* *}
    {if !isset($smarty.request.action) || $smarty.request.action eq "list"}

        {if $category eq '3' && $totalalbums neq '4'}
            <script type="text/javascript">
                {literal}
                    showMsg({'warn':['Debe tener 4 albums favoritos para portada.<br />  ']},'inline');
                {/literal}
            </script>
        {/if}

        <ul class="tabs2" style="margin-bottom: 28px;">
            <li>
                <a href="{$smarty.server.SCRIPT_NAME}?action=list&category=3" {if $category==3} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {else}{if $ca eq $datos_cat[0]->fk_content_category}style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}{/if} >WIDGET HOME</a>
            </li>
           {include file="menu_categorys.tpl" home=$smarty.server.SCRIPT_NAME|cat:"?action=list"}
        </ul>

        <br class="clear"/>
        {include file="botonera_up.tpl"}

        <div id="{$category}">
            <table class="adminheading">
                <tr>
                    <th nowrap>Albums</th>
                </tr>
            </table>
            <div><h2 style="color:#BB1313">{$smarty.request.msg}</h2></div>
            <table class="adminlist">
                <tr>
                    <th></th>
                    <th style="padding:10px;" align='left'>T&iacute;tulo</th>
                    <th>Creada</th>
                    <th>Visto</th>
                    <th align="center">Publicado</th>
                    <th>Favorito</th>
                    <th>Modificar</th>
                    <th>Eliminar</th>
                </tr>

                {section name=as loop=$albums}
                    <tr {cycle values="class=row0,class=row1"}>
                        <td style="padding:10px;font-size: 11px;">
                                <input type="checkbox" class="minput"  id="selected_{$smarty.section.as.iteration}" name="selected_fld[]" value="{$albums[as]->id}"  style="cursor:pointer;" >
                        </td>
                        <td style="padding:10px;font-size: 11px;width:60%;">
                                <a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$albums[as]->pk_album}');" title="{$albums[as]->title|clearslash}">
                                 {$albums[as]->title|clearslash}</a>
                        </td>
                        <td style="padding:10px;font-size: 11px;width:20%;">
                                 {$albums[as]->created}
                        </td>
                         <td style="padding:10px;font-size: 11px;width:20%;">
                                 {$albums[as]->views}
                        </td>
                        <td style="padding:10px;width:10%;" align="center">
                                {if $albums[as]->available == 1}
                                        <a href="?id={$albums[as]->pk_album}&amp;action=change_status&amp;status=0&amp;page={$paginacion->_currentPage}" title="Publicado">
                                                <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicado" /></a>
                                {else}
                                        <a href="?id={$albums[as]->pk_album}&amp;action=change_status&amp;status=1&amp;page={$paginacion->_currentPage}" title="Pendiente">
                                                <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Pendiente" /></a>
                                {/if}
                        </td>

                        <td style="padding:10px;font-size: 11px;width:20%;">
                                {if $albums[as]->favorite == 1}
                                   <a href="?id={$albums[as]->id}&amp;action=change_favorite&amp;status=0&amp;category={$category}&amp;page={$paginacion->_currentPage}" class="favourite_on" title="Quitar de Portada"></a>
                                {else}
                                    <a href="?id={$albums[as]->id}&amp;action=change_favorite&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage}" class="favourite_off" title="Meter en Portada"></a>
                                {/if}
                        </td>
                        <td style="padding:10px;width:10%;" align="center">
                                <a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$albums[as]->pk_album}');" title="Modificar">
                                        <img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
                        </td>

                        <td style="padding:10px;width:10%;" align="center">
                                <a href="#" onClick="javascript:delete_album('{$albums[as]->pk_album}','{$paginacion->_currentPage}');" title="Eliminar">
                                        <img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
                        </td>

                </tr>
                {sectionelse}
                <tr>
                        <td align="center" colspan=5><br><br><h2><b>Ning&uacute;n album guardado </b></h2><br><br></td>
                </tr>
            {/section}
            {if count($albums) gt 0}
                <tr>
                  <td colspan="6" style="padding:10px;font-size: 12px;" align="center"><br><br>{$paginacion->links}<br><br></td>
                </tr>
            {/if}
            </table>
        </div>

        {if $smarty.get.alert eq 'ok'}
            <script type="text/javascript" language="javascript">
                {literal}
                       alert('{/literal}{$smarty.get.msgdel}{literal}');
                {/literal}
            </script>
        {/if}


    {/if}
    
    {* FORMULARIO PARA ENGADIR UN CONTENIDO ALBUM ************************************** *}

    {if isset($smarty.request.action) && $smarty.request.action eq "new"}

        {include file="botonera_up.tpl"}

        <ul id="tabs">
            <li>
                    <a href="#edicion-contenido">Nuevo Album</a>
            </li>
        </ul>

        <div class="panel" id="edicion-contenido" style="width:95%">
            <table border="0" cellpadding="2" cellspacing="2" class="fuente_cuerpo" width="96%">
                <tbody>
                <tr>
                    <td valign="top" align="right" style="padding:4px;">
                        <label for="title">Titulo:</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap">
                        <input type="text" id="title" name="title" title="Album"
                            size="80" value="" onBlur="javascript:get_metadata(this.value);"  />

                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right" style="padding:4px;">
                        <label for="title">Agencia:</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap">
                        <input type="text" id="agency" name="agency" title="Album"
                            size="80" value="" />
                    </td>
                    <td rowspan="3">
                        <table style='background-color:#F5F5F5; padding:18px; width:99%;'>

                                    <tr>
                                        <td valign="top"  align="right" nowrap="nowrap">
                                            <label for="title">Secci&oacute;n:</label>
                                        </td>
                                        <td nowrap="nowrap">
                                            <select name="category" id="category"  >
                                                {section name=as loop=$allcategorys}
                                                    {acl hasCategoryAccess=$allcategorys[as]->pk_content_category}
                                                        <option value="{$allcategorys[as]->pk_content_category}" {if $category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$allcategorys[as]->title}" >{$allcategorys[as]->title}</option>
                                                        {section name=su loop=$subcat[as]}
                                                            {acl hasCategoryAccess=$allcategorys[as]->pk_content_category}
                                                                <option value="{$subcat[as][su]->pk_content_category}" {if $category eq $subcat[as][su]->pk_content_category}selected{/if} name="{$subcat[as][su]->title}">&nbsp;&nbsp;&nbsp;&nbsp;{$subcat[as][su]->title}</option>
                                                            {/acl}
                                                        {/section}
                                                    {/acl}
                                                {/section}
                                                </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td valign="top"  align="right" nowrap="nowrap">
                                            <label for="title"> Disponible: </label>
                                        </td>
                                        <td valign="top" nowrap="nowrap">
                                            <select name="available" id="available" class="required">
                                                <option value="0">No</option>
                                                <option value="1" selected>Si</option>
                                           </select>
                                </td>
                                    </tr>
                                </table>
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right" style="padding:4px;">
                        <label for="title">Descripci&oacute;n:</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap">
                        <textarea name="description" id="description"  title="descripcion" style="width:100%; height:8em;"></textarea>
                    </td>
                </tr>

                <tr>
                    <td valign="top" align="right" style="padding:4px;">
                        <label for="metadata">Palabras clave: </label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap"">
                        <input type="text" id="metadata" name="metadata" size="80" title="Metadatos" value="" /><br>
                        <label align='right'><sub>Separadas por comas</sub></label><br>
                    </td>
                </tr>
                {include file="album/album_images.tpl"}

                </tbody>
            </table>
        </div>

    {/if}


    {* FORMULARIO PARA ACTUALIZAR UN CONTENIDO*********************************** *}
    {if isset($smarty.request.action) && $smarty.request.action eq "read"}

        {include file="botonera_up.tpl"}

        <ul id="tabs">
            <li>
                <a href="#edicion-contenido">Edici&oacute;n Album</a>
            </li>

        </ul>

        <div class="panel" id="edicion-contenido" style="width:95%">
            <table border="0" cellpadding="2" cellspacing="2" class="fuente_cuerpo" width="90%">
                <tbody>
                    <tr>
                        <td valign="top" align="right" style="padding:4px;">
                            <label for="title">T&iacute;tulo:</label>
                        </td>
                        <td style="padding:4px;" nowrap="nowrap">
                            <input type="text" id="title" name="title" title="Album"
                                size="80" value="{$album->title|clearslash|escape:"html"}" onBlur="javascript:get_metadata(this.value);" />

                        </td>
                    </tr>
                    <tr>
                        <td valign="top" align="right" style="padding:4px;" >
                            <label for="title">Agencia:</label>
                        </td>
                        <td style="padding:4px;" nowrap="nowrap">
                            <input type="text" id="agency" name="agency" title="Album"
                                size="80" value="{$album->agency|clearslash|escape:"html"}" />
                        </td>
                        <td rowspan=3>
                            <table style='background-color:#F5F5F5; padding:18px; width:99%;'>
                                <tr>
                                    <td valign="top"  align="right" nowrap="nowrap">
                                    <label for="title">Secci&oacute;n:</label>
                                    </td>
                                    <td nowrap="nowrap">
                                        <select name="category" id="category"  >
                                            {*<option value="3" {if $category eq 3} selected{/if} name="Album" >Album</option>             *}
                                            {section name=as loop=$allcategorys}
                                                <option value="{$allcategorys[as]->pk_content_category}" {if $category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$allcategorys[as]->title}" >{$allcategorys[as]->title}</option>
                                                {section name=su loop=$subcat[as]}
                                                    <option value="{$subcat[as][su]->pk_content_category}" {if $category eq $subcat[as][su]->pk_content_category}selected{/if} name="{$subcat[as][su]->title}">&nbsp;&nbsp;&nbsp;&nbsp;{$subcat[as][su]->title}</option>
                                                {/section}
                                            {/section}
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top"  align="right" nowrap="nowrap">
                                        <label for="title"> Disponible: </label>
                                    </td>
                                    <td valign="top" nowrap="nowrap">
                                        <select name="available" id="available" class="required">
                                        <option value="0">No</option>
                                        <option value="1" selected>Si</option>
                                       </select>
                                    </td>
                                </tr>
                                </table>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" align="right" style="padding:4px;">
                            <label for="title">Descripci&oacute;n:</label>
                        </td>
                        <td style="padding:4px;" nowrap="nowrap">
                            <textarea name="description" id="description"  title="descripcion" style="width:100%; height:8em;">{$album->description|clearslash|escape:"html"}</textarea>
                        </td>
                    </tr>

                    <tr>
                        <td valign="top" align="right" style="padding:4px;">
                            <label for="metadata">Palabras clave: </label>
                        </td>
                        <td style="padding:4px;" nowrap="nowrap"">
                            <input type="text" id="metadata" name="metadata" size="80" title="Metadatos" value="{$album->metadata}" />
                            <br><label align='right'><sub>Separadas por comas</sub></label>
                        </td>
                    </tr>
                    {include file="album/album_images.tpl"}
                </tbody>
            </table>
        </div>
 
    {/if}

    <input type="hidden" id="action" name="action" value="" />
    <input type="hidden" name="id" id="id" value="{$id}" />
    </form>
{/block}
