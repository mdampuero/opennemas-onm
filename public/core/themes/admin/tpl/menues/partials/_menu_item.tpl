<div class="menu-item">
  <span ui-tree-handle>
    <span class="angular-ui-tree-icon"></span>
  </span>
  <span class="menu-item-type-icon fa fa-cube" ng-if="item.type == 'internal'" tooltip-placement="right" uib-tooltip="{t}Module{/t}"></span>
  <span class="menu-item-type-icon fa fa-bookmark" ng-if="item.type == 'blog-category'" tooltip-placement="right" uib-tooltip="{t}Category blog{/t}"></span>
  <span class="menu-item-type-icon fa fa-newspaper-o" ng-if="item.type == 'category'" tooltip-placement="right" uib-tooltip="{t}Frontpage{/t}"></span>
  <span class="menu-item-type-icon fa fa-file" ng-if="item.type == 'static'" tooltip-placement="right" uib-tooltip="{t}Static Page{/t}"></span>
  <span class="menu-item-type-icon fa fa-exchange" ng-if="item.type == 'syncBlogCategory'" tooltip-placement="right" uib-tooltip="{t}Synched category{/t}"></span>
  <span class="menu-item-type-icon fa fa-external-link" ng-if="item.type == 'external'" tooltip-placement="right" uib-tooltip="{t}External link{/t}"></span>
  <div class="p-l-35 p-r-35">
    <div class="row">
      <div class="col-sm-6 col-lg-6">
        <label class="visible-xs">
          {t}Title{/t}
        </label>
        <input class="menu-item-title" data-nodrag ng-model="item.title[lang]" tooltip-enable="languageData.default !== lang" type="text" uib-tooltip="{t}Original:{/t} [% item.title[languageData.default] %]">
      </div>
      <div class="col-sm-6 col-lg-6" ng-if="item.type == 'external'">
        <label class="visible-xs">
          {t}Link to{/t}
        </label>
        <input class="menu-item-link" data-nodrag ng-model="item.link[lang]" tooltip-enable="languageData.default !== lang" type="text" uib-tooltip="{t}Original:{/t} [% item.link[languageData.default] %]">
      </div>
    </div>
  </div>
  <div class="menu-item-button">
    <button data-nodrag class="btn btn-white" ng-click="removeItem($index{if isset($subitem) && $subitem}, parentIndex{/if})" type="button">
      <i class="fa fa-trash-o text-danger"></i>
    </button>
  </div>
  </span>
</div>
<ol ui-tree-nodes="" ng-model="item.submenu">
  <li ng-repeat="item in item.submenu" ui-tree-node ng-include="'menu-sub-item'"></li>
</ol>
