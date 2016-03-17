<div class="menu-item clearfix" ui-tree-handle>
  <span></span>
  <span class="item-type" ng-if="item.type == 'external'">
    {t}External link{/t}
  </span>
  <span class="item-type" ng-if="item.type == 'internal'">
    {t}Module{/t}
  </span>
  <span class="item-type" ng-if="item.type == 'blog-category'">
    {t}Category blog{/t}
  </span>
  <span class="item-type" ng-if="item.type == 'category'">
    {t}Frontpage{/t}
  </span>
  <span class="item-type" ng-if="item.type == 'albumCategory'">
    {t}Album category{/t}
  </span>
  <span class="item-type" ng-if="item.type == 'pollCategory'">
    {t}Poll category{/t}
  </span>
  <span class="item-type" ng-if="item.type == 'videoCategory'">
    {t}Video Category{/t}
  </span>
  <span class="item-type" ng-if="item.type == 'static'">
    {t}Static Page{/t}
  </span>
  <span class="item-type" ng-if="item.type == 'syncCategory'">
    {t}Synched category{/t}
  </span>
  <span class="item-type" ng-if="item.type == 'syncBlogCategory'">
    {t}Synched blog category{/t}
  </span>
  <span class="h-seperate"></span>
  <span class="input-wrapper no-padding">
    <i class="fa fa-pencil"></i>
    <input ng-model="item.title" type="text" data-nodrag>
  </span>
  <button data-nodrag class="btn btn-white pull-right" ng-click="removeItem($index{if $subitem}, parentIndex{/if})" type="button">
    <i class="fa fa-trash-o text-danger"></i>
  </button>
</div>
<ol ui-tree-nodes="" ng-model="item.submenu">
  <li ng-repeat="item in item.submenu" ui-tree-node ng-include="'menu-sub-item'">
  </li>
</ol>
