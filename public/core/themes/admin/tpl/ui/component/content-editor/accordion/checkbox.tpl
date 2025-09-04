<div class="form-group no-margin">
  <div class="checkbox">
    <input id="{$field}" ng-false-value="{if $isString}'0'{else}0{/if}" ng-model="item.{$field}" ng-true-value="{if $isString}'1'{else}1{/if}" type="checkbox">
    <label for="{$field}">{$title}</label>
  </div>
</div>
