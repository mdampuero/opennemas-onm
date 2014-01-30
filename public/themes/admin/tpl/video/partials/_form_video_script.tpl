<div class="control-group">
    <label for="title" class="control-label">{t}Title{/t}</label>
    <div class="controls">
        <input type="text" id="title" name="title" value="{$video->title|default:""}" required="required" class="input-xlarge"/>
    </div>
</div>
<div class="control-group">
    <label for="metadata" class="control-label">{t}Keywords{/t}</label>
    <div class="controls">
        <input type="text" id="metadata" name="metadata" value="{$video->metadata|default:""}" required="required" class="input-xlarge"/>
        <div class="help-block">{t}List of words separated by commas.{/t}</div>
    </div>
</div>
<div class="control-group">
    <label for="description" class="control-label">{t}Description{/t}</label>
    <div class="controls">
        <textarea name="description" id="description" required="required" rows="4" class="input-xxlarge">{$video->description|clearslash|default:""}</textarea>
    </div>
</div>
<div class="control-group">
    <label for="video-information" class="control-label">{if isset($video)}{t}Preview{/t}{else}{t}Write HTML code{/t}{/if}</label>
    <div class="controls">
        <textarea name="body" id="body" required="required" rows="8" class="input-xxlarge">{$video->body|clearslash|default:""}</textarea>
        <br />
        {if isset($video)}
        <div id="video-information" style="text-align:center; margin:0 auto;">
             {render_video video=$video height=$height width="400" height="300" base_url=$smarty.const.INSTANCE_MEDIA}
        </div>
        {/if}


    </div>
</div>

<input type="hidden" value="{$video->video_url}" name="video_url" />
<input type="hidden" value="{json_encode($information)|escape:"html"}" name="information" />
<input type="hidden" name="author_name" value="script"/>
