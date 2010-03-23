{*
    OpenNeMas project

    @theme      Lucidity
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 
<html  lang="es">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

        <meta name="keywords" content="" />
        <meta name="description" content="" />

        <link rel="stylesheet" href="{$params.CSS_DIR}screen.css" type="text/css" media="screen, projection">
        <link rel="stylesheet" href="{$params.CSS_DIR}print.css" type="text/css" media="print">
       
        <link rel="stylesheet" href="{$params.CSS_DIR}onm-mockup.css" type="text/css" media="screen,projection">
        <link rel="stylesheet" href="{$params.CSS_DIR}jquery-ui-custom/jquery-ui.css" type="text/css" media="screen,projection">

        <!--[if lt IE 8]><link rel="stylesheet" href="{$params.CSS_DIR}ie.css" type="text/css" media="screen, projection"><![endif]-->
        
        {if preg_match('/article\.php/',$smarty.server.SCRIPT_NAME) }
            <title>{$article->title|clearslash} - {$category_data.title|clearslash} - {$smarty.const.SITE_TITLE} </title>
            <link rel="stylesheet" href="{$params.CSS_DIR}parts/article.css" type="text/css" media="screen,projection">
            <link rel="stylesheet" href="{$params.CSS_DIR}parts/comments.css" type="text/css" media="screen,projection">
            <link rel="stylesheet" href="{$params.CSS_DIR}parts/utilities.css" type="text/css" media="screen,projection">
        
            <script type="text/javascript" src="{$params.JS_DIR}jquery-1.4.1.min.js"></script>
            <script type="text/javascript" src="{$params.JS_DIR}jquery-ui.js"></script>
            <script type="text/javascript" src="{$params.JS_DIR}functions.js"></script>
           
            

        {elseif preg_match('/video\.php/',$smarty.server.SCRIPT_NAME) }
            <title>{$video->title|clearslash|default:''}  Videos - {$category_data.title|clearslash} - {$smarty.const.SITE_TITLE}</title>
            <link rel="stylesheet" href="{$params.CSS_DIR}video.css" type="text/css" media="screen,projection">
            <link rel="stylesheet" href="{$params.CSS_DIR}parts/article.css" type="text/css" media="screen,projection">
            <link rel="stylesheet" href="{$params.CSS_DIR}parts/comments.css" type="text/css" media="screen,projection">            
            {literal}
            <style type="text/css">
                    #main_menu{
                            background-color:#009677;
                    }                 
            </style>
            {/literal}
        {else}
             <title> {$smarty.const.SITE_TITLE} </title>

            <script defer="defer" type="text/javascript" src="{$params.JS_DIR}jquery-1.4.1.min.js"></script>            
            <script defer="defer" type="text/javascript" src="{$params.JS_DIR}jquery-ui.js"></script>            
            <script defer="defer" type="text/javascript" src="{$params.JS_DIR}jquery.cycle.all.2.72.js"></script>
            <script defer="defer" type="text/javascript" src="{$params.JS_DIR}onm-mockup.js"></script>

        {/if}
        {literal}
            <style type="text/css">                   
                     {/literal}  {$categories_styles}{literal}
            </style>
        {/literal}

    </head>
    <body> 