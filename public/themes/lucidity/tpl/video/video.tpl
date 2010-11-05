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
            {if empty($video)}
                {include file="video/video_frontpage.tpl"}
            {else}
                {include file="video/video_inner.tpl"}
            {/if}
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
