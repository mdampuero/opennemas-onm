<div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.google_news_showcase = !expanded.google_news_showcase">
  <i class="fa fa-google m-r-10"></i>{t}Google News Showcase{/t}
  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.google_news_showcase }"></i>
</div>
<div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.google_news_showcase }">
  <div class="form-group">
    <div class="m-t-5">
      <div class="form-group">
        <div class="checkbox">
          <input id="showcase" ng-false-value="0" ng-model="item.showcase" ng-true-value="1" type="checkbox">
          <label for="showcase">{t}Include in Google News Showcase{/t}</label>
        </div>
      </div>
    </div>
    <div class="m-t-5">
      <label class="form-label" for="moment1">{t}Outstanding moment 1{/t}</label>
      <textarea class="showcase-moment" name="moment1" ng-model="moment1" rows="5"></textarea>
    </div>
    <div class="m-t-5">
      <label class="form-label" for="moment2">{t}Outstanding moment 2{/t}</label>
      <textarea class="showcase-moment" name="moment2" ng-model="moment2" rows="5"></textarea>
    </div>
    <div class="m-t-5">
      <i class="fa fa-info-circle m-r-5 text-info"></i>
      {t}To include the article in Google News Showcase you need to highlight two moments{/t}
    </div>
  </div>
</div>
