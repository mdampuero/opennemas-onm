<div class="checkbox {if $center}center{/if}">
  <input id="{if !isset($input_id)}ad-{$position_id}{else}{$input_id}{/if}" name="positions[]" type="checkbox" value="{$position_id}" ng-checked="positions.indexOf({$position_id}) > -1" ng-click="togglePosition({$position_id})"/>
  <label for="{if !isset($input_id)}ad-{$position_id}{else}{$input_id}{/if}" class="left">
    {if !empty($ads_positions_manager->getAdvertisementName($position_id))}
      {preg_replace('@\[[^\]]*\]@', '', $ads_positions_manager->getAdvertisementName($position_id))}
    {else}
      {t}Advertisement without name{/t}
    {/if}
    {$size}
  </label>
</div>
