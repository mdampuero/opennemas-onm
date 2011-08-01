<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="pragma" content="no-cache" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="author" content="OpenHost,SL" />
    <meta name="generator" content="OpenNemas - News Management System" />

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
		<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}menu.css"/>
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
			   <div><img src="{$smarty.const.TEMPLATE_ADMIN_PATH_WEB}images/logo-opennemas-small.png" alt="ONM"/></div>
			   <!--<div class="site-name">{$smarty.const.SITE_FULLNAME}</div>-->
			</a>
        </div>

        {admin_menu}

        <div class="info-left">
            <div id="user_box" style="width:auto;">
                <div style="padding-right:8px; float:left;" nowrap="nowrap">
                    <div id="pending_comments" title="{t}Pending comments{/t}">
                        <a class="spch-bub-inside" href="{$smarty.const.SITE_URL_ADMIN}/controllers/comment/comment.php?action=list&category=todos">
                            <span class="point"></span>
                            <em>&nbsp;{count_pending_comments}&nbsp;</em>
                        </a>
                    </div>
					&nbsp;&nbsp;&nbsp;
                </div>

                <div id="name-box" style="float:left; margin-right:5px;">
                  <strong>
                    {t escape="off" 1=$smarty.session.userid 2=$smarty.session.username}Welcome <a title="See my user preferences" href="{$smarty.const.SITE_URL_ADMIN}/controllers/acl/user.php?action=read&id=%1">%2</a>{/t}

                    {if isset($smarty.session.isAdmin)}
                        <img src="{$smarty.const.TEMPLATE_ADMIN_PATH_WEB}images/key.png" border="0" align="absmiddle"
                            title="{t}Admin privileges{/t}" alt="" />
                    {/if}
					{gmail_mailbox}
                  </strong>
                </div><!--end name-box-->

                {if Acl::check('BACKEND_ADMIN') eq true}
                <div style="padding-right:4px; float:left;" nowrap="nowrap">
                    <div id="user_activity" title="{t}Active users in backend{/t}">
                        {count_sessions}
                    </div>
					&nbsp;&nbsp;&nbsp;
                </div>
                {/if}

                <div id="session-actions" style="float:left;">
                  <a href="javascript:salir('{t}Do you really want to exit from backend?{/t}','{$smarty.const.SITE_URL_ADMIN}/logout.php');" class="logout" title="{t}Logout from control panel{/t}">
						{t}Log out{/t}
						<img src="{$smarty.const.TEMPLATE_ADMIN_PATH_WEB}images/logout.png" border="0"
							align="absmiddle" alt="Salir del Panel de Administración" />
                  </a>
                </div><!--end session-actions-->
            </div>

        </div>

    </div>

    <div id="content">

    {block name="content"}

    {/block}

    </div>



    <div id="copyright" class="wrapper-content clearfix">

        <div class="company left">
            <img align="left" src="{$params.IMAGE_DIR}logos/logo-opennemas-small-blue.png" alt="OpenNeMaS"/>
            made by OpenHost S.L.<br/>
            All rights reserved ® 2008 - {strftime("%Y")}
        </div>

        <ul class="support">
            <li><a href="http://www.openhost.es/">{t}Support & Help{/t}</a>
        </ul>

    </div>


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
