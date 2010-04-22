{*
    OpenNeMas project

    @theme      Clarity

*}


    <div class="video-preview">
        <h3 class="widget-title">Vídeo <img src="{$params.IMAGE_DIR}bullets/bars-red.png" /></h3>
            <div class="video-content clearfix span-8 ">
                {if $video->author_name eq 'youtube'}
                    <a href="{$video->permalink}" title="{$video->title|clearslash|escape:'html'}">
                       <object width="270" height="189">
                            <param value="http://www.youtube.com/v/{$video->videoid}" name="movie" />
                            <param value="true" name="allowFullScreen" />
                            <param value="always" name="allowscriptaccess">
                            <embed width="270" height="189" src="http://www.youtube.com/v/{$video->videoid}" />
                        </object>
                    </a>
                 {else}
                  <a href="{$video->permalink}" title="{$videos[0]->title|clearslash|escape:'html'}">
                    <object width="270" height="189">
                        <param name="allowfullscreen" value="true" />
                        <param name="allowscriptaccess" value="always" />
                        <param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id={$video->videoid}&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=00ADEF&amp;fullscreen=1" />
                        <embed src="http://vimeo.com/moogaloop.swf?clip_id={$video->videoid}&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=00ADEF&amp;fullscreen=1" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="270" height="189"></embed>
                    </object>
                    </a>
                  {/if}
            </div>
            <div class="video-explanation">
                  <h3><a href="{$video->permalink}" title="{$video->title|clearslash|escape:'html'}">
                       {$video->title|clearslash} </a>
                  </h3>
                  <p class="in-subtitle">  {$video->description|clearslash}</p>
            </div>
    </div><!-- .main-video -->

 
 