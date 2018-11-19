<ui-select class="{$class}" name="view" ng-model="{$ngModel}" theme="select2">
  <ui-select-match>
    {if $label}<strong>{t}View{/t}:</strong> {/if}[% $select.selected %]
  </ui-select-match>
  <ui-select-choices repeat="item in views | filter: $select.search">
    <div ng-bind-html="item | highlight: $select.search"></div>
  </ui-select-choices>
</ui-select>
