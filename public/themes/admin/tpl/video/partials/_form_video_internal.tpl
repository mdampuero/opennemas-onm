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
        <label for="video-information" class="control-label">{if isset($video)}{t}Preview{/t}{else}{t}Pick a file to upload{/t}{/if}</label>
        <div class="controls">
            {if isset($video)}
            <div id="video-information" style="text-align:center; margin:0 auto;">
                {script_tag src="/media/common_assets/fplayer/flowplayer-3.2.6.min.js" external=1}
                {render_video video=$video height=$height width="400" height="300" base_url=$smarty.const.INSTANCE_MEDIA}
            </div>
            {else}
                    <input type="file" name="video_file" id="video-information">
            {/if}
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
</div>
<input type="hidden" value="{$video->video_url}" name="video_url" />
<input type="hidden" value="{json_encode($information)|escape:"html"}" name="information" />
<input type="hidden" name="author_name" value="internal"/>

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

