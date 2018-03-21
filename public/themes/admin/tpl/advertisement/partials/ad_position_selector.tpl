<div class="checkbox {if $center}center{/if}">
  <input id="position-{$position_id}" name="positions[]" type="checkbox" value="{$position_id}" ng-checked="positions.indexOf({$position_id}) > -1" ng-click="togglePosition({$position_id})"/>
  <label for="position-{$position_id}" class="left">
    {if !empty($ads_positions_manager->getAdvertisementName($position_id))}
      {preg_replace('@\[[^\]]*\]@', '', $ads_positions_manager->getAdvertisementName($position_id))}
    {else}
      {t}Advertisement without name{/t}
    {/if}
    {$size}
  </label>
</div>
