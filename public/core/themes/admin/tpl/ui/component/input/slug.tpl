<div class="form-group {$iClass}">
  {if $iTitle}
    <label for="{$iField}" class="form-label">
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
      <input class="form-control" id="{$iField}" name="{$iField}" ng-disabled="flags.block.slug" ng-model="item.{$iField}" {if $iRequired}required{/if} type="text">
      {include file="ui/component/icon/status.tpl" iForm=$iField iClass="form-status-absolute"}
    </div>
  </div>
</div>
