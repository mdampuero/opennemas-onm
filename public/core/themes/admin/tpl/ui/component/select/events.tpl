<ui-select class="{$class}" name="author" ng-model="{$ngModel}" {if $required}required{/if} theme="select2">
  <ui-select-match>
    {if $label}<strong>{t}Event type{/t}:</strong> {/if}
    [% $select.selected.name %]
  </ui-select-match>
  <ui-select-choices group-by="'group'" repeat="eventType.slug as eventType in toArray(data.extra.events) | filter: { name: $select.search }">
    <div ng-bind-html="(eventType.name) | highlight: $select.search"></div>
  </ui-select-choices>
</ui-select>
