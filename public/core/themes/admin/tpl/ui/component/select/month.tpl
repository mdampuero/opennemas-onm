<ui-select class="{$class}" name="dates" ng-model="{$ngModel}" theme="select2" search-enabled="false" >
  <ui-select-match>
    <strong>{t}Date{/t}:</strong> [% $select.selected.name %] [% $select.selected.group %]
  </ui-select-match>
  <ui-select-choices group-by="'group'" repeat="item.value as item in toArray(addEmptyValue({$data}, 'value', 'name', '{t}All months{/t}'))">
    <div ng-bind-html="(item.name)"></div>
  </ui-select-choices>
</ui-select>
