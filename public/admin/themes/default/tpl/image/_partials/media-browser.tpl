{if !empty($search)}
    <div style='float:left;margin-left:10px;margin-top:10px;'><h4> {$search}</h4></div>
{/if}

<table class="listing-table nofill border" id="media-browser">
    <thead>
        <tr>
            <th>{t}List of images{/t}</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                {section name=n loop=$photos}
                <div class="photo">

                    <div id="image-{$smarty.section.n.index}" class="photo-image">

                        <div class="image-preview" onClick="javascript:$('selected_{$smarty.section.n.iteration}').click();">
                            {if preg_match('/^swf$/i', $photos[n]->type_img)}

                                <object>
                                    <param name="wmode" value="transparent"
                                           value="{$MEDIA_IMG_URL}{$photos[n]->path_file}{$photos[n]->name}" />
                                    <embed wmode="transparent"
                                           src="{$MEDIA_IMG_URL}{$photos[n]->path_file}{$photos[n]->name}"
                                           width="140" height="80" ></embed>
                                </object>
                                <img style="width:16px;height:16px;border:none;"  src="{$smarty.const.SITE_URL_ADMIN}/themes/default/images/flash.gif" />

                            {elseif preg_match('/^(jpeg|jpg|gif|png)$/i', $photos[n]->type_img)}

                                <img onClick="javascript:$('selected_{$smarty.section.n.iteration}').click();" src='{$MEDIA_IMG_URL}{$photos[n]->path_file}140-100-{$photos[n]->name}'
                                     class="thumbnail" />

                            {else}

                                <object>
                                    <param name="wmode" value="transparent"
                                           value="{$MEDIA_IMG_URL}{$photos[n]->path_file}{$photos[n]->name}" />
                                    <embed wmode="transparent"
                                           src="{$MEDIA_IMG_URL}{$photos[n]->path_file}{$photos[n]->name}"
                                           width="140" height="80" ></embed>
                                </object>

                            {/if}
                        </div>


                        <input type="checkbox"  id="selected_{$smarty.section.n.iteration}" name="selected_fld[]" value="{$photos[n]->id}" class ="minput" />

                    </div>

                    <div class="photo-data">

                        <ul>
                            <li><h3>{if !empty($photos[n]->title)}{$photos[n]->title|clearslash|escape:'html'} {else} {t}No available title{/t} {/if}</h3></li>
                            <li>{if !empty($photos[n]->description)}{$photos[n]->description_utf|clearslash|escape:'html'} {else} {t}No available description{/t} {/if}</h3></li>
                            <li><img src="{$params.IMAGE_DIR}icons/tag_red.png" /> {$photos[n]->metadata_utf|clearslash|escape:'html'|default:"No tags"}</li>
                            {if !empty($photos[n]->author_name)}
                                <li><strong>{t}Author:{/t}</strong> {$photos[n]->author_name|clearslash|escape:'html'|default:""}</li>
                            {/if}
                            <li><strong>{t}Type:{/t}</strong> {$photos[n]->type_img|default:""}<br></li>
                            <li><strong>{t}Created:{/t}</strong> {$photos[n]->date|date_format:"%Y-%m-%d %H:%M:%S"|default:""}<br></li>
                            <li><strong>{t}Image size:{/t}</strong> {$photos[n]->width}x{$photos[n]->height}</li>
                            <li><strong>{t}File size:{/t}</strong> {$photos[n]->size}KB</li>
                        </ul>


                        <div class="actions">
                            {acl isAllowed="IMAGE_UPDATE"}
                            <a class="edit-button" href="{$smarty.server.PHP_SELF}?action=show&amp;id[]={$photos[n]->pk_photo}">
                                <img src="{$params.IMAGE_DIR}edit.png" /> {t}Edit{/t}
                            </a>
                            {/acl}
                            {acl isAllowed="IMAGE_DELETE"}
                            <a class="delete-button" href="#" onclick="javascript:confirmar('?action=delete&amp;id={$photos[n]->pk_photo}');" title="Eliminar fichero">
                                <img src="{$params.IMAGE_DIR}template_manager/delete16x16.png" /> {t}Delete{/t}
                            </a>
                            {/acl}
                        </div>
                    </div>

                </div>

            {sectionelse}
                <div style="margin:20 auto; display:block; padding:10px; text-align:center;">
                    {t}No available images to list here{/t}
                </div>
            {/section}
            </td>
        </tr>
    </tbody>
    <tfoot>
        <tr class="pagination">
            <td>
                {$pages->links}
            </td>
        </tr>
    </tfoot>
</table>