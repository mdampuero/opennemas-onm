<div class="grid simple ng-cloak no-animate row" ng-show="!flags.http.loading && app.mode === 'grid' && items.length > 0">
  <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6 m-b-15 infinite-col media-item selectable" ng-class="{ 'selected': isSelected(getId(item)) }" ng-repeat="item in items">
    <div class="dynamic-image-placeholder">
      <dynamic-image class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="item.cover" transform="zoomcrop,400,400">
        <div class="hidden-select" ng-click="select(item); xsOnly($event, toggle, item)"></div>
        <div class="thumbnail-actions ng-cloak">
          {acl isAllowed="ALBUM_DELETE"}
            <div class="thumbnail-action remove-action" ng-click="sendToTrash(item);$event.stopPropagation()">
              <i class="fa fa-trash-o fa-2x"></i>
            </div>
          {/acl}
          {acl isAllowed="ALBUM_UPDATE"}
            <a class="thumbnail-action" href="[% routing.generate('backend_album_show', { id: getId(item) }) %]">
              <i class="fa fa-pencil fa-2x"></i>
            </a>
          {/acl}
        </div>
      </dynamic-image>
    </div>
  </div>
</div>
<div class="ng-cloak p-t-15 p-b-15 pointer text-center" ng-click="scroll()" ng-if="!flags.http.loading && app.mode == 'grid' && data.total != data.items.length">
  <h5>
    <i class="fa fa-circle-o-notch fa-spin fa-lg" ng-if="flags.loadingMore"></i>
    <span ng-if="!flags.loadingMore">{t}Load more{/t}</span>
    <span ng-if="flags.loadingMore">{t}Loading{/t}</span>
  </h5>
</div>
