{acl isAllowed='PHOTO_ADMIN'}
{is_module_activated name="IMAGE_MANAGER,VIDEO_MANAGER"}
<div class="contentform-wide clearfix">
    {is_module_activated name="IMAGE_MANAGER"}
    <ul class="related-images thumbnails">
        <li class="contentbox frontpage-image {if isset($photo1) && $photo1->name}assigned{/if}">
            <h3 class="title">{t}Frontpage image{/t}</h3>
            <div class="content">
                <div class="image-data">
                    <a href="#media-uploader" {acl isAllowed='PHOTO_ADMIN'}data-toggle="modal"{/acl} data-position="frontpage-image" class="image thumbnail">
                        {if is_object($photo1)}
                        <img src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$photo1->path_file}{$photo1->name}"/>
                        {/if}
                    </a>
                    <div class="article-resource-footer">
                        <label for="title">{t}Footer text{/t}</label>
                        <textarea name="img1_footer" style="width:95%" class="related-element-footer">{$article->img1_footer|clearslash|escape:'html'}</textarea>
                        <input type="hidden" name="img1" value="{$article->img1|default:""}" class="related-element-id" />
                    </div>
                </div>

                <div class="not-set">
                    {t}Image not set{/t}
                </div>

                <div class="btn-group">
                    <a href="#media-uploader" data-toggle="modal" data-position="frontpage-image" class="btn btn-small">{t}Set image{/t}</a>
                    <a href="#" class="unset btn btn-small btn-danger"><i class="icon icon-trash"></i></a>
                </div>
            </div>
        </li>
        <li class="contentbox inner-image {if isset($photo2) && $photo2->name}assigned{/if}">
            <h3 class="title">{t}Inner image{/t}</h3>
            <div class="content">
                <div class="image-data">
                    <a href="#media-uploader" data-toggle="modal" data-position="inner-image" class="image thumbnail">
                        {if is_object($photo2)}
                            <img src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$photo2->path_file}{$photo2->name}"/>
                        {/if}
                    </a>
                    <div class="article-resource-footer">
                        <label for="title">{t}Footer text{/t}</label>
                        <textarea name="img2_footer" style="width:95%" class="related-element-footer">{$article->img2_footer|clearslash|escape:'html'}</textarea>
                        <input type="hidden" name="img2" value="{$article->img2|default:""}" class="related-element-id"/>
                    </div>
                </div>

                <div class="not-set">
                    {t}Image not set{/t}
                </div>

                <div class="btn-group">
                    <a href="#media-uploader" data-toggle="modal" data-position="inner-image" class="btn btn-small">{t}Set image{/t}</a>
                    <a href="#" class="unset btn btn-small btn-danger"><i class="icon icon-trash"></i></a>
                </div>
            </div>
        </li>
        {is_module_activated name="CRONICAS_MODULES"}
        <li class="contentbox home-image {if isset($photo3) && $photo3->name}assigned{/if}">
            <h3 class="title">{t}Home image{/t}</h3>
            <div class="content">
                <div class="image-data">
                    <a href="#media-uploader" data-toggle="modal" data-position="home-image" class="image thumbnail">
                        {if is_object($photo3)}
                        <img src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$photo3->path_file}{$photo3->name}"/>
                        {/if}
                    </a>
                    <div class="article-resource-footer">
                        <label for="title">{t}Image footer text{/t}</label>
                        <textarea name="params[imageHomeFooter]" style="width:95%" class="related-element-footer">{$article->img2_footer|clearslash|escape:'html'}</textarea>
                        <input type="hidden" name="params[imageHome]" value="{$article->params['imageHome']|default:""}" class="related-element-id"/>
                    </div>
                </div>

                <div class="not-set">
                    {t}Image not set{/t}
                </div>

                <div class="btn-group">
                    <a href="#media-uploader" data-toggle="modal" data-position="home-image" class="btn btn-small">{t}Set image{/t}</a>
                    <a href="#" class="unset btn btn-small btn-danger"><i class="icon icon-trash"></i></a>
                </div>
            </div>
        </li>
        {/is_module_activated}
    </ul>
    {/is_module_activated}
</div>

<hr>
{if !isset($withoutVideo)}
<div class="contentform-inner clearfix">
    {acl isAllowed="VIDEO_ADMIN"}
    {is_module_activated name="VIDEO_MANAGER"}
    <div class="contentform-main">
        <div id="related-videos" class="resource-container tabs">
            <ul>
                <li><a href="#frontpage-video" title="{t}Image or video for frontpage:{/t}">{t}Video for frontpage{/t}{if isset($video1) && $video1->pk_video}<span class="marker">&#164;</span>{/if}</a></li>
                <li><a href="#inner-video" title="{t}Image for inner article page:{/t}">{t}Video for inner article page{/t}{if isset($video2) && $video2->pk_video}<span class="marker">&#164;</span>{/if}</a></li>
            </ul><!-- / -->

            <div id="frontpage-video" class="droppable-video-position droppable-position">
                <div>
                    <a class="delete-button" onclick="javascript:recuperar_eliminar('video1');">
                        <img src="/themes/admin/images/trash.png" id="remove_video1" alt="Eliminar" title="Eliminar" />
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
                                <div class="drop-here">
                                    {t}Drop a video to here{/t}
                                </div>
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
                        <img src="/themes/admin/images/trash.png" id="remove_video2" alt="Eliminar" title="Eliminar" />
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
                                <div class="drop-here">
                                    {t}Drop a video to here{/t}
                                </div>
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

    </div>
    <div class="contentbox-container">
        <div class="contentbox">
            <h3 class="title">{t}Available videos{/t}</h3>
            <div id="videos-container" class="photos content">
                <div class="input-append">
                    <input class="textoABuscar noentersubmit" id="stringVideoSearch" name="stringVideoSearch" type="text"
                           placeholder="{t}Search videos by title...{/t}"  style="width: 150px !important;"
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
        </div>
    </div>
    {/is_module_activated}
    {/acl}
</div>
{/if}
{is_module_activated name="IMAGE_MANAGER"}
<script>
jQuery(document).ready(function($){
    $('#related_media .unset').on('click', function (e, ui) {
        e.preventDefault();

        var parent = jQuery(this).closest('.contentbox');

        parent.find('.related-element-id').val('');
        parent.find('.related-element-footer').val('');
        parent.find('.image').html('');

        parent.removeClass('assigned');
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

    load_ajax_in_container('{url name=admin_videos_content_provider_gallery category=$category}', $('#videos'));

    function load_video_results () {
        var category = $('#category_video option:selected').val();
        var text = $('#stringVideoSearch').val();
        var url = '{url name=admin_videos_content_provider_gallery}?'+'category='+category+'&metadatas='+encodeURIComponent(text);
        load_ajax_in_container(
            url,
            $('#videos')
        );
    }
    $('#stringVideoSearch, #category_video').on('change', function(e, ui) {
        return load_video_results();
    });
    $('#stringVideoSearch').keydown(function(event) {
        if (event.keyCode == 13) {
            event.preventDefault();
            return load_video_results();
        }
    });

    $('#videos').on('click', '.pager a', function(e, ui) {
        e.preventDefault();
        var link = $(this);
        load_ajax_in_container(link.attr('href'), $('#videos'));
    });
});
</script>
{/is_module_activated}
{/is_module_activated}
{/acl}
