{*
    OpenNeMas project
    @theme      Lucidity
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns:fb="http://www.facebook.com/2008/fbml" lang="es">
<head>
    <meta http-equiv="pragma" content="no-cache" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="author" content="OpenHost,SL" />
    <meta name="generator" content="OpenNemas - Open Source News Management System" />
    {block name='meta'}
	{/block}
    {block name='header-css'}
    <link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}admin.css?cacheburst=1259173764"/>
    <link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}style.css"/>
    <!--[if IE]><link rel="stylesheet" href="{$params.CSS_DIR}ieadmin.css" type="text/css" /><![endif]-->
    <link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}botonera.css"/>
    <link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}lightview.css" />
    <link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}datepicker.css"/>
    <link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}welcomepanel.css?cacheburst=1257955982" />
    <link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}lightwindow.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}messageboard.css" media="screen" />
    {/block}

    {block name="header-js"}
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
    {*Move functions js - utils_header.js*}
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utils_header.js"></script>

    {/block}
</head>
<body>
    {* scriptsection name="body" *}
    <script type="text/javascript" src="{$params.JS_DIR}wz_tooltip.js"></script>
    {gmail_mailbox}

    <div id="container" class="span-24 last">
    {block name="content"}
        <table border="0" cellpadding="0" cellspacing="0" align="center" width="100%" height="100%">
        <tr><td valign="top" align="left"><!-- INICIO: Tabla contenedora -->
        <form action="#" method="post" name="formulario" id="formulario" {$formAttrs}>
        <table border="0" cellpadding="0" cellspacing="0" align="left" width="100%" height="100%">
            <tr>
                <td style="padding:10px;width:100%;" align="left" valign="top">

                {if isset($smarty.session.messages) && !empty($smarty.session.messages)}
                    {messageboard type="inline"}
                {else}
                    {messageboard type="growl"}
                {/if}
    {/block}
    </div><!-- #container -->

    <div id="container" class="span-24 last">
    {block name="footer"}
    {/block}
    </div>

    {block name="footer-js"}
    <script type="text/javascript">
    /* <![CDATA[ */
    new YpSlideOutMenuHelper();

    <?php if(Acl::_('USER_ADMIN')): ?>
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
                $('user_activity').update('<img src="<?php echo $RESOURCES_PATH?>images/loading.gif" border="0" width="16" height="16" />');
                new Ajax.Request('index.php', {
                    onSuccess: function(transport) {
                        // Actualizar o número de usuarios en liña e gardar o array en users_online
                        eval('users_online = ' + transport.responseText + ';');
                        $('user_activity').update( users_online.length );

                        //new Effect.Hightlight('user_activity', {startcolor: '#ffff99', endcolor: '#ffffff'});
                    }
                });
                //pe.stop();
            }, 5*60); // Actualizar cada 2*60 segundos
        }
    });
    <?php endif; ?>
    /* ]]> */
    </script>
    {/block}

</body>
</html>
