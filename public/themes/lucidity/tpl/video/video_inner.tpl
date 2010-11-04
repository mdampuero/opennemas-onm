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
                                {include file="utilities/widget_ratings.tpl"}
                        </div>
                </div><!-- /vote-block -->

                <div class="utilities span-6 last">
                    <div style="display: inline;" class="share-actions">
                          <a href="#" class="utilities-share" onclick="share();return false;" title="Compartir en las redes sociales"><img src="{$params.IMAGE_DIR}utilities/share-black.png"><span>Compartir en las redes sociales</span></a>
                          <ul style="display:none;">
                            <li><img alt="Share this post on Twitter" src="/themes/lucidity/images/utilities/toolsicon_anim.gif"> <a target="_blank"  onclick="share();" title="Compartir en Twiter" target="_blank" href="http://twitter.com/home?status={$video->title|clearslash} {$smarty.const.SITE_URL}{$video->permalink}">Send to Twitter</a></li>
                            <li><img alt="Share on Facebook" src="/themes/lucidity/images/utilities/facebook-share.gif"> <a title="Compartir en Facebook" href="http://www.facebook.com/sharer.php?u={$smarty.const.SITE_URL}{$video->permalink}&t={$video->title|clearslash}">Share on Facebook</a></li>
                          </ul>
                    </div>
                </div><!-- /utilities -->

            </div><!--fin toolbar -->

            <div id="main-video">
                <div id="video-content" class="clearfix span-16">
                      
                     {include file="video/widget_video_window.tpl" width="630" height="340"}

                </div>
                <div class="video-explanation">
                    <h1>{$video->title|clearslash|escape:'html'}</h1>
                    <p class="in-subtitle">{$video->description|clearslash|escape:'html'} </p>
                </div>
            </div><!-- .main-video -->

        </div>

        <div class="layout-column last-column opacity-reduced last span-8">
            {include file="video/widget_videos_lastest.tpl"}
            {include file="ads/widget_ad_video_button.tpl"}
        </div>
    </div><!-- span-24 -->

    <div class="span-24">
        <hr class="new-separator"/>
        <div class="span-8 toolbar-bottom ">
            {include file="utilities/widget_utilities_bottom_black.tpl"}
         </div><!--fin toolbar-bottom -->
        <div class=" span-9">
            <div class="vote-black">
                <div class="vote">
                       {include file="utilities/widget_ratings.tpl"}
                </div>
            </div><!-- /vote-bloc -->
        </div><!-- /utilities -->
        <div class="span-7 last ">
          
        </div><!-- /utilities -->
 
       

        <hr class="new-separator"/>
    </div>
    <div class="span-24 opacity-reduced">
        <div class="layout-column first-column span-16">
            <div class="border-dotted">
                  <a name="module_comments"></a>
                  {include file="module_comments.tpl" content=$video}
            </div>
        </div>
        <div class="layout-column last-column last span-8">
            {include file="video/widget_video_most.tpl"}
        </div>

    </div>

</div><!-- fin #main_content -->
