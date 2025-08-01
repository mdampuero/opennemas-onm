<div id="events_available" class="content-provider-block">
  {foreach from=$events item=content name=events_loop}
    {include file="event/content-provider/event.tpl"}
  {/foreach}
</div>
<div class="pagination-wrapper">
  {$pagination}
</div>
