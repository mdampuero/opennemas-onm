<div id="companies_available" class="content-provider-block">
  {foreach from=$companies item=content name=companies_loop}
    {include file="company/content-provider/company.tpl"}
  {/foreach}
</div>
<div class="pagination-wrapper">
  {$pagination}
</div>
