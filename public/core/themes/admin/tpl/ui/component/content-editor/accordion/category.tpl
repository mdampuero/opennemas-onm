<div class="grid-collapse-title pointer" ng-click="expanded.category = !expanded.category">
  <input name="categories" ng-value="categories" type="hidden">
  <i class="fa fa-bookmark m-r-10"></i>
  {t}Category{/t}
  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.category }"></i>
  <span class="pull-right" ng-if="!expanded.category">
    {include file="common/component/icon/status.tpl" iForm="form.category" iNgModel="item.$field" iValidation=true}
  </span>
  <span class="badge badge-default m-r-5 m-t-2 ng-cloak pull-right text-uppercase text-bold" ng-show="!expanded.category && item.{$field}">
    <strong>
        <span ng-if="selectedCategory.title.length > 100">...</span>
      [% selectedCategory.title %]
    </strong>
  </span>
</div>
<div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.category }">
  <div class="form-group no-margin">
    <div class="controls controls-validation">
      <onm-category-selector class="block" default-value-text="{t}Select a category{/t}…" export-model="selectedCategory" locale="config.locale.selected" name="category_id" ng-model="item.{$field}" placeholder="{t}Select a category{/t}…" required></onm-category-selector>
      {include file="common/component/icon/status.tpl" iClass="form-status-absolute" iForm="form.category" iNgModel="item.$field" iValidation=true}
    </div>
  </div>
</div>
