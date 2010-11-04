{*
    OpenNeMas project
    @theme      Lucidity
*}
{if !(empty($video->videoid))}
    <div class="tv-highlighter clearfix">
        <div class="tv-highlighter-header clearfix">
            <div class="tv-highlighter-big clearfix">
                {include file="video/widget_video_window.tpl" width="290" height="180"}
                <p><a href="{$video->permalink}"> {$video->title|clearslash|escape:'html'} </a><br/></p>
            </div>
        </div>
    </div><!-- fin tv-highlighter -->
{/if}