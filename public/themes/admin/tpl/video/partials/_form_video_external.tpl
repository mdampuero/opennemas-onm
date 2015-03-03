<div class="form-group">
    <label for="title" class="form-label">{t}Title{/t}</label>
    <div class="controls">
        <input type="text" id="title" name="title" value="{$video->title|default:""}" required="required" class="form-control"/>
    </div>
</div>

<div class="form-group">
    <label for="description" class="form-label">{t}Description{/t}</label>
    <div class="controls">
        <textarea name="description" id="description" required="required" rows="3" class="form-control">{$video->description|clearslash|default:""}</textarea>
    </div>
</div>

<div class="form-group">
    <label for="body" class="form-label">{t}Body{/t}</label>
    <div class="controls">
        <textarea name="body" id="body" rows="6" class="form-control onm-editor" data-preset="simple">{$video->body|clearslash|default:""}</textarea>
    </div>
</div>

<div class="form-group">
    <label for="typ_medida" class="form-label">{t}Video type and file URLs{/t}</label>
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
<div class="form-group">
    <label for="body" class="form-label">{t}Preview{/t}</label>
    {if isset($video)}
    <div id="video-information" style="text-align:center; margin:0 auto;" class="controls thumbnail video-container">
        <div class="thumbnail">
            {render_video video=$video base_url=$smarty.const.INSTANCE_MEDIA class="videojs"}
        </div>
    </div>
    {/if}
</div>

<div id="related_media" class="form-group">
    <label for="video_image" class="form-label">{t}Video cover{/t}</label>
    <div class="content cover-image {if isset($video) && $video->thumbnail}assigned{/if}">
        <div class="image-data">
            <a href="#media-uploader" {acl isAllowed='PHOTO_ADMIN'}data-toggle="modal"{/acl} data-position="inner-image" class="image thumbnail">
                {if !empty($video->thumbnail)}
                    <img src="{$smarty.const.MEDIA_IMG_PATH_WEB}{$video->thumbnail}"/>
                {/if}
            </a>
            <div class="article-resource-footer">
                <input type="hidden" name="video_image" value="{$video->information['thumbnail']}" class="related-element-id"/>
            </div>
        </div>

        <div class="not-set">
            {t}Image not set{/t}
        </div>

        <div class="btn-group">
            <a href="#media-uploader" {acl isAllowed='PHOTO_ADMIN'}data-toggle="modal"{/acl} data-position="cover-image" class="btn btn-small">{t}Set image{/t}</a>
            <a href="#" class="unset btn btn-small btn-danger"><i class="fa fa-trash"></i></a>
        </div>
    </div>
</div>
<input type="hidden" value="{json_encode($information)|escape:"html"}" name="information" />
<input type="hidden" name="author_name" value="external"/>

{script_tag src="/videojs/video.js" common=1}
{*script_tag src="/videojs/video.ads.js" common=1*}
<script>
    videojs('video', {}, function() {
      var player = this;
     // player.ads(); // initialize the ad framework
      // your custom ad integration code
    });
    jQuery(document).ready(function($){
        $('#continue').on('click', function(e, ui) {
            if ($('.related-element-id').val().length < 1) {
                $(".messages").html('<div class="alert alert-error"><button class="close" data-dismiss="alert">×</button>You must assign a cover video<br></div>');
                e.preventDefault();
            };
        });

        $('.type-selector .nav-tabs a').on('click', function(e, ui) {
            var type = $(this).data('type');
            $('#type').val(type);
        });
    });
</script>

