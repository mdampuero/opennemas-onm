<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
    {block name="meta"}
        <title>OpenNeMaS - Manager section</title>
    {/block}
    <meta http-equiv="pragma" content="no-cache" >
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
    <meta name="author" content="OpenHost,SL" >
    <meta name="generator" content="OpenNemas - News Management System" >
    <link rel="shorcut icon" href="{$params.IMAGE_DIR}favicon.png" >

    {block name="header-css"}
        {css_tag href="/admin.css"}
        <!--[if IE]>{css_tag href="/ie.css"}<![endif]-->
        {css_tag href="/buttons.css"}
        {css_tag href="/messageboard.css" media="screen"}
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

</head>
<body>

    <header class="global-nav manager clearfix">
        <div class="logoonm pull-right">
            <a  href="{$smarty.const.SITE_URL}admin/" id="logo-onm" title="{t}Go to admin main page{/t}">
               <img src="{$smarty.const.TEMPLATE_ADMIN_PATH_WEB}images/logo-opennemas-small.png" alt="opennemas" width="132" height="27"/>
            </a>
        </div>
        <div class="global-menu pull-left">
            {admin_menu}
        </div>
    </header>

    <div id="content">

    {block name="content"}

    {/block}

    </div>



    {block name="copyright"}
	<div id="copyright" class="wrapper-content clearfix">

        <div class="company left">
            <img align="left" src="{$params.IMAGE_DIR}logos/logo-opennemas-small-blue.png" alt="OpenNeMaS"/>
			{t} made by OpenHost S.L.{/t}<br/>
            {t 1=strftime("%Y") escape=off}All rights reserved &copy; 2008 - %1{/t}
        </div>

        <ul class="support">
            <li><a href="http://www.openhost.es/">{t}Support & Help{/t}</a> </li>
        </ul>

    </div>
	{/block}

    {block name="footer-js"}{/block}

</body>
</html>