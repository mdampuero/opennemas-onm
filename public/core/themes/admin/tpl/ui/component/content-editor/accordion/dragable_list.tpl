<div ng-if="{$iData}.length > 0">
  <div class="grid-collapse-title ng-cloak pointer" {if $iType && !$iSimple }ng-click="expanded['{$iType}'] = !expanded['{$iType}']"{/if}>
    <i class="{$iIcon} m-r-10"></i>{$iName}
    {if !$iSimple}
    <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded['{$iType}'] }"></i>
    {/if}
  </div>
  <div class="collapsable-container grid-collapse-body ng-cloak {if !$iType || $iSimple} expanded {/if}" {if $iType && !$iSimple} ng-class="{ 'expanded': expanded['{$iType}'] }" {/if}>
  {if $iSearchModel}
    <div class="list_search_bar_wrapper">
      <i class="fa fa-search m-r-10 search_icon"></i>
      <input class="list_search_bar" ng-model="search['{$iType}']" type="search">
    </div>
  {/if}
    <div class="form-group no-margin menu-dragable-accordion">
      <div class="menu-items ng-cloak" ui-tree="treeOptions" data-clone-enabled="true" data-nodrop-enabled="true" data-max-depth="1">
        <ol ui-tree-nodes="" ng-model={$iData}>
          {include file="ui/component/dragable/dragable_item.tpl"}
        </ol>
      </div>
    </div>
  </div>
</div>
