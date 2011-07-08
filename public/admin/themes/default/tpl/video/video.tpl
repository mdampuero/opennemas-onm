{extends file="base/admin.tpl"}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs}
	 style="max-width:70% !important; margin: 0 auto; display:block;">

    {* LISTADO ******************************************************************* *}
    {if !isset($smarty.request.action) || $smarty.request.action eq "list"}
        <ul class="tabs2" style="margin-bottom: 28px;">

             {include file="menu_categorys.tpl" home="video.php?action=list"}
        </ul>

        {include file="botonera_up.tpl"}

        <div id="{$category}">
            <table class="adminheading">
                <tr>
                    <th nowrap> Videos</th>
                </tr>
            </table>

            <table class="adminlist">
                <tr>
                    <th class="title" style="width:35px;"></th>
                    <th>Título</th>
                    <th align="center" style="width:35px;">Visto</th>
                    <th align="center">Fecha</th>
                    <th align="center" style="width:35px;">Estado</th>
                    <th align="center" style="width:35px;">Favorito</th>
                    <th align="center" style="width:35px;">Modificar</th>
                    <th align="center" style="width:35px;">Eliminar</th>
                </tr>
                {section name=c loop=$videos}
                    <tr {cycle values="class=row0,class=row1"}  style="cursor:pointer;">
                        <td style="padding:1px; font-size:11px;">
                            <input type="checkbox" class="minput"  id="selected_{$smarty.section.c.iteration}" name="selected_fld[]" value="{$videos[c]->id}"  style="cursor:pointer;">
                        </td>
                        <td style="padding:10px;font-size: 11px;"  onClick="javascript:document.getElementById('selected_{$smarty.section.c.iteration}').click();">
                            {$videos[c]->title|clearslash}
                        </td>

                        <td style="padding:1px; font-size:11px;" align="center">
                            {$videos[c]->views}
                        </td>
                        <td style="padding:1px; font-size:11px;" align="center">
                            {$videos[c]->created}
                        </td>
                        <td style="padding:1px; font-size:11px;" align="center">
                            {if $videos[c]->available == 1}
                                    <a href="?id={$videos[c]->id}&amp;action=change_status&amp;status=0&amp;category={$category}&amp;page={$paginacion->_currentPage}" title="Publicado">
                                            <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicado" /></a>
                            {else}
                                    <a href="?id={$videos[c]->id}&amp;action=change_status&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage}" title="Pendiente">
                                            <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Pendiente" /></a>
                            {/if}
                        </td>
                        <td style="padding:1px;font-size:11px;" align="center">
                                    {if $videos[c]->favorite == 1}
                                       <a href="?id={$videos[c]->id}&amp;action=change_favorite&amp;status=0&amp;category={$category}&amp;page={$paginacion->_currentPage}" class="favourite_on" title="Quitar de Portada"></a>
                                    {else}
                                        <a href="?id={$videos[c]->id}&amp;action=change_favorite&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage}" class="favourite_off" title="Meter en Portada"></a>
                                    {/if}
                            </td>

                        <td style="padding:1px; font-size:11px;" align="center">
                            <a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$videos[c]->id}');" title="Modificar">
                                    <img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
                        </td>
                        <td style="padding:1px; font-size:11px;" align="center">
                            <a href="#" onClick="javascript:delete_videos('{$videos[c]->id}','{$paginacion->_currentPage}');" title="Eliminar">
                                    <img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
                        </td>
                    </tr>

                {sectionelse}
                    <tr>
                        <td align="center" colspan="8"><br><br><p><h2><b>Ningun video guardado</b></h2></p><br><br></td>
                    </tr>
                {/section}
                {if !empty($pagination)}
                    <tr>
                        <td colspan="8" align="center">{$pagination}</td>
                    </tr>
                {/if}
            </table>
            {if $smarty.get.alert eq 'ok'}
                 <script type="text/javascript" language="javascript">
                    {literal}
                           alert('{/literal}{$smarty.get.msgdel}{literal}');
                    {/literal}
                    </script>
            {/if}
        </div>

    {/if}


