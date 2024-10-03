<div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.canonical = !expanded.canonical">
  <i class="fa fa-list m-r-10"></i>{t}Canonical URL{/t}
  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.canonical }"></i>
</div>
<div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded' : expanded.canonical }">
  <div class="form-group no-margin">
      {include file="ui/component/input/text.tpl" iField="canonicalurl" iRequired=false iTitle="{t}Canonical url{/t}" iValidation=false}
  </div>
</div>
