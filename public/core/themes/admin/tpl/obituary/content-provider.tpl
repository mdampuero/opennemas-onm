<div id="obituaries_available" class="content-provider-block">
  {foreach from=$obituaries item=content name=obituaries_loop}
    {include file="obituary/content-provider/obituary.tpl"}
  {/foreach}
</div>
<div class="pagination-wrapper">
  {$pagination}
</div>
