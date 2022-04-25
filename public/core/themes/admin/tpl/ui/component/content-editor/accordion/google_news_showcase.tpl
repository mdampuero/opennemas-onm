<div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.google_news_showcase = !expanded.google_news_showcase">
  <i class="fa fa-google m-r-10"></i>{t}Google News Showcase{/t}
  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.google_news_showcase }"></i>
</div>
<div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.google_news_showcase }">
  <div class="form-group">
    <div class="m-t-5">
      {include file="ui/component/content-editor/accordion/checkbox.tpl" field="showcase" title="{t}Include as standalone Google News Showcase new{/t}"}
    </div>
    <div class="m-t-5">
      {include file="ui/component/content-editor/accordion/checkbox.tpl" field="showcase_summary" title="{t}Include in the Google News Showcase summary{/t}"}
    </div>
  </div>
</div>
