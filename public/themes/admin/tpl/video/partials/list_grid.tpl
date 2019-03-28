
<div ng-show="mode === 'grid' && !flags.http.loading && items.length > 0">
  <div ng-repeat="item in items" class="col-lg-2 col-md-3 col-sm-3 col-xs-6 m-b-15 infinite-col media-item selectable" ng-class="{ 'selected': isSelected(item.id) }">
    <div class="dynamic-image-placeholder" ng-click="select(item); xsOnly($event, toggle, item)">
      <dynamic-image ng-if="item.thumb_image" class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="item.thumb_image">
        <div class="hidden-select" ng-click="toggle(item)"></div>
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
        <div class="hidden-select" ng-click="toggle(item)"></div>
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
</div>
