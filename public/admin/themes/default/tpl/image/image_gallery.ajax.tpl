<ul id='thelist' class="gallery_list clearfix" style="width:100%; margin:0px; padding:0px">
   {assign var=num value='1'}
   {section name=n loop=$photos}
        <li>
            <div style="float: left;">
                <a>
                    {if $photos[n]->type_img=='swf' || $photos[n]->type_img=='SWF'}
                        <object style="z-index:-3; cursor:default;">
                            <param name="movie" value="{$smarty.const.MEDIA_IMG_PATH_WEB}{$photos[n]->path_file}{$photos[n]->name}">
                            <param name="autoplay" value="false">
                            <param name="autoStart" value="0">
                            <embed  width="68" height="40"
                                    src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$photos[n]->path_file}{$photos[n]->name}"
                                    name="{$photos[n]->pk_photo}">
                            </embed>
                        </object>
                        <span  style="float:right; clear:none; width:100%; height:100%; z-index:1;">
                            <img id="draggable_img{$num}"
                                 class="draggable-handler"
                                 style="width:16px;height:16px;"
                                 src="{$smarty.const.SITE_URL_ADMIN}/themes/default/images/flash.gif"
                                 name="{$photos[n]->pk_photo}"
                                 data-id="{$photos[n]->pk_photo}"
                                 data-url="{$smarty.const.MEDIA_IMG_PATH_WEB}{$photos[n]->path_file}"
                                 data-filename="{$photos[n]->name}"
                                 data-filepath="{$photos[n]->path_file}"
                                 data-width="{$photos[n]->width}"
                                 data-height="{$photos[n]->height}"
                                 data-weight="{$photos[n]->size}"
                                 data-created="{$photos[n]->created}"
                                 data-type-img="{$photos[n]->type_img}"
                                 data-description="{$photos[n]->description}"
                                 data-tags="{$photos[n]->metadata}"
                                 />
                        </span>
                    {else}
                        <img style="{cssphotoscale width=$photos[n]->width height=$photos[n]->height resolution=67 photo=$photos[n]}"
                            src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$photos[n]->path_file}140-100-{$photos[n]->name}"
                            id="draggable_img{$num}"
                            class="draggable-handler"
                            name="{$photos[n]->pk_photo}"
                            data-id="{$photos[n]->pk_photo}"
                            data-url="{$smarty.const.MEDIA_IMG_PATH_WEB}{$photos[n]->path_file}{$photos[n]->name}"
                            data-filename="{$photos[n]->name}"
                            data-filepath="{$photos[n]->path_file}"
                            data-width="{$photos[n]->width}"
                            data-height="{$photos[n]->height}"
                            data-weight="{$photos[n]->size}"
                            data-created="{$photos[n]->created}"
                            data-type-img="{$photos[n]->type_img}"
                            data-description="{$photos[n]->description|clearslash|escape:'html'}"
                            data-tags="{$photos[n]->metadata}"
                    {/if}
                </a>
            </div>
        </li>
        {assign var=num value=$num+1}
    {/section}
</ul>
{if !empty($imagePager)}
    <div class="pagination"> {$imagePager} </div>
{/if}
<script>
jQuery(document).ready(function($){
    $( "#photos_container #photos .draggable-handler" ).draggable({ opacity: 0.5, helper: "clone"});
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