<table>
    <tr>
        <td>
            <div id="related-images">
                <div class="droppable-image-position droppable-position">
                    <div>
                        <a class="delete-button">
                            <img src="{$smarty.const.SITE_URL_ADMIN}/themes/default/images/trash.png" id="remove_img1" alt="Eliminar" title="Eliminar" />
                        </a>
                        <div class="clearfix">
                            <div class="thumbnail article-resource-image">
                                {if isset($image) && strtolower($image->type_img)=='swf'}
                                <div id="flash-container-replace"></div>
                                <!-- /flash-container-replace -->
                                <script>
                                        var flashvars = {};
                                        var params = { wmode: "opaque" };
                                        var attributes = {};
                                        swfobject.embedSWF("{$smarty.const.MEDIA_IMG_PATH_URL}{$image->path_file}{$image->name}", "flash-container-replace", "270", "150", "9.0.0", false, flashvars, params, attributes);
                                    </script>
                                {elseif isset($image) && $image->name}
                                <img src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$image->
                                path_file}{$image->name}" />
                                {else}
                                <img src="{$smarty.const.SITE_URL_ADMIN}/themes/default/images/default_img.jpg" width="270" />
                                {/if}
                            </div>
                            <div id="image-information" class="article-resource-image-info">
                                <div>
                                    <strong>{t}File name{/t}</strong>
                                    <span class="filename">{$image->name|default:'default_img.jpg'}</span>
                                </div>
                                <div>
                                    <strong>{t}Image size{/t}</strong>
                                    <span class="image_size">{$image->width|default:0} x {$image->height|default:0}</span>
                                    (px)
                                </div>
                                <div>
                                    <strong>{t}File size{/t}</strong>
                                    <span class="file_size">{$image->size|default:0}</span>
                                    Kb
                                </div>
                                <div>
                                    <strong>{t}Creation date{/t}</strong>
                                    <span class="created_time">{$image->created|default:""}</span>
                                </div>
                                <div>
                                    <strong>{t}Description{/t}</strong>
                                    <span class="description">{$image->description|escape:'html'}</span>
                                </div>
                                <div>
                                    <strong>{t}Tags{/t}</strong>
                                    <span class="tags">{$image->metadata|default:""}</span>
                                </div>
                            </div>
                        </div>
                        <!-- / -->
                        <div id="footer_img_portada" class="article-resource-footer">
                            <input class="related-element-id" type="hidden" name="image" value="{$image->pk_photo|default:""}" /></div>
                    </div>
                    <!-- / --> </div>
                <!-- /frontpage-image --> </div>
            <!-- /related-images --> </td>
        <td id="photos_container">
            <div style="border:1px double #ccc; border-bottom:0 none; background-color:#EEE; padding:10px;">
                <strong>{t}Available images{/t}</strong>
            </div>
            <div id="photos_container" class="photos" style="border:1px solid #ccc;  padding:7px;">
                <div class="input-append">
                    <input id="stringImageSearch" name="stringImageSearch" type="text"
                       placeholder="{t}Search images by title...{/t}" style="width: 150px;" class="noentersubmit"/>
                    <select style="width:140px" id="category_imag" name="category_imag">
                        <option value="0">GLOBAL</option>
                        {section name=as loop=$allcategorys}
                            <option value="{$allcategorys[as]->pk_content_category}" {if $category eq $allcategorys[as]->pk_content_category}selected{/if}>{$allcategorys[as]->title}</option>
                            {section name=su loop=$subcat[as]}
                                <option value="{$subcat[as][su]->pk_content_category}" {if $category eq $subcat[as][su]->pk_content_category}selected{/if}>&nbsp;&nbsp;&nbsp;&nbsp;{$subcat[as][su]->title}</option>
                            {/section}
                        {/section}
                        <option value="4">{t}Opinion{/t}</option>
                    </select>
                </div>
                <div id="photos">
                    {*AJAX imageGallery *}
                </div>
           </div>

        </td>
    </tr>
</table>
<style>
    #related-images.resource-container { border:1px solid #ccc; padding:10px; }
</style>
<script type="text/javascript">
    jQuery(document).ready(function ($){

        load_ajax_in_container('{url name=admin_images_content_provider_gallery category=$category}', $('#photos'));

        $('#stringImageSearch, #category_imag').on('change', function(e, ui) {
            var category = $('#category_imag option:selected').val();
            var text = $('#stringImageSearch').val();
            var url = '{url name=admin_images_content_provider_gallery}?'+'category='+category+'&metadatas='+encodeURIComponent(text);
            load_ajax_in_container(
                url,
                $('#photos')
            );
        });

        $('#photos').on('click', '.pager a', function(e, ui) {
            e.preventDefault();
            var link = $(this);
            load_ajax_in_container(link.attr('href'), $('#photos'));
        });

        jQuery('#related-images .delete-button').on('click', function () {
            var parent = jQuery(this).parent();
            var elementID = parent.find('.related-element-id');

            if (elementID.val() > 0) {
                elementID.data('id', elementID.val());
                elementID.val(null);
                parent.fadeTo('slow', 0.5);
            } else {
                elementID.val(elementID.data('id'));
                parent.fadeTo('slow', 1);
            };
        });
        $( ".droppable-image-position" ).droppable({
            accept: "#photos_container #photos img",
            drop: function( event, ui ) {
                var image = ui.draggable;
                var parent = $(this);

                if (image.data('type-img') != 'swf') {
                    // Change the image thumbnail to the new one
                    parent.find('.article-resource-image').html("<img src=\"" + image.data("url") + "\" />");
                } else {
                    parent.find('.article-resource-image').html(
                        "<div id=\"flash-container-replace\"><\/div>"+"<script> var flashvars = {}; var params = { wmode:\"opaque\" }; var attributes = {};" +
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
                article_inputs.find("textarea").attr('value', image.data("description"));

                $('#params_width').val(image.data("width"));
                $('#params_height').val(image.data("height"));
            }
        });
    });
</script>