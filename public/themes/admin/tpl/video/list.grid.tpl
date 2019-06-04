{extends file="common/extension/list.grid.tpl"}

{block name="item"}
  <dynamic-image class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-if="item.thumb_image" ng-model="item.thumb_image">
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
{/block}
