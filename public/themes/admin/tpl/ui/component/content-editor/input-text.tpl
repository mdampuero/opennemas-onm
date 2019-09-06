<div class="form-group">
  <label for="{$iField}" class="form-label">
      {$iTitle}
  </label>
  <div class="controls">
    {if $iCounter}
        <div class="input-group">
    {/if}
    <input type="text" id="{$iField}" name="{$iField}" ng-model="item.{$iField}" {if $iRequired}required{/if} class="form-control" placeholder="[% data.extra.locale.multilanguage && data.extra.locale.default !== config.locale.selected ? data.item.{$iField}[data.extra.locale.default] : '{$placeholder}' %]" uib-tooltip="{t}Original{/t}: [% data.item.title[data.extra.locale.default] %]" tooltip-enable="config.locale.multilanguage && config.locale.default !== config.locale.selected"/>
    {if $iCounter}
      <span class="input-group-addon">
        <span ng-class="{ 'text-warning': item.{$iField}.length >= 50 && item.{$iField}.length < 80, 'text-danger': item.{$iField}.length >= 80 }">[% item.{$iField} ? item.{$iField}.length : 0 %]</span>
      </span>
    {/if}
    {if $iCounter}
      </div>
    {/if}
  </div>
</div>
