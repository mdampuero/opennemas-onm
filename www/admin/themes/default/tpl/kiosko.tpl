{include file="header.tpl"}

{* LISTADO ******************************************************************* *}
{if !isset($smarty.request.action) || $smarty.request.action eq "list"}
    {* ZONA MENU CATEGORIAS ******* *}
    <ul id="tabs">
        {section name=as loop=$allcategorys}
        <li>
            {assign var=ca value=`$allcategorys[as]->pk_content_category`}
            <a href="kiosko.php?action=list&category={$ca}#" {if $category==$ca } style="color:#000000; font-weight:bold; background-color:#BFD9BF" {else}{if $ca eq $category}style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}{/if} >{$allcategorys[as]->title}</a>
        </li>
        {/section}
    </ul>

    <br />
    <br />
    {include file="botonera_up.tpl"}

    <div id="{$category}">
        <table class="adminheading">
            <tr>
                <th>Portadas</th>
            </tr>
        </table>
        <div id="pagina">
            {* NOTICIA DESTACADA ******* *}
            <table class="adminlist" border=0>
                 <tr>
                    <th align="center" style="width:100px;">Portada</th>
                    <th align="center">T&iacute;tulo</th>
                    <th align="center" style="width:100px;">Fecha</th>
                    <th align="center">Publisher</th>
                    <th align="center">Última edición</th>
                    <th align="center" style="width:10px;">Favorito</th>
                    <th align="center" style="width:110px;">Publicado</th>
                    <th align="center" style="width:50px;">Editar</th>
                    <th align="center" style="width:50px;">Elim</th>
                </tr>

                {section name=as loop=$portadas}
                <tr {cycle values="class=row0,class=row1"}>
                    <td style="padding:10px;font-size: 11px;">
                        <img src="{$MEDIA_IMG_PATH_WEB}kiosko{$portadas[as]->path}{$portadas[as]->name|regex_replace:"/.pdf$/":".jpg"}" title="{$portadas[as]->title|clearslash}" alt="{$portadas[as]->title|clearslash}" width="80" onmouseover="Tip('<img src={$MEDIA_IMG_PATH_WEB}kiosko{$portadas[as]->path}{$portadas[as]->name|regex_replace:"/.pdf$/":".jpg"} >', SHADOW, true, ABOVE, true, WIDTH, 200)" onmouseout="UnTip()" />
                    </td>
                    <td style="padding:10px;font-size: 11px;">
                        <a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$portadas[as]->pk_kiosko}');" title="{$portadas[as]->title|clearslash}">
                        {$portadas[as]->title|clearslash}</a>
                    </td>
                    <td style="padding:10px;font-size: 11px;">
                        {$portadas[as]->date}
                    </td>
                    <td  class='no_view' style="width:110px;" align="center">
                        {$portadas[as]->publisher}
                    </td>
                    <td  class='no_view' style="width:110px;" align="center">
                        {$portadas[as]->editor}
                    </td>
                    <td style="padding:10px;font-size: 11px;">
                        {if $portadas[as]->favorite == 1}
                            <a href="?id={$portadas[as]->id}&amp;action=change_favorite&amp;status=0&amp;category={$category}&amp;page={$paginacion->_currentPage}" class="favourite_on" title="Quitar de favorito"></a>
                        {else}
                            <a href="?id={$portadas[as]->id}&amp;action=change_favorite&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage}" class="favourite_off" title="Poner de favorito"></a>
                        {/if}
                    </td>
                    <td style="padding:10px;width:10%;" align="center">
                        {if $portadas[as]->available == 1}
                            <a href="?id={$portadas[as]->pk_kiosko}&amp;action=change_status&amp;status=0&amp;page={$paginacion->_currentPage}&amp;category={$category}" title="Publicado">
                                <img src="{php}echo($this->image_dir);{/php}publish_g.png" border="0" alt="Publicado" />
                            </a>
                        {else}
                            <a href="?id={$portadas[as]->pk_kiosko}&amp;action=change_status&amp;status=1&amp;page={$paginacion->_currentPage}&amp;category={$category}" title="Pendiente">
                                <img src="{php}echo($this->image_dir);{/php}publish_r.png" border="0" alt="Pendiente" />
                            </a>
                        {/if}
                    </td>
                    <td style="padding:10px;width:10%;" align="center">
                        <a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$portadas[as]->pk_kiosko}');" title="Modificar">
                            <img src="{php}echo($this->image_dir);{/php}edit.png" border="0" />
                        </a>
                    </td>
                    <td style="padding:10px;width:10%;" align="center">
                        <a href="#" onClick="confirm('¿Seguro que desea eliminar la portada?');enviar(this, '_self', 'delete', '{$portadas[as]->pk_kiosko}');" title="Eliminar">
                            <img src="{php}echo($this->image_dir);{/php}trash.png" border="0" />
                        </a>
                    </td>

                </tr>
                {sectionelse}
                <tr>
                    <td align="center" colspan=5><br><br><p><h2><b>Ninguna portada guardada </b></h2></p><br><br></td>
                </tr>
                {/section}

                {if count($portadas) gt 0}
                <tr>
                  <td colspan="6" style="padding:10px;font-size: 12px;" align="center"><br><br>{$paginacion->links}<br><br></td>
                </tr>
                {/if}
            </table>
        </div>
{/if}

