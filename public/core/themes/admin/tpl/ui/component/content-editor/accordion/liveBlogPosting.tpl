<div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.liveBlogPosting = !expanded.liveBlogPosting">
  <i class="fa fa-edit m-r-10"></i>{t}Live Blog Post{/t}
  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.liveBlogPosting }"></i>
  <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-uppercase text-bold" ng-show="!expanded.liveBlogPosting">
    <span ng-show="!item.liveBlogPosting"><strong>{t}No Live{/t}</strong></span>
    <span ng-show="item.liveBlogPosting">
      <strong>Live</span></strong>
    </span>
  </span>
</div>
<div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.liveBlogPosting }">
  <div class="form-group">
    <div class="m-t-5">
      {include file="ui/component/content-editor/accordion/checkbox.tpl" field="liveBlogPosting" title="{t}Live Blog Post{/t}"}
    </div>
  </div>
  <div class="form-group">
    <label class="form-label" for="coverageStartTime">
      {t}Coverage strat time{/t}
    </label>
    <div class="controls">
      <div class="input-group">
        <input class="form-control" datetime-picker datetime-picker-timezone="{$app.locale->getTimeZone()->getName()}" datetime-picker-use-current=true datetime-picker-min="item.created" id="coverageStartTime" name="coverageStartTime" ng-model="item.coverageStartTime" type="datetime">
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
        <input class="form-control" datetime-picker datetime-picker-timezone="{$app.locale->getTimeZone()->getName()}" datetime-picker-min="item.created" id="coverageEndTime" name="coverageEndTime" ng-model="item.coverageEndTime" type="datetime">
        <span class="input-group-addon add-on">
          <span class="fa fa-calendar"></span>
        </span>
      </div>
    </div>
  </div>
</div>
