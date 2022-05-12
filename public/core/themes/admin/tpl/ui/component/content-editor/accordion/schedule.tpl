<div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.timetable = !expanded.timetable">
  <i class="fa {$icon} m-r-10"></i>{t}Timetable{/t}
  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.timetable }"></i>
</div>
<div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.timetable }">
  <div class="form-group no-margin timetable">
    <div ng-repeat="day in item.timetable">
      <div class="timetable-day">
        <div>
          <input type="checkbox" ng-model="day.enabled">
          <label class="form-label m-l-5">[% day.name %]</label>
        </div>
        <label ng-if="!day.enabled" class="form-label closed-label m-r-5">{t}Closed{/t}
      </div>
      <div class="timetable-schedule" ng-if="day.enabled">
        <div ng-repeat="schedule in day.schedules track by $index">
          <div>
            <label class="form-label">{t}Since{/t}</label>
            <input type="text" ng-model="schedule.start">
          </div>
          <div>
            <label class="form-label">{t}Until{/t}</label>
            <input type="text" ng-model="schedule.end">
          </div>
          <button class="btn btn-danger" ng-click="removeSchedule($parent.$index, $index)" type="button">
            <i class="fa fa-trash-o"></i>
          </button>
        </div>
        <a href="#" ng-click="addSchedule($index)">{t}Add schedule{/t}</a>
      </div>
    </div>
  </div>
</div>
