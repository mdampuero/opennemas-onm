{*
    OpenNeMas project

    @category   OpenNeMas
    @package    OpenNeMas
    @theme      Lucidity

    @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)

    Smarty template: frontpage.tpl
*}
<div id="main_content" class="single-article span-24">
    <div class="span-24">
        <div class="layout-column first-column span-16">
            <div class="span-16 toolbar">
                <div class="vote-block span-10 clearfix">
                        <div class="vote">
                                {include file="widget_ratings.tpl"}
                        </div>
                </div><!-- /vote-block -->

                <div class="utilities span-6 last">
                    <div style="display: inline;" class="share-actions">
                          <a href="#" class="utilities-share" onclick="share();return false;" title="Compartir en las redes sociales"><span>Compartir en las redes sociales</span></a>
                          <ul style="display:none;">
                            <li><img alt="Share this post on Twitter" src="/themes/lucidity/images/utilities/toolsicon_anim.gif"> <a title="Compartir en Twiter" target="_blank" href="http://twitter.com/home?status={if !empty($article->title_int)}{$article->title_int|clearslash}{else}{$article->title|clearslash}{/if} {$smarty.const.SITE_URL}{$article->permalink}">Send to Twitter</a></li>
                            <li><img alt="Share on Facebook" src="/themes/lucidity/images/utilities/facebook-share.gif"> <a title="Compartir en Facebook" href="http://www.facebook.com/sharer.php?u={$smarty.const.SITE_URL}{$article->permalink}&t={if !empty($article->title_int)}{$article->title_int|clearslash}{else}{$article->title|clearslash}{/if}">Share on Facebook</a></li>
                          </ul>
                    </div>
                </div><!-- /utilities -->

            </div><!--fin toolbar -->

            <div id="main-video">
                <div id="video-content" class="clearfix span-16">
                     {if $video->author_name eq 'youtube'}
                         <object width="601" height="338">
                            <param value="http://www.youtube.com/v/{$video->videoid}" name="movie" />
                            <param value="true" name="allowFullScreen" />
                            <param value="always" name="allowscriptaccess">
                            <embed width="601" height="338" src="http://www.youtube.com/v/{$video->videoid}" />
                        </object>
                      {else}
                        <object width="601" height="338">
                            <param name="allowfullscreen" value="true" />
                            <param name="allowscriptaccess" value="always" />
                            <param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id={$video->videoid}&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=00ADEF&amp;fullscreen=1" />
                            <embed src="http://vimeo.com/moogaloop.swf?clip_id={$video->videoid}&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0&amp;show_portrait=0&amp;color=00ADEF&amp;fullscreen=1" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="601" height="338"></embed>
                        </object>
                      {/if}
                </div>
                <div class="video-explanation">
                    <h1>{$video->title|clearslash|escape:'html'}</h1>
                    <p class="in-subtitle">{$video->description|clearslash|escape:'html'} </p>
                </div>
            </div><!-- .main-video -->

        </div>

        <div class="layout-column last-column opacity-reduced last span-8">
            {include file="widget_videos_lastest.tpl"}
        </div>
    </div><!-- span-24 -->

    <div class="span-24">
        <hr class="new-separator"/>
        <div class="span-8 toolbar-bottom ">
            {include file="widget_utilities_bottom_black.tpl"}
         </div><!--fin toolbar-bottom -->
        <div class=" span-9">
            <div class="vote-black">
                <div class="vote vert-separator">
                       {include file="widget_ratings.tpl"}
                </div>
            </div><!-- /vote-bloc -->
        </div><!-- /utilities -->
        <div class="span-7 last ">
          {include file="widget_ad_video_button.tpl"}
        </div><!-- /utilities -->
 
       

        <hr class="new-separator"/>
    </div>
    <div class="span-24 opacity-reduced">
        <div class="layout-column first-column span-16">
            <div class="border-dotted">

                  {include file="module_comments.tpl" content=$video}
            </div>
        </div>
        <div class="layout-column last-column last span-8">
            {include file="widget_video_most.tpl"}
        </div>

    </div>

</div><!-- fin #main_content -->
