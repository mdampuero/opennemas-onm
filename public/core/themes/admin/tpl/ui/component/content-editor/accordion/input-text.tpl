<div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.{$field} = !expanded.{$field}">
  <i class="fa {$icon} m-r-10"></i>{$title}
  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.{$field} }"></i>
  <a ng-if="{$iRoute} && {$iRoute}.length > 0" ng-click="$event.stopPropagation()" ng-href="[% {$iRoute} %]" ng-show="!expanded.{$field} && item.pk_content > 0" class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-uppercase text-bold" target="_blank">
    <i class="fa fa-external-link"></i>
    {t}Link{/t}
  </a>
</div>
<div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.{$field} }">
  <div class="form-group no-margin">
    <div class="controls">
      <input class="form-control" id="{$field}" name="{$field}" ng-model="{$iRoute}" type="text" />
    </div>
  </div>
</div>
