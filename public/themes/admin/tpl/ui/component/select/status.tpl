<ui-select class="{$class}" name="status" ng-init="data.extra.status = [ { name: '{t}Any{/t}', value: null }, { name: '{t}Published{/t}', value: 1 }, { name: '{t}No published{/t}', value: 0 } ]" ng-model="{$ngModel}" theme="select2">
<ui-select-match>
  {if $label}<strong>{t}Status{/t}:</strong> {/if}[% $select.selected.name %]
</ui-select-match>
<ui-select-choices repeat="item.value as item in data.extra.status | filter: { name: $select.search }">
  <div ng-bind-html="item.name | highlight: $select.search"></div>
</ui-select-choices>
</ui-select>
