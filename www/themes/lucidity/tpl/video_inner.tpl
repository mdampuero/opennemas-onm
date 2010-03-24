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
                    <ul>
                        <li><img src="{$params.IMAGE_DIR}utilities/share-black.png" alt="Share" /></li>
                    </ul>
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
        <div class="span-24 toolbar-bottom ">
            {include file="widget_utilities_bottom_black.tpl"}
            <div class=" span-7">
                <div class="vote-black">
                    <div class="vote vert-separator">
                           {include file="widget_ratings.tpl"}
                    </div>
                </div><!-- /vote-bloc -->
            </div><!-- /utilities -->
            <div class="span-9 last ">
                {include file="widget_ad_button.tpl"}
            </div><!-- /utilities -->

        </div><!--fin toolbar-bottom -->

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
