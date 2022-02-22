<li ng-repeat="item in {$iData} | filter:filterItems track by item.uniqueID" ui-tree-node  ng-init="item.type = '{$iType}';filter({$iFilterData}, item)" {if $iSearchModel} {/if}class="ng-scope angular-ui-tree-node" >
  <div class="menu-item">
    <span ui-tree-handle>
      <span class="angular-ui-tree-icon"></span>
    </span>
    <span class="menu-item-type-icon {$iIcon}"  tooltip-placement="right" uib-tooltip="{t}{$iName}{/t}"></span>
    <div class="p-l-45">
      <div class="row">
        <div class="col-sm-6 col-lg-6">
          <label class="visible-xs">
            {t}Title{/t}
          </label>
          <input type="text" ng-model="item.title" class="menu-item-title" {if $iSearchModel}disabled {/if}value="[% item.title %]">
        </div>

        <div class="col-sm-6 col-lg-6" ng-if="item.link_name">
          <label class="visible-xs">
            {t}Link to{/t}
          </label>
          {* <input type="text" ng-model="item.link_name"   class="menu-item-link" {if $iSearchModel}disabled {/if} value="[% item.link_name %]"> *}
        </div>
      </div>
    </div>
    </span>
  </div>
</li>

