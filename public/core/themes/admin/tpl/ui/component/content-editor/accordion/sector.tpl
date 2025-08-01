<div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.sector = !expanded.sector">
  <i class="fa fa-pie-chart m-r-10"></i>{t}Sector{/t}
  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.sector }"></i>
  <span class="pull-right" ng-if="!expanded.sector">
    {include file="common/component/icon/status.tpl" iFlag="sector" iForm="form.sector" iNgModel="item.sector" iValidation=true}
  </span>
  <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-uppercase text-bold" ng-show="!expanded.sector">
    <span ng-show="!item.sector"><strong>{t}No sector{/t}</strong></span>
    <span ng-show="item.sector">
      <strong>[% getSectorTitle(item.sector) %]</strong>
    </span>
  </span>
</div>
<div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.sector }">
  <div class="form-group no-margin">
    {include file="ui/component/select/sector.tpl" class="form-control" ngModel="item.sector" select=true required=$required}
  </div>
</div>
