<div id="widgets_available" class="content-provider-block">
    {foreach from=$widgets item=content name=widget_loop}
        {include file="widget/content-provider/widget.tpl"}
    {/foreach}
</div>
<div class="pagination clearfix">
    {$pager->links}
</div><!-- / -->