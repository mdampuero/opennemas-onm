<div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.live_blog_posting = !expanded.live_blog_posting">
  <i class="fa fa-edit m-r-10"></i>{t}Live Blog Post{/t}
  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.live_blog_posting }"></i>
  <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-uppercase text-bold" ng-show="!expanded.live_blog_posting">
    <span ng-show="!item.live_blog_posting"><strong>{t}No Live{/t}</strong></span>
    <span ng-show="item.live_blog_posting">
      <strong>Live</span></strong>
    </span>
  </span>
</div>
<div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.live_blog_posting }">
  <div class="form-group">
    <div class="m-t-5">
      {include file="ui/component/content-editor/accordion/checkbox.tpl" field="live_blog_posting" title="{t}Live blog post{/t}"}
    </div>
  </div>
  <div class="form-group">
    <label class="form-label" for="coverage_start_time">
      {t}Coverage strat time{/t}
    </label>
    <div class="controls">
      <div class="input-group">
        <input class="form-control" datetime-picker datetime-picker-timezone="{$app.locale->getTimeZone()->getName()}" datetime-picker-use-current=true datetime-picker-min="item.created" id="coverage_start_time" name="coverage_start_time" ng-model="item.coverage_start_time" type="datetime">
        <span class="input-group-addon add-on">
          <span class="fa fa-calendar"></span>
        </span>
      </div>
    </div>
  </div>
  <div class="form-group no-padding">
    <label class="form-label" for="endtime">
      {t}Coverage end time{/t}
    </label>
    <div class="controls">
      <div class="input-group">
        <input class="form-control" datetime-picker datetime-picker-timezone="{$app.locale->getTimeZone()->getName()}" datetime-picker-min="item.created" id="coverage_end_time" name="coverage_end_time" ng-model="item.coverage_end_time" type="datetime">
        <span class="input-group-addon add-on">
          <span class="fa fa-calendar"></span>
        </span>
      </div>
    </div>
  </div>
</div>
