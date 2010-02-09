<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.1//EN" "http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head> 
	<title>Xornal.com versión móvil</title> 
    <link rel="stylesheet" href="{$params.CSS_DIR}{$smarty.const.BASE_URL}/estilo.css" type="text/css" media="screen" charset="UTF-8" />
    <link rel="stylesheet" href="{$params.CSS_DIR}{$smarty.const.BASE_URL}/estilo.css" type="text/css" media="handheld" charset="UTF-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />    
</head>
    
<body>	
    <div id="cabecera">
        <a href="http://www.xornal.com{$smarty.const.BASE_URL}/" title="Xornal de Galicia - Xornal.com">
            <img src="{$params.IMAGE_DIR}xornal-logo.jpg" alt="" /></a>
    </div>
    
    {* Static menu version*}
    <div id="menu">
        {if $section != 'home'}
            <a href="{$smarty.const.BASE_URL}/">portada</a>
        {/if}
        <a href="{$smarty.const.BASE_URL}/ultimas-noticias/"{if $section eq 'ultimas'} class="current"{/if}>últimas noticias</a>
        <a href="{$smarty.const.BASE_URL}/seccion/opinion/"{if strtolower($section) eq 'opinion'} class="current"{/if}>opinion</a>
        <a href="{$smarty.const.BASE_URL}/seccion/polItica/"{if strtolower($section) eq 'politica'} class="current"{/if}>politica</a>
        <a href="{$smarty.const.BASE_URL}/seccion/galicia/"{if strtolower($section) eq 'galicia'} class="current"{/if}>galicia</a>
        <a href="#secciones">más...</a>        
    </div>
    
    {* <div id="pathway">
        {if $section != 'home'}
            [<a href="{$smarty.const.BASE_URL}/seccion/{$section}/" class="current">{$ccm->get_title($section)}</a>]
        {/if}
    </div> *}
	
	<div id="contenido">
		