<div class="checkbox {if $center}center{/if}">
  <input id="{$input_id}" name="positions[]" type="checkbox" value="{$position_id}" {if isset($advertisement) && is_array($advertisement->type_advertisement) && in_array($position_id, $advertisement->type_advertisement)}checked="checked"{/if} ng-checked="type_advertisement.indexOf({$position_id}) > -1" ng-click="togglePosition({$position_id})"/>
  <label for="{$input_id}" class="left">
    {if !empty($ads_positions->getAdvertisementName($position_id))}
      {preg_replace('@\[[^\]]*\]@', '', $ads_positions->getAdvertisementName($position_id))}
    {else}
      {t}Advertisement without name{/t}
    {/if}
    {$size}
  </label>
</div>
