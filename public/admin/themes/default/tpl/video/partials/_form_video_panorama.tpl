<tr>
    <td style="padding:10px; vertical-align:top;" colspan="2">
        <label for="video_url">
        {if isset($video)}
            {t}Video URL:{/t}<br>
        {else}
            {t}Write the video url in the next input and push "Get video information"{/t}
        {/if}
        </label>
        <input type="text" id="video_url" name="video_url" title="Video url"
                value="{$video->video_url|default:""}" class="required" style="width:60%"
                onChange="javascript:loadVideoInformation(this.value);"/> &nbsp;
        <a href="#" class="onm-button blue"
             onClick="javascript:loadVideoInformation($('video_url').value); return false;">
            {t}Get video information{/t}
        </a>
    </td>
</tr>
    
<tr>
    <td style="width:100%; padding:10px" colspan="2">
        <div id="video-information">
            {* AJAX LOAD *}
            {if $smarty.request.action eq "read"}
                {include file="video/partials/_video_information.tpl"}
            {/if}
        </div>
    </td>
</tr>