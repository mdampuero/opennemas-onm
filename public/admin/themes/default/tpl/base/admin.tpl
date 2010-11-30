{*
    OpenNeMas project
    @theme      Lucidity
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="es">
<head>
    <meta http-equiv="pragma" content="no-cache" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="author" content="OpenHost,SL" />
    <meta name="generator" content="OpenNemas - Open Source News Management System" />

    {block name="meta"}
	<title>Test</title>
    {/block}

    {block name="header-css"}
	<link rel="stylesheet" type="text/css" href="{$smarty.const.TEMPLATE_ADMIN_PATH_WEB}css/general.css" />
    <link rel="stylesheet" type="text/css" href="{$smarty.const.TEMPLATE_ADMIN_PATH_WEB}css/admin.css" />
    <link rel="stylesheet" type="text/css" href="{$smarty.const.TEMPLATE_ADMIN_PATH_WEB}css/modalbox.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="{$smarty.const.TEMPLATE_ADMIN_PATH_WEB}css/multilevel-menu.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}admin.css?cacheburst=1259173764"/>
    <link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}style.css"/>
	<!--[if IE]><link rel="stylesheet" href="{$params.CSS_DIR}ieadmin.css" type="text/css" /><![endif]-->
    <link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}botonera.css"/>
    <link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}lightview.css" />
    <link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}datepicker.css"/>
    <link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}welcomepanel.css?cacheburst=1257955982" />
    <link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}lightwindow.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}messageboard.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}mediamanager.css" />
	{/block}

    {block name="header-js"}
    <script type="text/javascript" src="{$smarty.const.TEMPLATE_ADMIN_PATH_WEB}js/prototype.js">;</script>
    <script type="text/javascript" src="{$smarty.const.TEMPLATE_ADMIN_PATH_WEB}js/scriptaculous/scriptaculous.js?load=effects"></script>
    <script type="text/javascript" src="{$smarty.const.TEMPLATE_ADMIN_PATH_WEB}js/modalbox.js"></script>
    <script type="text/javascript" src="{$smarty.const.TEMPLATE_ADMIN_PATH_WEB}js/ypSlideOutMenus.js"></script>
    <script type="text/javascript" src="{$smarty.const.TEMPLATE_ADMIN_PATH_WEB}js/utils.js"></script>
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}lightview.js"></script>
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}prototype.js"></script>
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}prototype-date-extensions.js"></script>
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}scriptaculous/scriptaculous.js?load=effects,dragdrop,controls"></script>
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utils.js"></script>
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}fabtabulous.js"></script>
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}control.maxlength.js"></script>
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}datepicker.js"></script>
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}lightwindow.js"></script>
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}MessageBoard.js"></script>
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utils_header.js"></script>
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}photos.js"></script>
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilsarticle.js"></script>
	<script type="text/javascript" language="javascript" src="{$params.JS_DIR}editables.js"></script>
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilsarticle.js"></script>
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}validation.js"></script>
    {* FIXME: corregir para que pille bien el path *}
    {dhtml_calendar_init src=$params.JS_DIR|cat:'jscalendar/calendar.js' setup_src=$params.JS_DIR|cat:'/jscalendar/calendar-setup.js'
        lang=$params.JS_DIR|cat:'jscalendar/lang/calendar-es.js' css=$params.JS_DIR|cat:'/jscalendar/calendar-win2k-cold-2.css'}
	<script language="javascript" type="text/javascript" src="{$params.JS_DIR}tiny_mce/tiny_mce_gzip.js"></script>
	<script language="javascript" type="text/javascript" src="{$params.JS_DIR}lightview.js"></script>
	<script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilsadvertisement.js"></script>
    {/block}

