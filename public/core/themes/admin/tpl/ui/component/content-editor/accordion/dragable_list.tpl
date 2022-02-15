<div class="grid-collapse-title ng-cloak pointer" {if $iField }ng-click="expanded['{$iField}'] = !expanded['{$iField}']" {/if}>
  <i class="{$iIcon} m-r-10"></i>{t}{$iName}{/t}
  {if $iField}
  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded['{$iField}'] }"></i>
  {/if}
</div>

<div class="collapsable-container grid-collapse-body ng-cloak {if !$iField} expanded {/if}" {if $iField} ng-class="{ 'expanded': expanded['{$iField}'] }" {/if}>

{if $iSearcModel}
  <div class="list_search_bar_wrapper">
    <i class="fa fa-search m-r-10 search_icon"></i>
    <input class="list_search_bar" ng-model="{$iSearcModel}" type="search">
  </div>
{/if}
  <div class="form-group no-margin menu-dragable-accordion">
    <div class="menu-items ng-cloak" ui-tree data-nodrop-enabled="true" data-max-depth="1">
      <ol ui-tree-nodes="" ng-model={$iData}>
        {include file="ui/component/dragable/dragable_item.tpl" iFilterData="{$iFilterData}" iType="{$iType}" iData="{$iData}" iSearcModel="{$iSearcModel}" iName="{$iName}" iIcon="{$iIcon}"}
      </ol>
    </div>
  </div>
</div>
