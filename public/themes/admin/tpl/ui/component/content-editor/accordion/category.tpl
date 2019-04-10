<div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.category = !expanded.category">
  <input name="categories" ng-value="categories" type="hidden">
  <i class="fa fa-bookmark m-r-10"></i>{t}Category{/t}
  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.category }"></i>
  <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-uppercase text-bold" ng-show="!expanded.category || !{$field}" ng-class="{ 'badge-danger' : !{$field} }">
    <span ng-show="!{$field}">
      <strong>{t}Not selected{/t}</strong>
    </span>
    <span ng-show="{$field} && !flags.categories.none">
      <strong><span ng-repeat="category in selectedCategories">[% category.title %]</span></strong>
    </span>
  </span>
</div>
<div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.category }">
  <div class="form-group no-margin">
    <div class="controls">
      <onm-category-selector class="block" default-value-text="{t}Select a category{/t}…" locale="config.locale.selected" ng-model="{$field}" placeholder="{t}Select a category{/t}…" required selected="selectedCategories"/>
    </div>
  </div>
</div>
