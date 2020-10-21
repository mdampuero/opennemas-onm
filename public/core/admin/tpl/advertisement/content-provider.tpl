<div id="advertisments_available" class="content-provider-block">
  {foreach from=$ads item=content name=ads_loop}
    {include file="advertisement/content-provider/advertisement.tpl"}
  {/foreach}
</div>
<div class="pagination-wrapper">
  {$pagination}
</div>
