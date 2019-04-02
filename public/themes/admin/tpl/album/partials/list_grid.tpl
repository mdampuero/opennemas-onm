
 <div class="grid simple ng-cloak" ng-show="!flags.http.loading && !mode || mode === 'grid'">
    <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6 m-b-15 infinite-col media-item selectable" ng-class="{ 'selected': isSelected(item.id) }" ng-repeat="item in items">
      <div class="dynamic-image-placeholder" ng-click="select(item); xsOnly($event, toggle, item)">
        <dynamic-image class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="item.cover" transform="zoomcrop,400,400">
          <div class="hidden-select" ng-click="toggle(item)"></div>
          <div class="thumbnail-actions ng-cloak">
            {acl isAllowed="ALBUM_DELETE"}
              <div class="thumbnail-action remove-action" ng-click="sendToTrash(item);$event.stopPropagation()">
                <i class="fa fa-trash-o fa-2x"></i>
              </div>
            {/acl}
            {acl isAllowed="ALBUM_UPDATE"}
              <a class="thumbnail-action" href="[% edit(item.id, 'admin_album_show') %]" ng-click="$event.stopPropagation()">
                <i class="fa fa-pencil fa-2x"></i>
              </a>
            {/acl}
          </div>
        </dynamic-image>
      </div>
    </div>
  </div>
  <div class="ng-cloak p-t-15 p-b-15 pointer text-center" ng-click="scroll('backend_ws_contents_list')" ng-if="!loading && mode == 'grid' && total != contents.length">
    <h5>
      <i class="fa fa-circle-o-notch fa-spin fa-lg" ng-if="loadingMore"></i>
      <span ng-if="!loadingMore">{t}Load more{/t}</span>
      <span ng-if="loadingMore">{t}Loading{/t}</span>
    </h5>
  </div>
  <div class="row master-row ng-cloak">
    <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6 m-b-15 infinite-col media-item">
    </div>
  </div>
  <div class="content-sidebar hidden-sm ng-cloak" ng-if="mode === 'grid'">
    <div class="center p-t-15" ng-if="!selected.lastSelected">
      <h4>{t}No item selected{/t}</h4>
      <h6>{t}Click in one item to show information about it{/t}</h6>
    </div>
    <h4 class="ng-cloak" ng-show="selected.lastSelected">{t}Album details{/t}</h4>
    <div ng-if="selected.lastSelected">
      <div class="pointer thumbnail-wrapper" ng-click="open('modal-image', selected.lastSelected.cover_image)" ng-if="selected.lastSelected.cover_image.content_type_name == 'photo' && !isFlash(selected.lastSelected.cover_image)">
        <dynamic-image autoscale="true" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="selected.lastSelected.cover_image" only-image="true" transform="thumbnail,220,220"></dynamic-image>
      </div>
      <div class="pointer thumbnail-wrapper" ng-click="open('modal-image', selected.lastSelected.cover_image)" ng-if="selected.lastSelected.cover_image.content_type_name == 'video' && !selected.lastSelected.cover_image.thumb_image">
        <dynamic-image autoscale="true" ng-model="selected.lastSelected.cover_image" only-image="true" property="thumb"></dynamic-image>
      </div>
      <div class="pointer thumbnail-wrapper" ng-click="open('modal-image', selected.lastSelected.cover_image)" ng-if="isFlash(selected.lastSelected.cover_image)">
        <dynamic-image autoscale="true" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="selected.lastSelected.cover_image" only-image="true"></dynamic-image>
      </div>
      <ul class="media-information">
        <li>
          <strong>[% selected.lastSelected.name %]</strong>
        </li>
        <li>
          <a class="btn btn-default" ng-href="[% routing.generate('admin_album_show', { id: selected.lastSelected.id}) %]">
            <strong>
              <i class="fa fa-edit"></i>
              {t}Edit{/t}
            </strong>
          </a>
        </li>
        <li>[% selected.lastSelected.created | moment %]</li>
        <li>
          <div class="form-group">
            <label for="description">
              <strong>{t}Description{/t}</strong>
              <div class="pull-right">
                <i class="fa" ng-class="{ 'fa-circle-o-notch fa-spin': saving, 'fa-check text-success': saved, 'fa-times text-danger': error }"></i>
              </div>
            </label>
            <textarea id="description" ng-blur="saveDescription(selected.lastSelected.id)" ng-model="selected.lastSelected.description" cols="30" rows="2"></textarea>
          </div>
        </li>
      </ul>
    </div>
  </div>
</div>
