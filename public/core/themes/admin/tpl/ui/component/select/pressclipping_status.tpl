<ui-select class="{$class}" ng-init="pressclipping_status = [ { name: '{t}Any{/t}', value: null }, { name: '{t}Sent{/t}', value: 'Sended' }, { name: '{t}Not Sent{/t}', value: 0 } ]" ng-model="{$ngModel}" theme="select2">
<ui-select-match>
  {if $label}<strong>{t}Status{/t}:</strong> {/if}[% $select.selected.name %]
</ui-select-match>
<ui-select-choices repeat="item.value as item in pressclipping_status | filter: { name: $select.search }">
  <div ng-bind-html="item.name | highlight: $select.search"></div>
</ui-select-choices>
</ui-select>
