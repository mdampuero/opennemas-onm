<div class="row">
  <div class="col-md-12">
    {include file="advertisement/partials/ad_position_selector.tpl" position_id="1090"}
  </div>
</div>
<hr>
{foreach $extra['aditional_theme_positions'] as $ad_id => $ad}
{if $ad['theme'] == $app['theme']->uuid}
<div class="row">
  <div class="col-md-12">
    {include file="advertisement/partials/ad_position_selector.tpl" position_id=$ad_id}
  </div>
</div>
<hr>
{/if}
{/foreach}
<div class="row">
  <div class="col-md-12">
    {include file="advertisement/partials/ad_position_selector.tpl" position_id="10"}
  </div>
</div>
