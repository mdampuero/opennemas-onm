{extends file="common/extension/list.grid.tpl"}

{block name="item"}
  <dynamic-image class="img-thumbnail" ng-init="resourceRoute = routing.generate(routes.getContent, { id: item.id })" ng-model="resourceRoute" raw="true">
    <div class="hidden-select" ng-click="toggleItem(item); xsOnly($event, toggle, item)"></div>
    <div class="thumbnail-actions ng-cloak" ng-class="{ 'thumbnail-actions-fixed': isImported(item) }">
      <div class="thumbnail-action ng-cloak" ng-click="preview(item)" uib-tooltip="{t}Preview{/t}" tooltip-placement="top">
        <i class="fa fa-eye fa-2x text-default"></i>
      </div>
      <div class="thumbnail-action ng-cloak" ng-click="importItem(item)" ng-if="!isImported(item)" uib-tooltip="{t}Import{/t}" tooltip-class="tooltip-info">
        <i class="fa fa-cloud-download fa-2x text-info"></i>
      </div>
      <div class="thumbnail-action ng-cloak" ng-if="isImported(item)" uib-tooltip="{t}Imported{/t}" tooltip-class="tooltip-success">
        <i class="fa fa-check fa-2x text-success"></i>
      </div>
    </div>
  </dynamic-image>
{/block}

{block name="master-row"}
  <div class="row master-row ng-cloak">
    <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6 m-b-0 infinite-col media-item"> </div>
  </div>
{/block}
