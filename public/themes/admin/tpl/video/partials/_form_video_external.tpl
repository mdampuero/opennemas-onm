<div class="contentform-main">
    <div class="control-group">
        <label for="title" class="control-label">{t}Title{/t}</label>
        <div class="controls">
            <input type="text" id="title" name="title" value="{$video->title|default:""}" required="required" class="input-xxlarge"/>
        </div>
    </div>

    <div class="control-group">
        <label for="description" class="control-label">{t}Description{/t}</label>
        <div class="controls">
            <textarea name="description" id="description" required="required" rows="6" class="input-xxlarge">{$video->description|clearslash|default:""}</textarea>
        </div>
    </div>
</div>
<div class="contentform-main">
    <div class="control-group">
        <label for="typ_medida" class="control-label">{t}Video Type{/t}</label>
        <div class="controls">
            <select name="type" id="type">
                <option value="flv" {if !isset($video) || is_null($video->type)}selected="selected"{/if}>flv</option>
                <option value="html5" {if isset($video) && isset($video->type) && $video->type == 'html5'}selected="selected"{/if}>{t}html5{/t}</option>
            </select>
            <div class="help-block">{t}Show video{/t}.</div>
        </div>
    </div>
    <div class="control-group flv-type">
        <label for="video-information" class="control-label">Video Url</label>
        <div class="controls">
            <div class="input-prepend">
             <span class="btn">flv</span>
                <input type="text" id="video_url" name="video_url"
                    value="{$video->video_url|default:""}" class="input-xxlarge" />
            </div>
        </div>
    </div>
    <div class="control-group html5-type">
        <label for="video-information" class="control-label">Video Url's </label>
        <div class="controls">
            <div class="input-prepend">
              <span class="btn">mp4</span>
              <input type="text" class="input-xxlarge" placeholder="mp4" name="infor['mp4']" value="{$video->information[mp4]|default:""}">
            </div>
            <div class="input-prepend">
              <span class="btn">ogg</span>
              <input type="text" class="input-xxlarge" placeholder="ogg"name="infor['webm']" value="{$video->information[webm]|default:""}">
            </div>
            <div class="input-prepend">
              <span class="btn">webm</span>
              <input type="text" class="input-xxlarge" placeholder="webm" name="infor['webm']" value="{$video->information[webm]|default:""}">
            </div>

            <div class="input-append">
                 <textarea name="body" id="body" rows="6" class="input-xxlarge">{$video->body|clearslash|default:""}</textarea>
                 <br>
                 {if isset($video)}
                    <div id="video-information" style="text-align:center; margin:0 auto;">

                        {render_video video=$video height=$height width="400" height="300" base_url=$smarty.const.INSTANCE_MEDIA}
                    </div>
                {/if}
            </div>
        </div>
    </div>
</div>

<div class="contentbox-container">
    <div class="contentbox">
        <h3 class="title">{t}Tags{/t}</h3>
        <div class="content">
            <div class="control-group">
                <div class="controls">
                    <input  type="text" id="metadata" name="metadata" required="required" value="{$video->metadata}"/>
                </div>
            </div>
        </div>
    </div>
    <div class="contentbox" >
        <h3 class="title">{t}Thumbnail image{/t}</h3>
        <div class="content cover-image {if isset($video) && $video->thumbnail}assigned{/if}">
            <div class="image-data">
                <a href="#media-uploader" {acl isAllowed='IMAGE_ADMIN'}data-toggle="modal"{/acl} data-position="inner-image" class="image thumbnail">
                    {if !empty($video->thumbnail)}
                        <img src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$video->thumbnail}"/>
                    {/if}
                </a>
                <div class="article-resource-footer">
                    <input type="hidden" name="video_image" value="{$video->thumbnail}" class="video-frontpage-image"/>
                </div>
            </div>

            <div class="not-set">
                {t}Image not set{/t}
            </div>

            <div class="btn-group">
                <a href="#media-uploader" {acl isAllowed='IMAGE_ADMIN'}data-toggle="modal"{/acl} data-position="cover-image" class="btn btn-small">{t}Set image{/t}</a>
                <a href="#" class="unset btn btn-small btn-danger"><i class="icon icon-trash"></i></a>
            </div>
        </div>
    </div>
</div>
<input type="hidden" value="{json_encode($information)|escape:"html"}" name="information" />
<input type="hidden" name="author_name" value="external"/>

{block name="footer-js" append}
    {script_tag src="/onm/content-provider.js"}
    <script>
    jQuery(document).ready(function($){
    $('#title').on('change', function(e, ui) {
            fill_tags($('#title').val(),'#metadata', '{url name=admin_utils_calculate_tags}');
        });
    });
    </script>
    {include file="media_uploader/media_uploader.tpl"}
    <script>
    jQuery(document).ready(function($){
        var mediapicker = $('#media-uploader').mediaPicker({
            upload_url: "{url name=admin_image_create category=0}",
            browser_url : "{url name=admin_media_uploader_browser}",
            months_url : "{url name=admin_media_uploader_months}",
            maxFileSize: '{$smarty.const.MAX_UPLOAD_FILE}',
            // initially_shown: true,
            handlers: {
                'assign_content' : function( event, params ) {
                    var mediapicker = $(this).data('mediapicker');
                    var image_element = mediapicker.buildHTMLElement(params);

                    var container = $('#related_media').find('.'+params['position']);

                    var image_data_el = container.find('.image-data');
                    image_data_el.find('.related-element-id').val(params.content.pk_photo);
                    image_data_el.find('.related-element-footer').val(params.description);
                    image_data_el.find('.image').html(image_element);
                    container.addClass('assigned');
                }
            }
        });
        $('.article_images .unset').on('click', function (e, ui) {
            e.preventDefault();

            var parent = jQuery(this).closest('.contentbox');

            parent.find('.related-element-id').val('');
            parent.find('.image').html('');

            parent.removeClass('assigned');
        });
    });
    </script>
{/block}

