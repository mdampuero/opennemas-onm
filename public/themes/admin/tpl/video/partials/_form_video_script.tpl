{include file="ui/component/content-editor/input-text.tpl" title="{t}Title{/t}" field="title" required=true}

{include file="ui/component/content-editor/textarea.tpl" title="{t}Description{/t}" field="description" rows=5 imagepicker=true}

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
<input type="hidden" name="author_name" value="script"/>
<input type="hidden" name="infor" value=""/>
