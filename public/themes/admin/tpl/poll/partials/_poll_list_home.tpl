<div class="content" ng-init="init('poll', 'backend_ws_contents_list_home')">
  <div class="grid simple">
    <div class="grid-title">
      <h4>{t}Sort elements{/t}</h4>
    </div>
    <div class="grid-body">
      <div class="spinner-wrapper" ng-if="loading">
        <div class="loading-spinner"></div>
        <div class="spinner-text">{t}Loading{/t}...</div>
      </div>
      <div class="listing-no-contents ng-cloak" ng-if="!loading && contents.length == 0">
        <div class="center">
          <h4>{t}Unable to find any poll that matches your search.{/t}</h4>
          <h6>{t 1=$total_elements_widget}You must put %1 polls in the HOME{/t}</h6>
        </div>
      </div>
      <div class="ng-cloak" ui-tree="treeOptions" data-max-depth="1" ng-if="!loading && contents.length > 0">
        <ol ui-tree-nodes="" ng-model="contents">
          <li ng-repeat="item in contents" ui-tree-node ng-include="'item'"></li>
        </ol>
      </div>
    </div>
  </div>
</div>
<script type="text/ng-template" id="item">
  <div class="widget-home-item clearfix" ui-tree-handle>
    <span></span>
    <span class="item-title">
      <a data-nodrag ng-href="[% edit(item.id, 'admin_poll_show') %]">
        [% item.title %]
      </a>
      <p class="no-margin">
        [% item.starttime | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' %]
      </p>
    </span>
    <span class="h-seperate"></span>
    <span class="item-category">
      [% extra.categories[item.category_name] %]
    </span>
    <span class="h-seperate hidden-xs"></span>
    <span class="hidden-xs item-views">{t}Votes{/t}: [% item.votes ? item.votes : 0 %]</span>
    {acl isAllowed="POLL_HOME"}
      <button data-nodrag class="btn btn-white pull-right" ng-click="updateItem($index, item.id, 'backend_ws_content_toggle_in_home', 'in_home', 0, 'home_loading', true)" type="button">
        <i data-nodrag class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': item.home_loading == 1, 'fa-home text-info': !item.home_loading && item.in_home == 1, 'fa-home': !item.home_loading && item.in_home == 0 }"></i>
        <i data-nodrag class="fa fa-times fa-sub text-danger" ng-if="!item.home_loading && item.in_home == 0"></i>
      </button>
    {/acl}
  </div>
</script>
