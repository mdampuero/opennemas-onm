<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title>..: Panel de Control :..</title>
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}admin.css?cacheburst=1259173764"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}style.css"/>
<!--[if IE]>
    <link rel="stylesheet" href="{$params.CSS_DIR}ieadmin.css" type="text/css" />
<![endif]-->


<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}botonera.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}lightview.css" />
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}datepicker.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}welcomepanel.css?cacheburst=1257955982" />
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}lightwindow.css" media="screen" />

<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}messageboard.css" media="screen" />

{if preg_match('/mediamanager\.php/',$smarty.server.SCRIPT_NAME) || preg_match('/mediagraficos\.php/',$smarty.server.SCRIPT_NAME)}
    <link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}mediamanager.css" />
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}photos.js"></script>
{/if}

{scriptsection name="head"}
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}lightview.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}prototype.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}prototype-date-extensions.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}scriptaculous/scriptaculous.js?load=effects,dragdrop,controls"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}utils.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}fabtabulous.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}control.maxlength.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}datepicker.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}lightwindow.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}MessageBoard.js"></script>
{/scriptsection}

{if preg_match('/article\.php/',$smarty.server.SCRIPT_NAME)}
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilsarticle.js"></script>
    {if $smarty.request.action == 'list_pendientes' || $smarty.request.action == 'list_agency'}
        <script type="text/javascript" language="javascript" src="{$params.JS_DIR}editables.js"></script>
    {/if}
{/if}
{if preg_match('/pendiente\.php/',$smarty.server.SCRIPT_NAME)}
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilsarticle.js"></script>
{/if}
{if $smarty.request.action == 'new' || $smarty.request.action == 'read'}
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}validation.js"></script>
    {* FIXME: corregir para que pille bien el path *}
    {dhtml_calendar_init src=$params.JS_DIR|cat:'jscalendar/calendar.js' setup_src=$params.JS_DIR|cat:'/jscalendar/calendar-setup.js'
        lang=$params.JS_DIR|cat:'jscalendar/lang/calendar-es.js' css=$params.JS_DIR|cat:'/jscalendar/calendar-win2k-cold-2.css'}
    {if preg_match('/article\.php/',$smarty.server.SCRIPT_NAME)}
        {* <script language="javascript" type="text/javascript" src="{$params.JS_DIR}tiny_mce/tiny_mce.js"></script> *}
        <script language="javascript" type="text/javascript" src="{$params.JS_DIR}tiny_mce/tiny_mce_gzip.js"></script>
        <script language="javascript" type="text/javascript" src="{$params.JS_DIR}lightview.js"></script>
    {/if}
    {if preg_match('/pendiente\.php/',$smarty.server.SCRIPT_NAME)}
        <script language="javascript" type="text/javascript" src="{$params.JS_DIR}tiny_mce/tiny_mce_gzip.js"></script>
    {/if}
    {if preg_match('/comment\.php/',$smarty.server.SCRIPT_NAME)}
        <script language="javascript" type="text/javascript" src="{$params.JS_DIR}tiny_mce/tiny_mce_gzip.js"></script>
    {/if}
    {if preg_match('/advertisement\.php/',$smarty.server.SCRIPT_NAME)}
        <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilsadvertisement.js"></script>
    {/if}
    {if preg_match('/opinion\.php/',$smarty.server.SCRIPT_NAME)}
        <script language="javascript" type="text/javascript" src="{$params.JS_DIR}tiny_mce/tiny_mce_gzip.js"></script>
     {/if}
{/if}
{if preg_match('/album\.php/',$smarty.server.SCRIPT_NAME)}
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}cropper.js"></script>
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}scriptaculous/scriptaculous.js?load=builder"></script>

    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilsalbum.js"></script>

{/if}
{if preg_match('/category\.php/',$smarty.server.SCRIPT_NAME)}
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilscategory.js"></script>
    {if $smarty.request.action == 'new' || $smarty.request.action == 'read'}
        <script type="text/javascript" src="{$params.JS_DIR}MiniColorPicker.js"></script>
    {/if}
{/if}
{if preg_match('/poll\.php/',$smarty.server.SCRIPT_NAME)}
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilspoll.js"></script>
{/if}
{if preg_match('/comment\.php/',$smarty.server.SCRIPT_NAME)}
    {if !isset($smarty.post.action) || $smarty.post.action eq "read"}
        <script type="text/javascript" language="javascript" src="{$params.JS_DIR}lightview.js"></script>
    {/if}
{/if}
{if preg_match('/opinion\.php/',$smarty.server.SCRIPT_NAME)}
        <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilsopinion.js"></script>
    {/if}
{if preg_match('/dashboard\.php/',$smarty.server.SCRIPT_NAME)}
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}swfobject.js"></script>
    <link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}dashboard.css" />
{/if}

{*Move functions js - utils_header.js*}
 <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utils_header.js"></script>

</head>
<body topmargin="0" leftmargin="0" marginheight="0" marginwidth="0">
{* scriptsection name="body" *}
<script type="text/javascript" src="{$params.JS_DIR}wz_tooltip.js"></script>
{* /scriptsection *}

<table border="0" cellpadding="0" cellspacing="0" align="center" width="100%" height="100%">
<tr><td valign="top" align="left"><!-- INICIO: Tabla contenedora -->
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs}>
<table border="0" cellpadding="0" cellspacing="0" align="left" width="100%" height="100%">
{* <tr>
    <td class="barra_superior" style="height:50px;background-image:url(/admin/images/header_middle.png);">&nbsp;&nbsp;{$titulo_barra}</td>
</tr>  *}
<tr>
    <td style="padding:10px;width:100%;" align="left" valign="top">

{if isset($smarty.session.messages) && !empty($smarty.session.messages)}
    {messageboard type="inline"}
{else}
    {messageboard type="growl"}
{/if}
