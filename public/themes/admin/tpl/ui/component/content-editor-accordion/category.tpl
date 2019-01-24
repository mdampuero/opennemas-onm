<div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.category = !expanded.category">
  <input name="categories" ng-value="categories" type="hidden">
  <i class="fa fa-bookmark m-r-10"></i>{t}Category{/t}
  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.category }"></i>
  <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-uppercase text-bold" ng-show="!expanded.category || item.categories.length === 0 || !item.categories[0]" ng-class="{ 'badge-danger' : item.categories.length === 0 || !item.categories[0] }">
    <span ng-show="item.categories.length === 0 || !item.categories[0]">
      <strong>{t}Not selected{/t}</strong>
    </span>
    <span ng-show="item.categories.length !== 0 && item.categories[0] && !flags.categories.none">
      <strong><span ng-repeat="category in data.extra.categories|filter:{ pk_content_category: item.categories[0]}">[% category.title %]</span></strong>
    </span>
  </span>
</div>
<div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.category }">
  <div class="form-group">
    <div class="controls">
      <onm-category-selector class="block" ng-model="item.categories[0]" categories="data.extra.categories" placeholder="{t}Select a category{/t}" default-value-text="{t}Select a category...{/t}" required />
    </div>
  </div>
</div>
