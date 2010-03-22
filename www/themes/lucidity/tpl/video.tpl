{*
    OpenNeMas project

    @category   OpenNeMas
    @package    OpenNeMas
    @theme      Lucidity

    @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)

    Smarty template: video frontpage.tpl
*}

    {include file="module_head.tpl"}


    {include file="widget_ad_top.tpl"}

    <div class="wrapper video clearfix">
        <div class="container clearfix span-24">
            <div id="header" class="">

                {include file="frontend_header.tpl"}

                {include file="frontend_menu.tpl"}

            </div>
            {if empty($video)}
                {include file="video_frontpage.tpl"}
            {else}
                {include file="video_inner.tpl"}
            {/if}


        </div><!-- fin .container -->
    </div><!-- fin .wrapper -->

    <div class="wrapper clearfix">

        <div class="container clearfix span-24">
              {include file="frontend_footer.tpl"}
        </div><!-- fin .container -->
    </div>

    <script type="text/javascript" src="{$params.JS_DIR}jquery-1.4.1.min.js"></script>
    <script type="text/javascript" src="{$params.JS_DIR}jquery-ui.js"></script>
    <script type="text/javascript" src="{$params.JS_DIR}functions.js"></script>

  {if !empty($video)} {literal}
  
    <script type="text/javascript">
        jQuery(document).ready(function(){
            $("#tabs").tabs();
            $('#tabs').height($('#video-content').height()+15);
            $('#').height($('#video-content').height()+15);
        });
    </script>
    {/literal}
  {/if}

  </body>
</html>
