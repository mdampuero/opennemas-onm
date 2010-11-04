{*
OpenNeMas project
@category   OpenNeMas
@package    OpenNeMas
@theme      Clarity
@copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
Smarty template: article.tpl
*}
{include file="module_head.tpl"}
    
<div id="container" class="span-24">
    
    {* publicidad interstitial *}
    {*insert name="intersticial" type="50"*}
    {include file="ads/widget_ad_top.tpl"}

    <div class="wrapper clearfix">
        <div class="container clearfix span-24 last">
            <div id="header" class="clearfix span-24">
                {include file="frontpage/frontpage_header.tpl"}
                {include file="frontpage/frontpage_menu.tpl"}
            </div>
            <div id="main_content" class="single-article span-24">

            <div class="column1resultSearch">
                <div id="cse-search-results" style="padding:20px"></div>

                <script type="text/javascript">
                    var googleSearchIframeName = "cse-search-results";
                    var googleSearchFormName = "cse-search-box";
                    var googleSearchFrameWidth = 950;
                    var googleSearchDomain = "www.google.es";
                    var googleSearchPath = "/cse";
                </script>

                <script type="text/javascript" src="http://www.google.com/afsonline/show_afs_search.js"></script>
            </div>
                 
            </div><!-- fin #main_content -->
        </div><!-- fin .container -->
    </div><!-- fin .wrapper -->
    <div class="wrapper clearfix">
        <div class="container clearfix span-24 last">
            <div id="footer" class="">
                {include file="ads/widget_ad_bottom.tpl"}
                {include file="frontpage/frontpage_footer.tpl"}
            </div><!-- fin .footer -->
        </div><!-- fin .container -->
    </div>
</div><!-- #container -->

    {include file="misc_widgets/widget_analytics.tpl"}
</body>
</html>