<div id="album-contents" style="width:590px;display:inline-block;" class="tabs resource-container clearfix">
    <ul>
        <li><a href="#list-of-images">{t}Images in this album{/t}</a></li>
        <li><a href="#frontpage-image">{t}Cover image{/t}</a></li>
    </ul>
    <div id="list-of-images" class="list-of-images">
        <ul>
            {if !empty($photos)}
                {foreach from=$photos item=photo key=key name=album_photos}
                    <li class="image thumbnail">
                        <div class="overlay-image">
                            <div>
                                <ul class="image-buttons">
                                    <li><a href="#" class="edit-button" title="Editar"><img src="{$params.IMAGE_DIR}edit.png"></a></li>
                                    <li><a href="#" class="delete-button" title="{t}Drop{/t}"><img src="{$params.IMAGE_DIR}trash.png"></a></li>
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
    <div id="frontpage-image" class="droppable-video-position droppable-position">
        <div>
            <a class="delete-button">
                <img src="{$smarty.const.SITE_URL_ADMIN}/themes/default/images/trash.png" id="remove_video2" alt="Eliminar" title="Eliminar" />
            </a>
            <div class="clearfix">
                <div class="thumbnail article-resource-image">
                    {if isset($video2) && $video2->pk_video}
                        {if $video2->author_name == 'internal'}
                        <img src="{$smarty.const.MEDIA_IMG_PATH_WEB}/../{$video2->information['thumbnails']['normal']}" />
                        {else}
                        <img src="{$video2->information['thumbnail']}"/>
                        {/if}
                    {else}
                        <img src="http://placehold.it/290x226" />
                    {/if}
                </div>
                <div class="article-resource-image-info">
                    <div><label>{t}File name{/t}</label>     <span class="filename">{$video2->name|default:'default_img.jpg'}</span></div>
                    <div><label>{t}Creation date{/t}</label> <span class="created_time">{$video2->created|default:""}</span></div>
                    <div><label>{t}Description{/t}</label>   <span class="description">{$video2->description|escape:'html'}</span></div>
                    <div><label>{t}Tags{/t}</label>          <span class="tags">{$video2->metadata|default:""}</span></div>
                </div>
            </div>
            <div class="article-resource-footer">
                <input type="hidden" name="album_frontpage_image" value="{$video2->pk_video}" class="album-frontpage-image"/>
            </div>
        </div>
    </div><!-- /inner-video -->
</div>

<div style="width:340px;display:inline-block;">
    <div style="cursor:pointer; border:1px double #ccc; background-color:#EEE; padding:6px;">
        <strong>{t}Available images{/t}</strong>
    </div>
    <div id="photos_container" class="photos" style="border:1px solid #ccc; border-top:0 none;  padding:7px;">
        <form id="image_search">
        <table>
            <tr>
                <td>
                    <div class="cajaBusqueda">
                        <input id="stringImageSearch" name="stringImageSearch" type="text" style="width:90%"
                           onkeypress="onImageKeyEnter(event, $('category_imag').options[$('category_imag').selectedIndex].value,encodeURIComponent($('stringImageSearch').value),1);"
                           placeholder="{t}Search images by title...{/t}" form="image_search"/>
                    </div>
                </td>
                <td class="right">
                    <select id="category_imag" name="category_imag" class="required" onChange="getGalleryImages('list_by_category',this.options[this.selectedIndex].value,'',1);"  style="width:90%" form="image_search">
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
        </form>
        <div id="photos" class="photos"></div>
    </div>
</div>

{script_tag src="/tiny_mce/opennemas-config.js"}
<script>
document.observe('dom:loaded', function() {
    getGalleryImages('listByCategory','{$category}','','1');
});
jQuery(document).ready(function($){

    $('#album-contents').tabs();
    $( ".list-of-images ul" ).sortable({
        placeholder: "image-moving",
        contaiment:  "parent"
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
                            "<li><a href=\"#\" class=\"edit-button\" title=\"{t}Edit{/t}\"><img src=\"{$params.IMAGE_DIR}edit.png\"></a></li>" +
                            "<li><a href=\"#\" class=\"delete-button\" title=\"{t}Drop{/t}\"><img src=\"{$params.IMAGE_DIR}trash.png\"></a></li>" +
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

            $('.list-of-images .delete-button').on('click', function(event,ui){
                $(this).parents('.image.thumbnail').remove();
                event.preventDefault();
            });
        }
    });

    // Delete buttons for drop an image from the album
    $('.list-of-images .delete-button').on('click', function(event, ui){
        $(this).parents('.image.thumbnail').remove();
        event.preventDefault();
    });

    $("#frontpage-image").droppable({
        accept: "#photos_container #photos img",
        drop: function( event, ui ) {
            var image = ui.draggable;
            var parent = $(this);

            if (image.data('type-img') != 'swf') {
                // Change the image thumbnail to the new one
                parent.find('.article-resource-image').html("<img src=\"" + image.data("url") + "\" />");
            } else {
                parent.find('.article-resource-image').html( "<div id=\"flash-container-replace\"><\/div><script> var flashvars = {}; var params = {}; var attributes = {};" +
                    "swfobject.embedSWF(\"" + image.data("url") + image.data("filename")  + "\",  \"flash-container-replace\", \"270\", \"150\", \"9.0.0\", false, flashvars, params, attributes);<\/script>"
                );
            };

            // Change the image information to the new one
            var article_info = parent.find(".article-resource-image-info");
            article_info.find(".filename").html(image.data("filename"));
            article_info.find(".image_size").html(image.data("width") + " x "+ image.data("height") + " px");
            article_info.find(".file_size").html(image.data("weight") + " Kb");
            article_info.find(".created_time").html(image.data("created"));
            article_info.find(".description").html(image.data("description"));
            article_info.find(".tags").html(image.data("tags"));

            // Change the form values
            var article_inputs = parent.find(".article-resource-footer");
            article_inputs.find("input[type='hidden']").attr('value', image.data("id"));

        }
    });

    jQuery('#frontpage-image .delete-button').on('click', function () {
        var parent = jQuery(this).parent();
        var elementID = parent.find('.album-frontpage-image');

        if (elementID.val() > 0) {
            elementID.data('id', elementID.val());
            elementID.val(null);
            parent.fadeTo('slow', 0.5);
        } else {
            elementID.val(elementID.data('id'));
            parent.fadeTo('slow', 1);
        };
    });

});
</script>
