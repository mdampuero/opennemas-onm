{*
    OpenNeMas project

    @theme      Lucidity
*}
    
{if $type_video eq 'youtube'}
   
    <div class="youtube-highlighter clearfix">
        <div class="youtube-highlighter-header"><img src="{$smarty.const.MEDIA_PATH_URL}/sections/youtube.png" alt=""/></div>
        <div class="youtube-highlighter-big clearfix">
            <object  width="220" height="184" >
                <param value="http://www.youtube.com/v/{$videos[0]->videoid}" name="movie" />
                <param value="true" name="allowFullScreen" />
                <param value="always" name="allowscriptaccess">
                <embed width="240" height="180" src="http://www.youtube.com/v/{$videos[0]->videoid}" />
            </object>
        </div>
        <ul class="youtube-highligher-little-section-links">
            {section name=i loop=$videos start=0}
                {if $smarty.section.i.first}
                    <li class="first"><a href="#">
                        <img width="60" alt="{$videos[i]->title|clearslash|escape:'html'}" title="{$videos[i]->title|clearslash|escape:'html'}" src="http://i4.ytimg.com/vi/{$videos[i]->videoid}/default.jpg" />
                    </a></li>
                {elseif $smarty.section.i.last}
                    <li class="last"><a href="#">
                        <img width="60" alt="{$videos[i]->title|clearslash|escape:'html'}" title="{$videos[i]->title|clearslash|escape:'html'}" src="http://i4.ytimg.com/vi/{$videos[i]->videoid}/default.jpg" />
                    </a></li>
                {else}
                    <li class=""><a href="#">
                        <img width="60" alt="{$videos[i]->title|clearslash|escape:'html'}" title="{$videos[i]->title|clearslash|escape:'html'}" src="http://i4.ytimg.com/vi/{$videos[i]->videoid}/default.jpg" />
                    </a></li>
                {/if}

            {/section}
        </ul>
    </div>
   
{else}
   
    <div class="tv-highlighter clearfix">
        <div class="tv-highlighter-header clearfix">
            <img src="{$smarty.const.MEDIA_PATH_URL}/sections/tv.png" alt="" />

        </div>
        <div class="tv-highlighter-big clearfix">
            <object width="300" height="225">
                <param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id=8259304&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=0&amp;show_portrait=0&amp;color=b0113A&amp;fullscreen=1" />
                <embed src="http://vimeo.com/moogaloop.swf?clip_id=8259304&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=b0113A&amp;fullscreen=1" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="300" height="225"></embed>
            </object>
            <p>
                 Felix Rodriguez de la Fuente<br/>
                <img src="{$smarty.const.MEDIA_PATH_URL}/stars.png" alt="" />
            </p>
            <ul class="div.tv-highlighter">
            {section name=i loop=$videos}
                {if $smarty.section.i.first}
                    <li class="first"><a href="#">
                        <img   alt="{$videos[i]->title|clearslash|escape:'html'}" title="{$videos[i]->title|clearslash|escape:'html'}" src="{$videos[i]->thumbnail_small}" />
                    </a></li>
                {elseif $smarty.section.i.last}
                    <li class="last"><a href="#">
                        <img   alt="{$videos[i]->title|clearslash|escape:'html'}" title="{$videos[i]->title|clearslash|escape:'html'}" src="{$videos[i]->thumbnail_small}" />
                    </a></li>
                {else}
                    <li class="first"><a href="#">
                        <img  alt="{$videos[i]->title|clearslash|escape:'html'}" title="{$videos[i]->title|clearslash|escape:'html'}" src="{$videos[i]->thumbnail_small}" />
                    </a></li>
                {/if}

            {/section}
        </ul>
        </div>

    </div>
 
{/if}