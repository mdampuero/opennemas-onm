<!doctype html>
<html>
<head>
    {block name="title"}<title>{t}Instance not found - Opennemas{/t}</title>{/block}

    <meta charset="utf-8">
    <meta name="author"    content="OpenHost,SL">
    <meta name="generator" content="OpenNemas - News Management System">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="robots"    content="noindex, nofollow" />
    <meta name="description" content="OpenNeMaS - An specialized CMS focused in journalism." />
    <meta name="keywords" content="CMS, Opennemas, OpenHost, journalism" />

    <!-- Apple devices fullscreen -->
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <!-- Apple devices fullscreen -->
    <meta names="apple-mobile-web-app-status-bar-style" content="black-translucent" />

    <!-- Favicon -->
    <link rel="icon" href="{$params.IMAGE_DIR}favicon.png">

    {block name="header-css"}
        {css_tag href="/bootstrap/bootstrap.css" media="screen" common=1}
        {css_tag href="/style.css" media="screen" common=1}
        {css_tag href="/fontawesome/font-awesome.min.css" common=1}
    {/block}

</head>

<body class='error instance-error'>
    <div class="wrapper">
        {block name="content"}
        <div class="code"><i class="icon-warning-sign"></i></div>
        <div class="desc">{t}Online newspaper not found.{/t}</div>
        <div class="buttons">
            <a href="http://www.opennemas.com" class="btn btn-primary btn-large">{t}Maybe time for creating yours.{/t}</a>
        </div>
        {/block}
    </div>
    {block name="footer-js"}
        {script_tag src="/jquery/jquery.min.js" common=1}
        {script_tag src="/libs/bootstrap.js" common=1}
        {script_tag src="/libs/modernizr.min.js" common=1}
    {/block}
</body>
</html>
