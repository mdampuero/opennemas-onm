{extends file="common/extension/list.grid.tpl"}

{block name="item"}
  <dynamic-image class="img-thumbnail" instance="{$smarty.const.INSTANCE_MEDIA}" ng-model="item" transform="zoomcrop,400,400">
    <div class="hidden-select" ng-click="toggleItem(item); xsOnly($event, toggle, item)"></div>
    <div class="thumbnail-actions thumbnail-actions-3x ng-cloak">
      {acl isAllowed="PHOTO_UPDATE"}
        <a class="thumbnail-action" href="[% routing.generate('backend_photo_show', { id: getItemId(item) }) %]" uib-tooltip="{t}Edit{/t}">
          <i class="fa fa-pencil fa-2x text-default"></i>
        </a>
      {/acl}
      {acl isAllowed="PHOTO_DELETE"}
        <div class="thumbnail-action" ng-click="delete(item.pk_photo)" uib-tooltip="{t}Delete{/t}" tooltip-class="tooltip-danger">
          <i class="fa fa-trash-o fa-2x text-danger"></i>
        </div>
      {/acl}
      {acl isAllowed="PHOTO_ENHANCE"}
        <a class="thumbnail-action" ng-click="launchPhotoEditor(item)" uib-tooltip="{t}Enhance{/t}" tooltip-class="tooltip-info">
          <i class="fa fa-sliders fa-2x text-info"></i>
        </a>
      {/acl}
    </div>
  </dynamic-image>
{/block}
