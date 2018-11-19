<div class="content">
  <div class="grid simple">
    <div class="grid-title">
      <h4>{t}Director Articles{/t}</h4>
    </div>
    <div class="grid-body" ng-init="director = {json_encode($director)|clear_json}">
      <div class="ng-cloak" ui-tree="treeOptions" data-max-depth="1">
        <ol ui-tree-nodes="" ng-model="director">
          <li ng-repeat="item in director" ui-tree-node ng-include="'opinion-item'"></li>
        </ol>
      </div>
    </div>
  </div>
  <div class="grid simple">
    <div class="grid-title">
      <h4>{t}Editorial Articles{/t}</h4>
    </div>
    <div class="grid-body" ng-init="editorial = {json_encode($editorial)|clear_json}">
      <div class="ng-cloak" ui-tree="treeOptions" data-max-depth="2">
        <ol ui-tree-nodes="" ng-model="editorial">
          <li ng-repeat="item in editorial" ui-tree-node ng-include="'opinion-item'"></li>
        </ol>
      </div>
    </div>
  </div>
  <div class="grid simple">
    <div class="grid-title">
      <h4>{t}Other Articles{/t}</h4>
    </div>
    <div class="grid-body" ng-init="opinions = {json_encode($opinions)|clear_json}; authors = {json_encode($authors)|clear_json}">
      <div class="ng-cloak" ui-tree="treeOptions" data-max-depth="2">
        <ol ui-tree-nodes="" ng-model="opinions">
          <li ng-repeat="item in opinions" ui-tree-node ng-include="'opinion-item'"></li>
        </ol>
      </div>
    </div>
  </div>
  <script type="text/ng-template" id="opinion-item">
    <div class="opinion-frontpage-item clearfix" ui-tree-handle>
      <span></span>
      <span class="item-title">
        <a data-nodrag ng-href="[% edit(item.id, 'admin_opinion_show') %]">
          [% item.title %]
        </a>
        <p class="no-margin">
          [% item.starttime | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' %]
        </p>
      </span>
      <span class="h-seperate"></span>
      <span class="item-author" ng-if="item.type_opinion == 1">{t}Editorial{/t}</span>
      <span class="item-author" ng-if="item.type_opinion == 2">{t}Director{/t}</span>
      <span class="item-author" ng-if="item.type_opinion == 0">
        <a data-nodrag ng-href="[% edit(item.author.id, 'backend_user_show') %]">
          [% authors[item.fk_author].name %]
        </a>
      </span>
      <span class="h-seperate hidden-xs"></span>
      <span class="hidden-xs item-views">{t}Views{/t}: [% item.views ? item.views : 0 %]</span>
      {acl isAllowed="OPINION_HOME"}
        <button data-nodrag class="btn btn-white pull-right" ng-click="updateItem($index, item.id, 'backend_ws_content_toggle_in_home', 'in_home', 0, 'home_loading');reloadPage();" type="button">
          <i data-nodrag class="fa fa-home text-info" ng-class="{ 'fa-circle-o-notch': item.home_loading }"></i>
          <i data-nodrag class="fa fa-times fa-sub text-danger" ng-if="!item.home_loading"></i>
        </button>
      {/acl}
    </div>
  </script>
</div>
