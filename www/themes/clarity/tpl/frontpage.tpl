{*
OpenNeMas project

@category   OpenNeMas
@package    OpenNeMas
@theme      Clarity

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

            <div id="header" class="clearfix span-24">

                {include file="frontend_logo.tpl"}
               
                {include file="frontend_menu.tpl"}
                
            </div>

            <div id="main_content" class="span-24">
                <div class="span-24">
                    <div class="layout-column first-column span-12">
                        <div>
                            {renderplaceholder items=$column tpl='frontpage_article_head.tpl' placeholder="placeholder_0_0"}
                            <div class="span-12">
                                <div class="span-6">

                                    {renderplaceholder items=$column tpl='frontpage_article_big.tpl' placeholder="placeholder_0_1"}

                                    {renderplaceholder items=$column tpl='frontpage_article_little.tpl' placeholder="placeholder_0_2"}

                                </div>
                                <div class="span-6 last">
                                    {renderplaceholder items=$column tpl='frontpage_article_big.tpl' placeholder="placeholder_1_1"}

                                    {renderplaceholder items=$column tpl='frontpage_article_little.tpl' placeholder="placeholder_1_2"}

                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="layout-column middle-column span-5 border-dotted">
                        <div>
                            <div class="author-highlighted">
                                <h3 >Autores destacados</h3>
                                {include file="frontpage_article_author.tpl"}
                            </div>

                            <hr class="new-separator" />

                            <div class="middle-column-frontpage">
                                {renderplaceholder items=$column tpl='frontpage_article_middle_big.tpl' placeholder="placeholder_2_1"}

                                {include file="widget_ad_adsense.tpl"}

                                {renderplaceholder items=$column tpl='frontpage_article_middle_little.tpl' placeholder="placeholder_2_2"}
                                
                            </div>

                        </div>
                    </div><!-- fin -->
                    <div class="layout-column last-column last span-7">
                      {include file="widget_search.tpl"}

                      {include file="widget_frontend_video.tpl" video=$video[1]}
                       
                      {include file="widget_ad_lateral.tpl" type='3'}

                      {include file="widget_headlines.tpl"}
                    </div>

                </div>
 
                <hr class="new-separator">

                <div class="span-24">
                       {include file="widget_ad_separator.tpl"}
                </div>

                <div class="span-24">
                    <div class="layout-column first-column span-12">
                        {include file="widget_gallery.tpl" }
                    </div>
                    <div class="layout-column last-column last span-12">
                        {include file="widget_video.tpl"}
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
	