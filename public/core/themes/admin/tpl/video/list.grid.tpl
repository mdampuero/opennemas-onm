{extends file="common/extension/list.grid.tpl"}

{block name="item"}
  <dynamic-image class="img-thumbnail" instance="" ng-model="item.thumb">
    <div class="hidden-select" ng-click="toggleItem(item); xsOnly($event, toggle, item)"></div>
    <div class="thumbnail-actions ng-cloak">
      {acl isAllowed="VIDEO_UPDATE"}
        <a class="thumbnail-action" ng-href="[% routing.generate('backend_video_show', { id: getItemId(item) }) %]" uib-tooltip="{t}Edit{/t}">
          <i class="fa fa-pencil fa-2x text-default"></i>
        </a>
      {/acl}
      {acl isAllowed="VIDEO_DELETE"}
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
