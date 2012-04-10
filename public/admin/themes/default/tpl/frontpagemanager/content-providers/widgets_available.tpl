<div id="widgets_available" class="content-provider-block">
    {foreach from=$widgets item=content name=widget_loop}
        {include file="frontpagemanager/content-types/widget.tpl"}       
    {/foreach}
</div>