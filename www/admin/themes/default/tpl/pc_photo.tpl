{include file="pc_header.tpl"}

{* LISTADO ******************************************************************* *}
{if !isset($smarty.request.action) || $smarty.request.action eq "list"}

    <ul class="tabs2">
        {section name=as loop=$allcategorys}
            <li>
                 {assign var=ca value=`$allcategorys[as]->pk_content_category`}
                <a href="pc_photo.php?action=list&category={$ca}" {if $category==$ca } style="color:#000000; font-weight:bold; background-color:#BFD9BF" {else}{if $ca eq $datos_cat[0]->fk_content_category}style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if} {/if} >{$allcategorys[as]->title}</a>
            </li>
        {/section}
    </ul>
    <br/><br/>
	{include file="pc_botonera_up.tpl"}
	
    <div id="{$category}">
        <table class="adminheading">
            <tr>
                    <th nowrap>Plan Conecta: Fotografías del día</th>
            </tr>
        </table>
        <table class="adminlist">
            <tr>
                    <th class="title"></th>
                    <th class="title" align="left">Ver</th>
                    <th class="title" align="left">Título</th>
                    <th class="title" align="left">Autor</th>
                    <th class="title" align="left">IP</th>
                    <th class="title">Tamaño</th>
                    <th class="title">Resoluci&oacute;n</th>
                    <th align="center">Fecha</th>
                    <th align="center">Visto</th>
                    <th align="center">Favorito</th>
                    <th align="center">Publicar</th>
                    <th align="center">Archivar</th>
                    <th align="center">Modificar</th>
                    <th align="center">Eliminar</th>
            </tr>

            {section name=c loop=$photos}
                <tr {cycle values="class=row0,class=row1"} >
                    <td style="padding:10px;font-size: 11px;width:20px;">
                            <input type="checkbox" class="minput"  id="selected_{$smarty.section.c.iteration}" name="selected_fld[]" value="{$photos[c]->id}"  style="cursor:pointer;">
                    </td>
                    <td style="padding:1px;font-size: 11px;width:50px;" >
                             <img src="{php}echo($this->image_dir);{/php}preview_20.png" border="0" alt="Visualizar"  style="cursor:pointer;"  onmouseout="UnTip()" onmouseover="Tip('<img src=\'{$MEDIA_CONECTA_WEB}{$photos[c]->path_file}\' style=\'max-width:600px;\' > ', SHADOW, true, ABOVE, true, OFFSETY, 120, WIDTH, 600)" />
                    </td>
                    <td style="padding:1px;font-size: 11px;cursor:pointer;"  onClick="javascript:document.getElementById('selected_{$smarty.section.c.iteration}').click();" >
                            {$photos[c]->title|clearslash}
                    </td>
                    <td style="padding:1px;font-size: 11px;width:120px;">
                        {assign var='id_author' value=$photos[c]->fk_user}
                        {$conecta_users[$id_author]->nick}
                    </td>
                    <td style="padding:1px;font-size: 11px;width:120px;">
                        {$photos[c]->ip}
                    </td>
                    <td style="padding:1px;font-size: 11px;width:120px;">
                        {if $photos[c]->size}{math  x=$photos[c]->size y="1024" equation="round(x/y)"} KB {/if}
                    </td>
                    <td style="padding:1px;font-size: 11px;width:120px;">
                        {$photos[c]->resolution}
                    </td>
                    <td style="padding:1px;width:100px;font-size: 11px;" align="center">
                        {$photos[c]->created}
                    </td>
                    <td style="padding:1px;font-size: 11px;width:120px;" align="center">
                        {$photos[c]->views}
                    </td>
                    <td style="padding:1px;font-size: 11px;width:60px;" align="center">
                        {if $photos[c]->favorite == 1}
                                <a href="?id={$photos[c]->id}&amp;action=change_favorite&amp;status=0&amp;category={$category}&amp;page={$paginacion->_currentPage}" class="favourite_on" title="Publicado"></a>
                        {else}
                                <a href="?id={$photos[c]->id}&amp;action=change_favorite&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage}" class="favourite_off" title="Pendiente"></a>
                        {/if}
                    </td>
                    <td style="padding:1px;font-size: 11px;width:60px;" align="center">
                            {if $photos[c]->available == 1}
                                <a href="?id={$photos[c]->id}&amp;action=change_available&amp;status=0&amp;category={$category}&amp;page={$paginacion->_currentPage}" title="Publicado">
                                        <img src="{php}echo($this->image_dir);{/php}publish_g.png" border="0" alt="Publicado" /></a>
                            {else}
                                <a href="?id={$photos[c]->id}&amp;action=change_available&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage}" title="Pendiente">
                                        <img src="{php}echo($this->image_dir);{/php}publish_r.png" border="0" alt="Pendiente" /></a>
                            {/if}
                    </td>
                    <td style="padding:1px;width:60px;font-size: 11px;" align="center">
                        <a href="?id={$photos[c]->id}&amp;action=change_status&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage}" title="Archivar a Hemeroteca">
                            <img src="{php}echo($this->image_dir);{/php}save_hemeroteca_icon.png" border="0" alt="Archivar a Hemeroteca" /></a>
                    </td>
                    <td style="padding:1px;font-size: 11px;width:60px;" align="center">
                            <a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$photos[c]->id}');" title="Modificar">
                                    <img src="{php}echo($this->image_dir);{/php}edit.png" border="0" /></a>
                    </td>
                    <td style="padding:1px;font-size: 11px;width:60px;" align="center">
                            <a href="#" onClick="javascript:confirmar(this, '{$photos[c]->id}');" title="Eliminar">
                                    <img src="{php}echo($this->image_dir);{/php}trash.png" border="0" /></a>
                    </td>
                </tr>

            {sectionelse}
                <tr>
                     <td align="center" colspan="5"><br><br><p><h2><b>Ninguna fotografía guardada</b></h2></p><br><br></td>
                </tr>
            {/section}

            {if count($photos) gt 0}
            <tr>
                <td colspan="13" align="center">{$paginacion->links}</td>
            </tr>
            {/if}

        </table>
    </div>
{/if}

 {dialogo script="print"}
{* FORMULARIO PARA ENGADIR ************************************** *}

