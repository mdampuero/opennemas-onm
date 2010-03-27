{*
OpenNeMas project

@category   OpenNeMas
@package    OpenNeMas
@theme      Lucidity

@copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)

Smarty template: gallery.tpl
*}

{include file="module_head.tpl"}
 
{include file="widget_ad_top.tpl"}

<div class="wrapper clearfix">
    <div class="container clearfix span-24">

        <div id="header" class="">

           {include file="frontend_header.tpl"}

           {include file="frontend_menu.tpl"}

        </div>

    <div id="main_content" class="video single-article span-24">
        <div class="content-gallery span-24 clearfix">
             <div class="wrapper-gallery span-22">
                    <!-- Start Advanced Gallery Html Containers -->
                    <div id="gallery" class="content span-14">
                        <div id="controls" class="controls"></div>
                            <div class="slideshow-container">
                                <div id="loading" class="loader"></div>
                                <div id="slideshow" class="slideshow"></div>
                            </div>
                        <div id="caption" class="caption-container"></div>
                    </div>
                    <div id="thumbs" class="navigation span-8"> minis
                        <ul class="thumbs noscript">
                            {foreach key=k item=photo from=$albumPhotos2}
                                 {include file="widget_gallery_mini.tpl" photoData=$photo.photo}
                            {/foreach}
                    
                        </ul>
                    </div>
                    <!-- End Advanced Gallery Html Containers -->
             </div>               

            <div class="wrapper-gallery-utilities">

                <hr class="new-separator"/>

                <div class="span-8  vert-separator toolbar-bottom ">

                    {include file="widget_utilities_bottom_black.tpl"}
                </div><!--fin toolbar-bottom -->
                <div class="vote-block  vert-separator span-12">
                   <div class="vote">
                        {include file="widget_ratings.tpl"}
                  </div>
                </div>
                <div class=" span-4 last">
                     
                </div>


               

                <hr class="new-separator"/>
            </div>


        </div><!-- fin content-gallery -->

        <div class="span-24">
            <div class="layout-column first-column span-16">
              <div class="border-dotted">
          
                {include file="module_comments.tpl" content=$album}
              </div>
            </div>
            <div class="layout-column last span-8">
                {include file="widget_headlines_past.tpl"}
            </div>
          </div><!-- fin #span-24-->
        </div><!-- fin #main_content -->

      </div><!-- fin .container -->
    </div><!-- fin .wrapper -->


    <div class="wrapper clearfix">

        <div class="container clearfix span-24">
             {include file="frontend_footer.tpl"}

        </div><!-- fin .container -->

    </div>
    {include file="gallery_module_script.tpl"}
</body>
</html>
