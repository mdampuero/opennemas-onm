<div class="form-group {$iClass}">
  {if $iTitle}
    <label for="{$iName}" class="form-label">
      {$iTitle}
    </label>
  {/if}
  <div class="controls {if $iCounter || $iValidation}controls{if $iCounter}-counter{/if}{if $iValidation}-validation{/if}{/if}">
    <div class="input-group">
      <span class="input-group-btn">
        <button class="btn btn-default" ng-click="flags.block.{$iProp} = !flags.block.{$iProp}" type="button">
          <i class="fa" ng-class="{ 'fa-lock': flags.block.{$iProp}, 'fa-unlock-alt': !flags.block || !flags.block.{$iProp} }"></i>
        </button>
      </span>
      <input class="form-control" id="{$iName}" name="{$iName}" ng-disabled="flags.block.{$iProp}" ng-model="{$iNgModel}" {if $iRequired}required{/if} type="text">
      {include file="common/component/icon/status.tpl" iClass="form-status-absolute" iFlag="{$iFlag}" iForm="form.{$iProp}" iNgModel="item.{$iProp}" iCounter=true iValidation=true}
    </div>
  </div>
</div>
