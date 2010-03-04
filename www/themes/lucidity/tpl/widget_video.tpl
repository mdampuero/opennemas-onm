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
            <form action="#">
                <select name="asdf">
                    <option>Deportes</option>
                    <option>Cotilleo</option>
                    <option>Política</option>
                    <option>Otros</option>
                </select>
            </form>
        </div>
        <div class="tv-highlighter-big clearfix">
            <img src="{$smarty.const.MEDIA_PATH_URL}/video.futebol.png" alt="" align="center"/>
            <p>
                Partido sin complicaciones para los Vascos<br/>
                <img src="{$smarty.const.MEDIA_PATH_URL}/stars.png" alt="" />
            </p>
        </div>

    </div>
 
{/if}