<div id="letters_available" class="content-provider-block">
    {foreach from=$letters item=content name=letters_loop}
        {include file="letter/content-provider/letter.tpl"}
    {/foreach}
</div>
<div class="pagination clearfix">
    {$pager->links}
</div><!-- / -->
