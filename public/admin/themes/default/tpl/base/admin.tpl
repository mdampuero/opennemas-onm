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
        <title>OpenNeMaS - Admin section</title>
    {/block}

    {block name="header-css"}
        <link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}admin.css"/>
		<!--[if IE]><link rel="stylesheet" href="{$params.CSS_DIR}ie.css" type="text/css" /><![endif]-->
		<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}buttons.css" />
        <link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}datepicker.css"/>
        <link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}lightview.css" />
        <link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}lightwindow.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}mediamanager.css" />
        <link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}messageboard.css" media="screen" />

	{/block}

    {block name="header-js"}
        <script type="text/javascript" language="javascript" src="{$params.JS_DIR}prototype.js"></script>
        <script type="text/javascript" language="javascript" src="{$params.JS_DIR}scriptaculous/scriptaculous.js"></script>
        <script type="text/javascript" language="javascript" src="{$params.JS_DIR}scriptaculous/effects.js"></script>

        <script type="text/javascript" language="javascript" src="{$params.JS_DIR}lightview.js"></script>
        <script type="text/javascript" language="javascript" src="{$params.JS_DIR}prototype-date-extensions.js"></script>
        <script type="text/javascript" language="javascript" src="{$params.JS_DIR}fabtabulous.js"></script>
        <script type="text/javascript" language="javascript" src="{$params.JS_DIR}control.maxlength.js"></script>
        <script type="text/javascript" language="javascript" src="{$params.JS_DIR}datepicker.js"></script>
        <script type="text/javascript" language="javascript" src="{$params.JS_DIR}MessageBoard.js"></script>
        <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utils.js"></script>
        <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utils_header.js"></script>
        <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilsopinion.js"></script>
        <script type="text/javascript" language="javascript" src="{$params.JS_DIR}validation.js"></script>
		<script type="text/javascript" defer="defer" language="javascript" src="{$params.JS_DIR}lightwindow.js"></script>
		<script type="text/javascript" defer="defer" language="javascript" src="{$params.JS_DIR}/modalbox.js"></script>

        {* FIXME: corregir para que pille bien el path *}
        {dhtml_calendar_init src=$params.JS_DIR|cat:'jscalendar/calendar.js' setup_src=$params.JS_DIR|cat:'/jscalendar/calendar-setup.js'
            lang=$params.JS_DIR|cat:'jscalendar/lang/calendar-es.js' css=$params.JS_DIR|cat:'/jscalendar/calendar-win2k-cold-2.css'}
        <script language="javascript" type="text/javascript" src="{$params.JS_DIR}tiny_mce/tiny_mce_gzip.js"></script>
     {/block}

</head>
<body>
	{* scriptsection name="body" *}
	<script type="text/javascript" src="{$params.JS_DIR}wz_tooltip.js"></script>
	{* /scriptsection *}

    <div id="topbar-admin">
        <div id="logoonm">
	    <a  href="{$smarty.const.SITE_URL_ADMIN}/index.php" title="{t}Go to admin main page{/t}">
	       <div><img src="{$smarty.const.TEMPLATE_ADMIN_PATH_WEB}images/logo-opennemas-small.png" alt="opennemas"/></div>
	       <!--<div class="site-name">{$smarty.const.SITE_FULLNAME}</div>-->
	    </a>
        </div>

        {admin_menu}

        <div class="info-left">
            <div id="user_box">
		<ul>
		    {if {count_pending_comments} gt 0}
		    <li class="menu">
			<a class="spch-bub-inside" href="{$smarty.const.SITE_URL_ADMIN}/controllers/comment/comment.php?action=list&category=todos">
			    <em>{count_pending_comments} <span class="point"></span></em>
			</a>
		    </li>
		    {/if}
		    
		    {if Acl::check('BACKEND_ADMIN') eq true}
        	    <li class="menu" title="{t}Active users in backend{/t}">
			<a href="#" id="user_activity">
			    {count_sessions}
			    <img src="{$params.IMAGE_DIR}/users_activity.png" alt="" />
			</a>
		    </li>
		    {/if}
		    <li>
			{gmail_mailbox}
		    </li>
		    
		    <li class="menu">
			<a href="#" class="menu"><strong>{$smarty.session.username|ucfirst}</strong></a>
			<ul>
			    <li>
				{t escape="off" 1=$smarty.session.userid 2=$smarty.session.username 3=$smarty.const.SITE_URL_ADMIN}<a title="See my user preferences" href="%3/controllers/acl/user.php?action=read&id=%1">Settings</a>{/t}
			    </li>
			    <li class="divider"></li>
			    <li>
				<a href="javascript:salir('{t}Do you really want to exit from backend?{/t}','{$smarty.const.SITE_URL_ADMIN}/logout.php');" class="logout" title="{t}Logout from control panel{/t}">
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
            <li><a href="http://www.openhost.es/">{t}Support & Help{/t}</a>
        </ul>

    </div>
	{/block}


<script type="text/javascript">
    /* <![CDATA[ */
    //new YpSlideOutMenuHelper();

    {if Acl::check('USER_ADMIN') eq true}
    var users_online = [];
    function linkToMB() {
        $('MB_content').select('td a.modal').each(function(item) {
            item.observe('click', function(event) {
                Event.stop(event);

                Modalbox.show(this.href, {
                    title: '{t}Active users{/t}',
                    afterLoad: linkToMB,
                    width: 300
                });
            });
        });
    }

    document.observe('dom:loaded', function() {
        if( $('user_activity') ) {
            $('user_activity').observe('click', function() {
                Modalbox.show('{$smarty.const.SITE_URL}{$smarty.const.ADMIN_DIR}/index.php?action=show_panel', {
                    title: '{t}Active users{/t}',
                    afterLoad: linkToMB,
                    width: 300
                });
            });

            new PeriodicalExecuter( function(pe) {
                $('user_activity').update('<img src="{$smarty.const.TEMPLATE_ADMIN_PATH_WEB}images/loading.gif" border="0" width="16" height="16" />');
                new Ajax.Request('index.php', {
                    onSuccess: function(transport) {
                        // Actualizar o número de usuarios en liña e gardar o array en users_online
                        eval('users_online = ' + transport.responseText + ';');
                        $('user_activity').update( users_online.length );
                    }
                });
                //pe.stop();
            }, 5*60); // Actualizar cada 2*60 segundos
        }
    });
    {/if}
 </script>
	{block name="footer-js"}
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

			// Para activar los separadores/tabs
			$fabtabs = new Fabtabs('tabs');
		} catch(e) {
			// Escondemos los errores
			//console.log( e );
		}
                 </script>
		{/if}
	{/block}

</body>
</html>
