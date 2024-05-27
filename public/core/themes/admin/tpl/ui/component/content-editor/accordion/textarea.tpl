<div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.{$field} = !expanded.{$field}">
  <i class="fa fa-pencil m-r-10"></i>{$title}
  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.{$field} }"></i>
</div>
<div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.{$field} }">
  {if $imagepicker}
    {acl isAllowed='PHOTO_ADMIN'}
      <div class="pull-right">
        <div class="btn btn-mini" {if $required}required{/if} media-picker media-picker-mode="explore,upload" media-picker-selection="true" media-picker-max-size="1" media-picker-target="editor.{$field}" photo-editor-enabled="true">
          <i class="fa fa-plus"></i>
          {t}Insert image{/t}
        </div>
      </div>
  {/acl}
  {/if}
  {if $contentPicker}
    <div class="pull-right m-r-5">
      <div class="btn btn-mini" {if $required}required{/if} content-picker content-picker-intime="true" content-picker-locale="config.locale.selected" {if $id}content-picker-ignore="[{$id}]" {/if}content-picker-target="editor.{$field}" content-picker-selection="true" content-picker-type="album,article,attachment,event,opinion,poll,video,company" content-picker-max-size="10">
        <i class="fa fa-plus"></i>
        {t}Insert related{/t}
      </div>
    </div>
  {/if}
  <div class="controls">
    <textarea name="{$field}" id="{$field}" ng-model="item.{$field}" class="form-control" rows="{if !isset($rows)}15{else}{$rows}{/if}"></textarea>
  </div>
</div>
