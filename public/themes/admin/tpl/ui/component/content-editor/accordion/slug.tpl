<div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.slug = !expanded.slug">
  <i class="fa fa-external-link m-r-10"></i>
  {t}Slug{/t}
  <span ng-if="!expanded.slug">
    {include file="ui/component/icon/status.tpl" iFlag="slug" iField="slug" iRequired=true iValidation=true}
  </span>
  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.slug }"></i>
  <a class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-uppercase text-bold" ng-href="{$route}" ng-show="!expanded.slug && item.pk_content > 0 && item.slug.length > 0" target="_blank">
    <i class="fa fa-external-link"></i>
    <strong>{t}Link{/t}</strong>
  </a>
</div>
<div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.slug }">
  {include file="ui/component/input/slug.tpl" iClass="no-margin" iFlag="slug" iField="slug" iRequired=true iValidation=true}
  <div class="m-t-10 text-right" ng-if="item.pk_content > 0 && item.slug.length > 0">
    <a ng-href="{$route}" target="_blank">
      <i class="fa fa-external-link"></i>
      {t}Link{/t}
    </a>
  </div>
</div>
