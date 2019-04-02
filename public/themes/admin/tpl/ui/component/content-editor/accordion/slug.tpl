<div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.slug = !expanded.slug">
  <i class="fa fa-external-link m-r-10"></i>{t}Slug{/t}
  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.slug }"></i>
  <a ng-href="{$route}" ng-show="!expanded.slug && item.pk_content > 0 && item.slug.length > 0" class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-uppercase text-bold" target="_blank">
    <i class="fa fa-external-link"></i>
    <strong>{t}Link{/t}</strong>
  </a>
</div>
<div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.slug }">
  <div class="form-group no-margin">
    <div class="controls">
      <input class="form-control" id="slug" name="slug" ng-model="item.slug" type="text" ng-disabled="item.content_status != '0'">
    </div>
    <div class="m-t-10 text-right" ng-if="item.pk_content > 0 && item.slug.length > 0">
      <a ng-href="{$route}" target="_blank">
        <i class="fa fa-external-link"></i>
        {t}Link{/t}
      </a>
    </div>
  </div>
</div>
