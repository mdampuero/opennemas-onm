<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">

    <meta name="author"    content="OpenHost,SL">
    <meta name="generator" content="OpenNemas - News Management System">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    {block name="meta"}
        <title>{setting name=site_name} - {t}OpenNeMaS administration{/t}</title>
    {/block}

    <link rel="icon" href="{$params.IMAGE_DIR}favicon.png">
    {block name="header-css"}
        {css_tag href="/bootstrap/bootstrap.css" common=1}
        {css_tag href="/fontawesome/font-awesome.min.css" common=1}
        {css_tag href="/style.css" common=1}
        <!--[if IE]>{css_tag href="/ie.css"}<![endif]-->
        {css_tag href="/jquery/jquery-ui.css" media="all" type="text/css"}
	{/block}

    {block name="js-library"}
        {script_tag src="/jquery/jquery.min.js" common=1}
        {script_tag src="/libs/bootstrap.js" common=1}
        {script_tag src="/libs/jquery.tools.min.js" common=1}
        {script_tag src="/jquery-onm/jquery.onmvalidate.js" common=1}
    {/block}

    {block name="header-js"}
        {script_tag src="/libs/modernizr.min.js" common=1}
        {block name="js-library"}{/block}
        {script_tag src="/onm/scripts.js" common=1}
     {/block}

</head>
<body>
    <header class="clearfix">
        <div class="navbar navbar-inverse global-nav" style="position:fixed">
            <div class="navbar-inner">
                <a class="btn btn-navbar" data-toggle="collapse" data-target=".navbar-inverse-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>

                <a  href="{url name=admin_welcome}" class="brand ir logoonm" title="{t}Go to admin main page{/t}">OpenNemas</a>
                <div class="nav-collapse collapse navbar-inverse-collapse">
                    {admin_menu file='/Backend/Resources/Menu.php' base=$smarty.const.SRC_PATH}
                    <ul class="nav pull-right">
                        <li>
                            <form action="{url name=admin_search}" class="navbar-search global-search nofillonhover pull-right">
                                <input type="search" name="search_string" placeholder="{t}Search...{/t}" class="string-search" accesskey="s">
                            </form>
                        </li>
                        {if {count_pending_comments} gt 0}
                        <li class="notification-messages">
                            <a class="" title="{count_pending_comments} {t}Pending comments{/t}"
                                href="{url name=admin_comments}">
                                <span class="icon icon-inbox icon-large"></span>
                                <span class="icon count">{count_pending_comments} <span class="longtext">{t}Pending comments{/t}</span></span>
                            </a>
                        </li>
                        {/if}
                        <li class="help dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <span class="icon-large icon-question-sign"></span> {t}Help{/t}
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="http://opennemas.uservoice.com/knowledgebase">{t}FAQ{/t}</a>
                                </li>
                                <li>
                                    <a href="javascript:UserVoice.showPopupWidget();" class="support-button">{t}Contact support{/t}</a>
                                </li>
                            </ul>
                        </li>
                        <li class="dropdown usermenu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">{gravatar email=$smarty.session.email image_dir=$params.IMAGE_DIR image=true size="18"} <span class="longtext">{$smarty.session.username}</span> <b class="caret"></b></a>
                            <div class="dropdown-menu">
                                <div class="avatar">
                                    {gravatar email=$smarty.session.email image_dir=$params.IMAGE_DIR image=true size="150"}
                                </div><!-- /.avatar -->
                                <div class="user-info">
                                    <div class="complete-name">{$smarty.session.realname|ucfirst}</div>
                                    <div class="login-name">{$smarty.session.username}</div>
                                    <ul class="links">
                                        <li><a id="settings" title="{t}Edit my profile{/t}" href="{url name=admin_acl_user_show id=me}">{t}Edit my profile{/t}</a></li>
                                        {if Acl::check('BACKEND_ADMIN') eq true}
                                        {*<li><a href="#" id="user_activity" title="{t}Active users in backend{/t}">{t}Connected users{/t} ({count_sessions})</a></li>*}
                                        {/if}
                                        <li><a href="javascript:salir('{t}Do you really want to exit from backend?{/t}','{url name="admin_logout"  csrf=$smarty.session.csrf}');" id="logout" class="logout" title="{t}Logout from control panel{/t}">{t}Log out{/t}</a></li>
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
    <footer>
        <div class="wrapper-content clearfix">
            <nav class="left">
                <ul>
                    <li>&copy; {strftime("%Y")} OpenHost S.L.</li>
                </ul><!-- / -->
            </nav>
            <nav class="right">
                <ul>
                    <li><a href="http://www.openhost.es/opennemas" title="Go to opennemas website">{t}About{/t}</a></li>
                    <!-- <li><a href="#help" title="{t}Help{/t}">{t}Help{/t}</a></li> -->
                    <li><a href="#privacypolicy" title="{t}Privacy Policy{/t}">{t}Privacy Policy{/t}</a></li>
                    <li><a href="#legal" title="{t}Legal{/t}">{t}Legal{/t}</a></li>
                </ul>
            </nav>
        </div><!-- / -->
    </footer>
	{/block}

    {block name="footer-js"}
        {browser_update}
        {script_tag src="/onm/footer-functions.js" common=1}
        {script_tag src="/libs/tinycon.min.js"}
        <script type="text/javascript">
        Tinycon.setBubble({count_pending_comments});
        </script>
        {uservoice_widget}
        <script>
        var CKEDITOR_BASEPATH = '/assets/js/ckeditor/';
        </script>
        {script_tag src="/ckeditor/ckeditor.js" common=1}
        {script_tag src="/onm/jquery.onm-editor.js" common=1}
        <script type="text/javascript">
        $.onmEditor({
            language: '{$smarty.const.CURRENT_LANGUAGE_SHORT}' ,
        });
        </script>
	{/block}

    {if Acl::check('USER_ADMIN') eq true}
    {*include file="welcome/modals/_modal_users.tpl"*}
    {/if}

</body>
</html>
