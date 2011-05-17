{*
    OpenNeMas project
    @theme      Lucidity
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml" lang="es">
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <meta name="generator" content="OpenNemas - Open Source News Management System" />
    <meta name="google-site-verification" content="{$smarty.const.GOOGLE_SITE_VERIFICATION}" />
    <meta name="y_key" content="{$smarty.const.YAHOO_SITE_KEY}" />
    <meta name="author" content="OpenHost,SL" />
    <meta name="revisit-after" content="1 days" />
    <meta http-equiv="robots" content="index,follow" />
    <meta http-equiv="expires" content="0" />
    <meta http-equiv="last-modified" content="0" />
    <link rel="shorcut icon" href="{$params.IMAGE_DIR}/logos/favicon.png" />
    <meta http-equiv="Refresh" content="{$smarty.const.REFRESH_INTERVAL}; url=http://{$smarty.server.SERVER_NAME}{$smarty.server.REQUEST_URI}" />
    {block name='meta'}{/block}
    {asset_compile}

    <link rel="stylesheet" href="{$params.CSS_DIR}/bp/screen.css" type="text/css" media="screen, projection" />
    <link rel="stylesheet" href="{$params.CSS_DIR}/bp/print.css" type="text/css" media="print" />
    <!--[if lt IE 8]><link rel="stylesheet" href="{$params.CSS_DIR}/bp/ie.css" type="text/css" media="screen, projection" /><![endif]-->

    <link rel="stylesheet" href="{$params.CSS_DIR}nuevatribuna.css?{time()}" type="text/css" media="screen,projection" />
    <link rel="stylesheet" href="{$params.CSS_DIR}jui-nuevatribuna/jquery-ui.css?{time()}" type="text/css" media="screen,projection" />

    <link rel="stylesheet" href="{$params.CSS_DIR}parts/ads.css?{time()}" type="text/css" media="screen,projection" />
	<link rel="stylesheet" href="{$params.CSS_DIR}parts/menu.css?{time()}" type="text/css" media="screen,projection" />
    <link rel="stylesheet" href="{$params.CSS_DIR}parts/intersticial.css?{time()}" type="text/css" media="screen,projection" />
    <link rel="stylesheet" href="{$params.CSS_DIR}parts/widgets.css?{time()}" type="text/css" media="screen,projection" />

    <style type="text/css">{*$categories_styles*}</style>

	{/asset_compile}
	{block name='header-css'}
    {/block}
    {block name="header-js"}
    <script type="text/javascript" src="{$params.JS_DIR}jquery-1.4.1.min.js"></script>
	<script type="text/javascript" src="{$params.JS_DIR}jquery-ui.js"></script>
	<script type="text/javascript" src="{$params.JS_DIR}functions.js"></script>
	<script type="text/javascript" src="{$params.JS_DIR}jquery.cookie.js"></script>
	<script type="text/javascript" src="{$params.JS_DIR}jquery.intersticial.js"></script>
    <script type="text/javascript" src="{$params.JS_DIR}jquery.cycle.js"></script>
	{/block}
    <link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="{$smarty.const.SITE_URL}rss/" />
</head>
<body>
    {*Definición de la variable 'section_url usada en menu y footer'*}
    {if preg_match('/videos\.php/',$smarty.server.SCRIPT_NAME)}
        {assign var='section_url' value='/video/'}
    {elseif preg_match('/poll\.php/',$smarty.server.SCRIPT_NAME)}
        {assign var='section_url' value='/encuesta/'}
    {elseif preg_match('/gallery\.php/',$smarty.server.SCRIPT_NAME)}
        {assign var='section_url' value='/album/'}
    {else}
        {assign var='section_url' value='/seccion/'}
    {/if}

    <div class="container">
    {block name="content"}{/block}
    </div><!-- #container -->

    <div class="container container_with_border container_footer clearfix">
    {block name="footer"}{/block}
    </div><!-- #container -->

    {block name="footer-js"}
	<script type="text/javascript">var current_section = "{$category_name}";</script>
	{/block}

</body>
</html>
