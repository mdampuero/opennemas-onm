{extends file='base/frontpage_layout.tpl'}

{block name='meta'}
<title>{if !empty($category_real_name)}
{$category_real_name|clearslash|capitalize} -
{/if}
{if !empty($subcategory_real_name)}
{$subcategory_real_name|clearslash|capitalize} -
{/if}
Noticias de Galicia - {$smarty.const.SITE_TITLE} </title>
<meta name="keywords" content="{$smarty.const.SITE_KEYWORDS}" />
<meta name="description" content="{$smarty.const.SITE_DESCRIPTION}" />
{/block}

{block name='header-css'}
{$smarty.block.parent}
{/block}

{block name='header-js'}
{$smarty.block.parent}
{/block}

{block name='footer-js'}
    {$smarty.block.parent}
    {insert name="intersticial" type="50"}

    <script type="text/javascript">
        jQuery(document).ready(function(){
            $("#tabs").tabs();
            $("#tabs2").tabs();
        });
    </script>
    {include file="misc_widgets/widget_analytics.tpl"}
{/block}


{block name="content"}
    {include file="ads/widget_ad_top.tpl" type1='1' type2='2' nocache}
    <div class="wrapper clearfix">
        <div class="container clearfix span-24 last">
            <div id="header" class="">
               {include file="frontpage/frontpage_header.tpl"}
               {include file="frontpage/frontpage_menu.tpl"}
            </div>
            <div id="main_content" class="span-24">

                {include file="widget_breaking_news.tpl"}

                <div class="span-24">
                    <div class="layout-column first-column span-8">
                        <div>
                            {renderplaceholder items=$column tpl='frontpage/frontpage_article_head.tpl' placeholder="placeholder_0_0"}
                            {renderplaceholder items=$column tpl='frontpage/frontpage_article_head.tpl' placeholder="placeholder_0_1"}
                            {*<hr class="new-separator"/>*}
                            {include file="ads/widget_ad_button.tpl" type='3' nocache}
                            {*<hr class="new-separator"/>*}
                            {renderplaceholder items=$column tpl='frontpage/frontpage_article_head.tpl' placeholder="placeholder_0_2"}
                        </div>
                    </div>
                    <div class="layout-column middle-column span-8">
                        <div class="border-dotted">
                            {renderplaceholder items=$column tpl='frontpage/frontpage_article_head.tpl' placeholder="placeholder_1_0"}
                            {renderplaceholder items=$column tpl='frontpage/frontpage_article_head.tpl' placeholder="placeholder_1_1"}
                            {*<hr class="new-separator"/>*}
                            {renderplaceholder items=$column tpl='frontpage/frontpage_article_head.tpl' placeholder="placeholder_1_2"}
                            {include file="widget_ocio.tpl"}
                        </div>
                    </div>
                    <div class="layout-column last-column last span-8">
                        {renderplaceholder items=$column tpl='frontpage/frontpage_article_head.tpl' placeholder="placeholder_2_0"}
                        {renderplaceholder items=$column tpl='frontpage/frontpage_article_head.tpl' placeholder="placeholder_2_1"}
                        {renderplaceholder items=$column tpl='frontpage/frontpage_article_head.tpl' placeholder="placeholder_2_2"}
                        {include file="widget_galdic.tpl"}
                        {include file="widget_facebook_iframe.tpl"}
                        {include file="widget_headlines.tpl"}
                        <hr class="news-separator" />
                        {include file="ads/widget_ad_button.tpl" type='4' nocache}
                    </div>
                </div><!-- .span-24 -->

                <hr class="new-separator" />
                <div id="quick-news" class="span-24">
                    {include file="widget_social.tpl"}
                </div>
                <hr class="news-separator" />
                <div class="span-24">
                    <div class="span-12 layout-column">
                         {renderplaceholder items=$column tpl='frontpage/frontpage_article_lateral.tpl' placeholder="placeholder_0_3"}
                    </div>
                    <div class="span-4 layout-column">
                       {renderplaceholder items=$column tpl='frontpage/frontpage_article_image.tpl' placeholder="placeholder_1_3"}
                    </div>
                    {include file="widget_headlines_past.tpl"}
                </div>
                <hr class="news-separator" />
                {if !preg_match('/preview\.php/',$smarty.server.SCRIPT_NAME)}
                <div class="span-24">
                       {include file="ads/widget_ad_separator.tpl" nocache}
                </div>


                    <div class="span-24">
                        <div class="layout-column first-column span-12">
                            {include file="video/widget_video.tpl"}
                        </div>
                        <div class="layout-column last-column last span-12">
                            {include file="gallery/widget_gallery.tpl"}
                        </div>
                    </div>

                    {if $category_name eq 'home'}
                        {include file="module_other_headlines.tpl"}
                    {/if}
                {/if}
            </div><!-- fin #main_content -->

        </div><!-- fin .container -->

    </div><!-- fin .wrapper -->
{/block}

{block name="footer"}
<div id="wrapper-footer" class="wrapper clearfix">
    <div class="container clearfix span-24 last">
        <div id="footer" class="">
             {include file="ads/widget_ad_bottom.tpl" type1='9' type2='10' nocache}
             {include file="frontpage/frontpage_footer.tpl"}
        </div><!-- fin .footer -->
    </div><!-- fin .container -->
</div>
{/block}
