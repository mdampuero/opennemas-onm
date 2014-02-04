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
            <textarea name="description" id="description" required="required" rows="3" class="input-xxlarge">{$video->description|clearslash|default:""}</textarea>
        </div>
    </div>

    <div class="control-group">
        <label for="body" class="control-label">{t}Body{/t}</label>
        <textarea name="body" id="body" rows="6" class="input-xxlarge onm-editor" data-preset="simple">{$video->body|clearslash|default:""}</textarea>
    </div>
</div>
<div class="contentform-main">
    <div class="control-group">
        <label for="typ_medida" class="control-label">{t}Video type and file URLs{/t}</label>
        <div class="controls">
            <div class="tabbable tabs-left type-selector">
              <ul class="nav nav-tabs">
                <li {if empty($video->id ) || empty($video->video_url)} class="active"{/if}><a href="#html5-type-block" data-toggle="tab" data-type="html5">{t}HTML5 video{/t}</a></li>
                <li {if !empty($video->id ) && !empty($video->video_url)} class="active"{/if}><a href="#flv-type-block" data-toggle="tab" data-type="flv">{t}Flash Video{/t}</a></li>
              </ul>
              <div class="tab-content">
                    <div class="tab-pane {if empty($video->id) || empty($video->video_url)} active{/if}" id="html5-type-block">
                        <div class="input-prepend">
                            <span class="add-on span-2">{t}MP4 format{/t}</span>
                            <input type="text" class="input-xlarge" placeholder="{t}http://www.example.com/path/to/file.mp4{/t}" name="infor[source][mp4]" value="{$video->information['source']['mp4']|default:""}">
                        </div>
                        <div class="input-prepend">
                            <span class="add-on span-2">{t}Ogg format{/t}</span>
                            <input type="text" class="input-xlarge" placeholder="{t}http://www.example.com/path/to/file.ogg{/t}" name="infor[source][ogg]" value="{$video->information['source']['ogg']|default:""}">
                        </div>
                        <div class="input-prepend">
                            <span class="add-on span-2">{t}WebM format{/t}</span>
                            <input type="text" class="input-xlarge" placeholder="{t}http://www.example.com/path/to/file.webm{/t}" name="infor[source][webm]" value="{$video->information['source']['webm']|default:""}">
                        </div>
                    </div>
                    <div class="tab-pane {if !empty($video->id ) && !empty($video->video_url)} active{/if}" id="flv-type-block">
                        <div class="input-prepend">
                            <span class="add-on">{t}FLV format{/t}</span>
                            <input type="text" id="video_url" name="video_url" placeholder="{t}http://www.example.com/path/to/file.flv{/t}" value="{$video->video_url|default:""}" class="input-xlarge" />
                        </div>
                    </div>
              </div>
            </div>
            <input type="hidden" name="type" id="type" value="$video->type|default:'html5'">
        </div>
    </div>
    <div class="control-group">
        <label for="body" class="control-label">{t}Preview{/t}</label>
        {if isset($video)}
        <div id="video-information" style="text-align:center; margin:0 auto;" class="controls thumbnail video-container">
            {render_video video=$video base_url=$smarty.const.INSTANCE_MEDIA class="videojs"}
        </div>
        {/if}
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
        <div id="related_media" class="control-group">
            <h3 class="title">{t}Thumbnail image{/t}</h3>
            <div class="content cover-image {if isset($video) && $video->thumbnail}assigned{/if}">
                <div class="image-data">
                    <a href="#media-uploader" {acl isAllowed='IMAGE_ADMIN'}data-toggle="modal"{/acl} data-position="inner-image" class="image thumbnail">
                        {if !empty($video->thumbnail)}
                            <img src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$video->thumbnail}"/>
                        {/if}
                    </a>
                    <div class="article-resource-footer">
                        <input type="hidden" name="video_image" value="{$video->information[thumbnail]}" class="related-element-id"/>
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
</div>
<input type="hidden" value="{json_encode($information)|escape:"html"}" name="information" />
<input type="hidden" name="author_name" value="external"/>

{block name="footer-js" append}
    {script_tag src="/videojs/video.js" common=1}
    {*script_tag src="/videojs/video.ads.js" common=1*}

<script>
    videojs('video', {}, function() {
      var player = this;
     // player.ads(); // initialize the ad framework
      // your custom ad integration code
    });
</script>

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

        $('.type-selector .nav-tabs a').on('click', function(e, ui) {
            var type = $(this).data('type');
            $('#type').val(type);
        })
    });
    </script>
{/block}

