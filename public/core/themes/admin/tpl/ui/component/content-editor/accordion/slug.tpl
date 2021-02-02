<div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.slug = !expanded.slug">
  <i class="fa fa-link m-r-10"></i>
  {t}Slug{/t}
  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.slug }"></i>
  <span class="pull-right" ng-if="!expanded.slug">
    {include file="common/component/icon/status.tpl" iFlag="slug" iForm="form.slug" iNgModel="item.slug" iValidation=true}
  </span>
  {if $iRoute}
    <a class="badge badge-default m-r-10 pull-right text-bold text-uppercase" ng-click="$event.stopPropagation()" ng-href="{$iRoute}" ng-show="!expanded.slug && getItemId(item) && item.slug" target="_blank">
      <i class="fa fa-external-link"></i>
      {t}Link{/t}
    </a>
  {/if}
</div>
<div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.slug }">
  {include file="ui/component/input/slug.tpl" iClass="no-margin" iFlag="slug" iForm="form.slug" iName="slug" iNgModel="item.slug" iRequired=true iValidation=true}
  <div class="m-t-10 text-right" ng-if="item.pk_content && item.slug">
    <a ng-href="{$iRoute}" target="_blank">
      <i class="fa fa-external-link m-r-5"></i>{t}Link{/t}
    </a>
  </div>
</div>
