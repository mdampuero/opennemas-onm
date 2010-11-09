<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<!-- saved from url=(0022)http://m.vayatele.com/ -->
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>Retrincos.info versión móvil</title>
		<meta name="robots" content="NOODP">
		<meta name="description" content="">
		<meta name="robots" content="noindex">
		<link rel="apple-touch-icon" href="http://img.vayatele.com/lp2/mobile/images/apple-touch-icon.png">
		<link rel="stylesheet" href="{$params.CSS_DIR}/mobile.css" type="text/css" media="handheld,screen" charset="UTF-8" />
		{literal}<style type="text/css">{/literal}
				{$categories_styles}
		{literal}</style>{/literal}
</head>
<body>

	<a name="arriba" id="arriba"></a>
	<div id="headerwrap">
		<div id="header">
			<h1><a href="{$smarty.const.BASE_PATH}"><img src="{$params.CSS_DIR}../images/main-logo.big.white.png" /> <span>para móvil</span></a></h1>
		</div>
	</div>

	<div id="ad">

	</div>
    {include file="mobile/partials/sections.tpl"}
    {block name="content"}{/block}
    <div id="footerwrap">
		<div id="footer">
		   <ul class="list-horizontal">
		      <li><a href="#arriba" title="Arriba">Arriba</a></li>
		      <li><a href="/mobile/redirect_web/" title="Ver Retricos.info">ver a la versión estándar</a></li>
		   </ul>
		   <h2 id="wsl"><a href="http://www.retrincos.com/" title="Weblogs SL"><span></span>Retrincos 2010</a></h2>
		</div>
	</div>
	{include file="mobile/partials/modulo_analytics.tpl"}
</html>
