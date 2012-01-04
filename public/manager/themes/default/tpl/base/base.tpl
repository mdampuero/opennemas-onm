<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="pragma" content="no-cache" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="author" content="OpenHost,SL" />
    <meta name="generator" content="OpenNemas - News Management System" />
    <link rel="shorcut icon" href="{$params.IMAGE_DIR}/favicon.png" />

    {block name="meta"}
        <title>OpenNeMaS - Manager section</title>
    {/block}

    {block name="header-css"}     
        {css_tag href="/admin.css"}
        <!--[if IE]>{css_tag href="/ie.css"}<![endif]-->
        {css_tag href="/buttons.css"}
        {css_tag href="/datepicker.css"}
        {css_tag href="/lightview.css"}
        {css_tag href="/lightwindow.css" media="screen"}
        {css_tag href="/mediamanager.css"}
        {css_tag href="/messageboard.css" media="screen"}
        <style>
            div.wrapper-content {
                max-width: 1024px;
            }
        </style>

	{/block}

</head>
<body>

    <div id="topbar-admin" class="manager">
        <div class="logo-and-menu">
            <div id="logoonm">
                <a  href="{$smarty.const.SITE_URL}manager/index.php" id="logo-onm" title="{t}Go to admin main page{/t}">
                   <img src="{$smarty.const.TEMPLATE_ADMIN_PATH_WEB}images/logo-opennemas-small.png" alt="opennemas" width="132px" height="27px"/>
                </a>
            </div>

            {admin_menu}
        </div><!-- / -->

        <div class="info-left">
            <div id="user_box">
		<ul>
		    <li class="menu">
			<a href="#" id="menu" class="menu">{$smarty.session.username|ucfirst}</a>
			<ul>
			    <li>
				{t escape="off" 1=$smarty.session.userid 2=$smarty.session.username 3=$smarty.const.SITE_URL_ADMIN}<a id="settings" title="See my user preferences" href="%3/controllers/acl/user.php?action=read&id=%1">Settings</a>{/t}
			    </li>
			    <li class="divider"></li>
			    <li>
				<a href="javascript:salir('{t}Do you really want to exit from backend?{/t}','{$smarty.const.SITE_URL_ADMIN}/logout.php');" id="logout" class="logout" title="{t}Logout from control panel{/t}">
				    {t}Log out{/t}
				</a>
			    </li>
			</ul>
		    </li>
		</ul>

            </div>

        </div>

    </div>

    <div id="content">

    {block name="content"}

    {/block}

    </div>

    

    {block name="copyright"}
	<div id="copyright" class="wrapper-content clearfix">

        <div class="company left">
            <img align="left" src="{$params.IMAGE_DIR}logos/logo-opennemas-small-blue.png" alt="OpenNeMaS"/>
			{t} made by OpenHost S.L.{/t}<br/>
            {t 1=strftime("%Y")}All rights reserved ® 2008 - %1{/t}
        </div>

        <ul class="support">
            <li><a href="http://www.openhost.es/">{t}Support & Help{/t}</a> </li>
        </ul>

    </div>
	{/block}

    {block name="js-library"}
        {script_tag language="javascript" src="/prototype.js"}
        {script_tag language="javascript" src="/scriptaculous/scriptaculous.js"}
        {script_tag language="javascript" src="/scriptaculous/effects.js"}
        {script_tag language="javascript" src="/scriptaculous/dragdrop.js"}
    {/block}

    {block name="header-js"}
        {block name="js-library"}{/block}
        {script_tag language="javascript" src="/lightview.js"}
        {script_tag language="javascript" src="/prototype-date-extensions.js"}
        {script_tag language="javascript" src="/fabtabulous.js"}
        {script_tag language="javascript" src="/control.maxlength.js"}
        {script_tag language="javascript" src="/datepicker.js"}
        {script_tag language="javascript" src="/MessageBoard.js"}
        {script_tag language="javascript" src="/utils.js"}
        {script_tag language="javascript" src="/utils_header.js"}
        {script_tag language="javascript" src="/utilsopinion.js"}
        {script_tag language="javascript" src="/validation.js"}
        {script_tag language="javascript" src="/lightwindow.js" defer="defer"}
        {script_tag language="javascript" src="/modalbox.js" defer="defer"}
        {* FIXME: corregir para que pille bien el path *}
        <script type="text/javascript">
        try {
                // Activar la validación
                new Validation('formulario', { immediate : true });
                Validation.addAllThese([
                        ['validate-password',
                                '{t}Your password must have between 8 and 16 characters.{/t}', {
                                minLength : 8,
                                maxLength : 16
                        }]
                ]);

                // Para activar los separadores/tabs
                $fabtabs = new Fabtabs('tabs');
        } catch(e) {
                // Escondemos los errores
                //console.log( e );
        }
        </script>   
     {/block}

     {block name="footer-js"}
     {/block}

</body>
</html>