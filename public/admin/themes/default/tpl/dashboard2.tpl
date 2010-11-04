{include file="header.tpl"}
{* ************************* START HEADER ***************************** 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title>..: Panel de Control :..</title>
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<link rel='stylesheet' type='text/css' href='{$params.CSS_DIR}admin.css' />
<link rel='stylesheet' type='text/css' href='{$params.CSS_DIR}style.css' />
<link rel='stylesheet' type='text/css' href='{$params.JS_DIR}style.css' />
<!--[if IE]>
    <link rel="stylesheet" href="{$params.CSS_DIR}ieadmin.css" type="text/css" />
<![endif]-->

<script type="text/javascript" language="javascript" src="{php}echo($this->js_dir);{/php}prototype.js"></script>

<script type='text/javascript' src='{$params.JS_DIR}fabtabulous.js'></script>

{literal}
<script language="javascript">
<!-- //
var objForm = null;
var dialogo = null;
var editores = null;

function enviar(elto, trg, acc, id) {
	var parentEl = elto.parentNode;

	while(parentEl.nodeName != "FORM") {
		parentEl = parentEl.parentNode;
	}
	confirm (acc);
	parentEl.target = trg;
	parentEl.action.value = acc;
	parentEl.id.value = id;

	if(objForm != null) {
		objForm.submit();
	} else {
		parentEl.submit();
	}
}

</script>
{/literal}
</head>
<body topmargin="0" leftmargin="0" marginheight="0" marginwidth="0">
<form action="#" method="post" name="formulario" id="formulario"> 
<table border="0" cellpadding="0" cellspacing="0" align="left" width="100%" height="100%">
<tr>
  <td class="barra_superior"><div style="text-align:right; ">Settings&nbsp;<img src="{$params.IMAGE_DIR}admin_contenido.gif" border="0" /></div></td>
</tr>
<tr>
	<td style="padding:10px;" align="left" valign="top">

 *************************** END HEADER ***************************** *}

	<div id="nifty" style="width:830px; margin-left: auto;margin-right: auto; text-align:right;font-size: 12px;">
	<b class="rtop"><b class="r1"></b><b class="r2"></b><b class="r3"></b><b class="r4"></b></b>
		<div id="nifty" style="float:left"><b><em>FICHERO: {$conf_file}</em></b></div>
		<button type="button" style="cursor:pointer;" onClick="javascript:enviar(this, '_self', 'save', 0);" title="Guardar Positions" alt="Guardar Cambios">
		<img class="icon" src="{$params.IMAGE_DIR}save_button.png" title="Save Positions" alt="Guardar Cambios" >
		</button> &nbsp; &nbsp;
	<b class="rbottom"><b class="r4"></b><b class="r3"></b><b class="r2"></b><b class="r1"></b></b>
	</div>

	<div id="1" class="categ" style="margin-left: auto;margin-right: auto;width:830px; padding: 6px 2px;">
	
	<ul id="tabs">
		<li>
			<a href="#site">Visitas</a>
		</li>
		<li>
			<a href="#system">System configuration</a>
		</li>
	<li>
			<a href="#server">Services configuration</a>
		</li>
	</ul>

	<div id="site" class="panel" style="width:810px;">
	  <fieldset><legend>Visitas por categorias</legend>
	    <table width="750px" align=center border=0>	
	      {section name=a loop=$viewed}
		<tr><td>{$category}</td><td><b>{$viewed[a]->views}</b></td></tr>
	      {/section}	
	      {*section name=b loop=$categorys}
		<tr><td>{$categorys}</td><td><b>{$categorys[b]->name}</b></td></tr>
	      {/section*}	

	    </table></fieldset>
	</div>
	<div id="system" class="panel" style="width:810px">
		<fieldset><legend>System</legend>
			<table width="750px" align=center border=0>
			
				{foreach from=$config_vbles key=k item=v}
				{if $k|truncate:3:"" == 'SYS'}
					<tr><td>{$k}</td><td><input type=text name='{$k}' value='{$v}' size=70/></td></tr>
				{/if}
				{/foreach}
			
			</table></fieldset>
		<fieldset><legend>Media</legend>
			<table width="750px" align=center border=0>
				{foreach from=$config_vbles key=k item=v}
				{if ( $k|truncate:10:"" == 'MEDIA_PATH' ) or ( $k|truncate:9:"" == 'MEDIA_IMG' ) }
					{if $k == 'MEDIA_PATH_RELA'}<tr><td>{$k}</td><td><input type=text name='{$k}' value='{$MEDIA_PATH_RELA}' size=70/></td></tr>{/if}
					{if $k == 'MEDIA_PATH_URL'}<tr><td>{$k}</td><td><input type=text name='{$k}' value='{$MEDIA_PATH_URL}' size=70 style="border: 1px solid #999; background-color:#ddd" readonly="true"/></td></tr>{/if}
					{if $k == 'MEDIA_PATH'}<tr><td>{$k}</td><td><input type=text name='{$k}' value='{$MEDIA_PATH}' size=70 style="border: 1px solid #999; background-color:#ddd;" readonly="true"/></td></tr>{/if}
					{if $k == 'MEDIA_IMG_RELA_PATH'}<tr><td>{$k}</td><td><input type=text name='{$k}' value='{$MEDIA_IMG_RELA_PATH}' size=70/></td></tr>{/if}
					{if $k == 'MEDIA_IMG_PATH_URL'}<tr><td>{$k}</td><td><input type=text name='{$k}' value='{$MEDIA_IMG_PATH_URL}' size=70 style="border: 1px solid #999; background-color:#ddd;" readonly="true"/></td></tr>{/if}
					{if $k == 'MEDIA_IMG_PATH'}<tr><td>{$k}</td><td><input type=text name='{$k}' value='{$MEDIA_IMG_PATH}' size=70 style="border: 1px solid #999; background-color:#ddd;" readonly="true"/></td></tr>{/if}
				{else}
					{if $k|truncate:5:"" == 'MEDIA'}
						<tr><td>{$k}</td><td><input type=text name='{$k}' value='{$v}' size=70/></td></tr>
					{/if}
				{/if}
				{/foreach}
			
			</table>
		</fieldset>
	</div>
	<div id="server" class="panel" style="width:810px">
		<fieldset><legend>Data Base</legend>
		<table width="750px" align=center border=0>
			{foreach from=$config_vbles key=k item=v}
			{if $k|truncate:6:"" == 'BD_DSN'}
			  {if $k == 'BD_DSN'}<tr><td>{$k}</td><td><input type=text style="border: 1px solid #999; background-color:#ddd;" name='{$k}' value='{$BD_DSN}' size=70 readonly="true"/></td></tr>{/if}
			{else}
			  {if $k|truncate:2:"" == 'BD'}
			    <tr><td>{$k}</td><td><input type=text name='{$k}' value='{$v}' size=70/></td></tr>
			  {/if}
			{/if}
			{/foreach}
		</table></fieldset>
		<fieldset><legend>Mail server</legend>
		<table width="750px" align=center border=0>
			{foreach from=$config_vbles key=k item=v}
			{if $k|truncate:4:"" == 'MAIL'}
				<tr><td>{$k}</td><td><input type=text name='{$k}' value='{$v}' size=70/></td></tr>
			{/if}
			{/foreach}
		</table></fieldset>
	</div>
	</div>
	<div id="nifty" style="width:830px; margin-left: auto;margin-right: auto; text-align:right;font-size: 12px;">
	<b class="rtop"><b class="r1"></b><b class="r2"></b><b class="r3"></b><b class="r4"></b></b>
		<div id="nifty" style="float:left"><b><em>FICHERO: {$conf_file}</em></b></div>
		<button type="button" style="cursor:pointer;" onClick="javascript:enviar(this, '_self', 'save', 0);" title="Guardar Positions" alt="Guardar Cambios">
		<img class="icon" src="{$params.IMAGE_DIR}save_button.png" title="Save Positions" alt="Guardar Cambios" >
		</button> &nbsp; &nbsp;
	<b class="rbottom"><b class="r4"></b><b class="r3"></b><b class="r2"></b><b class="r1"></b></b>
	</div>

{* *************************** START FOOTER ***************************** 
   </td>
</tr>
</table>
<input type="hidden" id="action" name="action" value="" /><input type="hidden" name="id" value="{$id}" />
</form>

<script language="javascript" type="text/javascript" src="{php}echo($this->js_dir);{/php}wz_tooltip.js"></script>
</body>
</html>
 *************************** END FOOTER ***************************** *}
{include file="footer.tpl"}
