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
	<link rel="shorcut icon" href="/favicon.png" />
    <meta http-equiv="Refresh" content="900; url=http://{$smarty.server.SERVER_NAME}{$smarty.server.REQUEST_URI}" />
    {block name='meta'}{/block}
    {block name='header-css'}
    <link rel="stylesheet" href="{$params.CSS_DIR}screen.css" type="text/css" media="screen, projection">
    <link rel="stylesheet" href="{$params.CSS_DIR}print.css" type="text/css" media="print">
    <!--[if lt IE 8]><link rel="stylesheet" href="{$params.CSS_DIR}ie.css" type="text/css" media="screen, projection" /><![endif]-->
    <link rel="stylesheet" href="{$params.CSS_DIR}onm-mockup.css" type="text/css" media="screen,projection" />
    <link rel="stylesheet" href="{$params.CSS_DIR}jquery-ui-custom/jquery-ui.css" type="text/css" media="screen,projection">
    <link rel="stylesheet" href="{$params.CSS_DIR}parts/menu.css" type="text/css" media="screen,projection" />
    <link rel="stylesheet" href="{$params.CSS_DIR}parts/publi.css" type="text/css" media="screen,projection" />
    <link rel="stylesheet" href="{$params.CSS_DIR}parts/intersticial.css" type="text/css" media="screen,projection" />
    <style type="text/css">{$categories_styles}</style>
    {/block}
    {block name="header-js"}
    <script type="text/javascript" src="{$params.JS_DIR}jquery-1.4.1.min.js"></script>
    <script type="text/javascript" src="{$params.JS_DIR}jquery-ui.js"></script>
    <script type="text/javascript" src="{$params.JS_DIR}functions.js"></script>
    <script type="text/javascript" src="{$params.JS_DIR}jquery.cycle.all.2.72.js"></script>
    <script type="text/javascript" src="{$params.JS_DIR}jquery.cookie.js"></script>
    <script type="text/javascript" src="{$params.JS_DIR}jquery.intersticial.js"></script>
	<script type="text/javascript" src="{$params.JS_DIR}lazyload/jquery.lazyload.js"></script>
    {/block}
    <link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="{$smarty.const.SITE_URL}rss/" />
</head>
<body>

    <div id="container" class="span-24 last">
    {block name="content"}{/block}
    </div><!-- #container -->

    <div id="container" class="span-24 last">
    {block name="footer"}{/block}
    </div>

    {block name="footer-js"}
	<script type="text/javascript">
        jQuery(document).ready(function(){
            if(! navigator.userAgent.toLowerCase().match('ipad')){
				$('.nw-image').lazyload({ effect:'fadeIn', placeholder:'{$params.JS_DIR}/lazyload/img/blank.png' });
			}
        });
    </script>
    {/block}

</body>
</html>
