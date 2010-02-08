<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title>..: Panel de Control :..</title>
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

{stylesection name="head"}
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}admin.css" />
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}calendar_date_select.css" />
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}mediamanager.css" />
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}style.css" />
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}botonera.css"/>

<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}messageboard.css" media="screen" />
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}newsletter.css" media="screen" />
{/stylesection}



{scriptsection name="head"}
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}prototype.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}scriptaculous/scriptaculous.js"></script>

<script type="text/javascript" language="javascript" src="{$params.JS_DIR}validation.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}MessageBoard.js"></script>

<script type="text/javascript" language="javascript" src="{$params.JS_DIR}newsletter.js?cacheburst=1259855452"></script>
{/scriptsection}


{literal}
<script type="text/javascript">
// <!--

// -->
</script>
<style>

::selection { background: transparent; }
::-moz-selection { background: transparent; }

</style>
{/literal}
</head>
<body>

<table border="0" cellpadding="0" cellspacing="0" align="left" width="100%" height="100%">
<tr>
	<td class="barra_superior" style="height:50px;background-image:url(/admin/images/header_middle.png);">
	&nbsp;&nbsp;{$application_name}</td>
</tr>
<tr>
	<td style="padding:1px;" align="left" valign="top">

{if isset($smarty.session.messages) && !empty($smarty.session.messages)}
    {messageboard type="inline"}
{else}
    {messageboard type="growl"}
{/if}