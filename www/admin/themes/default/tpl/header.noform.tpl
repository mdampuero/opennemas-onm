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
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}uploader.css" />
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}botonera.css"/>

<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}messageboard.css" media="screen" />
{/stylesection}

<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}lightwindow.css" media="screen" />

{scriptsection name="head"}
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}prototype.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}scriptaculous/scriptaculous.js"></script>

{* <script type="text/javascript" language="javascript" src="{$params.JS_DIR}tiny_mce/tiny_mce.js"></script> *}
<script language="javascript" type="text/javascript" src="{$params.JS_DIR}tiny_mce/tiny_mce_gzip.js"></script>

<script type="text/javascript" language="javascript" src="{$params.JS_DIR}utils.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}photos.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}swfobject.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}validation.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}fabtabulous.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}jsvalidate/jsvalidate_beta04.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}MessageBoard.js"></script>

{/scriptsection}
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}lightwindow.js"></script>


{* FIXME: corregir para que pille bien el path 
{dhtml_calendar_init src='themes/default/js/jscalendar/calendar.js' setup_src='themes/default/js/jscalendar/calendar-setup.js'
	lang='themes/default/js/jscalendar/lang/calendar-es.js' css='themes/default/js/jscalendar/calendar-win2k-cold-2.css'}*}
{* /if *}
{if preg_match('/importXML\.php/',$smarty.server.SCRIPT_NAME)}
<script type="text/javascript" language="javascript" src="{php}echo($this->js_dir);{/php}addFiles.js"></script>
{/if}

{literal}
<script type="text/javascript">
<!-- //
// FIXME: Crear un ficheros actions.js con las funcionalidades tipo de javascript
var objForm = null;
var dialogo = null;
var editores = null;

function enviar(elto, trg, acc, id) {
	var parentEl = elto.parentNode;
	while(parentEl.nodeName != "FORM") {
		parentEl = parentEl.parentNode;
	}

	parentEl.target = trg;
	parentEl.action.value = acc;
	parentEl.id.value = id;

	if(objForm != null) {
		objForm.submit();
	} else {
		parentEl.submit();
	}
}

function confirmar_action(elto, action, id, texto) {
	if(confirm(texto)) {
		enviar(elto, '_self', action, id);
	}
}

function confirmar(elto, id) {
	if(confirm('¿Está seguro de querer eliminar este elemento?')) {
		enviar(elto, '_self', 'delete', id);
	}
}

function seleccionar_fichero(nombre_campo, tipo) {
	if(dialogo)
	{
		if(!dialogo.closed) dialogo.close();
	}

	dialogo = window.open('include/dialogo.archivo.php?campo_retorno='+nombre_campo+'&tipo_archivo='+tipo, 'dialogo', 'toolbar=no, location=no, directories=no, status=no, menub ar=no, scrollbar=no, resizable=no, copyhistory=yes, width=410, height=360, left=100, top=100, screenX=100, screenY=100');
	dialogo.focus();
}
// -->
</script>
{/literal}
</head>
<body>
{scriptsection name="body"}
<script type="text/javascript" src="{php}echo($this->js_dir);{/php}wz_tooltip.js"></script>
{/scriptsection}

<table border="0" cellpadding="0" cellspacing="0" align="left" width="100%" height="100%">
{*<tr>
	<td class="barra_superior" style="height:50px;background-image:url(/admin/images/header_middle.png);">
	&nbsp;&nbsp;{$titulo_barra}</td>
</tr>*}
<tr>
	<td style="padding:1px;" align="left" valign="top">

{if isset($smarty.session.messages) && !empty($smarty.session.messages)}
    {messageboard type="inline"}
{else}
    {messageboard type="growl"}
{/if}