<div class="form-group {$iClass}">
  {if $iTitle}
    <label for="{$iField}" class="form-label">
      {$iTitle}
    </label>
  {/if}
  <ui-select class="{$sClass}" id="{$iField}" name="{$iField}" {$iNgActions} ng-model="item.{$iField}" placeholder="[% data.extra.locale.multilanguage && data.extra.locale.default !== config.locale.selected ? data.item.{$iField}[data.extra.locale.default] : '{$iPlaceholder}' %]" {if $iRequired}required{/if} tooltip-enable="config.locale.multilanguage && config.locale.default !== config.locale.selected" type="select" uib-tooltip="{t}Original{/t}: [% data.item.title[data.extra.locale.default] %]"ng-init="type = [ { name: '{t}URL{/t}', value: 'url' }, { name: '{t}Internal Search{/t}', value: 'intsearch' }, { name: '{t}Email{/t}', value: 'email' } ]" ng-model="{$ngModel}" theme="select2">
  <ui-select-match>
    {if $label}<strong>{t}Type{/t}:</strong> {/if}[% $select.selected.name %]
  </ui-select-match>
  <ui-select-choices repeat="item.value as item in type | filter: { name: $select.search }">
    <div ng-bind-html="item.name | highlight: $select.search"></div>
  </ui-select-choices>
  </ui-select>
</div>
