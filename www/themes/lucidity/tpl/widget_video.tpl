{*
    OpenNeMas project

    @theme      Lucidity
*}
    
{if $type_video eq 'youtube'}
   
    <div class="youtube-highlighter clearfix">
        <div class="youtube-highlighter-header">
             <b>Videos</b>
        </div>
        <div style="float:right;">
                {section name=i loop=$videos start=0}
                    {if $smarty.section.i.first}
                        <div   class="first"><a href="{$videos[i]->permalink}" title="{$videos[i]->title|clearslash|escape:'html'}">
                            <img width="60" alt="{$videos[i]->title|clearslash|escape:'html'}" title="{$videos[i]->title|clearslash|escape:'html'}" src="http://i4.ytimg.com/vi/{$videos[i]->videoid}/default.jpg" />
                        </a></div>
                    {elseif $smarty.section.i.last}
                        <div   class="first"><a href="{$videos[i]->permalink}" title="{$videos[i]->title|clearslash|escape:'html'}">
                            <img width="60" alt="{$videos[i]->title|clearslash|escape:'html'}" title="{$videos[i]->title|clearslash|escape:'html'}" src="http://i4.ytimg.com/vi/{$videos[i]->videoid}/default.jpg" />
                        </a></div>
                    {else}
                         <div   class="first"><a href="{$videos[i]->permalink}" title="{$videos[i]->title|clearslash|escape:'html'}">
                            <img width="60" alt="{$videos[i]->title|clearslash|escape:'html'}" title="{$videos[i]->title|clearslash|escape:'html'}" src="http://i4.ytimg.com/vi/{$videos[i]->videoid}/default.jpg" />
                        </a></div>
                    {/if}
                {/section}            
        </div>
        <div class="youtube-highlighter-big clearfix">
            <a href="{$videos[0]->permalink}" title="{$videos[0]->title|clearslash|escape:'html'}">
                <object width="330" height="220">
                    <param value="http://www.youtube.com/v/{$videos[0]->videoid}" name="movie" />
                    <param value="true" name="allowFullScreen" />
                    <param value="always" name="allowscriptaccess">
                    <embed width="330" height="220" src="http://www.youtube.com/v/{$videos[0]->videoid}" />
                </object>
            </a>
        </div>
        <p> <a href="{$videos[0]->permalink}" title="{$videos[0]->title|clearslash|escape:'html'}">
                {$videos[0]->title|clearslash|escape:'html'} </a>
        </p>
    </div>
   
{else}
   
    <div class="tv-highlighter clearfix">
        <div class="tv-highlighter-header clearfix">
             <b>Videos</b>
        </div>
        <div class="tv-highlighter-minis">
                {section name=i loop=$videos}
                    {if $smarty.section.i.first}
                        <div   class="first"><a href="{$videos[i]->permalink}" title="{$videos[i]->title|clearslash|escape:'html'}">
                            <img   alt="{$videos[i]->title|clearslash|escape:'html'}" title="{$videos[i]->title|clearslash|escape:'html'}" src="{$videos[i]->thumbnail_small}" />
                        </a></div>
                    {elseif $smarty.section.i.last}
                        <div class="last"><a href="{$videos[i]->permalink}" title="{$videos[i]->title|clearslash|escape:'html'}">
                            <img   alt="{$videos[i]->title|clearslash|escape:'html'}" title="{$videos[i]->title|clearslash|escape:'html'}" src="{$videos[i]->thumbnail_small}" />
                        </a></div>
                    {else}
                        <div class="first"><a href="{$videos[i]->permalink}" title="{$videos[i]->title|clearslash|escape:'html'}">
                            <img  alt="{$videos[i]->title|clearslash|escape:'html'}" title="{$videos[i]->title|clearslash|escape:'html'}" src="{$videos[i]->thumbnail_small}" />
                        </a></div>
                    {/if}

                {/section}

         </div>
        <div class="tv-highlighter-big clearfix">
            <a href="{$videos[0]->permalink}" title="{$videos[0]->title|clearslash|escape:'html'}">
            <object width="330" height="220">
                <param name="allowfullscreen" value="true" />
                <param name="allowscriptaccess" value="always" />
                <param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id={$video->videoid}&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=00ADEF&amp;fullscreen=1" />
                <embed src="http://vimeo.com/moogaloop.swf?clip_id={$video->videoid}&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=00ADEF&amp;fullscreen=1" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="330" height="220"></embed>
            </object>
            </a>
            <p> <a href="{$videos[0]->permalink}" title="{$videos[0]->title|clearslash|escape:'html'}">
                {$videos[0]->title|clearslash|escape:'html'}
               </a>
            </p>
            
            <ul class="div.tv-highlighter">
            {section name=i loop=$videos}
                {if $smarty.section.i.first}
                    <li class="first">
                        <a href="#"><img   alt="{$videos[i]->title|clearslash|escape:'html'}" title="{$videos[i]->title|clearslash|escape:'html'}" src="{$videos[i]->thumbnail_small}" /></a>
                    </li>
                {elseif $smarty.section.i.last}
                    <li class="last">
                        <a href="#"><img   alt="{$videos[i]->title|clearslash|escape:'html'}" title="{$videos[i]->title|clearslash|escape:'html'}" src="{$videos[i]->thumbnail_small}" /></a>
                    </li>
                {else}
                    <li class="first">
                        <a href="#"><img  alt="{$videos[i]->title|clearslash|escape:'html'}" title="{$videos[i]->title|clearslash|escape:'html'}" src="{$videos[i]->thumbnail_small}" /></a>
                    </li>
                {/if}

    </div>
 
{/if}