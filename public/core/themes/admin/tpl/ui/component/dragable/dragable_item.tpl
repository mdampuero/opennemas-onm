<li ng-repeat="item in {$iData} track by item.link_name" ui-tree-node class="ng-scope angular-ui-tree-node" >
  <div ui-tree-handle class="menu-item angular-ui-tree-handle">
    <span class="menu-item-type-icon {$iIcon}"  tooltip-placement="right" uib-tooltip="{$iName}"></span>
    <div class="p-l-45">
      <div class="row" ng-if="item.pk_item">
        <div class="col-sm-6 col-lg-6">
          <label class="visible-xs">
            {t}Title{/t}
          </label>
          <input type="text" ng-model="item.title" class="menu-item-title" disabled value="[% item.title %]">
        </div>
        <div class="col-sm-6 col-lg-6" ng-if="item.link_name">
          <label class="visible-xs">
            {t}Link to{/t}
          </label>
        </div>
      </div>
      <div class="row rigth-menu-item-title" ng-if="!item.pk_item">
        <label class="visible-xs">
          {t}Title{/t}
        </label>
        <input type="text" ng-model="item.title" class="menu-item-title" disabled value="[% item.title %]">
      </div>
    </div>
  </div>
</li>

