<div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.author = !expanded.author">
  <i class="fa fa-edit m-r-10"></i>{t}Author{/t}
  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.author }"></i>
  <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-uppercase text-bold" ng-show="!expanded.author">
    <span ng-show="!item.fk_author"><strong>{t}No author{/t}</strong></span>
    <span ng-show="item.fk_author">
      <strong>[% (data.extra.authors | filter : { id: item.fk_author } : true)[0].name %]</span></strong>
    </span>
  </span>
</div>
<div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.author }">
  <div class="form-group no-margin">
    {include file="ui/component/select/author.tpl" class="form-control" ngModel="item.fk_author" select=true required=$required blog=$blog}
  </div>
</div>
