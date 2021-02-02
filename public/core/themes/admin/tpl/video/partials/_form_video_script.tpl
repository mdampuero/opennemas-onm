{include file="ui/component/input/text.tpl" iCounter=true iField="title" iNgActions="ng-blur=\"generate()\"" iRequired=true iTitle="{t}Title{/t}" iValidation=true}
{include file="ui/component/content-editor/textarea.tpl" title="{t}Description{/t}" field="description" rows=5 imagepicker=true}
<div class="form-group">
  <label for="video-information" class="form-label">{t}Write HTML code{/t}</label>
  <div class="controls">
    <textarea name="body" id="body" ng-model="item.body" rows="8" class="form-control"></textarea>
 </div>
</div>
<div class="form-group m-t-10" ng-if="item">
  <label  class="form-label">{t}Preview{/t}</label>
  <div ng-bind-html="trustHTML(item.body)" style="width:100%; text-align:center; margin:0 auto;"> </div>
</div>