</head>
<body>
	{* scriptsection name="body" *}
	<script type="text/javascript" src="{$params.JS_DIR}wz_tooltip.js"></script>
	{* /scriptsection *}

    <div id="topbar-admin">
        <div id="logoonm">
			<a  href="index.php" title="Ir a la página principal de administración">
			   <div><img src="{$smarty.const.TEMPLATE_ADMIN_PATH_WEB}images/logo-opennemas-small.png" alt="ONM"/></div>
			   <div class="site-name">{$smarty.const.SITE_FULLNAME}</div>
			</a>
        </div>

        {admin_menu}

        <div class="info-left">
            <div id="user_box" style="width:auto;">
                <div id="name-box" style="float:left; margin-right:5px;">
                  <strong>
                    Bienvenido
                    <a href="/admin/user.php?action=read&id={$_SESSION['userid']}" target="centro">
                        {$_SESSION['username']}
                    </a>
                    {if isset($_SESSION['isAdmin'])}
                        <img src="{$smarty.const.TEMPLATE_ADMIN_PATH_WEB}images/key.png" border="0" align="absmiddle"
                            title="Permisos de administrador" alt="" />
                    {/if}
                  </strong>
                </div><!--end name-box-->

                {if Acl::_('BACKEND_ADMIN') eq true}
                <div style="padding-right:4px; float:left;" nowrap="nowrap">
                    <div id="user_activity" title="Usuarios activos na sección de administración">
                        {count($sessions)}
                    </div>
                </div>
                {/if}

                <div id="session-actions" style="float:left;">
                  <a href="javascript:salir();" class="logout" title="Salir del panel de control">
                      <img src="{$smarty.const.TEMPLATE_ADMIN_PATH_WEB}images/logout.png" border="0"
                          align="absmiddle" alt="Salir del Panel de Administración" /> Salir
                  </a>
                </div><!--end session-actions-->
            </div>

            {gmail_mailbox}
        </div>

    </div>

    <div id="content">

	{if isset($smarty.session.messages)
		&& !empty($smarty.session.messages)}
		{messageboard type="inline"}
	{else}
		{messageboard type="growl"}
	{/if}

    {block name="content"}

    {/block}
    </div>



    <!--<div id="copyright">-->
    <!---->
    <!--    <div class="company">-->
    <!--        <img align="left" src="{$smarty.const.TEMPLATE_ADMIN_PATH_WEB}images/logo-opennemas-small.png" alt="OpenNeMaS"/>-->
    <!--        made by OpenHost S.L.<br/>-->
    <!--        All rights reserved ® 2010-->
    <!--    </div>-->
    <!---->
    <!--    <ul class="support">-->
    <!--        <li><a href="#">About</a>-->
    <!--        <li><a href="#">Help</a>-->
    <!--    </ul>-->
    <!---->
    <!--</div>-->


    <script type="text/javascript">
    /* <![CDATA[ */
    new YpSlideOutMenuHelper();

    {if Acl::_('USER_ADMIN') eq true}
    var users_online = [];
    function linkToMB() {
        $('MB_content').select('td a.modal').each(function(item) {
            item.observe('click', function(event) {
                Event.stop(event);

                Modalbox.show(this.href, {
                    title: 'Usuarios activos',
                    afterLoad: linkToMB,
                    width: 300
                });
            });
        });
    }

    document.observe('dom:loaded', function() {
        if( $('user_activity') ) {
            $('user_activity').observe('click', function() {
                Modalbox.show('./index.php?action=show_panel', {
                    title: 'Usuarios activos',
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

	{block name="footer-js"}
		{if $smarty.request.action == 'new' || $smarty.request.action == 'read'}
		try {
			// Activar la validación
			new Validation('formulario', { immediate : true });
			Validation.addAllThese([
				['validate-password',
					'Su password debe contener mas de 5 caracteres y no contener la palabra \'password\' o su nombre de usuario', {
					minLength : 6,
					notOneOf : ['password','PASSWORD','Password'],
					notEqualToField : 'login'
				}],
				['validate-password-confirm',
					'Compruebe su primer password, por favor intentelo de nuevo.', {
					equalToField : 'password'
				}]
			]);

			// Para activar los separadores/tabs
			$fabtabs = new Fabtabs('tabs');
		} catch(e) {
			// Escondemos los errores
			//console.log( e );
		}
		{/if}
	{/block}
    </script>

</body>
</html>
