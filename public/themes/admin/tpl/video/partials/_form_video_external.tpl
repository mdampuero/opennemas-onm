
{include file="ui/component/content-editor/input-text.tpl" title="{t}Title{/t}" field="title" required=true counter=true}
{include file="ui/component/content-editor/textarea.tpl" title="{t}Summary{/t}" field="description" rows=5 imagepicker=true}
{include file="ui/component/content-editor/textarea.tpl" title="{t}Body{/t}" field="body" rows=5 imagepicker=true}

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
        <input type="text" class="form-control" placeholder="{t}http://www.example.com/path/to/file.mp4{/t}" name="information[source][mp4]" ng-model="item.information.source.mp4" aria-describedby="basic-addon-mp4">
      </div>
      <br>
      <div class="input-group">
        <span class="input-group-addon">{t}Ogg format{/t}</span>
        <input type="text" class="form-control" placeholder="{t}http://www.example.com/path/to/file.ogg{/t}" name="information[source][ogg]" ng-model="item.information.source.ogg" aria-describedby="basic-addon-ogg">
      </div>
      <br>
      <div class="input-group">
        <span class="input-group-addon">{t}WebM format{/t}</span>
        <input type="text" class="form-control" placeholder="{t}http://www.example.com/path/to/file.webm{/t}" name="information[source][webm]" ng-model="item.information.source.webm" aria-describedby="basic-addon-webm">
      </div>
    </div>
    <div class="ng-cloak" ng-if="file_type == 'flv'">
      <div class="input-group">
        <span class="input-group-addon">{t}FLV format{/t}</span>
        <input type="text" class="form-control" placeholder="{t}http://www.example.com/path/to/file.flv{/t}" name="information[source][flv]" ng-model="item.information.source.flv" aria-describedby="basic-addon-flv">
      </div>
    </div>
  </div>
</div>
<div class="form-group" ng-if="item.id != ''">
  <label class="form-label">{t}Preview{/t}</label>
  {if isset($video)}
  <div class="controls">
    <div class="thumbnail inline" style="line-height: 0;">
      {render_video video=$video base_url=$smarty.const.INSTANCE_MEDIA}
    </div>
  </div>
  {/if}
</div>
