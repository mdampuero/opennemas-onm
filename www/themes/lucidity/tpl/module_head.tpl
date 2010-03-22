{*
    OpenNeMas project

    @theme      Lucidity
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"
    xmlns:fb="http://www.facebook.com/2008/fbml">


<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

        <meta name="keywords" content="" />
        <meta name="description" content="" />

        <link rel="stylesheet" href="{$params.CSS_DIR}screen.css" type="text/css" media="screen, projection">
        <link rel="stylesheet" href="{$params.CSS_DIR}print.css" type="text/css" media="print">
        <!--[if lt IE 8]><link rel="stylesheet" href="{$params.CSS_DIR}ie.css" type="text/css" media="screen, projection"><![endif]-->

        <link rel="stylesheet" href="{$params.CSS_DIR}/onm-mockup.css" type="text/css" media="screen,projection">
        {literal}
            <style type="text/css">
                    #main_menu{
                            background-color:{/literal}{$category_data.color}{literal};
                    }
                     {/literal}  {$categories_styles}{literal}
            </style>
            {/literal}
        {if preg_match('/article\.php/',$smarty.server.SCRIPT_NAME) }
            <title>Crónica comarcal - {$category_real_name|clearslash} - {$article->title|clearslash} </title>
            <link rel="stylesheet" href="{$params.CSS_DIR}parts/article.css" type="text/css" media="screen,projection">
            <link rel="stylesheet" href="{$params.CSS_DIR}parts/comments.css" type="text/css" media="screen,projection">

            <script defer type="text/javascript" src="{$params.JS_DIR}functions.js"></script>

            <script defer type="text/javascript" src="{$params.JS_DIR}jquery-1.4.1.min.js"></script>
            <script defer type="text/javascript" src="{$params.JS_DIR}onm-mockup.js"></script>

        {elseif preg_match('/video\.php/',$smarty.server.SCRIPT_NAME) }
            <title>Crónica comarcal - Videos - {$category_real_name|clearslash} - {$article->title|clearslash} </title>
            <link rel="stylesheet" href="{$params.CSS_DIR}parts/article.css" type="text/css" media="screen,projection">
            <link rel="stylesheet" href="{$params.CSS_DIR}parts/comments.css" type="text/css" media="screen,projection">

            <link rel="stylesheet" href="{$params.CSS_DIR}video.css" type="text/css" media="screen,projection">
            <link rel="stylesheet" href="{$params.CSS_DIR}jquery-ui-custom/jquery-ui.css" type="text/css" media="screen,projection">
            {literal}
            <style type="text/css">
                    #main_menu{
                            background-color:#009677;
                    }                 
            </style>
            {/literal}
        {else}
             <title>Crónica comarcal - noticias de España y del mundo </title>

            <script defer type="text/javascript" src="{$params.JS_DIR}jquery-1.4.1.min.js"></script>
            <script defer type="text/javascript" src="{$params.JS_DIR}jquery.cycle.all.2.72.js"></script>
            <script defer type="text/javascript" src="{$params.JS_DIR}onm-mockup.js"></script>

        {/if}

    </head>
    <body> 