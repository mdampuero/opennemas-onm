{foreach $extra['aditional_theme_positions'] as $ad_id => $ad}
{if $ad['theme'] == $app['theme']->uuid}
<div class="row">
  <div class="col-md-12">
    {capture name="inputId"}ad-{$adId}{/capture}
    {include file="advertisement/partials/ad_position_selector.tpl" position_id=$ad_id input_id=$inputId size=$ad['name']}
  </div>
</div>
<hr>
{/if}
{/foreach}
