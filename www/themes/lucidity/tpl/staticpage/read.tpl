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

    <div class="wrapper clearfix">
        <div class="container clearfix span-24">
            
            <div id="header" class="">
                {include file="frontend_header.tpl"}
                
                {* include file="frontend_menu.tpl" *}
                <div id="main_menu" class="span-24 clearfix">
                    <div>
                    {frontend_menu}
                    </div>
                </div>
            </div>
            
            <div id="main_content" class="span-24">
                
                <div class="span-24">
                    {$gridContent}
                </div>                            
                
            </div><!-- fin #main_content -->
            
        </div><!-- fin .container -->
    </div><!-- fin .wrapper -->

    <div id="wrapper-footer" class="wrapper clearfix">

        <div class="container clearfix span-24">
            {include file="frontend_footer.tpl"}
        </div><!-- fin .container -->

    </div>

</body>
</html>
