<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title>..: Panel de Control :..</title>
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}admin.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}botonera.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}style.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}datepicker.css"/>
<!--[if IE]>
    <link rel="stylesheet" href="{$params.CSS_DIR}ieadmin.css" type="text/css" />
<![endif]-->

{scriptsection name="head"}
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}prototype.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}prototype-date-extensions.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}scriptaculous/effects.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}scriptaculous/dragdrop.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}datepicker.js"></script>
{/scriptsection}

</head>
<body topmargin="0" leftmargin="0" marginheight="0" marginwidth="0">
    
<div id="adviceRefresh" style="display: none; background-color: #FFE9AF; color: #666; border: 1px solid #996699; padding: 10px; font-size: 1.1em; font-weight: bold; width: 550px; position: absolute; right: 0;">
    <img src="{$params.IMAGE_DIR}template_manager/messagebox_warning.png" border="0" align="absmiddle" />
    La accion "Renovar cache" sobre múltiples cachés puede ralentizar el sistema.
</div>

<table border="0" cellpadding="0" cellspacing="0" align="center" width="100%" height="100%">
<tr>
    <td valign="top" align="left"><!-- INICIO: Tabla contenedora -->

    <table border="0" cellpadding="0" cellspacing="0" align="left" width="100%" height="100%">
   {* <tr>
        <td class="barra_superior" style="height:50px;background-image:url(/admin/images/header_middle.png);">
            &nbsp;&nbsp;{$titulo_barra}</td>
    </tr> *}
    <tr>
        <td style="padding:10px;" align="left" valign="top">
            
            {block name="content"}
            {/block}
            
        </td>
    </tr>
    </table>

    </td>
</tr>
</table>
{block name="footer-js"}{/block}
</body>
</html>
