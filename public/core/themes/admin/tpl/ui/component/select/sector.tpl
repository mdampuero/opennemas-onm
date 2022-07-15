<ui-select class="{$class}" name="sector" ng-model="{$ngModel}" {if $required}required{/if} theme="select2">
  <ui-select-match placeholder="{t}Select a sector...{/t}">
    [% $select.selected.title %]
  </ui-select-match>
  <ui-select-choices repeat="item.name as item in data.extra.sectors | filter: $select.search track by item.name">
    <span ng-bind-html="item.title"></span>
  </ui-select-choices>
</ui-select>
