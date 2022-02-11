<div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.{$iName} = !expanded.{$iName}">
  <i class="fa fa-navicon m-r-10"></i>
  {$iTitle}
  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.{$iName} }"></i>
</div>
<div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.{$iName} }">
  <div>
    <div class="ng-cloak" data-max-depth="1" ui-tree="treeOptions">
      <div class="related" ui-tree-nodes="" ng-model="{$iName}">
        <div class="related-item" ng-repeat="related in {$iName}" ui-tree-node>
          <span ui-tree-handle>
            <span class="angular-ui-tree-icon"></span>
          </span>
          <div class="related-item-info">
            <span class="related-item-type">
              {include file="common/component/icon/content_type_icon.tpl" iField="data.extra.related_contents[related.target_id]" iFlagIcon=true}
            </span>
            <span class="related-item-title">
              [% localizeText(data.extra.related_contents[related.target_id].title ) %]
            </span>
            <span class="related-item-status" ng-if="related.content_status == 0">
              ({t}No published{/t})
            </span>
          </div>
          <button class="btn btn-danger" data-nodrag ng-click="removeItem('data.{$iName}', $index); removeItem('{$iName}', $index)">
            <i class="fa fa-trash-o"></i>
          </button>
        </div>
      </div>
    </div>
    <div class="text-center">
      <button class="btn btn-default" content-picker content-picker-ignore="[% related.getIds('{$iName}') %]" content-picker-selection="true" content-picker-max-size="10" content-picker-target="target.{$iName}" content-picker-type="event,album,article,attachment,letter,obituary,opinion,poll,special,video" type="button">
        <i class="fa fa-plus m-r-5"></i>
        {t}Add{/t}
      </button>
    </div>
  </div>
</div>