{if isset($smarty.request.action) && $smarty.request.action eq "new"}

        {include file="botonera_up.tpl"}
        <ul id="tabs">
            <li>
                <a href="#edicion-contenido">Nueva Portada</a>
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
                            <input type="text" id="title" name="title" title="Portada" size="80" value="{$kiosko->title|clearslash}" class="required" onBlur="javascript:get_metadata(this.value);" />
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" align="right" style="padding:4px;">
                            <label for="metadata">Palabras clave: </label>
                        </td>
                        <td style="padding:4px;" nowrap="nowrap"">
                            <input type="text" id="metadata" name="metadata" size="80" title="Metadatos" value="{$kiosko->metadata|clearslash}" /><br>
                            <label align='right'><sub>Separadas por comas</sub></label><br>
                        </td>
                        <td rowspan=3>
                            <table style='background-color:#F5F5F5; padding:18px; width:99%;'>
                                <tr>
                                    <td valign="top"  align="right" nowrap="nowrap">
                                        <label for="title">Portada de:</label>
                                    </td>
                                    <td nowrap="nowrap">
                                        <select name="category" id="category" class="required">
                                            {section name=as loop=$allcategorys}
                                                <option value="{$allcategorys[as]->pk_content_category}" {if $kiosko->category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$allcategorys[as]->title}" >{$allcategorys[as]->title}</option>
                                            {/section}
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top"  align="right" nowrap="nowrap">
                                        <label for="title"> Disponible:</label>
                                    </td>
                                    <td valign="top" nowrap="nowrap">
                                        {if !isset($kiosko)}
                                            <select name="available" id="available" class="required">
                                                <option value="0">No</option>
                                                <option value="1" selected>Si</option>
                                            </select>
                                        {else}
                                            <select name="available" id="available" class="required">
                                                <option value="0" {if $kiosko->available==0}selected{/if}>No</option>
                                                <option value="1" {if $kiosko->available==1}selected{/if}>Si</option>
                                            </select>
                                        {/if}
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top"  align="right" nowrap="nowrap">
                                        <label for="title"> Favorito:</label>
                                    </td>
                                    <td valign="top" nowrap="nowrap">
                                        {if !isset($kiosko)}
                                            <select name="favorite" id="favorite" class="required">
                                                <option value="0">No</option>
                                                <option value="1" selected>Si</option>
                                            </select>
                                        {else}
                                            <select name="favorite" id="favorite" class="required">
                                                <option value="0" {if $kiosko->favorite==0}selected{/if}>No</option>
                                                <option value="1" {if $kiosko->favorite==1}selected{/if}>Si</option>
                                            </select>
                                        {/if}
                                        <img class="favorite" src="{$params.IMAGE_DIR}selected.png" border="0" alt="En home" align="top" />
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" align="right" style="padding:4px;">
                            <label for="title">Fecha:</label>
                        </td>
                        <td style="padding:4px;" nowrap="nowrap">
                             <input type="text" id="date" name="date" size="18" title="Fecha de portada" value="{$kiosko->date}" tabindex="-1" class="required" /></div>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" align="right" style="padding:4px;">
                            <label for="title">PDF de portada:</label>
                        </td>
                        <td style="padding:4px;" nowrap="nowrap">
                             <input type="file" id="file" name="file" title="PDF de portada" class="required" /></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