{if isset($smarty.request.action) && ($smarty.request.action eq "new" || $smarty.request.action eq "read")}

    {include file="pc_botonera_up.tpl"}

    <table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="100%">
    <tbody>
        <tr>
            <td valign="top" align="right" style="padding:4px;">
                    <label for="title">T&iacute;tulo:</label>
            </td>
            <td style="padding:4px;" nowrap="nowrap" >
                    <input type="text" id="title" name="title" title="Título de la noticia" onBlur="javascript:get_tags(this.value);"
                            value="{$photo->title|clearslash|escape:"html"}" class="required" size="90" />

            </td>
            <td style="padding:4px;" nowrap="nowrap" rowspan='5'>
                <table style='background-color:#F5F5F5; padding:8px;' border='0' cellpadding="8">
                     <tr>
                        <td valign="top" align="right" style="padding:4px;" nowrap="nowrap">
                            <label for="title"> Secci&oacute;n: </label>
                        </td>
                        <td valign="top" style="padding:4px;" nowrap="nowrap">
                                <select name="fk_pc_content_category" id="fk_content_type" class="required">
                                    {section name=as loop=$categorys}
                                            <option value="{$categorys[as]->pk_content_category}" {if ($category eq $categorys[as]->pk_content_category) || ($photo->fk_pc_content_category eq $categorys[as]->pk_content_category)} selected{/if}>{$categorys[as]->title}</option>
                                    {/section}
                                </select>
                        </td>

                    </tr>
                   <tr>
                        <td valign="top" align="right" style="padding:4px;" nowrap="nowrap">
                            <label for="title"> Disponible: </label>
                        </td>
                        <td valign="top" style="padding:4px;" nowrap="nowrap">
                                <select name="available" id="avaliable" class="required">
                                    <option value="0" {if $photo->available eq 0} selected {/if}>No</option>
                                    <option value="1"  {if $photo->available eq 1} selected {/if}>Si</option>
                                </select>
                        </td>

                    </tr> <tr>
                        <td valign="top"  align="right" style="padding:4px;" nowrap="nowrap">
                            <label for="title"> Archivar: </label>
                        </td>
                        <td valign="top" style="padding:4px;" nowrap="nowrap">
                            <select name="content_status" id="content_status" class="required">
                                <option value="0"  {if $photo->content_status eq 0} selected {/if}>No</option>
                                <option value="1" {if $photo->content_status eq 1} selected {/if}>Si</option>
                            </select>
                        </td>
                    </tr>                 
                </table>
            </td>
        </tr>
        {if ($smarty.request.action eq "new")}
            <tr>
                <td valign="top" align="right" style="padding:4px;">
                        <label for="email">Fotografía:</label>
                </td>
                <td style="padding:4px;" nowrap="nowrap">
                        <input type="file" name="file" id="file" class="required" size="50" />
                </td>
            </tr>
        {else}
            <tr>
                <td valign="top" align="right" style="padding:4px;">
                        <label for="title">Autor:</label>
                </td>
                <td style="padding:4px;" nowrap="nowrap" >
                        <select name="fk_user" class="validate-selection">
                            <option>-- Seleccione autor --</option>
                            {foreach  key=as from=$conecta_users item=author}
                                {if $author->status neq 0}
                                  <option value="{$as}" {if $photo->fk_user eq $as}selected{/if}>{$author->nick}</option>
                                {/if}
                            {/foreach}
                        </select>

                   <label for="title"> IP: </label>
                   <input type="text" readonly id="ip" name="ip" title="ip" value="{$photo->ip}" />
                </td>
            </tr>
        {/if}

         <tr>
            <td valign="top" align="right" style="padding:4px;">
                    <label for="title">Palabras Clave:</label>
            </td>
            <td style="padding:4px;" nowrap="nowrap">
                    <input type="text" id="metadata" name="metadata" title="metadata"
                            value="{$photo->metadata}" class="required" size="90" />

            </td>
        </tr>
         <tr>
            <td valign="top" align="right" style="padding:4px;">
                    <label for="title">Localidad:</label>
            </td>
            <td style="padding:4px;" nowrap="nowrap">
                     <input type="text" id="locality" name="locality" title="locality"
                            value="{$photo->locality}" class="required" size="40" />

            </td>
        </tr>
        <tr>
            <td valign="top" align="right" style="padding:4px;">
                    <label for="title">Pa&iacute;s:</label>
            </td>
            <td style="padding:4px;" nowrap="nowrap">
                    <input type="text" id="country" name="country" title="country"
                            value="{$photo->country}" class="required" size="40" />
            </td>
        </tr>
        <tr>
            <td valign="top" align="right" style="padding:4px;" >
                    <label for="title">Descripcion:</label>
            </td>
            <td style="padding:4px;" nowrap="nowrap">
                    <textarea name="description" id="description" class="required" value=" "
                            title="Resumen de la noticia" style="width:500px; height:8em;">
                            {$photo->description|clearslash}</textarea>
            </td><td align="center" valign="top">
                {if $photo->path_file}
                    <img src="{$MEDIA_CONECTA_WEB}{$photo->path_file}" width="380" />
                {/if}
            </td>
        </tr>

        
    </table>
    <script type="text/javascript" src="{$params.JS_DIR}/tiny_mce/opennemas-config.js"></script>
    {literal}
        <script type="text/javascript" language="javascript">
            tinyMCE_GZ.init( OpenNeMas.tinyMceConfig.tinyMCE_GZ );
        </script>

        <script type="text/javascript" language="javascript">
            OpenNeMas.tinyMceConfig.advanced.elements = "description";
            tinyMCE.init( OpenNeMas.tinyMceConfig.advanced );
        </script>
    {/literal}

{/if}

{include file="footer.tpl"}