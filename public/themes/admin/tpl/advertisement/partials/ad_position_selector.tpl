<div class="checkbox {if $center}center{/if}">
  <input id="{$input_id}" name="positions[]" type="checkbox" value="{$position_id}" {if isset($advertisement) && is_array($advertisement->positions) && in_array($position_id, $advertisement->positions)}checked="checked"{/if} ng-checked="positions.indexOf({$position_id}) > -1" ng-click="togglePosition({$position_id})"/>
  <label for="{$input_id}" class="left">
    {if !empty($ads_positions_manager->getAdvertisementName($position_id))}
      {preg_replace('@\[[^\]]*\]@', '', $ads_positions_manager->getAdvertisementName($position_id))}
    {else}
      {t}Advertisement without name{/t}
    {/if}
    {$size}
  </label>
</div>
