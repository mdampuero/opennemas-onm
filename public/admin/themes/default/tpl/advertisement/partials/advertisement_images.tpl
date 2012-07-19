<h2>{t}Multimedia for this ad:{/t}</h2>
<table id="advertisement-images" style="{if isset($advertisement) && $advertisement->with_script == 1} display:none;{else}display:block;{/if}">
	<tr>
		<td style="width:430px">
            <div id="related-images" class="resource-container">
                <div class="droppable-image-position droppable-position">
                    <div>
                        <a class="delete-button">
                            <img src="{$smarty.const.SITE_URL_ADMIN}/themes/default/images/trash.png" id="remove_img1" alt="Eliminar" title="Eliminar" />
                        </a>
                        <div class="clearfix">
                            <div class="thumbnail article-resource-image">
                                {if isset($photo1) && strtolower($photo1->type_img)=='swf'}
                                    <div id="flash-container-replace"></div><!-- /flash-container-replace -->
                                    <script>
                                        var flashvars = {};
                                        var params = {};
                                        var attributes = {};
                                        swfobject.embedSWF("{$smarty.const.MEDIA_IMG_PATH_URL}{$photo1->path_file}{$photo1->name}", "flash-container-replace", "270", "150", "9.0.0", false, flashvars, params, attributes);
                                    </script>
                                {elseif isset($photo1) && $photo1->name}
                                    <img src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$photo1->path_file}{$photo1->name}" />
                                {else}
                                    <img src="{$smarty.const.SITE_URL_ADMIN}/themes/default/images/default_img.jpg" width="270" />
                                {/if}
                            </div>
                            <div id="image-information" class="article-resource-image-info">
                                <div><label>{t}File name{/t}</label>     <span class="filename">{$photo1->name|default:'default_img.jpg'}</span></div>
                                <div><label>{t}Image size{/t}</label>    <span class="image_size">{$photo1->width|default:0} x {$photo1->height|default:0}</span> (px)</div>
                                <div><label>{t}File size{/t}</label>     <span class="file_size">{$photo1->size|default:0}</span> Kb</div>
                                <div><label>{t}Creation date{/t}</label> <span class="created_time">{$photo1->created|default:""}</span></div>
                                <div><label>{t}Description{/t}</label>   <span class="description">{$photo1->description|escape:'html'}</span></div>
                                <div><label>{t}Tags{/t}</label>          <span class="tags">{$photo1->metadata|default:""}</span></div>
                            </div>
                        </div><!-- / -->
                        <div id="footer_img_portada" class="article-resource-footer">
                            <input class="related-element-id" type="hidden" name="img" value="{$advertisement->img|default:""}" />
                        </div>
                    </div><!-- / -->
                </div><!-- /frontpage-image -->
            </div><!-- /related-images -->
		</td>
		<td id="photos_container">
			<div id="photos" class="photos clearfix"
                 style="border:1px solid #ccc;
                 {if isset($advertisement) && $advertisement->with_script == 1}
                     display:none;
                 {else}
                     display:block;
                 {/if}" >
			</div>
		</td>
	</tr>
</table>
<style>
    #related-images.resource-container { border:1px solid #ccc; padding:10px; }
</style>
<script type="text/javascript">
    document.observe('dom:loaded', function() {
        getGalleryImages('listByCategory','2','','1','photos');
    });
    jQuery(document).ready(function ($){
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
                article_inputs.find("textarea").attr('value', image.data("description"));
            }
        });
    });
</script>