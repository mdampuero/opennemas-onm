{extends file='base/video_layout.tpl'}

{block name='header-css'}
{$smarty.block.parent}
{/block}

{block name='header-js'}
{$smarty.block.parent}
{/block}

{block name="footer-js"}
{if !empty($video)} {literal}
      <script defer="defer" type="text/javascript">
        jQuery(document).ready(function(){
            $("#tabs").tabs();
            $('#tabs').height($('#video-content').height()+15);
            $lock=false;

            jQuery("div.share-actions").hover(
              function () {
                    if (!$lock){
                      $lock=true;
                      jQuery(this).children("ul").fadeIn("fast");
                    }
                    $lock=false;
              },
              function () {
                    if (!$lock){
                      $lock=true;
                      jQuery(this).children("ul").fadeOut("fast");
                    }
                    $lock=false;
              }
            );
        });

        function share(){
            if (jQuery('div.share-actions').children("ul").css('display')=='none'){
                jQuery('div.share-actions').children("ul").fadeIn("fast");
             }else{
                jQuery('div.share-actions').children("ul").fadeOut("fast");
             }

        }
    </script>
    {/literal}
    {else}
        {literal}
         <script type="text/javascript">
            jQuery(document).ready(function(){
                $("#tabs").tabs();
            });
        </script>
        {/literal}
    {/if}
    {include file="misc_widgets/widget_analytics.tpl"}
{/block}


{block name="content"}
    {if empty($video)}
        {insert name="intersticial" type="250" nocache}
        {include file="ads/widget_ad_top.tpl" type1='201' type2='202' nocache}
    {else}
        {insert name="intersticial" type="350"}
        {include file="ads/widget_ad_top.tpl" type1='301' type2='302' nocache}
    {/if}
    <div class="wrapper video clearfix">
        <div class="container clearfix span-24 last">
            <div id="header" class="">
                {include file="frontpage/frontpage_header.tpl"}
                {include file="frontpage/frontpage_menu.tpl"}
            </div>
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
                              {include file="module_comments.tpl" content=$contentId nocache}
                        </div>
                    </div>
                    <div class="layout-column last-column last span-8">
                        {include file="video/widget_video_most.tpl"}
                    </div>
                </div>

            </div><!-- fin #main_content -->
        </div><!-- fin .container -->
    </div><!-- fin .wrapper -->
{/block}

{block name="footer"}
<div id="wrapper-footer" class="wrapper clearfix">
    <div class="container clearfix span-24 last">
        <div id="footer" class="">
             {include file="ads/widget_ad_bottom.tpl" type1='9' type2='10'}
             {include file="frontpage/frontpage_footer.tpl"}
        </div><!-- fin .footer -->
    </div><!-- fin .container -->
</div>
{/block}
