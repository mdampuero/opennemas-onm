{extends file="common/extension/list.grid.tpl"}

{block name="item"}
  <dynamic-image class="img-thumbnail" path="[% routing.generate('api_v1_backend_news_agency_resource_get_content', { id: item.id }) %]" raw="true">
    <div class="hidden-select" ng-click="toggleItem(item); xsOnly($event, toggle, item)"></div>
    <div class="thumbnail-actions ng-cloak">
      <div class="thumbnail-action" ng-click="importItem(item);$event.stopPropagation()" ng-if="!isImported(item)">
        <i class="fa fa-cloud-download fa-2x text-info"></i>
      </div>
      <div class="thumbnail-action" ng-if="isImported(item)">
        <i class="fa fa-check fa-2x text-success"></i>
      </div>
    </div>
  </dynamic-image>
{/block}
