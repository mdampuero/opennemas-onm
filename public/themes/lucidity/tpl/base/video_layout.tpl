{*
    OpenNeMas project
    @theme      Lucidity
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns:fb="http://www.facebook.com/2008/fbml" lang="es">
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <meta name="generator" content="OpenNemas - Open Source News Management System" />
    <meta name="google-site-verification" content="{$smarty.const.GOOGLE_SITE_VERIFICATION}" />
    <meta name="y_key" content="8bc0a34db8bce038">
    <meta name="author" content="OpenHost,SL" />
    <meta name="revisit-after" content="1 days" />
    <meta http-equiv="robots" content="index,follow" />
    <meta http-equiv="expires" content="0" />
    <meta http-equiv="last-modified" content="0" />
{if $smarty.request.action eq "inner"}
    <title>{$video->title|clearslash|default:''} -  {$video->category_title} - Vídeos de Galicia - {$smarty.const.SITE_TITLE}</title>
    <meta name="keywords" content="{$video->metadata|clearslash}" />
    <meta name="description" content="{$video->description|clearslash}" />
{else}
    <title>Videos destacados en {$category_real_name|clearslash|capitalize} {$subcategory_real_name|clearslash|capitalize} - Vídeos de Galicia - {$smarty.const.SITE_TITLE}</title>
    <meta name="keywords" content="video, {$smarty.const.SITE_KEYWORDS}" />
    <meta name="description" content="Videos: {$smarty.const.SITE_DESCRIPTION|strip_tags|clearslash}" />
{/if}

    {block name='header-css'}
    <link rel="stylesheet" href="{$params.CSS_DIR}screen.css" type="text/css" media="screen, projection">
    <link rel="stylesheet" href="{$params.CSS_DIR}print.css" type="text/css" media="print">
    <!--[if lt IE 8]><link rel="stylesheet" href="{$params.CSS_DIR}ie.css" type="text/css" media="screen, projection" /><![endif]-->
    <link rel="stylesheet" href="{$params.CSS_DIR}onm-mockup.css" type="text/css" media="screen,projection" />
    <link rel="stylesheet" href="{$params.CSS_DIR}jquery-ui-custom/jquery-ui.css" type="text/css" media="screen,projection">
    <link rel="stylesheet" href="{$params.CSS_DIR}parts/menu.css" type="text/css" media="screen,projection" />
    <link rel="stylesheet" href="{$params.CSS_DIR}parts/publi.css" type="text/css" media="screen,projection" />
    <link rel="stylesheet" href="{$params.CSS_DIR}parts/intersticial.css" type="text/css" media="screen,projection" />
    <style type="text/css">
        {$categories_styles}
    </style>
    <link rel="stylesheet" href="{$params.CSS_DIR}parts/article.css" type="text/css" media="screen,projection" />
    <link rel="stylesheet" href="{$params.CSS_DIR}parts/comments.css" type="text/css" media="screen,projection" />
    <link rel="stylesheet" href="{$params.CSS_DIR}video.css" type="text/css" media="screen,projection" />
    <link rel="stylesheet" href="{$params.CSS_DIR}parts/utilities.css" type="text/css" media="screen,projection" />
    <style type="text/css">
        #submenu ul li, div.toolbar-bottom a, div.utilities a, .transparent-logo{ background-color:#373737; }
    </style>
    {/block}
    
    {block name="header-js"}
    <script type="text/javascript" src="{$params.JS_DIR}jquery-1.4.1.min.js"></script>
    <script type="text/javascript" src="{$params.JS_DIR}jquery-ui.js"></script>
    <script type="text/javascript" src="{$params.JS_DIR}functions.js"></script>
    <script type="text/javascript"  src="{$params.JS_DIR}jquery.cycle.all.2.72.js"></script>
    <script type="text/javascript" src="{$params.JS_DIR}jquery.cookie.js"></script>
    <script type="text/javascript" src="{$params.JS_DIR}jquery.intersticial.js"></script>
    {/block}

    <link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="{$smarty.const.SITE_URL}rss/" />
</head>
<body>

    <div id="container" class="span-24">    
    {block name="content"}
    
    {/block}
    </div><!-- #container -->

    <div id="container" class="span-24">    
    {block name="footer"}
    {/block}
    </div>
    
    {block name="footer-js"}
    {/block}

</body>
</html>

