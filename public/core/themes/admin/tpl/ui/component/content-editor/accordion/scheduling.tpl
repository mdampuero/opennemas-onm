<div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.schedule = !expanded.schedule">
  <i class="fa fa-clock-o m-r-10"></i>{t}Schedule{/t}
  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.schedule }"></i>

  <span class="scheduling ng-cloak pull-right text-uppercase m-r-10 m-t-2" ng-show="!expanded.schedule">
    <span class="badge badge-primary text-bold" ng-show="getContentScheduling(item) == 1">{t}Planned{/t}<strong class="hidden-lg visible-xlg pull-right">: [% item.starttime %]</strong></span>
    <span class="badge badge-danger text-bold" ng-show="getContentScheduling(item) == -1">{t}Dued{/t}<strong class="hidden-lg visible-xlg pull-right">: [% item.endtime %]</strong></span>
    <span class="badge badge-default text-bold" ng-show="getContentScheduling(item) == 0"><strong>{t}In time{/t}</strong></span>
  </span>
</div>
<div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.schedule }">
  <div class="form-group">
    <label class="form-label" for="starttime">
      {t}Publication start date{/t}
    </label>
    <div class="controls">
      <div class="input-group">
        <input class="form-control" datetime-picker datetime-picker-timezone="{$app.locale->getTimeZone()->getName()}" datetime-picker-use-current=true datetime-picker-min="item.created" id="starttime" name="starttime" ng-model="item.starttime" type="datetime">
        <span class="input-group-addon add-on">
          <span class="fa fa-calendar"></span>
        </span>
      </div>
    </div>
  </div>
  <div class="form-group no-padding">
    <label class="form-label" for="endtime">
      {t}Publication end date{/t}
    </label>
    <div class="controls">
      <div class="input-group">
        <input class="form-control" datetime-picker datetime-picker-timezone="{$app.locale->getTimeZone()->getName()}" datetime-picker-min="item.starttime" id="endtime" name="endtime" ng-model="item.endtime" type="datetime">
        <span class="input-group-addon add-on">
          <span class="fa fa-calendar"></span>
        </span>
      </div>
    </div>
  </div>
  {acl isAllowed="MASTER"}
    {include file="ui/component/input/text.tpl" iNgActions="ng-disabled=\"true\"" iField="urldatetime" iTitle="{t}URL Datetime{/t}"}
  {/acl}
  <span><i class="fa fa-info-circle text-info"></i> {t}This content will only be available in the time range specified above.{/t}</span>
  <span class="help-block">
    {t}Server hour:{/t} {$smarty.now|date_format:"%Y-%m-%d %H:%M:%S"} ({$app.locale->getTimeZone()->getName()})
  </span>
</div>
