{*
    OpenNeMas project

    @theme      Lucidity

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
 
<html xmlns:fb="http://www.facebook.com/2008/fbml" lang="es">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <meta name="generator" content="OpenNeMaS - Open Source News Management System" />

        <meta name="keywords" content="{$smarty.const.SITE_KEYWORDS}" />
        <meta name="description" content="{$smarty.const.SITE_DESCRIPTION}" />
        <meta name="google-site-verification" content="ryT9siqC-qAlYTjfaoeCUnxhvtisvrLFGgoKXcFPjlE" />
        
        <meta name="author" content="OpenHost, SL" />
        <meta name="revisit-after" content="1 days" />
        <meta http-equiv="robots" content="index,follow" />
        <meta http-equiv="expires" content="0" />
        <meta http-equiv="last-modified" content="0" />
        <meta http-equiv="Refresh" content="900; url={$smarty.const.SITE_URL}" />
        
        <link rel="stylesheet" href="{$params.CSS_DIR}screen.css" type="text/css" media="screen, projection" />
        <link rel="stylesheet" href="{$params.CSS_DIR}print.css" type="text/css" media="print" />
        <link rel="stylesheet" href="{$params.CSS_DIR}onm-mockup.css" type="text/css" media="screen,projection" />
        <link rel="stylesheet" href="{$params.CSS_DIR}jquery-ui-custom/jquery-ui.css" type="text/css" media="screen,projection" />

       {* <script type="text/javascript" src="{$params.JS_DIR}swfobject.js"></script> *}
        <!--[if lt IE 8]><link rel="stylesheet" href="{$params.CSS_DIR}ie.css" type="text/css" media="screen, projection" /><![endif]-->
        
        {if preg_match('/article\.php/',$smarty.server.SCRIPT_NAME)}
            <title>{$article->title} - {$category_data.title} - {$smarty.const.SITE_TITLE} </title>
            <link rel="stylesheet" href="{$params.CSS_DIR}parts/article.css" type="text/css" media="screen,projection" />
            <link rel="stylesheet" href="{$params.CSS_DIR}parts/comments.css" type="text/css" media="screen,projection" />
            <link rel="stylesheet" href="{$params.CSS_DIR}parts/utilities.css" type="text/css" media="screen,projection" />
              
            <script type="text/javascript" src="{$params.JS_DIR}jquery-1.4.1.min.js"></script>
            <script type="text/javascript" src="{$params.JS_DIR}jquery-ui.js"></script>
            <script type="text/javascript" src="{$params.JS_DIR}functions.js"></script>
           
        {elseif preg_match('/gallery\.php/',$smarty.server.SCRIPT_NAME)}
            <title>{$gallery->title|default:''}  Album   {$category_data.title} - {$smarty.const.SITE_TITLE}</title>

            <link rel="stylesheet" href="{$params.CSS_DIR}parts/article.css" type="text/css" media="screen,projection" />
            <link rel="stylesheet" href="{$params.CSS_DIR}parts/comments.css" type="text/css" media="screen,projection" />
            <link rel="stylesheet" href="{$params.CSS_DIR}parts/utilities.css" type="text/css" media="screen,projection" />

            <link rel="stylesheet" href="{$params.CSS_DIR}gallerific/galleriffic-3.css" type="text/css" />
            <link rel="stylesheet" href="{$params.CSS_DIR}gallerific/black.css" type="text/css" />
            <link rel="stylesheet" href="{$params.CSS_DIR}gallery.css" type="text/css" media="screen,projection" />
            {literal}
                <style type="text/css">
                  #main_menu, .transparent-logo {
                    background-color:#ffbc21;
                  }
                  div.toolbar-bottom a, div.utilities a{
                    background-color:#373737;
                  }
                </style>            
                <script type="text/javascript">
                    document.write('<style>.noscript { display: none; }</style>');
                </script>
            {/literal}
            <script  type="text/javascript" src="{$params.JS_DIR}jquery-1.4.1.min.js"></script>
            <script  type="text/javascript" src="{$params.JS_DIR}jquery-ui.js"></script>

        {elseif preg_match('/video\.php/',$smarty.server.SCRIPT_NAME)}
            <title>{$video->title|default:''}  Videos - {$category_data.title} - {$smarty.const.SITE_TITLE}</title>
            
            <link rel="stylesheet" href="{$params.CSS_DIR}parts/article.css" type="text/css" media="screen,projection" />
            <link rel="stylesheet" href="{$params.CSS_DIR}parts/comments.css" type="text/css" media="screen,projection" />            
            <link rel="stylesheet" href="{$params.CSS_DIR}video.css" type="text/css" media="screen,projection" />
            <link rel="stylesheet" href="{$params.CSS_DIR}parts/utilities.css" type="text/css" media="screen,projection" />
         
            {literal}
            <style type="text/css">
              
                    #main_menu, div.toolbar-bottom a, div.utilities a, .transparent-logo{
                            background-color:#373737;
                    }
            </style>
            {/literal}
            <script  type="text/javascript" src="{$params.JS_DIR}jquery-1.4.1.min.js"></script>
            <script  type="text/javascript" src="{$params.JS_DIR}jquery-ui.js"></script>
            <script type="text/javascript" src="{$params.JS_DIR}functions.js"></script>
        {else}
            <title> {$category_data.title|default:'Portada'} - {$smarty.const.SITE_TITLE}</title>

            <script  type="text/javascript" src="{$params.JS_DIR}jquery-1.4.1.min.js"></script>
            <script  type="text/javascript" src="{$params.JS_DIR}jquery-ui.js"></script>
            <script  type="text/javascript" src="{$params.JS_DIR}jquery.cycle.all.2.72.js"></script>
            <script  type="text/javascript" src="{$params.JS_DIR}onm-mockup.js"></script>

        {/if}
        {if !preg_match('/video\.php/',$smarty.server.SCRIPT_NAME)}
            {literal}
                <style type="text/css">
                         {/literal}  {$categories_styles}{literal}
                </style>
            {/literal}
        {/if}
        
        {* /rss/ *}
        <link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="{$smarty.const.SITE_URL}rss/" />
        
        {* Intersticial banner dependencies *}
        <link rel="stylesheet" href="{$params.CSS_DIR}parts/intersticial.css" type="text/css" media="screen,projection" />
        <script type="text/javascript" src="{$params.JS_DIR}jquery.cookie.js"></script>
        <script type="text/javascript" src="{$params.JS_DIR}jquery.intersticial.js"></script>
        
    </head>
    <body> 