{*
    OpenNeMas project
    @category   OpenNeMas
    @package    OpenNeMas
    @theme      Lucidity
    @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
    Smarty template: gallery.tpl
*}
{include file="module_head.tpl"}

{*insert name="intersticial" type="50"*}

    <div id="container" class="span-24">
        {*include file="ads/widget_ad_top.tpl" type1='1' type2='2' *}
        <div class="wrapper clearfix">
            <div class="container clearfix span-24 last">
                    <div id="header" class="">
                       {include file="frontpage/frontpage_header.tpl"}
                       {include file="frontpage/frontpage_menu.tpl"}
                    </div>
                    <div id="main_content" class="single-article span-24 last">
                        <h1><img src="{$params.IMAGE_DIR}/sections/logo-gallery-frontpage.png" alt="La informaci&oacute;n en im&aacute;genes"> </h1>
                        <script type="application/x-javascript">
                            {literal}
                                $('div.big-album div.description span').css('color','Red !important');
                            {/literal}
                        </script>
                        
                        <style type="text/css">
                        {literal}
                            #main_content h1{
                                margin-top:-25px;
                                padding-top:10px;
                                font-family:Georgia;
                                color:#666;
                                background: url({/literal}{$params.IMAGE_DIR}/sections/back-gallery-frontpage.png{literal}) bottom right no-repeat;
                                background-position:660px -80px;
                            }
                            div.gallery-frontpage-top-widget{
                                margin-bottom:20px;
                            }
                            div.gallery-frontpage-top-widget div.top-lateral-section{
                                
                            }
                            div.gallery-frontpage-top-widget div.big-album,
                            div.gallery-frontpage-top-widget div.publi,
                            div.gallery-frontpage-top-widget div.little-widget{
                                background:#505050;
                                position:relative;
                            }
                            div.gallery-frontpage-top-widget div.big-album
                            {
                                width:590px;
                                height:367px;
                                overflow:hidden;
                            }
                            div.gallery-frontpage-top-widget div.big-album img{
                                width:590px;
                            }
                            
                            div.gallery-frontpage-top-widget div.big-album div.description{
                                position:absolute;
                                bottom:0;
                                left:0;
                                width:100%;
                            }
                            div.gallery-frontpage-top-widget div.big-album div.description span.transparent{
                                width:100%;
                                height:100%;
                                filter:alpha(opacity=50); 
                                -moz-opacity:0.5; 
                                -khtml-opacity: 0.5; 
                                opacity: 0.5;
                                background:Black;
                                position:absolute;
                                bottom:0;
                                left:0;
                                z-index:8;
                            }
                            div.gallery-frontpage-top-widget div.big-album div.description span.content{
                                padding:15px;
                                display:block;
                                color:White !important;
                                font-family:Arial;
                                font-size:13px;
                                z-index:10;
                            }
                            div.gallery-frontpage-top-widget div.widget-lastest-tab{
                                width:370px;
                                height:365px;
                                border-top:10px solid #eeee !important;
                            }
                            
                            div.gallery-frontpage-top-widget div.publi{
                                width:370px;
                                height:155px;
                                margin-bottom:10px
                            }
                            div.gallery-frontpage-top-widget div.little-widget{
                                width:180px;
                                height:200px;
                            }
                            div#tabs2 div.ui-tabs-panel{
                                max-height:262px !important;
                            }
                        {/literal}
                        </style>
                        <div class="span-24 last gallery-frontpage-top-widget clearfix">
                            <div class="span-14 big-album">
                                
                                <a href="{$firstalbum->permalink}" title="Ver la galeria: {$firstalbum->title}">
                                    <img src="{$smarty.const.MEDIA_URL}/media/images/{$firstalbum->cover}" alt="">
                                    <div class="description">
                                        <span class="transparent">&nbsp;</span>
                                        <span class="content"><strong>{$firstalbum->created|date_format:"%A, %e de %B de %Y"}</strong>:
                                        {$firstalbum->title}</span>
                                    </div>
                                </a>
                            </div>
                            <div class="span6 last top-lateral-section">
                                {include file="widget_headlines_past.tpl"}
                                <!--<div class="span-6 publi last">-->
                                <!--    {include file="ads/widget_ad_lateral.tpl"}  -->
                                <!--</div>-->
                                <!--<div class="span-3 little-widget">-->
                                <!--    other-->
                                <!--</div>-->
                                <!--<div class="span-3 last little-widget last">-->
                                <!--    other-->
                                <!--</div>-->
                            </div>
                        </div>
                    
                        {include file="gallery/widget_10_2_rows.tpl" galleries=$albums}
                        
                        <div class="span-24">
                            <div class="layout-column first-column span-16">
                                <div class="border-dotted">          
                                  
                                </div>
                            </div>
                            <div class="layout-column last span-8">
                                {*include file="gallery/widget_gallerys_lastest.tpl"*}
                            </div>
                        </div><!-- fin #span-24-->        
                    </div><!-- fin #main_content -->
            </div><!-- fin .container -->
        </div><!-- fin .wrapper -->
        <div class="wrapper clearfix">
            <div class="container clearfix span-24 last">
                <div id="footer" class="">
                     {*include file="ads/widget_ad_bottom.tpl" type1=9 type2=10 *}
                     {include file="frontpage/frontpage_footer.tpl"}
                </div><!-- fin .footer -->
            </div><!-- fin .container -->
        </div>
    </div><!-- #container -->
    {include file="gallery/gallery_module_script.tpl"}
    {include file="misc_widgets/widget_analytics.tpl"}
</body>
</html>