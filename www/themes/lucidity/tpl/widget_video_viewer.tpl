{*
    OpenNeMas project

    @theme      Lucidity
*}
<div class="main_video">
     
    {if $video->author_name eq 'youtube'}

        <div id="video-content" class="clearfix span-8">
                <object width="290" height="163">
                    <param value="http://www.youtube.com/v/{$video->videoid}" name="movie" />
                    <param value="true" name="allowFullScreen" />
                    <param value="always" name="allowscriptaccess">
                    <embed width="290" height="163" src="http://www.youtube.com/v/{$video->videoid}" />
                </object>
        </div>
        <div class="video-explanation">
            <h1><img src="{$params.IMAGE_DIR}utilities/share-black.png" alt="Share" /> {$video->title|clearslash|escape:'html'}</h1>
            <p class="in-subtitle">
                {$video->description|clearslash|escape:'html'}
            </p>
        </div>

    {else}
        <div id="video-content" class="clearfix span-8">
            <object width="290" height="163"><param name="allowfullscreen" value="true" />
                <param name="allowscriptaccess" value="always" /><param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id=9851483&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=ffffff&amp;fullscreen=1" />
                <embed src="http://vimeo.com/moogaloop.swf?clip_id={$video->videoid}&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=ffffff&amp;fullscreen=1" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="290" height="163"></embed>
            </object>
        </div>
        <div class="video-explanation">
            <h1><img src="{$params.IMAGE_DIR}utilities/share-black.png" alt="Share" /> {$video->title|clearslash|escape:'html'}</h1>
            <p class="in-subtitle">
                {$video->description|clearslash|escape:'html'}
            </p>
        </div>

    {/if}
</div><!-- .main-video -->