<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>{block name="head-title"}{t}Control Panel{/t}{/block}</title>

<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}general.css" />


<script type="text/javascript" language="javascript" src="{$params.JS_DIR}jquery-1.4.2.min.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}jquery-ui-1.8.1.custom.min.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}ypSlideOutMenus-jquery.js"></script>
{block name="head-js"}{/block}

<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}admin.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}style.css"/>
<!--[if IE]>
    <link rel="stylesheet" href="{$params.CSS_DIR}ieadmin.css" type="text/css" />
<![endif]-->


<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}smoothness/jquery-ui-1.8.2.custom.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}toolbar.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}flashmessenger.css"/>
{block name="head-css"}{/block}

</head>

<body margin="0" topmargin="0" leftmargin="0" marginheight="0" marginwidth="0">

<table width="100%" height="100%" cellpadding="0" cellspacing="0">
<tbody>
	<tr>
		<td valign="top">
				<table id="cuadromenu" cellpadding="0" cellspacing="0">
				<tr>
					<td id="logoonm">
						<a href="{baseurl}/{url route="panel-index"}" class="logout"
                           title="Ir a la página principal de administración">
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
					<td valign="top" align="left" width="100%" height="100%" colspan="3">
					
					<table class="adminform" border="0" cellpadding="0" cellspacing="0" align="left" width="100%" height="100%">
					<tr><td style="padding:10px;" align="left" valign="top">
						
						{block name="body-content"}
							{include file="panel.tpl"}
						{/block}
					
					</td></tr>
					</table>
					
					</td>
				</tr>
            </table><!--#topbar-admin-->                       
		</td>
	</tr>
</tbody>
</table>


<script type="text/javascript">
/* <![CDATA[ */                         
new YpSlideOutMenuHelper();                        
/* ]]> */
</script>

{block name="foot-js"}{/block}
</body>
</html>