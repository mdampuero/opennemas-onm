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
            <textarea name="description" id="description" required="required" rows="4" class="input-xxlarge onm-editor" data-preset="simple">{$video->description|clearslash|default:""}</textarea>
        </div>
    </div>
</div>

<div class="contentform-main">
    <div class="control-group">
        <label for="video-information" class="control-label">{t}Write HTML code{/t}</label>
        <div class="controls">
            <textarea name="body" id="body" rows="8" class="input-xxlarge">{$video->body|clearslash|default:""}</textarea>
            <br /><br />

            {if isset($video)}
            <label  class="control-label">{t}Preview{/t}</label>
            <div id="video-information" class="video-container" style="width:530px; text-align:center; margin:0 auto;">
                 {render_video video=$video height=$height width="400" height="300" base_url=$smarty.const.INSTANCE_MEDIA}
            </div>
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
    <div class="contentbox" >
    <div id="related_media" class="control-group">
        <h3 class="title">{t}Video Cover{/t}</h3>
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
                <a href="#" class="unset btn btn-small btn-danger"><i class="icon icon-trash"></i></a>
            </div>
        </div>
    </div>
    </div>
</div>

<input type="hidden" value="{$video->video_url}" name="video_url" />
<input type="hidden" value="{json_encode($information)|escape:"html"}" name="information" />
<input type="hidden" name="author_name" value="script"/>
<input type="hidden" name="infor" value=""/>

<script>
    jQuery(document).ready(function($){
        $('#continue').on('click', function(e, ui) {
            if ($('.related-element-id').val().length < 1) {
                $(".messages").html('<div class="alert alert-error"><button class="close" data-dismiss="alert">×</button>You must assign a cover video<br></div>');
                e.preventDefault();
            };
        });
    });
</script>
