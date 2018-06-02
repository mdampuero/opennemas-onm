<div class="form-controlgroup">
  <label for="title" class="form-label">{t}Title{/t}</label>
  <div class="controls">
    <input type="text" id="title" name="title" ng-model="title" value="{$video->title|clearslash|escape:"html"|default:""}" required class="form-control"/>
  </div>
</div>

<div class="form-controlgroup">
  <label for="description" class="form-label">{t}Description{/t}</label>
  <div class="controls">
    <textarea onm-editor onm-editor-preset="simple" ng-model="description" name="description" id="description" required rows="4" class="form-control onm-editor" data-preset="simple">{$video->description|clearslash|default:""}</textarea>
  </div>
</div>

<div class="form-controlgroup">
  <label for="video-information" class="form-label">{t}Write HTML code{/t}</label>
  <div class="controls">
    <textarea name="body" id="body" rows="8" class="form-control">{$video->body|clearslash|default:""}</textarea>
    <br /><br />

    {if isset($video)}
    <label  class="form-label">{t}Preview{/t}</label>
    <div id="video-information" class="video-container" style="width:530px; text-align:center; margin:0 auto;">
     {render_video video=$video height=$height width="400" height="300" base_url=$smarty.const.INSTANCE_MEDIA}
   </div>
   {/if}
 </div>
</div>
<!--
<div id="related_media" class="form-controlgroup">
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
            <a href="#" class="unset btn btn-small btn-danger"><i class="fa fa-trash"></i></a>
        </div>
    </div>
  </div>
-->

<input type="hidden" value="{$video->video_url}" name="video_url" />
<input type="hidden" value="{json_encode($information)|escape:"html"}" name="information" />
<input type="hidden" name="author_name" value="script"/>
<input type="hidden" name="infor" value=""/>
{javascripts}
  <script>
    jQuery(document).ready(function($){
      'use strict';

      $('.video-form').on('submit', function(e, ui) {
        if ($('.related-element-id').val().length < 1) {
          $(".messages").html('<div class="alert alert-error"><button class="close" data-dismiss="alert">×</button>You must assign a cover video<br></div>');
          e.preventDefault();

          return false;
        };
      });
    });
  </script>
{/javascripts}
