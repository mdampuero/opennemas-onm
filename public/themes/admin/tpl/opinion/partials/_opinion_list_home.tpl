<div class="content">
  {render_messages}
  <div class="grid simple">
    <div class="grid-title">
      <h4>{t}Director Articles{/t}</h4>
    </div>
    <div class="grid-body" ng-init="director = {json_encode($director)|replace:'"':'\''}">
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
    <div class="grid-body" ng-init="editorial = {json_encode($editorial)|replace:'"':'\''}">
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
    <div class="grid-body" ng-init="opinions = {json_encode($opinions)|replace:'"':'\''}">
      <div class="ng-cloak" ui-tree="treeOptions" data-max-depth="2">
        <ol ui-tree-nodes="" ng-model="opinions">
          <li ng-repeat="item in opinions" ui-tree-node ng-include="'opinion-item'"></li>
        </ol>
      </div>
    </div>
  </div>
  <script type="text/ng-template" id="opinion-item">
    <div class="menu-item clearfix" ui-tree-handle>
      <span class="item-author" ng-if="item.type_opinion == 1">{t}Editorial{/t}</span>
      <span class="item-author" ng-if="item.type_opinion == 2">{t}Director{/t}</span>
      <span class="item-author" ng-if="item.type_opinion == 0">
        <a data-nodrag ng-href="[% edit(item.author.id, 'admin_acl_user_show') %]">
          [% item.author.name %]
        </a>
      </span>
      <span class="h-seperate"></span>
      <span class="item-title">
        <a data-nodrag ng-href="[% edit(item.id, 'admin_opinion_show') %]">
          [% item.title %]
        </a>
      </span>
      <span class="h-seperate hidden-xs hidden-sm"></span>
      <span class="hidden-xs hidden-sm item-created">[% item.starttime | moment : null : '{$smarty.const.CURRENT_LANGUAGE_SHORT}' : '{$timezone}' %]</span>
      <span class="h-seperate hidden-xs"></span>
      <span class="hidden-xs item-views">{t}Views{/t}: [% item.views ? item.views : 0 %]</span>
      </div>
    </div>
  </script>
</div>
