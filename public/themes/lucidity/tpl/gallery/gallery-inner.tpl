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
{/block}

{block name="footer-js"}
    {include file="misc_widgets/widget_analytics.tpl"}
    {include file="gallery/gallery_module_script.tpl"}
{/block}



{block name='content'}
    {insert name="intersticial" type="50"}
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
                        <div class="border-dotted">{include file="module_comments.tpl" contentId=$contentId nocache} </div>
                    </div>
                    <div class="layout-column last span-8">
                        {include file="gallery/widget_gallerys_lastest.tpl"}
                    </div>
                </div><!-- fin #span-24-->
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
