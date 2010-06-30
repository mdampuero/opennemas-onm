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

    {include file="widget_ad_top.tpl"}

    <div class="wrapper clearfix">
        <div class="container clearfix span-24">

            <div id="header" class="clearfix span-24">

                {include file="frontend_logo.tpl"}
               
                {* include file="frontend_menu.tpl" *}
                <div id="main_menu" class="span-24 clearfix">
                    <div>
                    {frontend_menu}
                    </div>
                </div>
                
            </div>

            <div id="main_content" class="span-24">
                
                <div class="span-24">
                    {$grid_content}
                </div>
 
                <hr class="new-separator" />

                <div class="span-24">
                       {include file="widget_ad_separator.tpl"}
                </div>

                <div class="span-24">
                    <div class="layout-column first-column span-12">
                        {include file="widget_gallery.tpl"}
                    </div>
                    <div class="layout-column last-column last span-12">
                        {include file="widget_video.tpl"}
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

    <script type="text/javascript">
        jQuery(document).ready(function(){
            $("#tabs").tabs();
             $("#tabs2").tabs();
        });                
    </script>

  </body>
</html>	