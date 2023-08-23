<div class="form-group {$class}">
  <label class="form-label clearfix" for="{$field}">
    <div class="pull-left">{$title}</div>
  </label>
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
    <textarea name="{$field}" id="{$field}" incomplete="incomplete" ng-model="item.{$field}" onm-editor onm-editor-preset="{if !isset($preset)}simple{else}{$preset}{/if}" class="form-control" rows="{if !isset($rows)}15{else}{$rows}{/if}"></textarea>
  </div>
</div>
