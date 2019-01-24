<div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.schedule = !expanded.schedule">
  <i class="fa fa-clock-o m-r-10"></i>{t}Schedule{/t}
  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.schedule }"></i>

  {* <span ng-if="now.getTime() < (new Date(item.starttime).getTime())"></span>
  <span class="badge badge-default pull-right m-r-10 m-t-2" ng-if="!item.starttime && !item.endtime">{t}In time{/t}</span>
  <span class="badge badge-default pull-right m-r-10 m-t-2" ng-if="!item.starttime && !item.endtime">{t}Future{/t}<span class="hidden-lg visible-xlg pull-right">: [% item.starttime %]</span><span>: [% item.endtime %]</span></span>
  <span class="badge badge-default pull-right m-r-10 m-t-2" ng-if="!item.starttime && !item.endtime">{t}Dued{/t}<span class="hidden-lg visible-xlg pull-right">: [% item.endtime %]</span></span> *}
  <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-uppercase text-bold" ng-show="!expanded.schedule && item.endtime">
    <strong>{t}End{/t}</strong>
    <span class="hidden-lg visible-xlg pull-right">: [% item.endtime %]</span>
  </span>
  <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-uppercase text-bold" ng-show="!expanded.schedule && item.starttime">
    <strong>{t}Start{/t}</strong>
    <span class="hidden-lg visible-xlg pull-right">: [% item.starttime %]</span>
  </span>
</div>
<div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.schedule }">
  <div class="form-group">
    <label class="form-label" for="starttime">
      {t}Publication start date{/t}
    </label>
    <div class="controls">
      <div class="input-group">
        <input class="form-control" datetime-picker datetime-picker-timezone="{$timezone}" datetime-picker-use-current=true datetime-picker-min="item.created" id="starttime" name="starttime" ng-model="item.starttime" type="datetime">
        <span class="input-group-addon add-on">
          <span class="fa fa-calendar"></span>
        </span>
      </div>
      <span class="help-block">
        {t}Server hour:{/t} {$smarty.now|date_format:"%Y-%m-%d %H:%M:%S"}
      </span>
    </div>
  </div>
  <div class="form-group">
    <label class="form-label" for="endtime">
      {t}Publication end date{/t}
    </label>
    <div class="controls">
      <div class="input-group">
        <input class="form-control" datetime-picker datetime-picker-timezone="{$timezone}" datetime-picker-use-current=true datetime-picker-min="item.endtime" id="endtime" name="endtime" ng-model="item.endtime" type="datetime">
        <span class="input-group-addon add-on">
          <span class="fa fa-calendar"></span>
        </span>
      </div>
    </div>
  </div>
  <span><i class="fa fa-info-circle text-info"></i> {t}This content will only be available in the time range specified above.{/t}</span>
</div>
