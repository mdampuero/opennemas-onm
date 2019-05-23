{extends file="common/extension/list.grid.tpl"}

{block name="item"}
  <dynamic-image class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="item.cover" transform="zoomcrop,400,400">
    <div class="hidden-select" ng-click="toggleItem(item); xsOnly($event, toggle, item)"></div>
    <div class="thumbnail-actions ng-cloak">
      {acl isAllowed="ALBUM_DELETE"}
      <div class="thumbnail-action remove-action" ng-click="sendToTrash(item);$event.stopPropagation()">
        <i class="fa fa-trash-o fa-2x"></i>
      </div>
      {/acl}
      {acl isAllowed="ALBUM_UPDATE"}
      <a class="thumbnail-action" href="[% routing.generate('backend_album_show', { id: getItemId(item) }) %]">
        <i class="fa fa-pencil fa-2x"></i>
      </a>
      {/acl}
    </div>
  </dynamic-image>
{/block}
