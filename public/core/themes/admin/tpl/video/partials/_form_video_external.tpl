{include file="ui/component/input/text.tpl" iCounter=true iField="title" iNgActions="ng-blur=\"generate()\"" iRequired=true iTitle="{t}Title{/t}" iValidation=true}
{include file="ui/component/content-editor/textarea.tpl" title="{t}Summary{/t}" field="description" rows=5 imagepicker=true}

<div class="form-group">
  <label for="typ_medida" class="form-label">{t}Video type and file URLs{/t}</label>
  <div class="controls">
    <select name="file_type" id="file_type" ng-model="item.type" required>
      <option value="html5">{t}HTML5 video{/t}</option>
      <option value="flv">{t}Flash video{/t}</option>
    </select>
    <p></p>
    <div class="ng-cloak" ng-if="item.type == 'html5'">
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
    <div class="ng-cloak" ng-if="item.type == 'flv'">
      <div class="input-group">
        <span class="input-group-addon">{t}FLV format{/t}</span>
        <input type="text" class="form-control" placeholder="{t}http://www.example.com/path/to/file.flv{/t}" name="information[source][flv]" ng-model="item.information.source.flv" aria-describedby="basic-addon-flv">
      </div>
    </div>
  </div>
</div>
<div class="form-group" ng-if="getItemId(item) && (preview.webm || preview.ogg || preview.mp4)">
  <label class="form-label">{t}Preview{/t}</label>
  <div class="controls">
    <div class="thumbnail inline" style="line-height: 0;">
      <video style="margin: 0 auto; width:100%" controls>
        <source ng-if="preview.webm" ng-src="[% preview.webm %]" type="video/webm">
        <source ng-if="preview.ogg" ng-src="[% preview.ogg %]" type="video/ogg">
        <source ng-if="preview.mp4" ng-src="[% preview.mp4 %]" type="video/mp4">
      </video>
    </div>
  </div>
</div>