{* FORMULARIO PARA ENGADIR || ACTUALIZAR *********************************** *}

    {if isset($smarty.request.action) && ($smarty.request.action eq "new" || $smarty.request.action eq "read")}

        {include file="botonera_up.tpl"}

        <table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="700">
            <tbody>
                 <tr>
                    <td valign="top" align="right" style="padding:4px;" >
                            <label for="title">Video URL:</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap" >
                            <input type="text" id="video_url" name="video_url" value="{$video->video_url}" size="70" title="Video url" class="required" />
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right" style="padding:4px;" width="30%">
                            <label for="title">T&iacute;tulo:</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap" width="70%">
                            <input type="text" id="title" name="title" title="Título de la noticia"  onChange="javascript:get_metadata(this.value);"
                                    value="{$video->title|clearslash|escape:"html"}" class="required" size="100" />

                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right" style="padding:4px;">
                            <label for="metadata">Palabras clave: </label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap">
                            <input type="text" id="metadata" name="metadata" size="70" title="Metadatos" value="{$video->metadata}" />
                            <sub>Separadas por comas</sub>
                    </td>
                    <td rowspan="5" valign="top">
                      <div class="utilities-conf"  >
                        <table style='background-color:#F5F5F5; padding:18px; width:99%;'>
                            <tr>
                                <td valign="top"  align="right" nowrap="nowrap">
                                    <label for="title">Secci&oacute;n:</label>
                                </td>
                                <td nowrap="nowrap">
                                    <select name="category" id="category"  >
                                        {section name=as loop=$allcategorys}
                                            <option value="{$allcategorys[as]->pk_content_category}" {if $video->category eq $allcategorys[as]->pk_content_category || $category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$allcategorys[as]->title}" >{$allcategorys[as]->title}</option>
                                            {section name=su loop=$subcat[as]}
                                                <option value="{$subcat[as][su]->pk_content_category}" {if $video->category eq $subcat[as][su]->pk_content_category || $category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$subcat[as][su]->title}">&nbsp;&nbsp;&nbsp;&nbsp;{$subcat[as][su]->title}</option>
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
                                         <option value="1" {if $video->available eq '1'} selected {/if}>Si</option>
                                         <option value="0" {if $video->available eq '0'} selected {/if}>No</option>
                                    </select>
                                    <input type="hidden" value="1" name="content_status">
                                </td>
                            </tr>
                        </table>
                        </div>
                    </td>
                </tr>
                 <tr>
                    <td valign="top" align="right" style="padding:4px;" >
                            <label for="title">Descripción:</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap"  >
                            <textarea name="description" id="description" class="required" value=" "
                                    title="Resumen de la noticia" style="width:98%; height:6em;">{$video->description|clearslash}</textarea>
                    </td>
                </tr>

                {if $smarty.request.action eq "read"}
                     <tr>
                        <td valign="top" align="right" style="padding:4px;" >
                                <label for="title">Enlace:</label>
                        </td>
                        <td style="padding:4px;" nowrap="nowrap" >
                               <a href="{$smarty.const.SITE_URL}{$video->permalink}" target="_blank">
                                   {$smarty.const.SITE_URL}{$video->permalink}
                               </a>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" align="right" style="padding:4px;" >
                               <label for="title">{t}Information{/t}:</label>
                        </td>
                        <td style="padding:4px;" nowrap="nowrap" >
                            <div id="imgcc">
                                 
                                 {foreach from=$video->information key=key item=value}
                                         <strong>{$key}</strong>: {$value} <br/>
                                  {/foreach}
                            </div>
                         </td>
                    </tr>
                {/if}
            </tbody>
        </table>


    {/if}

    <input type="hidden" id="action" name="action" value="" />
    <input type="hidden" name="id" id="id" value="{$id}" />
</form>
{/block}
