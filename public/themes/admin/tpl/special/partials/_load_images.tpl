{is_module_activated name="IMAGE_MANAGER"}
<hr>
<table style="width:100%;margin:0;">
    <tr>
        <td>
            <div id="related-images" class="resource-container tabs">
                <ul>
                    <li><a href="#special-image" title="{t}Image for home:{/t}">{t}Image{/t}</a></li>
                </ul><!-- / -->
                <div id="frontpage-image" class="droppable-image-position droppable-position">
                    <div>
                        <a class="delete-button">
                            <img src="{$smarty.const.SITE_URL_ADMIN}/themes/default/images/trash.png" id="remove_img1" alt="Eliminar" title="Eliminar" />
                        </a>
                        <div class="clearfix">
                            <div class="thumbnail article-resource-image">
                                {if isset($photo1) && $photo1->name}
                                    <img src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$photo1->path_file}{$photo1->name}" id="frontpage_image" name="{$article->img1}" />
                                {else}
                                    <img src="http://placehold.it/290x226" id="frontpage_image" />
                                {/if}
                            </div>
                            <div class="article-resource-image-info">
                                <div><label>{t}File name{/t}</label>     <span class="filename">{$photo1->name|default:'default_img.jpg'}</span></div>
                                <div><label>{t}Image size{/t}</label>    <span class="image_size">{$photo1->width|default:0} x {$photo1->height|default:0}</span> (px)</div>
                                <div><label>{t}File size{/t}</label>     <span class="file_size">{$photo1->size|default:0}</span> Kb</div>
                                <div><label>{t}Creation date{/t}</label> <span class="created_time">{$photo1->created|default:""}</span></div>
                                <div><label>{t}Description{/t}</label>   <span class="description">{$photo1->description|escape:'html'}</span></div>
                                <div><label>{t}Tags{/t}</label>          <span class="tags">{$photo1->metadata|default:""}</span></div>
                            </div>
                        </div><!-- / -->
                        <div id="footer_img_portada" class="article-resource-footer">
                            <input type="hidden" name="img1" value="{$special->img1|default:""}" class="related-element-id" />
                        </div>
                    </div><!-- / -->
                </div><!-- /frontpage-image -->
            </div><!-- /related-images -->
        </td>
          <td style="width:430px">
            <div style="border:1px double #ccc; border-bottom:0 none; background-color:#EEE; padding:10px;">
                <a><strong>{t}Available images{/t}</strong></a>
            </div>
            <div id="photos_container" class="photos" style="border:1px solid #ccc;  padding:7px;min-height: 450px;">
                <table>
                    <tr>
                        <td >
                            <input id="stringImageSearch" name="stringImageSearch" type="text"
                               placeholder="{t}Search images by title...{/t}" />
                        </td>
                        <td>
                            <select style="width:140px" id="category_imag" name="category_imag">
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
                <div id="photos">
                    {*AJAX imageGallery *}
                </div>
           </div>
        </td>
    </tr>
</table>

<script>
jQuery(document).ready(function($){
    $('#related-images').tabs();
    $('#related-images .delete-button').on('click', function () {
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
});
</script>


<script>
jQuery(document).ready(function($){
    $( ".droppable-image-position" ).droppable({
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
});
</script>
{/is_module_activated}
