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


            <div id="main_content" class="single-article span-24 portada-videos">

                <div class="layout-column first-column span-8 featured-videos">
                    <h3>{if !empty($subcategory_real_name)} {$subcategory_real_name}{else} {$category_real_name}{/if}:: vídeos </h3>
                    <hr class="new-separator"/>
                        {* step para que no coincidad con los del widget incategory*}
                        {section name=i loop=$videos step=2}
                            {include file="video/widget_video_viewer.tpl" video=$videos[i]}
                        {/section}
                </div>
                <div class="layout-column last-column last span-16 ">
                    <div class="span-16" id="videos_incategory">
                       {include file="video/widget_video_incategory.tpl"}

                    </div>
                    <div class="span-16" id="videos_more">
                       {include file="video/widget_video_more.tpl"}
                    </div>
                    <div class="span-16">
                        <div class="span-8 opacity-reduced">
                            <div class="article-comments">
                                    <div class="title-comments"><h3><span>Más vídeos</span></h3></div>
                            </div>
                           {include file="video/widget_videos_lastest.tpl"}
                        </div>
                        <div class="span-8 last">
                            {include file="module_video_comments.tpl"}
                        </div>

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
