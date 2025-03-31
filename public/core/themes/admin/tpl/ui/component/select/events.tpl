<ui-select class="{$class}" name="events" ng-model="{$ngModel}" theme="select2">
  <ui-select-match>
    [% $select.selected.name %]
  </ui-select-match>
  <ui-select-choices group-by="'group'"
    repeat="eventType.slug as eventType in toArray(addEmptyValue(data.extra.events, 'slug', 'name', '{t}Select a type{/t}...')) | filter: { name: $select.search }">
    <div ng-bind-html="(eventType.name) | highlight: $select.search"></div>
  </ui-select-choices>
</ui-select>
