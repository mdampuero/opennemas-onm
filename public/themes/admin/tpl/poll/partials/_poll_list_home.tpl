<div class="content" ng-init="init('poll', { content_status: -1, category_name: -1, in_home: {if $category == 'widget'}1{else}-1{/if}, title_like: '', in_litter: 0{if $category == 'widget'}, in_home: 1{/if} }, {if $category == 'widget'}'position'{else}'created'{/if}, {if $category == 'widget'}'asc'{else}'desc'{/if}, 'backend_ws_contents_list_home', '{{$smarty.const.CURRENT_LANGUAGE}}', null)">
  {render_messages}
  <div class="grid simple">
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
  <div class="menu-item clearfix" ui-tree-handle>
    <span class="item-title">
      <a data-nodrag ng-href="[% edit(item.id, 'admin_opinion_show') %]">
        [% item.title %]
      </a>
    </span>
    <span class="h-seperate"></span>
    <span class="item-category">
      [% item.category_name %]
    </span>
    <span class="h-seperate hidden-xs hidden-sm"></span>
    <span class="hidden-xs hidden-sm item-created">[% item.starttime | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]</span>
    <span class="h-seperate hidden-xs"></span>
    <span class="hidden-xs item-views">{t}Votes{/t}: [% item.votes ? item.votes : 0 %]</span>
    </div>
  </div>
</script>
