{*
    OpenNeMas project

    @category   OpenNeMas
    @package    OpenNeMas
    @theme      Lucidity

    @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)

    Smarty template: frontpage.tpl
*}
    <!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
    {include file="module_head.tpl"}

    <body>
        {include file="widget_ad_top.tpl"}

        <div class="wrapper video clearfix">
            <div class="container clearfix span-24">
                <div id="header" class="">
                
                    {include file="frontend_header.tpl"}

                    {include file="frontend_menu.tpl"}

                </div>

                <div id="main_content" class="single-article span-24 portada-videos">

                    <div class="layout-column first-column span-8 featured-videos">
                            <h3>Featured vídeos</h3>
                            <hr class="new-separator"/>

                            {include file="widget_video_viewer.tpl"}
                            {include file="widget_video_viewer.tpl"}
                            {include file="widget_video_viewer.tpl"}


                        </div>
                        <div class="layout-column last-column last span-16 ">
                            <div class="span-16">
                               {include file="widget_videos_category.tpl"}

                            </div>
                            <div class="span-16">
                               {include file="widget_other_videos.tpl"}
                            </div>
                            <div class="span-16">
                                <div class="span-8 opacity-reduced">
                                    <div class="article-comments">
                                            <div class="title-comments"><h3><span>Nuevos vídeos</span></h3></div>
                                    </div>
                                    <img src="images/lastest.png" />
                                </div>
                                <div class="span-8 last">
                                    {include file="module_video_comments.tpl"}
                                </div>

                            </div>
                        </div>
                    </div><!-- fin #main_content -->

            </div><!-- fin .container -->
    </div><!-- fin .wrapper -->

    <div class="wrapper clearfix">

        <div class="container clearfix span-24">
              {include file="frontend_footer.tpl"}
        </div><!-- fin .container -->
    </div>
    
    <script type="text/javascript" src="javascripts/jquery-1.4.1.min.js"></script>
    <script type="text/javascript" src="javascripts/jquery-ui.js"></script>
    <script type="text/javascript" src="javascripts/functions.js"></script>
  </body>
</html>
