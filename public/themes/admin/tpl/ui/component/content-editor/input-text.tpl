<div class="form-group">
  <label for="{$field}" class="form-label">{$title}</label>
  <div class="controls">
    {if $counter}<div class="input-group">{/if}
      <input type="text" id="{$field}" name="{$field}" ng-model="item.{$field}" {if $required}required{/if} class="form-control"/>
      {if $counter}
      <span class="input-group-addon">
        <span ng-class="{ 'text-warning': item.{$field}.length >= 50 &amp;&amp; item.{$field}.length < 80, 'text-danger': item.{$field}.length >= 80 }">[% item.{$field} ? item.{$field}.length : 0 %]</span>
      </span>
      {/if}
    {if $counter}</div>{/if}
  </div>
</div>
