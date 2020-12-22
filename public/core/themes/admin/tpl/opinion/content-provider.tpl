<div id="opinions_available" class="content-provider-block">
  {foreach from=$opinions item=content name=opinions_loop}
    {include file="opinion/content-provider/opinion.tpl"}
  {/foreach}
</div>
<div class="pagination-wrapper">
  {$pagination}
</div>
