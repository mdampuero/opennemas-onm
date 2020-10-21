<ui-select class="{$class}" name="blog"  ng-init="data.extra.type = [ { name: '{t}Any{/t}', value: null }, { name: '{t}Opinion{/t}', value: 0 }{is_module_activated name="BLOG_MANAGER"}, { name: '{t}Blog{/t}', value: 1 }{/is_module_activated} ]" ng-model="{$ngModel}" theme="select2">
  <ui-select-match>
    {if $label}<strong>{t}Type{/t}:</strong> {/if}[% $select.selected.name %]
  </ui-select-match>
  <ui-select-choices repeat="item.value as item in data.extra.type | filter: { name: $select.search }">
    <div ng-bind-html="item.name | highlight: $select.search"></div>
  </ui-select-choices>
</ui-select>
