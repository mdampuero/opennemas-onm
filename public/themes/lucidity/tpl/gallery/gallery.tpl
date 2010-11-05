{*
    OpenNeMas project
    @category   OpenNeMas
    @package    OpenNeMas
    @theme      Lucidity
    @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
    Smarty template: gallery.tpl
*}
{include file="module_head.tpl"}

{insert name="intersticial" type="50"}

<div id="container" class="span-24">

    {include file="ads/widget_ad_top.tpl" type1='1' type2='2'}

    <div class="wrapper clearfix">
        <div class="container clearfix span-24 last">
            <div id="header" class="">
               {include file="frontpage/frontpage_header.tpl"}
               {include file="frontpage/frontpage_menu.tpl"}
            </div>
            <div id="main_content" class="single-article span-24 last">
                <div class="content-gallery span-24">
                    <div class="span-24">
                        {include file="gallery/gallery-slideshow.tpl" gallery=$album galleryPhotos=$albumPhotos2}
                    </div>
                </div><!-- fin content-gallery -->

                <div class="span-24">
                    <div class="layout-column first-column span-16">
                      <div class="border-dotted">
                        {include file="module_comments.tpl" content=$album nocache}
                      </div>
                    </div>
                    <div class="layout-column last span-8">
                        {include file="gallery/widget_gallerys_lastest.tpl"}
                    </div>
                  </div><!-- fin #span-24-->
                </div><!-- fin #main_content -->
          </div><!-- fin .container -->
        </div><!-- fin .wrapper -->
        <div class="wrapper clearfix">
            <div class="container clearfix span-24 last">
                <div id="footer" class="">
                     {include file="ads/widget_ad_bottom.tpl" type1=9 type2=10}
                     {include file="frontpage/frontpage_footer.tpl"}
                </div><!-- fin .footer -->
            </div><!-- fin .container -->
        </div>
    </div><!-- #container -->
    {include file="gallery/gallery_module_script.tpl"}
    {include file="misc_widgets/widget_analytics.tpl"}
</body>
</html>
