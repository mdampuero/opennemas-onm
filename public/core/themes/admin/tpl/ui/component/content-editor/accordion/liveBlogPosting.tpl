<div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.live_blog_posting = !expanded.live_blog_posting">
  <i class="fa fa-edit m-r-10"></i>{t}Live blog post{/t}
  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.live_blog_posting }"></i>
  <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-uppercase text-bold" ng-show="!expanded.live_blog_posting">
    <span ng-show="!item.params.live_blog_posting"><strong>{t}Standard{/t}</strong></span>
    <span ng-show="item.params.live_blog_posting">
      <strong>{t}Live blog{/t}</span></strong>
    </span>
  </span>
</div>
<div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.live_blog_posting }">
  <div class="form-group">
    <div class="m-t-5">
      <div class="form-group no-margin">
        <div class="checkbox">
          <input id="live_blog_posting" ng-false-value=0 ng-model="item.params.live_blog_posting" ng-true-value=1 type="checkbox">
          <label for="live_blog_posting">{t}Enable live blog posting{/t}</label>
        </div>
      </div>
    </div>
  </div>
  <div class="form-group" ng-if="item.params.live_blog_posting">
    <label class="form-label" for="coverage_start_time">
      {t}Coverage strat time{/t}
    </label>
    <div class="controls">
      <div class="input-group">
        <input class="form-control" ng-required="item.params.live_blog_posting" datetime-picker datetime-picker-timezone="{$app.locale->getTimeZone()->getName()}" datetime-picker-use-current=true datetime-picker-min="item.created" id="coverage_start_time" name="coverage_start_time" ng-model="item.coverage_start_time" type="datetime">
        <span class="input-group-addon add-on">
          <span class="fa fa-calendar"></span>
        </span>
      </div>
    </div>
  </div>
  <div class="form-group no-padding" ng-if="item.params.live_blog_posting">
    <label class="form-label" for="endtime">
      {t}Coverage end time{/t}
    </label>
    <div class="controls">
      <div class="input-group">
        <input class="form-control" ng-required="item.params.live_blog_posting" datetime-picker datetime-picker-timezone="{$app.locale->getTimeZone()->getName()}" datetime-picker-min="item.created" id="coverage_end_time" name="coverage_end_time" ng-model="item.coverage_end_time" type="datetime">
        <span class="input-group-addon add-on">
          <span class="fa fa-calendar"></span>
        </span>
      </div>
    </div>
  </div>
</div>
