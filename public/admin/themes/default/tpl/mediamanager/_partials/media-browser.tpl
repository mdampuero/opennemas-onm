{if !empty($search)}
    <div style='float:left;margin-left:10px;margin-top:10px;'><h4> {$search}</h4></div>
{/if}

<table class="adminheading">
    <tbody>
        <tr>
            <th>{t}List of images{/t}</th>
        </tr>
    </tbody>
</table>
<div id="media-browser" class="cpanel clearfix">
    {section name=n loop=$photo}
        <div class="photo">
            
            <div id="image-{$smarty.section.n.index}" class="photo-image">
                
                <div class="image-preview" onClick="javascript:$('selected_{$smarty.section.n.iteration}').click(); console.log('ho')">
                    {if preg_match('/^swf$/i', $photo[n]->type_img)}

                        <object>
                            <param name="wmode" value="transparent"
                                   value="{$MEDIA_IMG_URL}{$photo[n]->path_file}{$photo[n]->name}" />
                            <embed wmode="transparent"
                                   src="{$MEDIA_IMG_URL}{$photo[n]->path_file}{$photo[n]->name}"
                                   width="140" height="80" ></embed>
                        </object>
                        <img class="flash-flag"  src="themes/default/images/flash.gif" />
                        
                    {elseif preg_match('/^(jpeg|jpg|gif|png)$/i', $photo[n]->type_img)}

                        <img onClick="javascript:$('selected_{$smarty.section.n.iteration}').click();" src='{$MEDIA_IMG_URL}{$photo[n]->path_file}140-100-{$photo[n]->name}'
                             class="thumbnail" />

                    {else}

                        <object>
                            <param name="wmode" value="transparent"
                                   value="{$MEDIA_IMG_URL}{$photo[n]->path_file}{$photo[n]->name}" />
                            <embed wmode="transparent"
                                   src="{$MEDIA_IMG_URL}{$photo[n]->path_file}{$photo[n]->name}"
                                   width="140" height="80" ></embed>
                        </object>
                        
                    {/if}
                </div>
                
                
                <input type="checkbox"  id="selected_{$smarty.section.n.iteration}" name="selected_fld[]" value="{$photo[n]->id}" />

            </div>
            
            <div class="photo-data">
                
                <ul>
                    <li><strong>Descripción:</strong> {$photo[n]->description_utf|clearslash|escape:'html'|default:""}</li>
                    <li><strong>Metadatos:</strong> {$photo[n]->metadata_utf|clearslash|escape:'html'|default:""}</li>
                    <li><strong>Autor:</strong> {$photo[n]->author_name|clearslash|escape:'html'|default:""}</li>
                    <li><strong>Tipo:</strong> {$photo[n]->type_img|default:""}<br></li>
                    <li><strong>Creado:</strong> {$photo[n]->date|date_format:"%Y-%m-%d %H:%M:%S"|default:""}<br></li>
                    <li><strong>Tamaño:</strong> {$photo[n]->size}KB ({$photo[n]->width}x{$photo[n]->height})</li>
                </ul>
                
                
                <div class="actions">
                    <a class="edit-button" href="{$smarty.server.PHP_SELF}?action=image_data&id={$photo[n]->pk_photo}&category={$category}">
                        <img src="{$params.IMAGE_DIR}edit.png" /> {t}Edit{/t}
                    </a>
                    <a class="delete-button" href="#" onclick="javascript:confirmar('?action=delFile&amp;id={$photo[n]->pk_photo}&amp;basename={$photo[n]->name}&amp;path={$photo[n]->path_file}&amp;listmode=weeks&amp;category={$category}&amp;page={$smarty.get.page|default:""}');" title="Eliminar fichero">
                        <img src="{$params.IMAGE_DIR}template_manager/delete16x16.png" /> {t}Delete{/t}
                    </a>
                </div>
            </div>
            
        </div>

    {sectionelse}
        <div style="margin:20 auto; display:block; padding:10px; text-align:center;">
            {if $smarty.server.PHP_SELF eq '/admin/controllers/mediamanager/mediamanager.php'}
                {t}No available images to list here{/t}
            {else}
               {t}No available graphics to list here{/t}
            {/if}
        </div>
    {/section}
</div>

    <div class="pagination">
        {if !empty($pages->links)}
            {$pages->links}
        {else}
            {$paginacion->links}
        {/if}
    </div>
    <!-- .pagination -->
</div>
<input type="hidden" name="listmode" value="weeks" />
