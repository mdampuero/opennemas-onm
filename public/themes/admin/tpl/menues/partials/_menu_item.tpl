<div class="menu-item clearfix" ui-tree-handle ng-init="showForm = false">
    <div class="menu-item-heading clearfix">
      <span class="item-type" ng-if="item.type == 'external'"><span class="fa fa-external-link"></span> {t}External link{/t} </span>
      <span class="item-type semi-bold" ng-if="item.type == 'internal'"> {t}Module{/t} </span>
      <span class="item-type" ng-if="item.type == 'blog-category'"> {t}Category blog{/t} </span>
      <span class="item-type" ng-if="item.type == 'category'"> {t}Frontpage{/t} </span>
      <span class="item-type" ng-if="item.type == 'albumCategory'"> {t}Album category{/t} </span>
      <span class="item-type" ng-if="item.type == 'pollCategory'"> {t}Poll category{/t} </span>
      <span class="item-type" ng-if="item.type == 'videoCategory'"> {t}Video Category{/t} </span>
      <span class="item-type" ng-if="item.type == 'static'"> {t}Static Page{/t} </span>
      <span class="item-type" ng-if="item.type == 'syncCategory'"> {t}Synched category{/t} </span>
      <span class="item-type" ng-if="item.type == 'syncBlogCategory'"> {t}Synched blog category{/t} </span>
      <span>:</span>
      <span class="item-title">[% item.title %] <small>([% item.link %])</small></span>
      <div class="btn-group pull-right">
        <a class="btn btn-white" data-nodrag class="pull-right"  ng-click="showForm = !showForm">
          <i class="fa" ng-class="(showForm === false) ? 'fa-arrow-down' : 'fa-arrow-up'"></i>
        </a>
        <button data-nodrag class="btn btn-white" ng-click="removeItem($index{if $subitem}, parentIndex{/if})" type="button" data-toggle="collapse" data-target="#demo">
          <i class="fa fa-trash-o text-danger"></i>
        </button>
      </div>
    </div>

    <div class="menu-item-form ng-cloak ng-hide" data-nodrag ng-show="showForm">
      <div class="form-group">
        <label for="form-label">Title</label>
        <div class="controls">
          <input ng-model="item.title" type="text" class="form-control">
        </div>
      </div>
      <div class="form-group">
        <label for="form-label">Link</label>
        <div class="controls">
          <input ng-model="item.link" type="text" class="form-control">
        </div>
      </div>
    </div>
  </div>
</div>
<ol ui-tree-nodes="" ng-model="item.submenu">
  <li ng-repeat="item in item.submenu" ui-tree-node ng-include="'menu-sub-item'">
  </li>
</ol>
