<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
    <meta charset="utf-8">

    <meta name="author"    content="OpenHost,SL">
    <meta name="generator" content="OpenNemas - News Management System">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    {block name="meta"}
    <title>OpenNeMaS - Manager</title>
    {/block}

    <link rel="icon" href="{$params.COMMON_ASSET_DIR}images/favicon.png">
    {block name="header-css"}
        {stylesheets
            src="@Common/css/bootstrap/bootstrap.css,
                @Common/css/fontawesome/font-awesome.min.css,
                @Common/css/style.css,
                @Common/css/jquery/jquery-ui.css,
                @Common/css/jquery/select2/select2-bootstrap.css,
                @Common/css/jquery/select2/select2.css,
                @Common/css/jquery/messenger/messenger.css,
                @Common/css/jquery/messenger/messenger-spinner.css,
                @Common/css/jquery/bootstrap-checkbox/bootstrap-checkbox.css,
                @AdminTheme/css/jquery/bootstrap-nav-wizard.css"
            filters="cssrewrite"}
            <link rel="stylesheet" type="text/css" href="{$asset_url}">
        {/stylesheets}
        <!--[if IE]>{css_tag href="/ie.css"}<![endif]-->
    {/block}

    {block name="header-js"}
        {javascripts
            src="@Common/js/jquery/jquery.min.js,
                @Common/js/libs/bootstrap.js,
                @Common/js/jquery/select2/select2.min.js,
                @Common/js/jquery-onm/jquery.onmvalidate.js,
                @Common/js/libs/jquery.tools.min.js,
                @Common/js/libs/tinycon.min.js,
                @Common/js/libs/modernizr.min.js,
                @Common/js/onm/scripts.js,
                @Common/js/onm/footer-functions.js,
                @Common/js/onm/jquery.onm-editor.js,
                @AdminTheme/js/jquery/bootstrap-nav-wizard.js"}
            <script type="text/javascript" src="{$asset_url}"></script>
        {/javascripts}
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
                <div class="nav-collapse collapse navbar-inverse-collapse">
                    <ul class="nav pull-right">
                        <li class="dropdown usermenu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                {if $smarty.session.email}
                                    {gravatar email=$smarty.session.email image_dir="{$params.COMMON_ASSET_DIR}images/" image=true size="24"}
                                {else}
                                    <span class="usericon"></span>
                                {/if}
                                <span class="longtext">{$smarty.session.username}</span> <b class="caret"></b>
                            </a>
                            <div class="dropdown-menu">
                                <div class="avatar">
                                    {if $smarty.session.email}
                                        {gravatar email=$smarty.session.email image_dir="{$params.COMMON_ASSET_DIR}images/" image=true size="150"}
                                    {else}
                                        <span class="usericon"></span>
                                    {/if}
                                </div><!-- /.avatar -->
                                <div class="user-info">
                                    <div class="complete-name">{$smarty.session.realname|ucfirst}</div>
                                    <div class="login-name">{$smarty.session.username}</div>
                                    <ul class="links">
                                        <li><a id="settings" title="{t}Edit my profile{/t}" href="{url name=manager_acl_user_show id=me}">{t}Edit my profile{/t}</a></li>
                                        <li><a href="javascript:salir('{t}Do you really want to exit from manager?{/t}','{url name="manager_logout"  csrf=$smarty.session.csrf}');" id="logout" class="logout" title="{t}Logout from manager{/t}">{t}Log out{/t}</a></li>
                                    </ul><!-- /.links -->
                                </div><!-- /.user-info -->
                            </div>
                        </li>
                    </ul>

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
                    <li><a href="http://www.opennemas.com" target="_blank" title="Go to opennemas website">{t}About{/t}</a></li>
                    <li><a href="http://help.opennemas.com" target="_blank" title="{t}Help{/t}">{t}Help{/t}</a></li>
                    <li><a href="http://help.opennemas.com/knowledgebase/articles/235300-opennemas-pol%C3%ADtica-de-privacidad"
                           target="_blank" title="{t}Privacy Policy{/t}">{t}Privacy Policy{/t}</a></li>
                    <li><a href="http://help.opennemas.com/knowledgebase/articles/235418-terminos-de-uso-de-opennemas"
                           target="_blank" title="{t}Legal{/t}">{t}Legal{/t}</a></li>
                </ul>
            </nav>
        </div><!-- / -->
    </footer>
    {/block}

    {block name="footer-js"}
        {browser_update}
    {/block}

    <!--[if lt IE 7 ]>
        <script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.2/CFInstall.min.js"></script>
        <script>window.attachEvent("onload",function(){ CFInstall.check({ mode:"overlay" }) })</script>
    <![endif]-->

</body>
</html>
