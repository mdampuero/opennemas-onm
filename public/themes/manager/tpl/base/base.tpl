<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
    <meta charset="utf-8">

    <meta name="author"    content="OpenHost,SL">
    <meta name="generator" content="OpenNemas - News Management System">
    <meta name="viewport" content="width=device-width">

    {block name="meta"}
    <title>OpenNeMaS - Administration section</title>
    {/block}

    <link rel="icon" href="{$params.IMAGE_DIR}favicon.png">
    {block name="header-css"}
        {css_tag href="/bootstrap/bootstrap.css" common=1}
        {css_tag href="/fontawesome/font-awesome.min.css" common=1}
        {css_tag href="/style.css" common=1}
        {css_tag href="/style-navbar.css"}
        <!--[if IE]>{css_tag href="/ie.css"}<![endif]-->
        {css_tag href="/jquery/jquery-ui.css" media="all" type="text/css" common=1}
    {/block}

    {block name="js-library"}
        {script_tag src="/jquery/jquery.min.js" common=1}
        {script_tag src="/libs/bootstrap.js" common=1}
        {script_tag src="/libs/jquery.tools.min.js" common=1}
        {script_tag src="/jquery-onm/jquery.onmvalidate.js" common=1}
        {block name="prototype"}{/block}
    {/block}

    {block name="header-js"}
        {script_tag src="/libs/modernizr.min.js" common=1}
        {block name="js-library"}{/block}
        {script_tag src="/onm/scripts.js"}
        {script_tag src="/tiny_mce/tiny_mce_gzip.js" common=1}
     {/block}

</head>
<body class="manager">

    <header class="clearfix">
        <div class="navbar navbar-inverse global-nav manager" style="position:fixed">
            <div class="navbar-inner">
                <a class="btn btn-navbar" data-toggle="collapse" data-target=".navbar-inverse-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>

                <a  href="{url name=manager_welcome}" class="brand ir logoonm" title="{t}Go to admin main page{/t}">OpenNemas</a>
                <div class="nav pull-left" accesskey="m">
                    {admin_menu file='/Manager/Resources/Menu.php' base=$smarty.const.SRC_PATH}
                </div>
            </div>
        </div>
    </header>

    <div id="content" role="main">
    {block name="content"}{/block}
    </div>

    {block name="copyright"}
    <footer class="wrapper-content">
        <div class="clearfix">
            <nav class="left">
                <ul>
                    <li>&copy; {strftime("%Y")} OpenHost S.L.</li>
                </ul><!-- / -->
            </nav>
            <nav class="right">
                <ul>
                    <li><a href="http://www.openhost.es/opennemas" title="Go to opennemas website">{t}About{/t}</a></li>
                    <li><a href="#help" title="{t}Help{/t}">{t}Help{/t}</a></li>
                    <li><a href="#privacypolicy" title="{t}Privacy Policy{/t}">{t}Privacy Policy{/t}</a></li>
                    <li><a href="#legal" title="{t}Legal{/t}">{t}Legal{/t}</a></li>
                </ul>
            </nav>
        </div><!-- / -->
    </footer>
    {/block}

    {block name="footer-js"}
        {browser_update}
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
        }
        </script>
        {/if}
    {/block}


    <!--[if lt IE 7 ]>
        <script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.2/CFInstall.min.js"></script>
        <script>window.attachEvent("onload",function(){ CFInstall.check({ mode:"overlay" }) })</script>
    <![endif]-->

</body>
</html>
