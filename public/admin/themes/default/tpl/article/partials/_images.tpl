{is_module_activated name="IMAGE_MANAGER,VIDEO_MANAGER"}
<h2>{t}Multimedia associated to this article:{/t}</h2>
<table style="width:100%">
    {is_module_activated name="IMAGE_MANAGER"}
    <tr>
        <td>
            <div id="related-images" class="resource-container tabs">
                <ul>
                    <li><a href="#frontpage-image" title="{t}Image or video for frontpage:{/t}">{t}Image for frontpage{/t}{if isset($photo1) && $photo1->name}<span class="marker">&#164;</span>{/if}</a></li>
                    <li><a href="#inner-image" title="{t}Image for inner article page:{/t}">{t}Image for inner article page{/t}{if isset($photo2) && $photo2->name}<span class="marker">&#164;</span>{/if}</a></li>
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
                </div><!-- /frontpage-image -->
            </div><!-- /related-images -->

        </td>
        <td style="width:430px">
            <div style="border:1px double #ccc; border-bottom:0 none; background-color:#EEE; padding:10px;">
                <a onclick="new Effect.toggle($('photos_container'),'blind')" ><strong>{t}Available images{/t}</strong></a>
            </div>
            <div id="photos_container" class="photos" style="border:1px solid #ccc;  padding:7px;">
                <table>
                    <tr>
                        <td >
                            <input id="stringImageSearch" name="stringImageSearch" type="text"
                               onkeypress="onImageKeyEnter(event, $('category_imag').options[$('category_imag').selectedIndex].value,encodeURIComponent($('stringImageSearch').value),1);"
                               onclick="this.select();" placeholder="{t}Search images by title...{/t}" />
                        </td>
                        <td>
                            <select style="width:140px" id="category_imag" name="category_imag" class="required" onChange="getGalleryImages('listbyCategory',this.options[this.selectedIndex].value,'', 1);">
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
    {/is_module_activated}

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
                                    <input type="hidden" id="input_video" name="fk_video" value="{$video1->pk_video}">
                                    <img src="{$video1->information['thumbnail']}"
                                         name="{$video1->pk_video}" style="width:120px" />
                                {else}
                                    <input type="hidden" id="input_video" name="fk_video" value="">
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
                            <label for="title">{t}Footer text for frontpage image:{/t}</label>
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
                <table>
                    <tr>
                        <td>
                            <div class="cajaBusqueda" style="width:100%;" >
                                <input class="textoABuscar" id="stringVideoSearch" name="stringVideoSearch" type="text"
                                       onkeypress="onVideoKeyEnter(event, $('category_imag').options[$('category_video').selectedIndex].value, $('stringVideoSearch').value,1);"
                                       onclick="this.select();" placeholder="{t}Search videos by title...{/t}"
                                       />
                            </div>
                        </td>
                        <td>
                            <select style="width:140px"  id="category_video" name="category_video" class="required" onChange="getGalleryVideos('listbyCategory',this.options[this.selectedIndex].value,'', 1,'videos');">
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
                <br>
                <div id="videos">
                    {*AJAX videoGallery *}
               </div>
            </div>
        </td>
    </tr>   
    
    {/is_module_activated}
</table>

{is_module_activated name="IMAGE_MANAGER"}
<script>
jQuery(document).ready(function($){
    $('#related-images').tabs();
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
});
getGalleryImages('listByCategory','{$category}','','1');
</script>
{/is_module_activated}

{is_module_activated name="VIDEO_MANAGER"}
<script>
jQuery(document).ready(function($){
    $('#related-videos').tabs();
});
getGalleryVideos('listByCategory','{$category}','','1', 'videos');
</script>
{/is_module_activated}

{/is_module_activated}
