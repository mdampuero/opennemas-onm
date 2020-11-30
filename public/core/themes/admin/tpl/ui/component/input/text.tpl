<div class="form-group {$iClass}">
  {if $iTitle}
    <label for="{$iField}" class="form-label">
      {$iTitle}
    </label>
  {/if}
  <div class="controls {if $iCounter || $iValidation}controls{if $iCounter}-counter{/if}{if $iValidation}-validation{/if}{/if}">
    <input class="form-control" id="{$iField}" name="{$iField}" {$iNgActions} ng-model="item.{$iField}" placeholder="[% data.extra.locale.multilanguage && data.extra.locale.default !== config.locale.selected ? data.item.{$iField}[data.extra.locale.default] : '{$iPlaceholder}' %]" {if $iRequired}required{/if} tooltip-enable="config.locale.multilanguage && config.locale.default !== config.locale.selected" type="text" uib-tooltip="{t}Original{/t}: [% data.item.title[data.extra.locale.default] %]">
    {include file="ui/component/icon/status.tpl" iClass="form-status-absolute" iForm=$iField}
    {if $iHelp}
      <div class="help m-l-3 m-t-5" ng-show="isHelpEnabled()">
        <i class="fa fa-info-circle m-r-5 text-info"></i>
        {$iHelp}
      </div>
    {/if}
  </div>
</div>
