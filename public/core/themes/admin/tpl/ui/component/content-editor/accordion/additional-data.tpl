<div ng-repeat="group in data.extra.extra_fields">
    <div class="grid-collapse-title ng-cloak pointer" ng-click="expanded[group.title] = !expanded[group.title]">
        <i class="fa fa-magic m-r-10"></i>[% group.title %]
        <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded[group.title] }"></i>
    </div>
    <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded[group.title] }">
        <autoform ng-model="item" fields-by-module="[ group ]">
    </div>
</div>
