<ui-select class="{$class}" name="author" ng-model="{$ngModel}" {if $required}required{/if} theme="select2">
  <ui-select-match>
    {if $label}<strong>{t}Event type{/t}:</strong> {/if}
    [% $select.selected.name %]
  </ui-select-match>
  <ui-select-choices repeat="eventType.slug as eventType in toArray(data.extra.events) | filter: { name: $select.search }">
    <div ng-if="eventType.category">
      <strong>[% eventType.name %]</strong>
    </div>
    <div ng-if="!eventType.category" style="padding-left: 15px;">
      <div ng-bind-html="(eventType.name) | highlight: $select.search"></div>
    </div>
  </ui-select-choices>
</ui-select>
