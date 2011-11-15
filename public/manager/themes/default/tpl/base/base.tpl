<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
    <meta charset="utf-8">

    <meta name="author"    content="OpenHost,SL">
    <meta name="generator" content="OpenNemas - News Management System">
    <meta name="viewport"  content="width=device-width,initial-scale=1">

    {block name="meta"}
        <title>OpenNeMaS - Manager section</title>
    {/block}

    <link rel="icon" href="{$params.IMAGE_DIR}favicon.png">
    {block name="header-css"}
    {css_tag href="/bootstrap/bootstrap.css"}
        {css_tag href="/style.css"}
        {css_tag href="/admin.css"}
        <!--[if IE]>{css_tag href="/ie.css"}<![endif]-->
        {css_tag href="/buttons.css"}
        {css_tag href="/jquery/jquery-ui.css" media="all" type="text/css"}
        {css_tag href="/lightview.css"}
        {css_tag href="/lightwindow.css" media="screen"}
    {/block}

    {block name="js-library"}
        {script_tag src="/jquery/jquery.min.js"}
        <script type="text/javascript">
        jQuery.noConflict();
        </script>
        {script_tag src="/jquery/bootstrap-modal.js" language="javascript"}
        {script_tag src="/prototype.js"}
        {script_tag src="/scriptaculous/scriptaculous.js"}
        {script_tag src="/scriptaculous/effects.js"}
    {/block}

    {block name="header-js"}
        {block name="js-library"}{/block}
        {script_tag src="/onm/scripts.js"}
        {script_tag src="/modernizr/modernizr-2.5.0.min.js"}
        {script_tag src="/prototype-date-extensions.js"}
        {*script_tag src="/fabtabulous.js"*}
        {script_tag src="/control.maxlength.js"}
        {script_tag src="/utils.js"}
        {script_tag src="/utils_header.js"}
        {script_tag src="/utilsopinion.js"}
        {script_tag src="/validation.js"}
        {script_tag src="/lightview.js"}
        {script_tag src="/lightwindow.js" defer="defer"}
     {/block}

     {block name="footer-js"}
        {script_tag src="/tiny_mce/tiny_mce_gzip.js"}
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

    <div id="content" role="main">
    {block name="content"}{/block}
    </div>


    {block name="copyright"}
    <footer id="copyright" class="wrapper-content">
        <div class="company left">
            <img src="{$params.IMAGE_DIR}logos/logo-opennemas-small-blue.png" alt="OpenNeMaS"/>
            {t} made by OpenHost S.L.{/t}<br/>
            {t 1=strftime("%Y") escape=off}All rights reserved &copy; 2008 - %1{/t}
        </div>
        <ul class="support">
            <li><a href="http://www.openhost.es/">{t}Support & Help{/t}</a>
        </ul>
    </footer>

    {block name="footer-js"}
        {browser_update}

        {if isset($smarty.request.action) && ($smarty.request.action == 'new' || $smarty.request.action == 'read')}
        <script type="text/javascript">
        try {
            // Activar la validación
            new Validation('formulario', { immediate : true });
            Validation.addAllThese([
                ['validate-password',
                    '{t}Your password must contain 5 characters and dont contain the word <password> or your user name.{/t}', {
                    minLength : 6,
                    notOneOf : ['password','PASSWORD','Password'],
                    notEqualToField : 'login'
                }],
                ['validate-password-confirm',
                    '{t}Please check your first password and check again.{/t}', {
                    equalToField : 'password'
                }]
            ]);
        } catch(e) {
            // Escondemos los errores
            //console.log( e );
        }
        </script>
        {/if}
    {/block}

</head>
<body>

    <div id="topbar-admin" class="manager">
        <div id="logoonm">
    	    <a  href="{$smarty.const.SITE_URL}manager/index.php" id="logo-onm" title="{t}Go to admin main page{/t}">
    	       <img src="{$smarty.const.TEMPLATE_ADMIN_PATH_WEB}images/logo-opennemas-small.png" alt="opennemas"/>
    	    </a>
        </div><!-- /logoonm -->

        {admin_menu}

        <div class="info-left">
            <div id="user_box">
        		<ul>
        		    <li class="menu">
        			<a href="#" id="menu" class="menu"><strong>{$smarty.session.username|ucfirst}</strong></a>
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
            </div><!-- /user_box -->
        </div><!-- /info-left -->

    </div><!-- /topbar-admin -->

    <div id="content">
    {block name="content"}
    {/block}
    </div><!-- /content -->



    {block name="copyright"}
	<div id="copyright" class="wrapper-content clearfix">

        <div class="company left">
            <img align="left" src="{$params.IMAGE_DIR}logos/logo-opennemas-small-blue.png" alt="OpenNeMaS"/>
			{t} made by OpenHost S.L.{/t}<br/>
            {t 1=strftime("%Y")}All rights reserved ® 2008 - %1{/t}
        </div><!-- /company -->

        <ul class="support">
            <li><a href="http://www.openhost.es/">{t}Support & Help{/t}</a> </li>
        </ul><!-- /support -->

    </div><!-- /copyright -->
	{/block}

</body>
</html>