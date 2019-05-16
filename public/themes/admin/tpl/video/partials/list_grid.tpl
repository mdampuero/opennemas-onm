<div class="grid simple ng-cloak" ng-show="config.mode === 'grid' && !flags.http.loadingMore && items.length > 0">
  <div ng-repeat="item in items" class="col-lg-2 col-md-3 col-sm-3 col-xs-6 m-b-15 infinite-col media-item selectable" ng-class="{ 'selected': isSelected(item.id) }">
    <div class="dynamic-image-placeholder">
      <dynamic-image ng-if="item.thumb_image" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="item.thumb_image">
        <div class="hidden-select" ng-click="select(item); xsOnly($event, toggle, item)"></div>
        <div class="thumbnail-actions ng-cloak">
          {acl isAllowed="VIDEO_DELETE"}
            <div class="thumbnail-action remove-action" ng-click="sendToTrash(item);$event.stopPropagation()">
              <i class="fa fa-trash-o fa-2x"></i>
            </div>
          {/acl}
          {acl isAllowed="VIDEO_UPDATE"}
            <a class="thumbnail-action" ng-href="[% routing.generate('backend_video_show', { id: getId(item) }) %]">
              <i class="fa fa-pencil fa-2x"></i>
            </a>
          {/acl}
        </div>
      </dynamic-image>
      <dynamic-image ng-if="!item.thumb_image" class="img-thumbnail" ng-model="item.thumb">
        <div class="hidden-select" ng-click="select(item); xsOnly($event, toggle, item)"></div>
        <div class="thumbnail-actions ng-cloak">
          {acl isAllowed="VIDEO_DELETE"}
            <div class="thumbnail-action remove-action" ng-click="sendToTrash(item);">
              <i class="fa fa-trash-o fa-2x"></i>
            </div>
          {/acl}
          {acl isAllowed="VIDEO_UPDATE"}
            <a class="thumbnail-action" ng-href="[% routing.generate('backend_video_show', { id: getId(item) }) %]">
              <i class="fa fa-pencil fa-2x"></i>
            </a>
          {/acl}
        </div>
      </dynamic-image>
    </div>
  </div>
  <div class="ng-cloak p-t-15 p-b-15 pointer text-center" ng-click="scroll()" ng-if="!flags.http.loading && mode == 'grid' && data.total != data.items.length">
    <h5>
      <i class="fa fa-circle-o-notch fa-spin fa-lg" ng-if="flags.loadingMore"></i>
      <span ng-if="!flags.loadingMore">{t}Load more{/t}</span>
      <span ng-if="flags.loadingMore">{t}Loading{/t}</span>
    </h5>
  </div>
</div>