{/if}

{if isset($smarty.request.action) && $smarty.request.action eq "read"}

        {include file="botonera_up.tpl"}
        <ul id="tabs">
            <li>
                <a href="#edicion-contenido">Editar Portada</a>
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
                            <input type="text" id="title" name="title" title="Portada" size="80" value="{$kiosko->title|clearslash}" onBlur="javascript:get_metadata(this.value);" />
                        </td>
                        <td rowspan=3>
                            <table style='background-color:#F5F5F5; padding:18px; width:99%;'>
                                <tr>
                                    <td valign="top"  align="right" nowrap="nowrap">
                                        <label for="title">Portada de:</label>
                                    </td>
                                    <td nowrap="nowrap">
                                        <select name="category" id="category" class="required">
                                            {section name=as loop=$allcategorys}
                                                <option value="{$allcategorys[as]->pk_content_category}" {if $kiosko->category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$allcategorys[as]->title}" >{$allcategorys[as]->title}</option>
                                            {/section}
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top"  align="right" nowrap="nowrap">
                                        <label for="title"> Disponible:</label>
                                    </td>
                                    <td valign="top" nowrap="nowrap">
                                        <select name="available" id="available" class="required">
                                            <option value="0" {if $kiosko->available==0}selected{/if}>No</option>
                                            <option value="1" {if $kiosko->available==1}selected{/if}>Si</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top"  align="right" nowrap="nowrap">
                                        <label for="title"> Favorito:</label>
                                    </td>
                                    <td valign="top" nowrap="nowrap">
                                        <select name="favorite" id="favorite" class="required">
                                            <option value="0" {if $kiosko->favorite==0}selected{/if}>No</option>
                                            <option value="1" {if $kiosko->favorite==1}selected{/if}>Si</option>
                                        </select>
                                        <img class="favorite" src="{$params.IMAGE_DIR}selected.png" border="0" alt="En home" align="top" />
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" align="right" style="padding:4px;">
                            <label for="metadata">Palabras clave: </label>
                        </td>
                        <td style="padding:4px;" nowrap="nowrap"">
                            <input type="text" id="metadata" name="metadata" size="80" title="Metadatos" value="{$kiosko->metadata}" /><br>
                            <label align='right'><sub>Separadas por comas</sub></label><br>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" align="right" style="padding:4px;">
                            <label for="title">Fecha:</label>
                        </td>
                        <td style="padding:4px;" nowrap="nowrap">
                             {$kiosko->date}
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" colspan="3">
                            <p style="text-align: center;">
                                <img src="{$MEDIA_IMG_PATH_WEB}kiosko{$kiosko->path}{$kiosko->name|regex_replace:"/.pdf$/":".jpg"}" title="{$kiosko->title|clearslash}" alt="{$kiosko->title|clearslash}" />
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    
{/if}

{if $smarty.request.action eq "new"}
        {* Susbtituted by the Control.DatePicker prototype widget *}
        {dhtml_calendar inputField="date" button="date" singleClick=true ifFormat="%Y-%m-%d" firstDay=1 align="CR"}
{/if}

{include file="footer.tpl"}                            
