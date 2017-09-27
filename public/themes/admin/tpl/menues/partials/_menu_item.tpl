<div class="menu-item clearfix" ui-tree-handle ng-init="showForm = false">
    <div class="menu-item-heading clearfix">
      <div class="menu-item-info clearfix pull-left">
        <div class="menu-item-type" ng-if="item.type == 'external'"><span class="fa fa-external-link"></span> {t}External link{/t} </div>
        <div class="menu-item-type" ng-if="item.type == 'internal'"><span class="fa fa-cube"></span> {t}Module{/t} </div>
        <div class="menu-item-type" ng-if="item.type == 'blog-category'"><span class="fa fa-folder-o"></span> {t}Category blog{/t} </div>
        <div class="menu-item-type" ng-if="item.type == 'category'"><span class="fa fa-newspaper-o"></span> {t}Frontpage{/t} </div>
        <div class="menu-item-type" ng-if="item.type == 'albumCategory'"><span class="fa fa-folder-o"></span> {t}Album category{/t} </div>
        <div class="menu-item-type" ng-if="item.type == 'pollCategory'"><span class="fa fa-folder-o"></span> {t}Poll category{/t} </div>
        <div class="menu-item-type" ng-if="item.type == 'videoCategory'"><span class="fa fa-folder-o"></span> {t}Video Category{/t} </div>
        <div class="menu-item-type" ng-if="item.type == 'static'"> {t}Static Page{/t} </div>
        <div class="menu-item-type" ng-if="item.type == 'syncCategory'"><span class="fa fa-folder-o"></span> {t}Synched category{/t} </div>
        <div class="menu-item-type" ng-if="item.type == 'syncBlogCategory'"><span class="fa fa-folder-o"></span> {t}Synched blog category{/t} </div>
        <div class="menu-item-title">[% item.title[lang] %] <small>([% item.link[lang] %])</small></div>
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
  <li ng-repeat="item in item.submenu" ui-tree-node ng-include="'menu-sub-item'">
  </li>
</ol>
