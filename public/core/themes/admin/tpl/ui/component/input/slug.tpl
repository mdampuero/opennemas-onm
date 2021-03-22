<div class="form-group {$iClass}">
  {if $iTitle}
    <label for="{$iName}" class="form-label">
      {$iTitle}
    </label>
  {/if}
  <div class="controls {if $iCounter || $iValidation}controls{if $iCounter}-counter{/if}{if $iValidation}-validation{/if}{/if}">
    <div class="input-group">
      <span class="input-group-btn">
        <button class="btn btn-default" ng-click="flags.block.slug = !flags.block.slug" type="button">
          <i class="fa" ng-class="{ 'fa-lock': flags.block.slug, 'fa-unlock-alt': !flags.block || !flags.block.slug }"></i>
        </button>
      </span>
      <input class="form-control" id="{$iName}" name="{$iName}" ng-disabled="flags.block.slug" ng-model="{$iNgModel}" {if $iRequired}required{/if} type="text">
      {include file="common/component/icon/status.tpl" iClass="form-status-absolute" iFlag="slug" iForm="form.slug" iNgModel="item.slug" iValidation=true}
    </div>
  </div>
</div>
