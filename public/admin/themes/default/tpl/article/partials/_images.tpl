{is_module_activated name="IMAGE_MANAGER,VIDEO_MANAGER"}
<table style="width:100%">
    {is_module_activated name="IMAGE_MANAGER"}
    <tr>
        <td>
            <div id="related-images" class="resource-container tabs">
                <ul>
                    <li><a href="#frontpage-image" title="{t}Image or video for frontpage:{/t}">{t}Image for frontpage{/t}{if isset($photo1) && $photo1->name}<span class="marker">&#164;</span>{/if}</a></li>
                    <li><a href="#inner-image" title="{t}Image for inner article page:{/t}">{t}Image for inner article page{/t}{if isset($photo2) && $photo2->name}<span class="marker">&#164;</span>{/if}</a></li>
                    {is_module_activated name="AVANCED_ARTICLE_MANAGER"}
                    <li><a href="#home-image" title="{t}Image for home:{/t}">{t}Image for Home{/t}{if isset($photo3) && $photo3->name}<span class="marker">&#164;</span>{/if}</a></li>
                    {/is_module_activated}
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
                            <label for="title">{t}Footer text for frontpage image:{/t}</label>
                            <textarea name="img1_footer" style="width:95%" class="related-element-footer">{$article->img1_footer|clearslash|escape:'html'}</textarea>
                            <input type="hidden" name="img1" value="{$article->img1|default:""}" class="related-element-id" />
                        </div>
                    </div><!-- / -->
                </div><!-- /frontpage-image -->
                <div id="inner-image" class="droppable-image-position droppable-position">
                    <div>
                        <a class="delete-button">
                            <img src="{$smarty.const.SITE_URL_ADMIN}/themes/default/images/trash.png" id="remove_img2" alt="Eliminar" title="Eliminar" />
                        </a>
                        <div class="clearfix">
                            <div id="droppable_div2" class="thumbnail article-resource-image">
                                {if isset($photo2) && $photo2->name}
                                    <img src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$photo2->path_file}{$photo2->name}" id="inner_image" name="{$article->img2}" />
                                {else}
                                    <img src="http://placehold.it/290x226" id="inner_image" />
                                {/if}
                            </div>
                            <div class="article-resource-image-info">
                                <div><label>{t}File name{/t}</label>     <span class="filename">{$photo2->name|default:'default_img.jpg'}</span></div>
                                <div><label>{t}Image size{/t}</label>    <span class="image_size">{$photo2->width|default:0} x {$photo1->height|default:0} (px)</span></div>
                                <div><label>{t}File size{/t}</label>     <span class="file_size">{$photo2->size|default:0} Kb</span></div>
                                <div><label>{t}Creation date{/t}</label> <span class="created_time">{$photo2->created|default:""}</span></div>
                                <div><label>{t}Description{/t}</label>   <span class="description">{$photo2->description|escape:'html'}</span></div>
                                <div><label>{t}Tags{/t}</label>          <span class="tags">{$photo2->metadata|default:""}</span></div>
                            </div>
                        </div>
                        <div id="footer_img_interior" class="article-resource-footer">
                            <label for="title">{t}Footer text for inner image:{/t}</label>
                            <textarea name="img2_footer" title="Imagen" style="width:95%" class="related-element-footer">{$article->img2_footer|clearslash|escape:'html'}</textarea>
                            <input type="hidden" name="img2" value="{$article->img2|default:""}" class="related-element-id"/>
                        </div>
                    </div>
                </div><!-- /inner-image -->
                {is_module_activated name="AVANCED_ARTICLE_MANAGER"}
                <div id="home-image" class="droppable-image-position droppable-position">
                    <div>
                        <a class="delete-button">
                            <img src="{$smarty.const.SITE_URL_ADMIN}/themes/default/images/trash.png" id="remove_imgHome" alt="Eliminar" title="Eliminar" />
                        </a>
                        <div class="clearfix">
                            <div class="thumbnail article-resource-image">
                                {if isset($article->params['imageHome']) && $photo3->name}
                                    <img src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$photo3->path_file}{$photo3->name}" id="home_image" name="{$photo3->name}" />
                                {else}
                                    <img src="http://placehold.it/290x226" id="home_image" />
                                {/if}
                            </div>
                            <div class="article-resource-image-info">
                                <div><label>{t}File name{/t}</label>     <span class="filename">{$photo3->name|default:'default_img.jpg'}</span></div>
                                <div><label>{t}Image size{/t}</label>    <span class="image_size">{$photo3->width|default:0} x {$photo3->height|default:0}</span> (px)</div>
                                <div><label>{t}File size{/t}</label>     <span class="file_size">{$photo3->size|default:0}</span> Kb</div>
                                <div><label>{t}Creation date{/t}</label> <span class="created_time">{$photo3->created|default:""}</span></div>
                                <div><label>{t}Description{/t}</label>   <span class="description">{$photo3->description|escape:'html'}</span></div>
                                <div><label>{t}Tags{/t}</label>          <span class="tags">{$photo3->metadata|default:""}</span></div>
                            </div>
                        </div><!-- / -->
                        <div id="footer_img_portada" class="article-resource-footer">
                            <label for="title">{t}Footer text for Home image:{/t}</label>
                            <textarea name="params[imageHomeFooter]" style="width:95%" class="related-element-footer">{$article->params['imageHomeFooter']|clearslash|escape:'html'}</textarea>
                            <input type="hidden" name="params[imageHome]" value="{$article->params['imageHome']|default:""}" class="related-element-id" />
                        </div>
                    </div><!-- / -->
                </div><!-- /home-image -->

                {/is_module_activated}
            </div><!-- /related-images -->

        </td>
        <td style="width:430px">
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
                    </select>
                </div>
                <div id="photos">
                    {*AJAX imageGallery *}
                </div>
           </div>
        </td>
    </tr>
    {/is_module_activated}


    {acl isAllowed="VIDEO_ADMIN"}
    {is_module_activated name="VIDEO_MANAGER"}
    <tr>
        <td colspan=2><hr></td>
    </tr>
    <tr>
        <td>
            <div id="related-videos" class="resource-container tabs">
                <ul>
                    <li><a href="#frontpage-video" title="{t}Image or video for frontpage:{/t}">{t}Video for frontpage{/t}{if isset($video1) && $video1->pk_video}<span class="marker">&#164;</span>{/if}</a></li>
                    <li><a href="#inner-video" title="{t}Image for inner article page:{/t}">{t}Video for inner article page{/t}{if isset($video2) && $video2->pk_video}<span class="marker">&#164;</span>{/if}</a></li>
                </ul><!-- / -->

                <div id="frontpage-video" class="droppable-video-position droppable-position">
                    <div>
                        <a class="delete-button" onclick="javascript:recuperar_eliminar('video1');">
                            <img src="{$smarty.const.SITE_URL_ADMIN}/themes/default/images/trash.png" id="remove_video1" alt="Eliminar" title="Eliminar" />
                        </a>
                        <div class="clearfix">
                            <div class="thumbnail article-resource-image">
                                {if $video1->pk_video}
                                    <img src="{$video1->information['thumbnail']}"
                                         name="{$video1->pk_video}" style="width:120px" />
                                {else}
                                    {if isset($video1) && $video1->pk_video}
                                        {if $video1->author_name == 'internal'}
                                        <img src="{$smarty.const.MEDIA_IMG_PATH_WEB}/../{$video1->information['thumbnails']['normal']}" />
                                        {else}
                                        <img src="{$video1->information['thumbnail']}" />
                                        {/if}
                                    {else}
                                        <img src="http://placehold.it/290x226" />
                                    {/if}
                                {/if}
                            </div>
                            <div class="article-resource-image-info">
                                <div><label>{t}File name{/t}</label>     <span class="filename">{$video1->name|default:'default_img.jpg'}</span></div>
                                <div><label>{t}Creation date{/t}</label> <span class="created_time">{$video1->created|default:""}</span></div>
                                <div><label>{t}Description{/t}</label>   <span class="description">{$video1->description|escape:'html'}</span></div>
                                <div><label>{t}Tags{/t}</label>          <span class="tags">{$video1->metadata|default:""}</span></div>
                            </div>
                        </div><!-- / -->
                        <div class="article-resource-footer">
                            <!-- <label for="title">{t}Footer text for frontpage image:{/t}</label> -->
                            <!-- <textarea name="img1_footer" style="width:95%" class="related-element-footer">{$article->img1_footer|clearslash|escape:'html'}</textarea> -->
                            <input type="hidden" name="fk_video" value="{$article->fk_video|default:""}" class="related-element-id" />
                        </div>
                    </div><!-- / -->
                </div><!-- /frontpage-video -->

                <div id="inner-video" class="droppable-video-position droppable-position">
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
                            <label for="title">{t}Footer text for inner video:{/t}</label>
                            <textarea name="footer_video2" style="width:95%" class="related-element-footer">{$article->footer_video2|clearslash|escape:'html'}</textarea>
                            <input type="hidden" name="fk_video2" value="{$video2->pk_video}" class="related-element-id"/>
                        </div>
                    </div>
                </div><!-- /inner-video -->
            </div><!-- /related-videos -->
        </td>
        <td>
            <div style="border:1px double #ccc; border-bottom:0 none; background-color:#EEE; padding:7px;">
                <strong>{t}Available videos{/t}</strong>
            </div>
            <div id="videos-container" class="photos" style=" border:1px solid #ccc;  padding:7px;">
                <div class="input-append">
                    <input class="textoABuscar noentersubmit" id="stringVideoSearch" name="stringVideoSearch" type="text"
                           placeholder="{t}Search videos by title...{/t}"  style="width: 150px;"
                           />
                    <select style="width:140px"  id="category_video" name="category_video">
                        <option value="0">GLOBAL</option>
                        {section name=as loop=$allcategorys}
                            <option value="{$allcategorys[as]->pk_content_category}" {if $category eq $allcategorys[as]->pk_content_category}selected{/if}>{$allcategorys[as]->title}</option>
                            {section name=su loop=$subcat[as]}
                                    <option value="{$subcat[as][su]->pk_content_category}" {if $category eq $subcat[as][su]->pk_content_category}selected{/if}>&nbsp;&nbsp;&nbsp;&nbsp;{$subcat[as][su]->title}</option>
                            {/section}
                        {/section}
                    </select>
                </div>
                <div id="videos">
                    <!-- Ajax -->
               </div>
            </div>
        </td>
    </tr>
    {/is_module_activated}
    {/acl}
</table>

{is_module_activated name="IMAGE_MANAGER"}
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

    load_ajax_in_container('{url name=admin_videos_content_provider_gallery category=$category}', $('#videos'));

    $('#stringVideoSearch, #category_video').on('change', function(e, ui) {
        var category = $('#category_video option:selected').val();
        var text = $('#stringVideoSearch').val();
        var url = '{url name=admin_videos_content_provider_gallery}?'+'category='+category+'&metadatas='+encodeURIComponent(text);
        load_ajax_in_container(
            url,
            $('#videos')
        );
    });

    $('#videos').on('click', '.pager a', function(e, ui) {
        e.preventDefault();
        var link = $(this);
        load_ajax_in_container(link.attr('href'), $('#videos'));
    });

});
</script>
{/is_module_activated}

{is_module_activated name="VIDEO_MANAGER"}
<script>
jQuery(document).ready(function($){
    $('#related-videos').tabs();
    jQuery('#related-videos .delete-button').on('click', function () {
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
// getGalleryVideos('listByCategory','{$category}','','1', 'videos');
</script>
{/is_module_activated}
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
            article_inputs.find("textarea").attr('value', image.data("description"));
        }
    });
});
</script>
{/is_module_activated}
