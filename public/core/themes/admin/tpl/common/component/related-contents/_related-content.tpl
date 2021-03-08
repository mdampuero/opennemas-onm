<div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.{$iName} = !expanded.{$iName}">
  <i class="fa fa-list m-r-10"></i>
  {$iTitle}
  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.{$iName} }"></i>
</div>
<div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.{$iName} }">
  <div>
    <div class="ng-cloak" data-max-depth="1" ui-tree="treeOptions">
      <div class="related" ui-tree-nodes="" ng-model="{$iName}">
        <div class="related-item" ng-repeat="r in {$iName}" ui-tree-node>
          <span ui-tree-handle>
            <span class="angular-ui-tree-icon"></span>
          </span>
          <div class="related-item-info">
            <span class="related-item-type">
              <span class="fa" ng-class="{ 'fa-file-text-o': related[r.target_id].content_type_name == 'article', 'fa-quote-right': related[r.target_id].content_type_name == 'opinion', 'fa-pie-chart': related[r.target_id].content_type_name == 'poll', 'fa-file': related[r.target_id].content_type_name == 'static_page', 'fa-envelope': related[r.target_id].content_type_name == 'letter', 'fa-paperclip': related[r.target_id].content_type_name == 'attachment', 'fa-film': related[r.target_id].content_type_name == 'video', 'fa-camera': related[r.target_id].content_type_name == 'album'  }" uib-tooltip="[% related[r.target_id].content_type_l10n_name %]"></span>
            </span>
            <span class="related-item-title">
              [% related[r.target_id].title %]
            </span>
            <span class="related-item-status" ng-if="related[r.target_id].content_status == 0">
              ({t}No published{/t})
            </span>
          </div>
          <button class="btn btn-danger" data-nodrag ng-click="removeItem('{$iName}', $index)">
            <i class="fa fa-trash-o"></i>
          </button>
        </div>
      </div>
    </div>
    <div class="text-center">
      <button class="btn btn-default" content-picker content-picker-ignore="[% getRelatedIds({$iName}) %]" content-picker-selection="true" content-picker-max-size="10" content-picker-target="{$iNameTarget}" content-picker-type="album,article,attachment,letter,opinion,poll,special,video" type="button">
        <i class="fa fa-plus m-r-5"></i>
        {t}Add{/t}
      </button>
    </div>
  </div>
</div>
