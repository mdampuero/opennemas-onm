<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>..: Panel de Control - {$title}:..</title>

<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}general.css" />

<script type="text/javascript" language="javascript" src="{$params.JS_DIR}jquery-1.4.2.min.js"></script>

{scriptsection name="head"}
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}ypSlideOutMenus-jquery.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}utils.js"></script>
{/scriptsection}

<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}admin.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}style.css"/>
<!--[if IE]>
    <link rel="stylesheet" href="{$params.CSS_DIR}ieadmin.css" type="text/css" />
<![endif]-->

{stylesection name="head"}
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}toolbar.css"/>
{/stylesection}

<script type="text/javascript" language="javascript">
/* <![CDATA[ */
sinFrames();
/* ]]> */
</script>

</head>

<body margin="0" topmargin="0" leftmargin="0" marginheight="0" marginwidth="0">

<table style="border: 1px solid rgb(0, 75, 142); width: 100%;" width="100%" height="100%" cellpadding="0" cellspacing="0">
<tbody>
	<tr>
		<td id="ocultar" height="100%" valign="top">
				<table id="topbar-admin" cellpadding="0" cellspacing="0">
				<tr>
					<td id="logoonm">
						<a href="index.php"  class="logout" title="Ir a la página principal de administración">
							<img src="{$params.IMAGE_DIR}/logo-opennemas.png" border="0" align="absmiddle" alt="Inicio" width="136" height=30"" />&nbsp;
						</a>
					</td>
					<td>
                        {* Smarty plugin to render backend menu *}
                        {ypmenu}                        
					</td>

                    <td style="font-size: 12px;width:100%; color: #666;" nowrap="nowrap">
                        {* Smarty plugin to render online users*}
                        {online_users}
                    </td>
				</tr>

				<tr>
					<td valign="top" align="left" width="100%" height="100%" colspan="4">
                        
