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
            <textarea name="description" id="description" required="required" rows="6" class="input-xxlarge onm-editor" data-preset="simple">{$video->description|clearslash|default:""}</textarea>
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
