<div class="form-group">
    <label for="title" class="form-label">{t}Title{/t}</label>
    <div class="controls">
        <input type="text" id="title" name="title" ng-blur="generate()" ng-model="title" value="{$video->title|clearslash|escape:"html"|default:""}" required class="form-control"/>
    </div>
</div>

<div class="form-group">
    <label for="description" class="form-label">{t}Description{/t}</label>
    <div class="controls">
      <textarea onm-editor onm-editor-preset="simple" ng-model="description" name="description" id="description" required rows="3" class="form-control">{$video->description|clearslash|default:""}</textarea>
    </div>
</div>

<div class="form-group">
    <label for="body" class="form-label">{t}Body{/t}</label>
    <div class="controls">
        <textarea name="body" id="body" rows="6" class="form-control" onm-editor ng-model="model" data-preset="simple">{$video->body|clearslash|default:""}</textarea>
    </div>
</div>

<div class="form-group">
    <label for="typ_medida" class="form-label">{t}Video type and file URLs{/t}</label>
    <div class="controls">
        <select name="file_type" id="file_type" ng-model="file_type" required>
          <option value="html5" {if !empty($video->id) && $video->type == 'html5'}selected="selected"{/if}>{t}HTML5 video{/t}</option>
          <option value="flv" {if !empty($video->id) && $video->type == 'flv'}selected="selected"{/if}>{t}Flash video{/t}</option>
        </select>
        <p></p>
        <div class="ng-cloak" ng-if="file_type == 'html5'">
          <div class="input-group">
            <span class="input-group-addon">{t}MP4 format{/t}</span>
            <input type="text" class="form-control" placeholder="{t}http://www.example.com/path/to/file.mp4{/t}" name="information[source][mp4]" value="{$video->information['source']['mp4']|default:""}" aria-describedby="basic-addon-mp4">
          </div>
          <br>
          <div class="input-group">
            <span class="input-group-addon">{t}Ogg format{/t}</span>
            <input type="text" class="form-control" placeholder="{t}http://www.example.com/path/to/file.ogg{/t}" name="information[source][ogg]" value="{$video->information['source']['ogg']|default:""}" aria-describedby="basic-addon-ogg">
          </div>
          <br>
          <div class="input-group">
            <span class="input-group-addon">{t}WebM format{/t}</span>
            <input type="text" class="form-control" placeholder="{t}http://www.example.com/path/to/file.webm{/t}" name="information[source][webm]" value="{$video->information['source']['webm']|default:""}" aria-describedby="basic-addon-webm">
          </div>
        </div>
        <div class="ng-cloak" ng-if="file_type == 'flv'">
          <div class="input-group">
            <span class="input-group-addon">{t}FLV format{/t}</span>
            <input type="text" class="form-control" placeholder="{t}http://www.example.com/path/to/file.flv{/t}" name="information[source][flv]" value="{$video->video_url}" aria-describedby="basic-addon-flv">
          </div>
        </div>
        <input type="hidden" name="type" id="type" value="$video->type|default:'html5'">
    </div>
</div>
<div class="form-group" ng-if="id != ''">
    <label for="body" class="form-label">{t}Preview{/t}</label>
    {if isset($video)}
    <div id="video-information" style="text-align:center; margin:0 auto;" class="controls thumbnail video-container">
        <div class="thumbnail">
            {render_video video=$video base_url=$smarty.const.INSTANCE_MEDIA class="videojs"}
        </div>
    </div>
    {/if}
</div>
<input type="hidden" name="author_name" value="external"/>

{script_tag src="/videojs/video.js" common=1}
{*script_tag src="/videojs/video.ads.js" common=1*}
<script>
    videojs('video', {}, function() {
      // var player = this;
    });
    // jQuery(document).ready(function($){
    //     $('#continue').on('click', function(e, ui) {
    //         if ($('.related-element-id').val().length < 1) {
    //             $(".messages").html('<div class="alert alert-error"><button class="close" data-dismiss="alert">×</button>You must assign a cover video<br></div>');
    //             e.preventDefault();
    //         };
    //     });
    // });
</script>

