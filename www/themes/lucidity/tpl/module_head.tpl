{*
    OpenNeMas project

    @theme      Lucidity
*}
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
   
    <meta name="keywords" content="" />
    <meta name="description" content="" />

    <link rel="stylesheet" href="{$params.CSS_DIR}screen.css" type="text/css" media="screen, projection">
    <link rel="stylesheet" href="{$params.CSS_DIR}print.css" type="text/css" media="print">
    <!--[if lt IE 8]><link rel="stylesheet" href="{$params.CSS_DIR}ie.css" type="text/css" media="screen, projection"><![endif]-->

    <link rel="stylesheet" href="{$params.CSS_DIR}/onm-mockup.css" type="text/css" media="screen,projection">
    
    {if preg_match('/article\.php/',$smarty.server.SCRIPT_NAME) && ($smarty.request.action eq "read")}
        <title>Crónica comarcal - {$category_real_name|clearslash} - {$article->title|clearslash} </title>
        <link rel="stylesheet" href="{$params.CSS_DIR}parts/article.css" type="text/css" media="screen,projection">
        <link rel="stylesheet" href="{$params.CSS_DIR}parts/comments.css" type="text/css" media="screen,projection">

        <script defer type="text/javascript" src="{$params.JS_DIR}functions.js"></script>
    {else}
         <title>Crónica comarcal - noticias de España y del mundo </title>
    {/if}
    <script defer type="text/javascript" src="{$params.JS_DIR}jquery-1.4.1.min.js"></script>
    <script defer type="text/javascript" src="{$params.JS_DIR}jquery.cycle.all.2.72.js"></script>
    <script defer type="text/javascript" src="{$params.JS_DIR}onm-mockup.js"></script>

</head>