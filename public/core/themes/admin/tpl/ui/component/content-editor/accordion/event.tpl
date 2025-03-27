<div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.eventType = !expanded.eventType">
  <i class="fa fa-edit m-r-10"></i>{t}Event Type{/t}
  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.eventType }"></i>
  <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-uppercase text-bold" ng-show="!expanded.eventType">
    <span ng-show="!item.event_type">
      <strong>{t}No event type selected{/t}</strong>
    </span>
    <span ng-show="item.event_type">
      <strong>
        [% getEventName(item.event_type) %]
      </strong>
    </span>
  </span>
</div>

<div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.eventType, 'event-slug-class': item.event_type && (data.extra.events.slug | filter : { slug: item.event_type } : true)[0].slug === 'desired-slug' }">
  <div class="form-group no-margin">
    {include file="ui/component/select/events.tpl" class="form-control" ngModel="item.event_type" select=true required=$required}
  </div>
</div>
