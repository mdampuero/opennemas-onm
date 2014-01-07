<div id="polls_available" class="content-provider-block">
    {foreach from=$polls item=content name=poll_loop}
        {include file="poll/content-provider/poll.tpl"}
    {/foreach}
</div>
<div class="pagination pagination-mini clearfix">
    {$pager->links}
</div><!-- / -->
