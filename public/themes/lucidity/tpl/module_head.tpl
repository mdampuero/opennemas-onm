{*
    OpenNeMas project
    @theme      Lucidity
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"> 
<html xmlns:fb="http://www.facebook.com/2008/fbml" lang="es">
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <meta name="generator" content="OpenNemas - Open Source News Management System" />
    <meta name="google-site-verification" content="WdDBf1JZkFkdDJPleY5n_xXpVlVb_eJS4zlRWb5tIeM" />
    <meta name="y_key" content="8bc0a34db8bce038">
    <meta name="author" content="OpenHost,SL" />
    <meta name="revisit-after" content="1 days" />
    <meta http-equiv="robots" content="index,follow" />
    <meta http-equiv="expires" content="0" />
    <meta http-equiv="last-modified" content="0" />
    <meta http-equiv="Refresh" content="900; url=http://{$smarty.server.SERVER_NAME}{$smarty.server.REQUEST_URI}" />
    <!--[if lt IE 8]><link rel="stylesheet" href="{$params.CSS_DIR}ie.css" type="text/css" media="screen, projection" /><![endif]-->
    <link rel="stylesheet" href="{$params.CSS_DIR}screen.css" type="text/css" media="screen, projection">
    <link rel="stylesheet" href="{$params.CSS_DIR}print.css" type="text/css" media="print">
    <link rel="stylesheet" href="{$params.CSS_DIR}onm-mockup.css" type="text/css" media="screen,projection" />
    <link rel="stylesheet" href="{$params.CSS_DIR}jquery-ui-custom/jquery-ui.css" type="text/css" media="screen,projection">
    <link rel="stylesheet" href="{$params.CSS_DIR}parts/menu.css" type="text/css" media="screen,projection" />
    <link rel="stylesheet" href="{$params.CSS_DIR}parts/publi.css" type="text/css" media="screen,projection" />
    <script type="text/javascript" src="{$params.JS_DIR}jquery-1.4.1.min.js"></script>
    <script type="text/javascript" src="{$params.JS_DIR}jquery-ui.js"></script>
    <script type="text/javascript" src="{$params.JS_DIR}functions.js"></script>
    <script type="text/javascript"  src="{$params.JS_DIR}jquery.cycle.all.2.72.js"></script>
    
    {if preg_match('/article\.php/',$smarty.server.SCRIPT_NAME) ||preg_match('/preview_content\.php/',$smarty.server.SCRIPT_NAME)} 
        <title>{$article->title|clearslash} - {$category_real_name|clearslash|capitalize} {$subcategory_real_name|clearslash|capitalize} - Noticias de Galicia - {$smarty.const.SITE_TITLE} </title>
        <meta name="keywords" content="{$article->metadata|clearslash}" />
        <meta name="description" content="{$article->summary|strip_tags|clearslash}" />
        <link rel="stylesheet" href="{$params.CSS_DIR}parts/article.css" type="text/css" media="screen,projection" />
        <link rel="stylesheet" href="{$params.CSS_DIR}parts/comments.css" type="text/css" media="screen,projection" />
        <link rel="stylesheet" href="{$params.CSS_DIR}parts/utilities.css" type="text/css" media="screen,projection" />
        <link rel="stylesheet" href="{$params.CSS_DIR}video-js.css" type="text/css" media="screen,projection" />
        <link rel="stylesheet" href="{$params.JS_DIR}/facebox/facebox.css" media="screen" type="text/css"/>
        <script type="text/javascript" src="{$params.JS_DIR}facebox/facebox.js"></script>
        <script type="text/javascript" src="{$params.JS_DIR}jquery.form.js"></script>
        <script type="text/javascript" src="{$params.JS_DIR}videojs.js"></script>
        <script charset="utf-8" type="text/javascript">
            $(function(){
              VideoJS.setup();
            })
        </script>
     {elseif preg_match('/poll\.php/',$smarty.server.SCRIPT_NAME)}
        <title>{$article->title|clearslash} - {$category_real_name|clearslash|capitalize} {$subcategory_real_name|clearslash|capitalize} - Noticias de Galicia - {$smarty.const.SITE_TITLE} </title>
        <meta name="keywords" content="{$article->metadata|clearslash}" />
        <meta name="description" content="{$article->summary|strip_tags|clearslash}" />
        <link rel="stylesheet" href="{$params.CSS_DIR}parts/poll.css" type="text/css" media="screen,projection" />
        <link rel="stylesheet" href="{$params.CSS_DIR}parts/article.css" type="text/css" media="screen,projection" />
        <link rel="stylesheet" href="{$params.CSS_DIR}parts/comments.css" type="text/css" media="screen,projection" />
        <link rel="stylesheet" href="{$params.CSS_DIR}parts/utilities.css" type="text/css" media="screen,projection" />
        <link rel="stylesheet" href="{$params.CSS_DIR}video-js.css" type="text/css" media="screen,projection" />
        <link rel="stylesheet" href="{$params.JS_DIR}/facebox/facebox.css" media="screen" type="text/css"/>
        <script type="text/javascript" src="{$params.JS_DIR}facebox/facebox.js"></script>
        <script type="text/javascript" src="{$params.JS_DIR}jquery.form.js"></script>
        <script type="text/javascript" src="{$params.JS_DIR}videojs.js"></script>
        <script type="text/javascript" src="http://www.google.com/jsapi"></script>
        <script charset="utf-8" type="text/javascript">
            $(function(){
              VideoJS.setup();
            })
        </script>

    {elseif preg_match('/gallery\.php/',$smarty.server.SCRIPT_NAME)}
        <title>{$album->title|clearslash|default:''} - {$category_real_name|clearslash|capitalize} {$subcategory_real_name|clearslash|capitalize} - Álbumes de Galicia - {$smarty.const.SITE_TITLE}</title>
        <meta name="keywords" content="{$album->metadata|clearslash|escape:'html'}" />
        <meta name="description" content="{$album->description|clearslash|escape:'html'}" />
        <link rel="stylesheet" href="{$params.CSS_DIR}gallery.css" type="text/css" media="screen,projection">
        <link rel="stylesheet" href="{$params.CSS_DIR}parts/article.css" type="text/css" media="screen,projection" />
        <link rel="stylesheet" href="{$params.CSS_DIR}parts/comments.css" type="text/css" media="screen,projection" />
        <link rel="stylesheet" href="{$params.CSS_DIR}parts/utilities.css" type="text/css" media="screen,projection" />
        <link rel="stylesheet" href="{$params.CSS_DIR}jquery-ui-custom/jquery-ui.css" type="text/css" media="screen,projection">
        <!--<link rel="stylesheet" href="{$params.JS_DIR}jquery.ad-gallery.1.2.2/jquery.ad-gallery.css" type="text/css" media="screen,projection">-->
        <link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}/jcarousel/jquery.jcarousel.css" />
        <link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}/jcarousel/skin.css" />
        <link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}/jcarousel/galleries-toolbar.css" />
        
        {literal}
        <style type="text/css">
          #main_menu, .transparent-logo {
            background-color:#ffbc21;
          }
          div.toolbar-bottom a, div.utilities a{
            background-color:#373737;
          }
        </style>
        {/literal}
        
    {elseif preg_match('/opinion(.*)\.php/',$smarty.server.SCRIPT_NAME)}
        {if $smarty.request.action eq "read"}
            <title>{$opinion->name|clearslash} - {$opinion->title|clearslash} - Opinión de Galicia {$smarty.const.SITE_TITLE} </title>
            <meta name="keywords" content="{$opinion->metadata|clearslash}" />
            <meta name="description" content="{$opinion->summary|clearslash}" />
        {elseif $smarty.request.action eq 'list_op_author'}
             <title>{$author_name} - Artículos de Opinión  - Opinión de Galicia - {$smarty.const.SITE_TITLE}</title>
            <meta name="keywords" content="opinion, {$smarty.const.SITE_KEYWORDS}" />
            <meta name="description" content="Opinión: {$smarty.const.SITE_DESCRIPTION|strip_tags|clearslash}" />
        {else}
            <title> Articulos Opinion - Opinión de Galicia - {$smarty.const.SITE_TITLE}</title>
            <meta name="keywords" content="opinion, {$smarty.const.SITE_KEYWORDS}" />
            <meta name="description" content="Opinión: {$smarty.const.SITE_DESCRIPTION|strip_tags|clearslash}" />

        {/if}
        <link rel="stylesheet" href="{$params.CSS_DIR}parts/article.css" type="text/css" media="screen,projection" />
        <link rel="stylesheet" href="{$params.CSS_DIR}parts/opinion.css" type="text/css" media="screen,projection" />
        <link rel="stylesheet" href="{$params.CSS_DIR}parts/comments.css" type="text/css" media="screen,projection" />
        <link rel="stylesheet" href="{$params.CSS_DIR}parts/utilities.css" type="text/css" media="screen,projection" />
        <link rel="stylesheet" href="{$params.JS_DIR}/facebox/facebox.css" media="screen" type="text/css"/>
        <script type="text/javascript" src="{$params.JS_DIR}facebox/facebox.js"></script>

        {literal}
        <style type="text/css">
            div.opinion #submenu { background-color:#e9ddaf !important; }
            div.opinion #submenu > ul, div.opinion div.toolbar-bottom a, div.opinion div.utilities a.share-action, div.opinion .transparent-logo{
                    background-color:#d3bc5f !important;
            }
        </style>
        {/literal}
        
    {elseif preg_match('/videos\.php/',$smarty.server.SCRIPT_NAME)}
        {if $smarty.request.action eq "inner"}
            <title>{$video->title|clearslash|default:''} -  {$video->category_title} - Vídeos de Galicia - {$smarty.const.SITE_TITLE}</title>
            <meta name="keywords" content="{$video->metadata|clearslash}" />
            <meta name="description" content="{$video->description|clearslash}" />
        {else}
            <title>Videos destacados en {$category_real_name|clearslash|capitalize} {$subcategory_real_name|clearslash|capitalize} - Vídeos de Galicia - {$smarty.const.SITE_TITLE}</title>
            <meta name="keywords" content="video, {$smarty.const.SITE_KEYWORDS}" />
            <meta name="description" content="Videos: {$smarty.const.SITE_DESCRIPTION|strip_tags|clearslash}" />
        {/if}
        <link rel="stylesheet" href="{$params.CSS_DIR}parts/article.css" type="text/css" media="screen,projection" />
        <link rel="stylesheet" href="{$params.CSS_DIR}parts/comments.css" type="text/css" media="screen,projection" />
        <link rel="stylesheet" href="{$params.CSS_DIR}video.css" type="text/css" media="screen,projection" />
        <link rel="stylesheet" href="{$params.CSS_DIR}parts/utilities.css" type="text/css" media="screen,projection" />
        {literal}
        <style type="text/css">
            #submenu ul li, div.toolbar-bottom a, div.utilities a, .transparent-logo{
                    background-color:#373737;
            }
        </style>
        {/literal}
    {else}
        <title> {$category_real_name|clearslash|capitalize|default:''} {$subcategory_real_name|clearslash|capitalize} - Noticias de Galicia  - {$smarty.const.SITE_TITLE}</title>
        <meta name="keywords" content="{$smarty.const.SITE_KEYWORDS}" />
        <meta name="description" content="{$smarty.const.SITE_DESCRIPTION|strip_tags|clearslash}" />
        <script  type="text/javascript" src="{$params.JS_DIR}jquery.cycle.all.2.72.js"></script>
        <script  type="text/javascript" src="{$params.JS_DIR}onm-mockup.js"></script>

    {/if}
    {if !preg_match('/videos\.php/',$smarty.server.SCRIPT_NAME)}
        {literal}<style type="text/css">{/literal}
{$categories_styles}
        {literal}</style>{/literal}
    {/if}

    {* /rss/ *}
    <link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="{$smarty.const.SITE_URL}rss/" />
    {* Intersticial banner dependencies*}
    <link rel="stylesheet" href="{$params.CSS_DIR}parts/intersticial.css" type="text/css" media="screen,projection" />
    <script type="text/javascript" src="{$params.JS_DIR}jquery.cookie.js"></script>
    <script type="text/javascript" src="{$params.JS_DIR}jquery.intersticial.js"></script>

</head>
<body> 
