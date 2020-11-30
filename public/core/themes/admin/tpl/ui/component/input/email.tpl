<div class="form-group">
  <label for="{$iField}" class="form-label">
    {$iTitle}
  </label>
  <div class="controls {if $iCounter || $iValidation}controls{if $iCounter}-counter{/if}{if $iValidation}-validation{/if}{/if}">
    <input class="form-control" id="{$iField}" name="{$iField}" {$iNgActions} ng-model="item.{$iField}" placeholder="{$iPlaceholder}" {if $iRequired}required{/if} type="email">
    {include file="ui/component/icon/status.tpl" iForm=$iField}
  </div>
</div>
