<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">

    <meta name="author"    content="OpenHost,SL">
    <meta name="generator" content="OpenNemas - News Management System">
    <meta name="viewport"  content="width=device-width">

    {block name="meta"}
        <title>{setting name=site_name} - OpenNeMaS administration</title>
    {/block}

    <link rel="icon" href="{$params.IMAGE_DIR}favicon.png">
    {block name="header-css"}
        {css_tag href="/bootstrap/bootstrap.css"}
        {css_tag href="/style.css"}
        <!--[if IE]>{css_tag href="/ie.css"}<![endif]-->
        {css_tag href="/jquery/jquery-ui.css" media="all" type="text/css"}
	{/block}

    {*block name="prototype"}
        {script_tag src="/prototype.js"}
        {script_tag src="/scriptaculous/scriptaculous.js"}
        {script_tag src="/scriptaculous/effects.js"}
    {/block*}

    {block name="js-library"}
        {script_tag src="/jquery/jquery.min.js"}
        {script_tag src="/libs/bootstrap.js"}
        {script_tag src="/libs/jquery.tools.min.js"}
        {script_tag src="/jquery-onm/jquery.onmvalidate.js"}
        {block name="prototype"}{/block}
    {/block}

    {block name="header-js"}
        {script_tag src="/libs/modernizr.min.js"}
        {block name="js-library"}{/block}
        {script_tag src="/onm/scripts.js"}
        {script_tag src="/tiny_mce/tiny_mce_gzip.js"}
     {/block}

</head>
<body>
    <header class="global-nav clearfix">
        <div class="logoonm pull-right">
            <a  href="{url name=admin_welcome}" id="logo-onm" class="brand ir" title="{t}Go to admin main page{/t}">OpenNemas</a>
            <ul>
               <li><a href="/">{t}Visit site{/t}</a></li>
            </ul>
        </div>
        <div class="global-menu pull-left">
            {admin_menu file='/Backend/Resources/Menu.php' base=$smarty.const.APP_PATH}
        </div>
        <div class="global-user-tools pull-right">

            <div class="global-search nofillonhover">
                <form action="{url name=admin_search}">
                    <input type="search" name="search_string" placeholder="{t}Search...{/t}" class="string-search">
                </form>
            </div>

            {if {count_pending_comments} gt 0}
            <div class="notification-messages">
                <a  class="comments-available" title="{t}There are new comments to moderate{/t}"
                    href="{url name=admin_comments}">
                    <span class="icon">{count_pending_comments}</span>
                </a>
            </div>
            {/if}

            <div class="usermenu">
                <a href="#" class="menu"><span class="icon">&nbsp;</span></a>
    			<div>
                    <div class="avatar">
                        {gravatar email=$smarty.session.email image_dir=$params.IMAGE_DIR image=true size="50"}
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
        {script_tag src="/libs/tinycon.min.js"}
        <script type="text/javascript">
        Tinycon.setBubble({count_pending_comments});
        </script>
	{/block}

    {if Acl::check('USER_ADMIN') eq true}
    {*include file="welcome/modals/_modal_users.tpl"*}
    {/if}

</body>
</html>
