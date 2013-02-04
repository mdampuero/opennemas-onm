<!doctype html>
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>

    <meta charset="utf-8">

    <meta name="author"    content="OpenHost,SL">
    <meta name="generator" content="OpenNemas - News Management System">
    <meta name="viewport"  content="width=device-width,initial-scale=1">

    {block name="meta"}
        <title>{setting name=site_name} - OpenNeMaS - Administration section</title>
    {/block}

    <link rel="icon" href="{$params.IMAGE_DIR}favicon.png">
    {block name="header-css"}
        {css_tag href="/bootstrap/bootstrap.css" common=1}
        {css_tag href="/style.css"}
        {css_tag href="/admin.css"}
        <!--[if IE]>{css_tag href="/ie.css"}<![endif]-->
        {css_tag href="/buttons.css"}
        {css_tag href="/jquery/jquery-ui.css" media="all" type="text/css"}
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
        {script_tag src="/modernizr/modernizr-2.0.6.min.js"}
        {script_tag src="/utils.js"}
        {script_tag src="/utils_header.js"}
        {script_tag src="/validation.js"}
     {/block}

     {block name="footer-js"}
        {script_tag src="/tiny_mce/tiny_mce_gzip.js"}
     {/block}

</head>
<body>

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
    {/block}

    {block name="footer-js"}
        {script_tag src="/onm/footer-functions.js"}

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

</body>
</html>
