{include file="ui/component/content-editor/input-text.tpl" title="{t}Title{/t}" field="title" required=true}

{include file="ui/component/content-editor/textarea.tpl" title="{t}Description{/t}" field="description" rows=5 imagepicker=true}

<div class="form-controlgroup">
  <label for="video-information" class="form-label">{t}Write HTML code{/t}</label>
  <div class="controls">
    <textarea name="body" id="body" ng-model="item.body" rows="8" class="form-control"></textarea>
 </div>
</div>

<div class="form-controlgroup m-t-10" ng-if="item">
  <label  class="form-label">{t}Preview{/t}</label>
  <div ng-bind-html="trustHTML(item.body)" style="width:100%; text-align:center; margin:0 auto;"> </div>
</div>
