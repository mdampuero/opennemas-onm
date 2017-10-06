<div class="menu-item clearfix" ui-tree-handle ng-init="showForm = false">
    <div class="menu-item-heading clearfix">
      <div class="menu-item-info clearfix pull-left" uib-tooltip="{t}Link to {/t} '[% item.link[lang] %]'">
        <span class="menu-item-type-icon fa fa-cube" ng-if="item.type == 'internal'"></span>
        <span class="menu-item-type-icon fa fa-folder-o" ng-if="item.type == 'blog-category'"></span>
        <span class="menu-item-type-icon fa fa-newspaper-o" ng-if="item.type == 'category'"></span>
        <span class="menu-item-type-icon fa fa-folder-o" ng-if="item.type == 'albumCategory'"></span>
        <span class="menu-item-type-icon fa fa-folder-o" ng-if="item.type == 'pollCategory'"></span>
        <span class="menu-item-type-icon fa fa-folder-o" ng-if="item.type == 'videoCategory'"></span>
        <span class="menu-item-type-icon fa fa-folder-o" ng-if="item.type == 'static'"></span>
        <span class="menu-item-type-icon fa fa-folder-o" ng-if="item.type == 'syncCategory'"></span>
        <span class="menu-item-type-icon fa fa-folder-o" ng-if="item.type == 'syncBlogCategory'"></span>
        <span class="menu-item-type-icon fa fa-external-link" ng-if="item.type == 'external'"></span>

        <div class="menu-item-title">[% item.title[lang] %]</div>

        <div class="menu-item-type">
          <small>
            <span ng-if="item.type == 'external'">({t}External link{/t})</span>
            <span ng-if="item.type == 'internal'">({t}Module{/t})</span>
            <span ng-if="item.type == 'blog-category'">({t}Category blog{/t})</span>
            <span ng-if="item.type == 'category'">({t}Frontpage{/t})</span>
            <span ng-if="item.type == 'albumCategory'">({t}Album category{/t})</span>
            <span ng-if="item.type == 'pollCategory'">({t}Poll category{/t}</span>
            <span ng-if="item.type == 'videoCategory'">({t}Video Category{/t})</span>
            <span ng-if="item.type == 'static'">({t}Static Page{/t})</span>
            <span ng-if="item.type == 'syncCategory'">({t}Synched category{/t})</span>
            <span ng-if="item.type == 'syncBlogCategory'">({t}Synched blog category{/t})</span>
          </small>
        </div>
      </div>

      <div class="btn-group pull-right">
        <a class="btn btn-white" data-nodrag class="pull-right"  ng-click="showForm = !showForm">
          <i class="fa" ng-class="(showForm === false) ? 'fa-caret-down' : 'fa-caret-up'"></i>
        </a>
        <button data-nodrag class="btn btn-white" ng-click="removeItem($index{if $subitem}, parentIndex{/if})" type="button">
          <i class="fa fa-trash-o text-danger"></i>
        </button>
      </div>
    </div>

    <div class="menu-item-form ng-cloak" data-nodrag ng-show="showForm">
      <div class="form-group">
        <label for="form-label">{t}Title{/t}</label>
        <div class="controls">
          <input ng-model="item.title[lang]" type="text" class="form-control">
        </div>
      </div>
      <div class="form-group">
        <label for="form-label">{t}Link{/t}</label>
        <div class="controls">
          <input ng-model="item.link[lang]" type="text" class="form-control">
        </div>
      </div>
    </div>
  </div>
</div>
<ol ui-tree-nodes="" ng-model="item.submenu">
  <li ng-repeat="item in item.submenu" ui-tree-node ng-include="'menu-sub-item'"></li>
</ol>
