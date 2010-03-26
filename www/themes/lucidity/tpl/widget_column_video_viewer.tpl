{*
    OpenNeMas project

    @theme      Lucidity
*}
{if !(empty($video->videoid)) }
    <div class="tv-highlighter clearfix">
        <div class="tv-highlighter-header clearfix">
            <div class="tv-highlighter-big clearfix">
                {if $video->author_name eq 'youtube'}
                    <object width="290" height="163">
                        <param value="http://www.youtube.com/v/{$video->videoid}" name="movie" />
                        <param value="true" name="allowFullScreen" />
                        <param value="always" name="allowscriptaccess">
                        <embed width="290" height="163" src="http://www.youtube.com/v/{$video->videoid}" />
                    </object>

                {else}

                    <object width="290" height="163"><param name="allowfullscreen" value="true" />
                        <param name="allowscriptaccess" value="always" /><param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id=9851483&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=ffffff&amp;fullscreen=1" />
                        <embed src="http://vimeo.com/moogaloop.swf?clip_id={$video->videoid}&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=ffffff&amp;fullscreen=1" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="290" height="163"></embed>
                    </object>

                {/if}

                <p>
                   <a href="{$video->permalink}"> {$video->title|clearslash|escape:'html'} </a><br/>
                   
                </p>
            </div>
        </div>
    </div><!-- fin tv-highlighter -->
{/if}