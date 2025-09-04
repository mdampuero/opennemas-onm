<div ui-tree-handle class="menu-item">
  <span ui-tree-handle>
    <span class="angular-ui-tree-icon"></span>
  </span>
  <span class="menu-item-type-icon fa fa-cube" ng-if="item.type == 'internal'" tooltip-placement="right" uib-tooltip="{t}Module{/t}"></span>
  <span class="menu-item-type-icon fa fa-bookmark" ng-if="item.type == 'category'" tooltip-placement="right" uib-tooltip="{t}Categories{/t} - [% item.title %]"></span>
  <span class="menu-item-type-icon fa fa-file" ng-if="item.type == 'static'" tooltip-placement="right" uib-tooltip="{t}Static Pages{/t} - [% item.title %]"></span>
  <span class="menu-item-type-icon fa fa-tag" ng-if="item.type == 'tags'" tooltip-placement="right" uib-tooltip="{t}Tags{/t} - [% item.title %]"></span>
  <span class="menu-item-type-icon fa fa-exchange" ng-if="item.type == 'syncBlogCategory'" tooltip-placement="right" uib-tooltip="{t}Synchronized instances{/t}"></span>
  <span class="menu-item-type-icon fa fa-external-link" ng-if="item.type == 'external'" tooltip-placement="right" uib-tooltip="{t}External link{/t}"></span>
  <div class="p-l-35 p-r-35">
    <div class="row">
      <div class="col-sm-3 col-lg-3">
        <input data-nodrag class="menu-item-title" ng-model="item.title" type="text">
      </div>
      <div ng-if="item.type === 'external'" class="col-sm-9 col-lg-9 form-group" ng-class="{ 'has-error': invalidLinks[item.pk_item] }">
        <input data-nodrag class="menu-item-link form-control" ng-model="item.link_name" type="text" ng-blur="validateExternalLink(item)" ng-focus="clearExternalValidation(item)">
      </div>
    </div>
  </div>
  <div class="menu-item-button">
    <button data-nodrag class="btn btn-white" ng-click="removeItem(item)" type="button">
      <i class="fa fa-trash-o text-danger"></i>
    </button>
  </div>
  </span>
</div>
<ol ui-tree-nodes="" ng-model="childs[item.pk_item]">
  <li ng-repeat="item in childs[item.pk_item] track by item.pk_item" ui-tree-node ng-include="'menu-sub-item'" ></li>
</ol>
