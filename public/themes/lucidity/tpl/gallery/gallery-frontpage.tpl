{extends file='base/frontpage_layout.tpl'}


{block name='meta-css'}
    <title>{$album->title|clearslash|escape:'html'|default:''} - {$category_real_name|clearslash|capitalize} {$subcategory_real_name|clearslash|capitalize} - Álbumes de Galicia - {$smarty.const.SITE_TITLE}</title>
    <meta name="keywords" content="{$album->metadata|clearslash|escape:'html'}" />
    <meta name="description" content="{$album->description|clearslash|escape:'html'}" />
{/block}

{block name='header-css'}
{$smarty.block.parent}
    <link rel="stylesheet" href="{$params.CSS_DIR}gallery.css" type="text/css" media="screen,projection">
    <link rel="stylesheet" href="{$params.CSS_DIR}parts/article.css" type="text/css" media="screen,projection" />
    <link rel="stylesheet" href="{$params.CSS_DIR}parts/comments.css" type="text/css" media="screen,projection" />
    <link rel="stylesheet" href="{$params.CSS_DIR}parts/utilities.css" type="text/css" media="screen,projection" />
    <link rel="stylesheet" href="{$params.CSS_DIR}jquery-ui-custom/jquery-ui.css" type="text/css" media="screen,projection">
    <!--<link rel="stylesheet" href="{$params.JS_DIR}jquery.ad-gallery.1.2.2/jquery.ad-gallery.css" type="text/css" media="screen,projection">-->
    <link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}/jcarousel/jquery.jcarousel.css" />
    <link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}/jcarousel/skin.css" />
    <link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}/jcarousel/galleries-toolbar.css" />
    <style type="text/css">
      #main_menu, .transparent-logo {
        background-color:#ffbc21;
      }
      div.toolbar-bottom a, div.utilities a{
        background-color:#373737;
      }
    </style>
    <style type="text/css">

        #main_content h1{
            margin-top:-25px;
            padding-top:10px;
            font-family:Georgia;
            color:#666;
            background: url({$params.IMAGE_DIR}/sections/back-gallery-frontpage.png) bottom right no-repeat;
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
    </style>
{/block}

{block name="footer-js"}
    {include file="misc_widgets/widget_analytics.tpl"}
    {include file="gallery/gallery_module_script.tpl"}
{/block}

{block name='content'}
<div class="wrapper clearfix">
    <div class="container clearfix span-24 last">
        <div id="header" class="">
           {include file="frontpage/frontpage_header.tpl"}
           {include file="frontpage/frontpage_menu.tpl"}
        </div>
        <div id="main_content" class="single-article span-24 last">
            <h1><img src="{$params.IMAGE_DIR}/sections/logo-gallery-frontpage.png" alt="La informaci&oacute;n en im&aacute;genes"> </h1>
            <script type="application/x-javascript">
                $('div.big-album div.description span').css('color','Red !important');
            </script>

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
                </div>
            </div>

            {include file="gallery/widget_10_2_rows.tpl" galleries=$albums}

        </div><!-- fin #main_content -->
    </div><!-- fin .container -->
</div><!-- fin .wrapper -->
{/block}

{block name="footer"}
<div class="wrapper clearfix">
    <div class="container clearfix span-24 last">
        <div id="footer" class="">
             {include file="ads/widget_ad_bottom.tpl" type1='9' type2='10' nocache}
             {include file="frontpage/frontpage_footer.tpl"}
        </div><!-- fin .footer -->
    </div><!-- fin .container -->
</div><!-- .wrapper -->
{/block}
