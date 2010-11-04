 {if !empty($search)}
            <div style='float:left;margin-left:10px;margin-top:10px;'><h4> {$search}</h4></div>
 {/if}
        <div id="media-browser"  style="width:100%; padding: 6px 4px 4px 2px;">
                {* Vista en miniatura de los ficheros ******************************************* *}
                <table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="100%">
                <tbody>
                    <tr>
                        <td valign="top">
                            <form action="{$smarty.server.SCRIPT_NAME}">
                                <div id="container-thumbnails">
                                    <br class="clearer" />
                                    {section name=n loop=$photo}
                                        <div class="thumbnail"  onmouseover="document.getElementById('header1-{$smarty.section.n.index}').style.display='inline';" onmouseout="document.getElementById('header1-{$smarty.section.n.index}').style.display='none';"  {if  $smarty.server.PHP_SELF neq '/admin/mediamanager.php'} style="margin-bottom: 24px;"{/if}>
                                            {if $photo[n]->content_status eq 1}
                                                <div id="header1-{$smarty.section.n.index}" style="display:none" class="thumbnail-file" z-index="1000">
                                                    {*$photo[n]->name|truncate:20*}
                                                    <a onmouseout="UnTip()" onmouseover="Tip('<b>Descripción:</b> {$photo[n]->description_utf|clearslash|escape:'html'} <br /> <b>Metadatos:</b> {$photo[n]->metadata_utf|clearslash|escape:'html'} <br /> <b>Autor:</b> {$photo[n]->author_name|clearslash|escape:'html'} <br> <b>Tipo:</b> {$photo[n]->type_img}<br> <b>Creado:</b> {$photo[n]->date|date_format:"%Y-%m-%d %H:%M:%S"}', SHADOW, true, ABOVE, true, WIDTH, 400)" href="{$home}?action=image_data&amp;id={$photo[n]->pk_photo}&amp;category={$category}" title="Editar datos de la imagen" >
                                                        Visualizar datos &nbsp;
                                                    </a>

                                                    <a style="cursor:pointer;" onmouseover="Tip('<img src=\'{$MEDIA_IMG_URL}{$photo[n]->path_file}{$photo[n]->name}\'>', SHADOW, true, ABOVE, true, WIDTH, {$photo[n]->width})" onmouseout="UnTip()" >
                                                        <img src="{$params.IMAGE_DIR}mediamanager/lupa.gif" border="0" width="18" height="18" align="absmiddle" />
                                                        Zoom
                                                    </a>
                                                </div>

                                                <div class="table_div">
                                                    <div>
                                                        {if preg_match('/^swf$/i', $photo[n]->type_img)}
                                                            <object>
                                                                <param name="wmode" value="transparent"
                                                                       value="{$MEDIA_IMG_URL}{$photo[n]->path_file}{$photo[n]->name}" />
                                                                <embed wmode="transparent"
                                                                       src="{$MEDIA_IMG_URL}{$photo[n]->path_file}{$photo[n]->name}"
                                                                       width="140" height="80" ></embed>
                                                            </object>

                                                            <span style="float:right; z-index:4;">
                                                                <img  onClick="javascript:$('selected_{$smarty.section.n.iteration}').click();" onmouseover="document.getElementById('header1-{$smarty.section.n.index}').style.display='inline';document.getElementById('header2-{$smarty.section.n.index}').style.display='inline';" onmouseout="document.getElementById('header1-{$smarty.section.n.index}').style.display='none';document.getElementById('header2-{$smarty.section.n.index}').style.display='inline';" src="themes/default/images/flash.gif" style="width:20px" border="0" />
                                                            </span>

                                                        {elseif preg_match('/^(jpeg|jpg|gif|png)$/i', $photo[n]->type_img)}
                                                            <img  onClick="javascript:$('selected_{$smarty.section.n.iteration}').click();" onmouseover="document.getElementById('header1-{$smarty.section.n.index}').style.display='inline';document.getElementById('header2-{$smarty.section.n.index}').style.display='inline';" onmouseout="document.getElementById('header1-{$smarty.section.n.index}').style.display='none';document.getElementById('header2-{$smarty.section.n.index}').style.display='inline';" src='{$MEDIA_IMG_URL}{$photo[n]->path_file}140x100-{$photo[n]->name}' style='border:0px;'  maxWidth='140' maxHeight='100' />

                                                        {else}
                                                            <object  onClick="javascript:$('selected_{$smarty.section.n.iteration}').click();" onmouseover="document.getElementById('header1-{$smarty.section.n.index}').style.display='inline';document.getElementById('header2-{$smarty.section.n.index}').style.display='inline';" onmouseout="document.getElementById('header1-{$smarty.section.n.index}').style.display='none';document.getElementById('header2-{$smarty.section.n.index}').style.display='inline';" ><param name="movie" value="{$MEDIA_IMG_URL}{$photo[n]->path_file}{$photo[n]->name}" /><embed src="{$MEDIA_IMG_URL}{$photo[n]->path_file}{$photo[n]->name}" width="140" height="80" ></embed></object>
                                                        {/if}
                                                    </div>
                                                </div>

                                                <div style="display:inline" id="header2-{$smarty.section.n.index}" class="thumbnail-info" z-index="1000">
                                                    <input type="checkbox" style="float:left;vertical-align:bottom;margin-bottom:1px;margin-top:0px;margin-right:1px;" class="minput"  id="selected_{$smarty.section.n.iteration}" name="selected_fld[]" value="{$photo[n]->id}"  style="cursor:pointer;">
                                                    {if $smarty.server.PHP_SELF eq '/admin/mediamanager.php'}
                                                        {$photo[n]->size}KB ({$photo[n]->width}x{$photo[n]->height})
                                                    {else}
                                                        <a onmouseover="Tip('{$MEDIA_IMG_URL}{$photo[n]->path_file}{$photo[n]->name}', ABOVE, true,OFFSETY, -30, WIDTH, 400, CLOSEBTN, true, CENTERMOUSE, true, FOLLOWMOUSE, false)">Ver URL</a>
                                                    {/if}

                                                    &nbsp;
                                                    <a href="#" onclick="javascript:confirmar('?action=delFile&amp;id={$photo[n]->pk_photo}&amp;basename={$photo[n]->name}&amp;path={$path}&amp;listmode=weeks&amp;category={$category}&amp;page={$smarty.get.page}');" title="Eliminar fichero">
                                                        <img src="{$params.IMAGE_DIR}iconos/eliminar.gif" border="0" align="absmiddle" />
                                                    </a>

                                                </div>

                                            {else}
                                                <img  onClick="javascript:$('selected_{$smarty.section.n.iteration}').click();" title="{$photo[n]->name}" alt="{$photo[n]->name}" src='{$MEDIA_IMG_URL}/140x100-nodisp_img.jpg' style='border:0px;'  maxWidth='140' maxHeight='100' />

                                                <div style="display:inline" onmouseover="document.getElementById('header2-{$smarty.section.n.index}').style.display='inline';" onmouseout="document.getElementById('header2-{$smarty.section.n.index}').style.display='inline';" id="header2-{$smarty.section.n.index}" class="thumbnail-info" z-index="1000">
                                                      <input type="checkbox"  style="float:left;vertical-align:bottom;margin-bottom:1px;margin-top:0px;margin-right:1px;"  class="minput"  id="selected_{$smarty.section.n.iteration}" name="selected_fld[]" value="{$photo[n]->id}"  style="cursor:pointer;">
                                                        {$photo[n]->size}KB ({$photo[n]->width}x{$photo[n]->height})
                                                    <a href="#" onclick="javascript:confirmar('?action=delFile&amp;id={$photo[n]->pk_photo}&amp;basename={$photo[n]->name}&amp;path={$path}&amp;listmode=weeks&amp;category={$category}');" title="Eliminar fichero">
                                                        <img src="{$params.IMAGE_DIR}iconos/eliminar.gif" border="0" align="absmiddle" />
                                                    </a>

                                                </div>
                                            {/if}
                                        </div>

                                    {sectionelse}
                                        {if $smarty.server.PHP_SELF eq '/admin/mediamanager.php'}
                                            <br /><br /><h2 style="text-align:center;">No hay imágenes</h2>
                                        {else}
                                           <br /><br /> <h2 style="text-align:center;">No hay gráficos</h2>
                                        {/if}
                                    {/section}

                                    <br class="clearer" />

                                    {if count($photo) gt 0}
                                        <br class="clearer" />
                                        {if !empty($pages->links)}
                                            <div align="center">{$pages->links} </div>
                                        {else}
                                            <div align="center">{$paginacion->links} </div>
                                         {/if}
                                    {/if}
                                </div>

                                <input type="hidden" name="listmode" value="weeks" />
                            </form>
                        </td>
                    </tr>
                </tbody>
                </table>
            </div>