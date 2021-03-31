{extends file="common/extension/list.grid.tpl"}

{block name="item"}
  <dynamic-image class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="getFeaturedMedia(item, 'featured_frontpage').path" transform="zoomcrop,400,400">
    <div class="hidden-select" ng-click="toggleItem(item); xsOnly($event, toggle, item)"></div>
    <div class="thumbnail-actions ng-cloak">
      {acl isAllowed="ALBUM_UPDATE"}
        <a class="thumbnail-action" href="[% routing.generate('backend_album_show', { id: getItemId(item) }) %]" uib-tooltip="{t}Edit{/t}">
          <i class="fa fa-pencil fa-2x text-default"></i>
        </a>
      {/acl}
      {acl isAllowed="ALBUM_DELETE"}
        <div class="thumbnail-action" ng-click="sendToTrash(item)" uib-tooltip="{t}Delete{/t}" tooltip-class="tooltip-danger">
          <i class="fa fa-trash-o fa-2x text-danger"></i>
        </div>
      {/acl}
    </div>
  </dynamic-image>
{/block}


{block name="master-row"}
<div class="row master-row ng-cloak">
  <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6 m-b-0 infinite-col media-item"> </div>
</div>
{/block}
