{*
    OpenNeMas project

    @theme      Lucidity
*}
 

    <div class="tv-highlighter clearfix">
        <div class="tv-highlighter-header">
             <b>Videos</b>
        </div>
       <div class="tv-highlighter-minis">
                {section name=i loop=$videos start=0}
                    {if $smarty.section.i.first}
                        <div  class="first"><a href="{$videos[i]->permalink}" title="{$videos[i]->title|clearslash|escape:'html'}">
                    {elseif $smarty.section.i.last}
                        <div class="last"><a href="{$videos[i]->permalink}" title="{$videos[i]->title|clearslash|escape:'html'}">
                    {else}
                         <div class=""><a href="{$videos[i]->permalink}" title="{$videos[i]->title|clearslash|escape:'html'}">
                    {/if}
                    {if $videos[i]->author_name eq 'youtube'}
                         <img width="60" alt="{$videos[i]->title|clearslash|escape:'html'}" title="{$videos[i]->title|clearslash|escape:'html'}" src="http://i4.ytimg.com/vi/{$videos[i]->videoid}/default.jpg" />
                    {else}
                          <img  alt="{$videos[i]->title|clearslash|escape:'html'}" title="{$videos[i]->title|clearslash|escape:'html'}" src="{$videos[i]->thumbnail_small}" />
                    {/if}
                        </a></div>
                {/section}
        </div>
        <div class="tv-highlighter-big span-8 clearfix">
            {if $videos[0]->author_name eq 'youtube'}
                <a href="{$videos[0]->permalink}" title="{$videos[0]->title|clearslash|escape:'html'}">
                   <object width="300" height="210">
                        <param value="http://www.youtube.com/v/{$videos[0]->videoid}" name="movie" />
                        <param value="true" name="allowFullScreen" />
                        <param value="always" name="allowscriptaccess">
                        <embed width="300" height="210" src="http://www.youtube.com/v/{$videos[0]->videoid}" />
                    </object> 
                </a>
             {else}
              <a href="{$videos[0]->permalink}" title="{$videos[0]->title|clearslash|escape:'html'}">
                <object width="320" height="220">
                    <param name="allowfullscreen" value="true" />
                    <param name="allowscriptaccess" value="always" />
                    <param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id={$videos[0]->videoid}&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=00ADEF&amp;fullscreen=1" />
                    <embed src="http://vimeo.com/moogaloop.swf?clip_id={$videos[0]->videoid}&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=00ADEF&amp;fullscreen=1" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="330" height="220"></embed>
                </object>
                </a>
              {/if}

                <p class="span-6"> <a href="{$videos[0]->permalink}" title="{$videos[0]->title|clearslash|escape:'html'}">
                        {$videos[0]->title|clearslash|escape:'html'} </a>
                </p>
                 <ul class="div.tv-highlighter">
                    {section name=i loop=$videos}
                        {if $smarty.section.i.first}
                            <li class="first">
                               
                        {elseif $smarty.section.i.last}
                            <li class="last">

                        {else}
                            <li class="">
                         {/if}
                            <a href="{$videos[i]->permalink}" title="{$videos[i]->title|clearslash|escape:'html'}">
                             {if $videos[i]->author_name eq 'youtube'}
                                   <img width="60" alt="{$videos[i]->title|clearslash|escape:'html'}" title="{$videos[i]->title|clearslash|escape:'html'}" src="http://i4.ytimg.com/vi/{$videos[i]->videoid}/default.jpg" />
                               {else}
                                  <img  alt="{$videos[i]->title|clearslash|escape:'html'}" title="{$videos[i]->title|clearslash|escape:'html'}" src="{$videos[i]->thumbnail_small}" />
                            {/if}  

                                </a>
                            </li>
                       
                    {/section}
                </ul>
        </div>

    </div>


