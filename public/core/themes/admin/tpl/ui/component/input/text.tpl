<div class="form-group {$iClass}">
  {if $iTitle}
    <label for="{$iField}" class="form-label">
      {$iTitle}
    </label>
  {/if}
  <div class="controls {if $iCounter || $iValidation}controls{if $iCounter}-counter{/if}{if $iValidation}-validation{/if}{/if}">
    {if $iProp}
    <div class="input-group">
      <span class="input-group-btn">
        <button class="btn btn-default" ng-click="flags.block.{$iProp} = !flags.block.{$iProp}" type="button">
          <i class="fa" ng-class="{ 'fa-lock': flags.block.{$iProp}, 'fa-unlock-alt': !flags.block || !flags.block.{$iProp} }"></i>
        </button>
      </span>
    {/if}
    <input class="form-control" id="{$iField}" name="{$iField}" {if $iProp}ng-disabled="flags.block.{$iProp}"{/if} {$iNgActions} ng-model="item.{$iField}" placeholder="[% data.extra.locale.multilanguage && data.extra.locale.default !== config.locale.selected ? data.item.{$iField}[data.extra.locale.default] : '{$iPlaceholder}' %]" {if $iRequired}required{/if} tooltip-enable="config.locale.multilanguage && config.locale.default !== config.locale.selected" type="text" uib-tooltip="{t}Original{/t}: [% data.item.title[data.extra.locale.default] %]" iCounter={$iCounter} iValidation={$iValidation}>
    {include file="common/component/icon/status.tpl" iClass="form-status-absolute" iForm="form.$iField" iNgModel="item.$iField"}
    {if $iProp}
    </div>
    {/if}
    {if $iHelp}
      <div class="help m-l-3 m-t-5" ng-show="isHelpEnabled()">
        <i class="fa fa-info-circle m-r-5 text-info"></i>
        {$iHelp}
      </div>
    {/if}
  </div>
</div>
