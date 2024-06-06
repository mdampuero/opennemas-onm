<div class="form-group {$iClass}">
  {if $iTitle}
    <label for="{$iField}" class="form-label">
      {$iTitle}
    </label>
  {/if}
  <div class="controls {if $iCounter || $iValidation}controls{if $iCounter}-counter{/if}{if $iValidation}-validation{/if}{/if}">
    {if $iSource}
    <div class="input-group">
      <span class="input-group-btn">
        <button class="btn btn-default" ng-class="{ 'btn-danger': undoing, 'btn-default': !undoing }" ng-mouseover="undo()" ng-mouseleave="redo()" ng-click="flags.block.{$iField} = !flags.block.{$iField}">
          <i class="fa" ng-class="{ 'fa-pencil': flags.block.{$iField}, 'fa-undo': !flags.block.{$iField} }"></i>
        </button>
      </span>
    {/if}
    <input class="form-control" id="{$iField}" name="{$iField}" {if $iSource}ng-disabled="flags.block.{$iField}"{/if} ng-class="{ 'input-faded': flags.block.{$iField} }" {$iNgActions} ng-model="item.{$iField}" placeholder="[% data.extra.locale.multilanguage && data.extra.locale.default !== config.locale.selected ? data.item.{$iField}[data.extra.locale.default] : '{$iPlaceholder}' %]" {if $iRequired}required{/if} tooltip-enable="config.locale.multilanguage && config.locale.default !== config.locale.selected" type="text" uib-tooltip="{t}Original{/t}: [% data.item.title[data.extra.locale.default] %]" iCounter={$iCounter} iValidation={$iValidation}>
    {include file="common/component/icon/status.tpl" iClass="form-status-absolute" iForm="form.$iField" iNgModel="item.$iField"}
    {if $iSource}
    </div>
    {/if}
    {if $iHelp}
      <div class="help m-l-3 m-t-5" ng-show="isHelpEnabled()">
        <i class="fa fa-info-circle m-r-5 text-info"></i>
        {$iHelp}
      </div>
    {/if}
    {if $IA}
        {include file="ui/component/button/ia_modal.tpl"}
    {/if}
  </div>
</div>
