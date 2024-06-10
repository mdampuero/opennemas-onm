<li ng-repeat="item in {$iData} track by item.link_name+item.referenceId" ui-tree-node class="ng-scope angular-ui-tree-node"  {if $iFilter}ng-show="visible(item, true)"{/if}>
  <div ui-tree-handle class="menu-item angular-ui-tree-handle">
    <span ui-tree-handle>
    <span class="angular-ui-tree-icon"></span>
    </span>
    <span class="menu-item-type-icon {$iIcon}"  tooltip-placement="right" {if $iShowSlug}uib-tooltip="{$iName} - [% item.link_name %]"{else}uib-tooltip="{$iName}"{/if}></span>
    <div class="p-l-30">
      <div class="row" ng-if="item.pk_item">
        <div class="col-sm-6 col-lg-6">
          <input type="text" ng-model="item.title" class="menu-item-title" disabled value="[% item.title %]">
        </div>
        <div class="col-sm-6 col-lg-6" ng-if="item.link_name">
        </div>
      </div>
      <div class="row rigth-menu-item-title m-r-5" ng-if="!item.pk_item">
        <label class="hidden">
          {t}Title{/t}
        </label>
        <input type="text" ng-model="item.title" class="menu-item-title" disabled value="[% item.title %]">
      </div>
    </div>
  </div>
</li>
