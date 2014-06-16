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
        {css_tag href="/bootstrap/bootstrap.css" common=1}
        {css_tag href="/fontawesome/font-awesome.min.css" common=1}
        {css_tag href="/style.css" common=1}
        <!--[if IE]>{css_tag href="/ie.css"}<![endif]-->
        {css_tag href="/jquery/jquery-ui.css" media="all" type="text/css" common=1}
        {css_tag href="/jquery/select2/select2-bootstrap.css" media="all" type="text/css" common=1}
        {css_tag href="/jquery/select2/select2.css" media="all" type="text/css" common=1}
        {css_tag href="/jquery/bootstrap-checkbox/bootstrap-checkbox.css" media="all" type="text/css" common=1}
        {css_tag href="/jquery/messenger/messenger.css" media="all" type="text/css" common=1}
        {css_tag href="/jquery/messenger/messenger-spinner.css" media="all" type="text/css" common=1}
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
        {script_tag src="/onm/scripts.js" common=1}
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
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="usericon"></span> <span class="longtext">{$smarty.session.username}</span> <b class="caret"></b></a>
                            <div class="dropdown-menu">
                                <div class="avatar">
                                    {gravatar email=$smarty.session.email image_dir="{$params.COMMON_ASSET_DIR}images/" image=true size="150"}
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
        {script_tag src="/onm/footer-functions.js" common=1}
    {/block}


    <!--[if lt IE 7 ]>
        <script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.2/CFInstall.min.js"></script>
        <script>window.attachEvent("onload",function(){ CFInstall.check({ mode:"overlay" }) })</script>
    <![endif]-->

</body>
</html>
