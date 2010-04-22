{*
    OpenNeMas project

    @theme      clarity
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

        <link rel="stylesheet" href="{$params.CSS_DIR}screen.css" type="text/css" media="screen, projection">
        <link rel="stylesheet" href="{$params.CSS_DIR}print.css" type="text/css" media="print">
        <link rel="stylesheet" href="{$params.CSS_DIR}advertisement.css" type="text/css" media="screen,projection">
        <!--[if lt IE 8]><link rel="stylesheet" href="{$params.CSS_DIR}ie.css" type="text/css" media="screen, projection"><![endif]-->

        <link rel="stylesheet" href="{$params.CSS_DIR}architecture-v1.css" type="text/css" media="screen,projection">
        <link rel="stylesheet" href="{$params.CSS_DIR}jquery-ui-custom/jquery-ui.css" type="text/css" media="screen,projection">

        <script type="text/javascript" src="{$params.JS_DIR}jquery-1.4.1.min.js"></script>
        <script type="text/javascript" src="{$params.JS_DIR}jquery-ui.js"></script>
        <script type="text/javascript" src="{$params.JS_DIR}functions.js"></script>
        {literal}
            <style type="text/css">
                    #main_menu, div.toolbar-bottom a, div.utilities a, .transparent-logo {
                            background-color:#aa0000;
                    }
                    #main_menu{
                      background-color:Black;
                    }
            </style>
        {/literal}
        {*<meta name="keywords" content="{$smarty.const.SITE_KEYWORDS}" />
        <meta name="description" content="{$smarty.const.SITE_DESCRIPTION}" /> *}
        <meta name="description" content="También arquitectura - su portal de noticias sobre arquitectura, interiorismo y decoración." />
        
        {if preg_match('/article\.php/',$smarty.server.SCRIPT_NAME) }
            <title>{$article->title|clearslash} - {$category_data.title|clearslash} - {$smarty.const.SITE_TITLE} </title>


            <link rel="stylesheet" href="{$params.CSS_DIR}parts/article.css" type="text/css" media="screen,projection">
            <link rel="stylesheet" href="{$params.CSS_DIR}parts/comments.css" type="text/css" media="screen,projection">
            <link rel="stylesheet" href="{$params.CSS_DIR}parts/utilities.css" type="text/css" media="screen,projection">
            <link rel="stylesheet" href="{$params.CSS_DIR}parts/widgets.css" type="text/css" media="screen,projection">


            <script type="text/javascript" src="{$params.JS_DIR}cufon-yui.js"></script>
            <script type="text/javascript" src="{$params.JS_DIR}droid.js"></script>
            {literal}
                <script type="text/javascript">
                  Cufon.replace('#description-category ul li', {
                  });

                </script>

                <style type="text/css">

                        #logo{
                          height:165px !important;
                          min-height:165px;
                          background-position:top left;
                        }
                        #description-category ul li{

                          color:#016e95; /* cambiar segundo a categoría*/
                        }
                </style>
            {/literal}
 
        {/if}


        {* /rss/ 
        <link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="{$smarty.const.SITE_URL}rss/" />
        *}
        {* Intersticial banner dependencies  
        <link rel="stylesheet" href="{$params.CSS_DIR}parts/intersticial.css" type="text/css" media="screen,projection" />
        <script type="text/javascript" src="{$params.JS_DIR}jquery.cookie.js"></script>
        <script type="text/javascript" src="{$params.JS_DIR}jquery.intersticial.js"></script>
        *}
    </head>
    <body> 