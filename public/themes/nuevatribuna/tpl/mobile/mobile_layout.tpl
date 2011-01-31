<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<!-- saved from url=(0022)http://m.vayatele.com/ -->
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	    <title>{if !empty($category_real_name)}{$category_real_name|clearslash|capitalize} - {/if}{if !empty($subcategory_real_name)} {$subcategory_real_name|clearslash|capitalize} - {/if}{$smarty.const.SITE_TITLE_MOBILE}</title>
		<meta name="keywords" content="{$smarty.const.SITE_KEYWORDS}" />
		<meta name="description" content="{$smarty.const.SITE_DESCRIPTION}" />
		<meta name="robots" content="NOODP">
		<meta name="description" content="">
		<meta name="robots" content="noindex">
		<link rel="apple-touch-icon" href="{$params.CSS_DIR}../images/logos/nuevatribuna-apple-touch-icon.png">
		<link rel="shorcut icon" href="{$params.IMAGE_DIR}/logos/favicon.png" />
		<link rel="stylesheet" href="{$params.CSS_DIR}parts/mobile.css" type="text/css" media="handheld,screen" charset="UTF-8" />		
</head>
<body>

	<a name="arriba" id="arriba"></a>
	<div id="headerwrap">
		<div id="header">
			<h1><a href="{$smarty.const.BASE_PATH}"><img src="{$params.CSS_DIR}../images/logos/nuevatribuna-mobil.png" /> <span>para móvil</span></a></h1>
		</div>
	</div>

	<div id="ad">

	</div>
    {include file="mobile/partials/sections.tpl"}
    {block name="content"}{/block}
    <div id="footerwrap">
		<div id="footer">
		   <ul class="list-horizontal">
		      <li><a href="#arriba" title="Arriba">Volver Arriba</a></li>
		      <li><a href="/mobile/redirect_web/" title="Ver {$smarty.const.SITE_TITLE_MOBILE}">ver a la versión estándar</a></li>
		   </ul>
		   <h2 id="wsl"><a href="{$smarty.const.SITE}" title="Weblogs SL"><span></span>{$smarty.const.SITE_TITLE_MOBILE} &copy; 2010</a></h2>
		</div>
	</div>
	{include file="mobile/partials/modulo_analytics.tpl"}
</html>
