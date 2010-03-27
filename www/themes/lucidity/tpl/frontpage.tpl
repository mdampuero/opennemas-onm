{*
OpenNeMas project

@category   OpenNeMas
@package    OpenNeMas
@theme      Lucidity

@copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)

Smarty template: frontpage.tpl
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
*}



{include file="module_head.tpl"}

    {* publicidad interstitial *}
    {insert name="intersticial" type="50"}

    {include file="widget_ad_top.tpl"}

    <div class="wrapper clearfix">
        <div class="container clearfix span-24">

            <div id="header" class="">

               {include file="frontend_header.tpl"}

               {include file="frontend_menu.tpl"}

            </div>

            <div id="main_content" class="span-24">

                {include file="widget_breaking_news.tpl"}

                <div class="span-24">
                    <div class="layout-column first-column span-8">
                        <div>

                            {renderplaceholder items=$column tpl='frontpage_article_head.tpl' placeholder="placeholder_0_0"}

                            <hr class="new-separator"/>

                            {renderplaceholder items=$column tpl='frontpage_article.tpl' placeholder="placeholder_0_1"}

                            {*<hr class="new-separator"/>*}

                            {include file="widget_ad_button.tpl" type='3'}

                            {*<hr class="new-separator"/>*}

                            {renderplaceholder items=$column tpl='frontpage_article.tpl' placeholder="placeholder_0_2"}

                        </div>
                    </div>
                    <div class="layout-column middle-column span-8">
                        <div class="border-dotted">

                            {renderplaceholder items=$column tpl='frontpage_article_head.tpl' placeholder="placeholder_1_0"}

                            {*<hr class="new-separator"/>*}

                            {renderplaceholder items=$column tpl='frontpage_article.tpl' placeholder="placeholder_1_1"}

                            {*<hr class="new-separator"/>*}

                            {renderplaceholder items=$column tpl='frontpage_article.tpl' placeholder="placeholder_1_2"}

                            {include file="widget_ocio.tpl"}
                        </div>
                    </div>
                    <div class="layout-column last-column last span-8">
                        {renderplaceholder items=$column tpl='frontpage_article_head.tpl' placeholder="placeholder_2_0"}

                        {*<hr class="new-separator"/>*}

                        {renderplaceholder items=$column tpl='frontpage_article.tpl' placeholder="placeholder_2_1"}

                        {renderplaceholder items=$column tpl='frontpage_article.tpl' placeholder="placeholder_2_2"}
                        
                        {include file="widget_headlines.tpl"}

                        {include file="widget_ad_button.tpl" type='4'}

                        
                    </div>

                </div>
                <hr class="new-separator" />
                <div id="quick-news" class="span-24">

                    {include file="widget_social.tpl"}

                </div>

                <hr class="news-separator" />

                <div class="span-24">
                    <div class="span-12 layout-column">
                         {renderplaceholder items=$column tpl='frontpage_article_lateral.tpl' placeholder="placeholder_0_3"}
                    </div>
                    <div class="span-4 layout-column">
                       {renderplaceholder items=$column tpl='frontpage_article_image.tpl' placeholder="placeholder_1_3"}
                    </div>
                    
                    {include file="widget_headlines_past.tpl"}

                </div>

                <hr class="news-separator" />
                <div class="span-24">
                       {include file="widget_ad_separator.tpl"}
                </div>
                <div class="span-24">
                    <div class="layout-column first-column span-12">
                        {include file="widget_video.tpl" }
                    </div>
                    <div class="layout-column last-column last span-12">
                        {include file="widget_galery.tpl"}
                    </div>
                     
                </div>
                {if $category_name eq 'home'}
                    {include file="module_other_headlines.tpl"}
                   
                {/if}
            </div><!-- fin #main_content -->

        </div><!-- fin .container -->
    </div><!-- fin .wrapper -->

    <div class="wrapper clearfix">

        <div class="container clearfix span-24">
             {include file="frontend_footer.tpl"}

        </div><!-- fin .container -->

    </div>
   {literal}
        <script type="text/javascript">
            jQuery(document).ready(function(){
                $("#tabs").tabs();
                $("#tabs2").tabs();
            });
        </script>
    {/literal}
</body>
</html>
