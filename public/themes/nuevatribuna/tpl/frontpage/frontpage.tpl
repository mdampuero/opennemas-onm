{extends file='base/frontpage_layout.tpl'}

{block name='meta'}
    <title>{if !empty($category_real_name)}{$category_real_name|mb_capitalize|clearslash} - {/if}{if !empty($subcategory_real_name)} {$subcategory_real_name|mb_capitalize|clearslash} - {/if}{$smarty.const.SITE_TITLE}</title>
    <meta name="keywords" content="{$smarty.const.SITE_KEYWORDS}" />
    <meta name="description" content="{$smarty.const.SITE_DESCRIPTION}" />
{/block}


{block name='footer-js'}
    {$smarty.block.parent}
    {include file="internal_widgets/widget_google_analytics.tpl"}
{/block}

{block name="content"}
    <div class="container_ads">
        {include file="ads/ad_in_header.tpl" type1='1' type2='2' nocache}
    </div>
    <div class="wrapper clearfix">
        <div class="container container_with_border">

            <div id="header">
               {include file="base/partials/_frontpage_header.tpl"}
               {include file="base/partials/_frontpage_menu.tpl"}
            </div><!-- #header -->

            <div id="main_content" class="wrapper_content_inside_container span-24 {$category_name}">

                <div class="span-24">
                    <div class="wrapper-two-columns span-16 last">
                        <div class="layout-column wrapper-highlighted">
                            {renderplaceholder items=$column tpl='frontpage/frontpage_article_highlighted.tpl' placeholder="highlighted_0" cssclass="highlighted"}
                        </div>
                        <div class="layout-column span-9 first-column">
                            <div>
                                {renderplaceholder items=$column tpl='frontpage/frontpage_article_head.tpl' placeholder="placeholder_0_0" cssclass="top-firstcol"}
                                {include file="ads/ad_in_column.tpl" type=11 nocache}
                                {renderplaceholder items=$column tpl='frontpage/frontpage_article_head.tpl' placeholder="placeholder_0_1"}
                                {include file="ads/ad_in_column.tpl" type='12' nocache}
                                {renderplaceholder items=$column tpl='frontpage/frontpage_article_head.tpl' placeholder="placeholder_0_2"}
                            </div>
                        </div><!-- first-column -->
                        <div class="layout-column span-7 second-column ">
                            <div>
                                {renderplaceholder items=$column tpl='frontpage/frontpage_article_head.tpl' placeholder="placeholder_1_0" cssclass="top-secondcol"}
                                {include file="ads/ad_in_column.tpl" type='21' width=200 nocache}
                                {renderplaceholder items=$column tpl='frontpage/frontpage_article_head.tpl' placeholder="placeholder_1_1"}
                                {include file="ads/ad_in_column.tpl" type='22' nocache}
                                {renderplaceholder items=$column tpl='frontpage/frontpage_article_head.tpl' placeholder="placeholder_1_2"}
                            </div>
                        </div> <!--second-column-->
                    </div><!-- wrapper-two-columns -->
                    <div class="layout-column span-8 last third-column">
                        <div>
                            {renderplaceholder items=$column tpl='frontpage/frontpage_article_head.tpl' placeholder="placeholder_2_0" cssclass="top-thirdcol"}
                            {include file="ads/ad_in_column.tpl" type='31' nocache}
                            {renderplaceholder items=$column tpl='frontpage/frontpage_article_head.tpl' placeholder="placeholder_2_1"}
                            {include file="ads/ad_in_column.tpl" type='32' nocache}
                            {renderplaceholder items=$column tpl='frontpage/frontpage_article_head.tpl' placeholder="placeholder_2_2"}
                        </div>
                    </div><!--third-column-->
                </div><!-- .span-24 -->
                
                <br style="clear:both; margin-top:10px;">
                <hr class="news-separator" />
                
                {if $category_name eq 'home'}
                    <div id="quick-news" class="span-24">
                        {include file="internal_widgets/frontpage_3cols.tpl"}
                    </div>
                    <br style="clear:both">
                    <hr class="news-separator" />
                {/if}

                {include file="ads/ad_break_content.tpl" type1='3' type2='4' nocache}
                <hr class="news-separator" />

                <div class="span-24">
                    <div class="layout-column span-9 first-column">
                        <div>
                            {renderplaceholder items=$column tpl='frontpage/frontpage_article_head.tpl' placeholder="placeholder_0_3"}
                            {include file="ads/ad_in_column.tpl" type='14' nocache}
                            {renderplaceholder items=$column tpl='frontpage/frontpage_article_head.tpl' placeholder="placeholder_0_4"}
                            {include file="ads/ad_in_column.tpl" type='15' nocache}
                            {renderplaceholder items=$column tpl='frontpage/frontpage_article_head.tpl' placeholder="placeholder_0_5"}
                        </div>
                    </div><!-- first-column -->
                    <div class="layout-column span-7 second-column ">
                        <div class="border-dotted">
                            {renderplaceholder items=$column tpl='frontpage/frontpage_article_head.tpl' placeholder="placeholder_1_3"}
                            {include file="ads/ad_in_column.tpl" type='24' nocache}
                            {renderplaceholder items=$column tpl='frontpage/frontpage_article_head.tpl' placeholder="placeholder_1_4"}
                            {include file="ads/ad_in_column.tpl" type='25' nocache}
                            {renderplaceholder items=$column tpl='frontpage/frontpage_article_head.tpl' placeholder="placeholder_1_5"}
                        </div>
                    </div> <!--second-column-->
                    <div class="layout-column span-8 last third-column">
                            {renderplaceholder items=$column tpl='frontpage/frontpage_article_head.tpl' placeholder="placeholder_2_3"}
                            {include file="ads/ad_in_column.tpl" type='34' nocache}
                            {renderplaceholder items=$column tpl='frontpage/frontpage_article_head.tpl' placeholder="placeholder_2_4"}
                            {include file="ads/ad_in_column.tpl" type='35' nocache}
                            {renderplaceholder items=$column tpl='frontpage/frontpage_article_head.tpl' placeholder="placeholder_2_5"}
                    </div><!--third-column-->
                </div>
                <br style="clear:both">
                <br style="clear:both">
                <hr class="news-separator" />
                <div id="quick-news" class="span-24">
                    {include file="internal_widgets/frontpage_3cols_bottom.tpl"}
                </div>
                
            </div><!-- fin #main_content -->

        </div><!-- fin .container -->

    </div><!-- fin .wrapper -->
{/block}


{block name="footer"}
    <div class="container_ads">
        {include file="ads/ad_in_footer.tpl" type1='5' type2='6' nocache}
    </div>
<div id="wrapper-footer" class="wrapper clearfix">
    <div class="container clearfix span-24 last">
        <div id="footer" class="">
             {include file="base/partials/_frontpage_footer.tpl"}
        </div><!-- fin .footer -->
    </div><!-- fin .container -->
</div>
{/block}
