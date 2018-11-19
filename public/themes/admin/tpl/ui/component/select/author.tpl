<ui-select class="{$class}" name="author" ng-model="{$ngModel}" theme="select2">
  <ui-select-match>
    {if $label}<strong>{t}Author{/t}:</strong> {/if}[% $select.selected.name %]{if $blog} [% $select.selected.is_blog == 1 ? '(Blog)' : '' %]{/if}
  </ui-select-match>
  <ui-select-choices repeat="item.id as item in toArray(addEmptyValue(data.extra.authors, 'id'{if $select}, '{t}Select an author...{/t}'{/if})) | filter: { name: $select.search }">
    <div ng-bind-html="(item.name{if $blog} + (item.is_blog == 1 ? ' (Blog)' : ''){/if})  | highlight: $select.search"></div>
  </ui-select-choices>
</ui-select>
