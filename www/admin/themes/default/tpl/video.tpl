{include file="header.tpl"}

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
                <th>Tipo</th>
                <th>Secci&oacute;n</th>
                <th align="center" style="width:35px;">Visto</th>
                <th align="center">Fecha</th>
                <th align="center" style="width:35px;">Estado</th>
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
                    <td style="padding:10px;font-size: 11px;">
                        {$videos[c]->author_name|upper|clearslash}
                    </td>
                    
                    <td style="padding:10px;font-size: 11px;">
                        {section name=as loop=$allcategorys}
                            {if $videos[c]->category eq $allcategorys[as]->pk_content_category}
                                {$allcategorys[as]->title}
                            {/if}
                                
                            {section name=su loop=$subcat[as]}
                                {if $videos[c]->category eq $subcat[as][su]->pk_content_category}
                                    &rarrow; {$subcat[as][su]->title}
                                {/if}
                            {/section}
                        {/section}
                    </td>
                    
                    <td style="padding:1px; font-size:11px;" align="center">
                        {$videos[c]->views}
                    </td>
                    <td style="padding:1px; font-size:11px;" align="center">
                        {$videos[c]->created}
                    </td>
                    <td style="padding:1px; font-size:11px;" align="center">
                        {if $videos[c]->content_status == 1}
                                <a href="?id={$videos[c]->id}&amp;action=change_status&amp;status=0&amp;category={$category}&amp;page={$paginacion->_currentPage}" title="Publicado">
                                        <img src="{php}echo($this->image_dir);{/php}publish_g.png" border="0" alt="Publicado" /></a>
                        {else}
                                <a href="?id={$videos[c]->id}&amp;action=change_status&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage}" title="Pendiente">
                                        <img src="{php}echo($this->image_dir);{/php}publish_r.png" border="0" alt="Pendiente" /></a>
                        {/if}
                    </td>
                    <td style="padding:1px; font-size:11px;" align="center">
                        <a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$videos[c]->id}');" title="Modificar">
                                <img src="{php}echo($this->image_dir);{/php}edit.png" border="0" /></a>
                    </td>
                    <td style="padding:1px; font-size:11px;" align="center">
                        <a href="#" onClick="javascript:delete_videos('{$videos[c]->id}',{$paginacion->_currentPage});" title="Eliminar">
                                <img src="{php}echo($this->image_dir);{/php}trash.png" border="0" /></a>
                    </td>
                </tr>

            {sectionelse}
                <tr>
                    <td align="center" colspan="8"><br><br><p><h2><b>Ningun video guardado</b></h2></p><br><br></td>
                </tr>
            {/section}
            {if count($videos) gt 0}
                <tr>
                    <td colspan="8" align="center">{$paginacion->links}</td>
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
                <td valign="top" align="right" style="padding:4px;" width="30%">
                        <label for="title">T&iacute;tulo:</label>
                </td>
                <td style="padding:4px;" nowrap="nowrap" width="70%">
                        <input type="text" id="title" name="title" title="Título de la noticia"  onChange="javascript:get_metadata(this.value);"
                                value="{$video->title|clearslash|escape:"html"}" class="required" size="100" />
                        <input type="hidden" id="available" name="available" title="available"
                                value="{$video->available}" size="100" />
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
                    <table style='background-color:#F5F5F5; padding:18px; width:99%;'>
			<tr>
                            <td valign="top"  align="right" nowrap="nowrap">
                                <label for="title">Secci&oacute;n:</label>
                            </td>
                            <td nowrap="nowrap">  
                                <select name="category" id="category"  >
                                    {section name=as loop=$allcategorys}
                                        <option value="{$allcategorys[as]->pk_content_category}" {if $video->category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$allcategorys[as]->title}" >{$allcategorys[as]->title}</option>
                                        {section name=su loop=$subcat[as]}
                                            <option value="{$subcat[as][su]->pk_content_category}" {if $video->category eq $subcat[as][su]->pk_content_category}selected{/if} name="{$subcat[as][su]->title}">&nbsp;&nbsp;&nbsp;&nbsp;{$subcat[as][su]->title}</option>
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
                <td valign="top" align="right" style="padding:4px;" >
                        <label for="title">Fuente:</label>
                </td>
                <td style="padding:4px;" nowrap="nowrap" >
                    <select name="author_name" id="author_name" title="fuente">
                       <option  value="vimeo" {if $video->author_name eq 'vimeo'} selected {/if} >Vimeo </option/>
                       <option  value="youtube" {if $video->author_name eq 'youtube'} selected {/if}>Youtube </option/>
                       <option  value="otro" {if $video->author_name eq 'otro'} selected {/if}>Otro </option/>
                    </select>

                </td>
            </tr>
             <tr>
                <td valign="top" align="right" style="padding:4px;" >
                        <label for="title">Video ID:</label>
                </td>
                <td style="padding:4px;" nowrap="nowrap" >
                        <input type="text" id="videoid" name="videoid" value="{$video->videoid}" size="70" title="Video ID" class="required" value="" />
                </td>
            </tr>
            {if !empty($video->videoid)}
            <tr>
                <td valign="top" align="right" style="padding:4px;" >

                </td>
                <td style="padding:4px;" nowrap="nowrap" >
                    <div id="imgcc">
                        <div id="nifty" style="width:520px;">
                            <b class="rtop"><b class="r1"></b><b class="r2"></b><b class="r3"></b><b class="r4"></b></b>
                            <table border='0' width='90%'>
                            <tr>
                              <td align="center">
                                     <div id="ejep">
                                            {* <object width="416" height="150">
                                                <param name="movie" value="http://www.youtube.com/v/{$video->videoid}&hl=es&fs=1"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param>
                                                <embed src="http://www.youtube.com/v/{$video->videoid}&hl=es&fs=1" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="416" height="150"></embed></object>
                                             *}
                                             {$video->htmlcode|clearslash}
                                             <br>
                                             * Tamaño recomendado 290x225
                                    </div>
                                    <br>

                                    </td>
                            </tr>
                            </table>
                            <b class="rbottom"><b class="r4"></b><b class="r3"></b><b class="r2"></b><b class="r1"></b></b>
                        </div>
                    </div>
                </td>
            </tr>
            {/if}
           
            <tr>
                <td valign="top" align="right" style="padding:4px;" >
                        <label for="title">Código HTML:</label>
                </td>
                <td style="padding:4px;" nowrap="nowrap" >
                        <textarea name="htmlcode" id="htmlcode" title="Código HTML" style="width:98%; height:6em;">{$video->htmlcode|clearslash}</textarea>
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

        </tbody>
    </table>


{/if}

{include file="footer.tpl"}