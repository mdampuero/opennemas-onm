{foreach $extra['aditional_theme_positions'] as $ad_id => $ad}
{if $ad['theme'] == $app['theme']->uuid}
<div class="row">
  <div class="col-md-12">
    {include file="advertisement/partials/ad_position_selector.tpl" position_id=$ad_id size=$ad['name']}
  </div>
</div>
<hr>
{/if}
{/foreach}
