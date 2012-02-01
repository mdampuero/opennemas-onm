<div style="width:59%;display:inline-block;">
    <div style="cursor:pointer; border:1px double #ccc; background-color:#EEE; padding:7px;">
        <strong>{t}Images in this album (drop here images from the side block){/t}</strong>
    </div>
    <div class="list-of-images">
        <ul>
            {if !empty($photoData)}
                {foreach from=$photoData item=photo key=key name=album_photos}
                    <li class="image thumbnail">
                        <div class="overlay-image">
                            <div>
                                <ul class="image-buttons">
                                    <li><a href="#" title="{t}Mark as album image{/t}"><img src="{$params.IMAGE_DIR}publish_r.png"></a></li>
                                    <li><a href="/admin/article.php?action=read&amp;id=41107" title="Editar"><img src="{$params.IMAGE_DIR}edit.png"></a></li>
                                    <li><a href="#" title="{t}Drop{/t}"><img src="{$params.IMAGE_DIR}trash.png"></a></li>
                                </ul>
                            </div>
                        </div>
                        <img
                             src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$photo->path_file}{$photo->name}"
                             id="img{$photo->pk_photo}"
                             data-id="{$photo->pk_photo}"
                             data-title="{$photo->name}"
                             data-description="{$photo->description|escape:"html"}"
                             data-path="{$photo->path_file}"
                             data-width="{$photo->width}"
                             data-height="{$photo->height}"
                             data-filesize="{$photo->size}"
                             data-created="{$photo->created}"
                             data-tags="{$photo->metadata}"
                             data-footer="{$otherPhotos[n][2]|escape:"html"}"
                             alt="{$photo->name}"/>
                        <textarea name="album_photos_footer[]">{$photo->name}</textarea>
                        <input type="hidden" name="album_photos_ids[]" value="{$photo->pk_photo}">
                    </li><!-- /image -->
                {/foreach}
            {/if}
        </ul>
    </div><!-- /list-of-images -->
</div>

<div style="width:40%;display:inline-block;">
    <div style="cursor:pointer; border:1px double #ccc; background-color:#EEE; padding:7px;">
        <strong>{t}Available images{/t}</strong>
    </div>
    <div id="photos_container" class="photos" style="border:1px solid #ccc;  padding:7px;">
        <table>
            <tr>
                <td>
                    <div class="cajaBusqueda">
                        <input id="stringImageSearch" name="stringImageSearch" type="text" style="width:"
                           onkeypress="onImageKeyEnter(event, $('category_imag').options[$('category_imag').selectedIndex].value,encodeURIComponent($('stringImageSearch').value),1);"
                           placeholder="{t}Search images by title...{/t}"/>
                    </div>
                </td>
                <td>
                    <select id="category_imag" name="category_imag" class="required" onChange="getGalleryImages('list_by_category',this.options[this.selectedIndex].value,'',1);">
                        <option value="0">GLOBAL</option>
                        {section name=as loop=$allcategorys}
                        <option value="{$allcategorys[as]->pk_content_category}" {if $category eq $allcategorys[as]->pk_content_category}selected{/if}>{$allcategorys[as]->title}</option>
                        {section name=su loop=$subcat[as]}
                                <option value="{$subcat[as][su]->pk_content_category}" {if $category eq $subcat[as][su]->pk_content_category}selected{/if}>&nbsp;&nbsp;&nbsp;&nbsp;{$subcat[as][su]->title}</option>
                        {/section}
                        {/section}
                    </select>
                </td>
            </tr>
        </table>
        <div id="photos" class="photos"></div>
    </div>
</div>


    <label>{t}Cut the image that is in frontpage view. ({$crop_width}x{$crop_height} px){/t} </label>
    <img src="{$params.IMAGE_DIR}default_img.jpg" id="testImage"  width="300" />

    <div id="previewArea">
        {if !empty($album->cover)}
            <img id="crop_img" src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$album->cover}"
                 alt={t}"Frontpage image"{/t}  style="maxWidth:600px;maxHeight:400px;" />
        {/if}
    </div>
    <input type="hidden" name="album_highlighted_image[x1]" id="x1" value=""/>
    <input type="hidden" name="album_highlighted_image[y1]" id="y1" value=""/>

    <input type="hidden" name="album_highlighted_image[width]" id="width" value="" />
    <input type="hidden" name="album_highlighted_image[height]" id="height" value=""/>

    <input type="hidden" name="album_highlighted_image[media_path]" id="media_path"
        value="{$smarty.const.MEDIA_IMG_PATH_WEB}"/>
    <input type="hidden" name="album_highlighted_image[path_img]" id="path_img" value=""/>
    <input type="hidden" name="album_highlighted_image[name_img]" id="name_img" value=""/>
</div>


{script_tag src="/tiny_mce/opennemas-config.js"}
<script>
document.observe('dom:loaded', function() {
    getGalleryImages('listByCategory','{$category}','','1');
});
jQuery(document).ready(function($){
    $( ".list-of-images ul" ).sortable({
        placeholder: "image-moving"
    }).disableSelection();

    $( ".list-of-images ul" ).droppable({
        accept: "#photos_container #photos img",
        drop: function( event, ui ) {
            var image = ui.draggable;
            var parent = $(this);

            parent.append(
                "<li class=\"image thumbnail\">" +
                    "<div class=\"overlay-image\"><div>" +
                        "<ul class=\"image-buttons\">" +
                            "<li><a href=\"#\" title=\"{t}Mark as album image{/t}\"><img src=\"{$params.IMAGE_DIR}publish_r.png\"></a></li>" +
                            "<li><a href=\"#\" title=\"{t}Edit{/t}\"><img src=\"{$params.IMAGE_DIR}edit.png\"></a></li>" +
                            "<li><a href=\"#\" title=\"{t}Drop{/t}\"><img src=\"{$params.IMAGE_DIR}trash.png\"></a></li>" +
                        "</ul>" +
                    "</div></div>" +
                    "<img " +
                         "src=\"" + image.data("url") + "\"" +
                         "id=\"image" + image.data("id") + "\"" +
                         "data-id=\"" + image.data("id") + "\"" +
                         "data-title=\"" + image.data("created") + "\"" +
                         "data-description=\"" + image.data("created") + "\"" +
                         "data-path=\"" + image.data("created") + "\"" +
                         "data-width=\"" + image.data("width") + "\"" +
                         "data-height=\"" + image.data("height") + "\"" +
                         "data-filesize=\"" + image.data("filesize") + "\"" +
                         "data-created=\"" + image.data("created") + "\"" +
                         "data-tags=\"" + image.data("tags") + "\"" +
                         "data-footer=\"" + image.data("description") + "\"" +
                         "alt=\"" + image.data("description") + "\"/>" +
                    "<textarea name=\"album_photos_footer[]\">" + image.data("description") + "</textarea>" +
                    "<input type=\"hidden\" name=\"album_photos_id[]\" value=\"" + image.data("id") + "\">" +
                "</li>"
            );
        }
    });
});
</script>
