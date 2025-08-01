<ui-select class="{$class}" ng-init="status = [ { name: '{t}Any{/t}', value: null }, { name: '{t}Sent{/t}', value: 1 }, { name: '{t}Scheduled{/t}', value: 0 }, { name: '{t}Error{/t}', value: 2 } ]" ng-model="{$ngModel}" theme="select2">
<ui-select-match>
  {if $label}<strong>{t}Status{/t}:</strong> {/if}[% $select.selected.name %]
</ui-select-match>
<ui-select-choices repeat="item.value as item in status | filter: { name: $select.search }">
  <div ng-bind-html="item.name | highlight: $select.search"></div>
</ui-select-choices>
</ui-select>
