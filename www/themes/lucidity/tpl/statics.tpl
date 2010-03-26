{*
OpenNeMas project

@category   OpenNeMas
@package    OpenNeMas
@theme      Lucidity

@copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)

Smarty template: article.tpl
*}
 
{include file="module_head.tpl"}

    {include file="widget_ad_top.tpl"}

    <div class="wrapper clearfix">
        <div class="container clearfix span-24">
            
            <div id="header" class="">
               {include file="frontend_header.tpl"}
               {include file="frontend_menu.tpl"}
            </div>
            
            <div id="main_content" class="single-article span-24">
                
                <div class="in-big-title span-24">
                    <h1>{$page->title|clearslash}</h1>                
                </div>
                
                <div class="span-24">
                    <div class="layout-column first-column span-24">
                        <div class="border-dotted">
                            
                            <div class="content-article">                                
                                {$page->body}
                            </div><!-- /content-article -->
                            
                            <hr class="new-separator"/>
                            
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
    
</body>
</html>
