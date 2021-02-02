<div class="form-group {$iClass}">
  {if $iTitle}
    <label for="{$iField}" class="form-label">
      {$iTitle}
    </label>
  {/if}
  <div class="controls {if $iCounter || $iValidation}controls{if $iCounter}-counter{/if}{if $iValidation}-validation{/if}{/if}">
    <div class="input-group">
      <span class="input-group-addon">
        <i class="fa fa-calendar"></i>
      </span>
      <input class="form-control" datetime-picker {if $iFormat}datetime-picker-format="{$iFormat}"{/if} datetime-picker-timezone="{$app.locale->getTimeZone()->getName()}" datetime-picker-use-current="true" name="{$iField}" ng-model="item.{$iField}" {if $iRequired}required{/if} type="datetime">
    </div>
    {include file="common/component/icon/status.tpl" iForm="form.$iField" iNgModel="item.$iField" iClass="form-status-absolute"}
  </div>
</div>
