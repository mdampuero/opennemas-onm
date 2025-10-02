<ui-select class="{$class}" name="dates" ng-model="{$ngModel}" theme="select2" search-enabled="false" >
  <ui-select-match>
    <strong>{if $iTitle}{$iTitle}{else}{t}Date{/t}{/if}:</strong> [% $select.selected.name %]
    <span ng-if="$select.selected.filter !== 'Quick'">
    [% $select.selected.group %]
    </span>
  </ui-select-match>
  <ui-select-choices group-by="'group'" repeat="item.value as item in toArray(addEmptyValue({$data}, 'value', 'name', '{t}All months{/t}'))">
    <div ng-bind-html="(item.name)"></div>
  </ui-select-choices>
</ui-select>
