<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

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

    {block name="prototype"}
        {script_tag src="/prototype.js"}
        {script_tag src="/scriptaculous/scriptaculous.js"}
        {script_tag src="/scriptaculous/effects.js"}
    {/block}

    {block name="js-library"}
        {script_tag src="/jquery/jquery.min.js"}
        <script type="text/javascript">
        jQuery.noConflict();
        </script>
        {script_tag src="/bootstrap/bootstrap.js"}
        {script_tag src="/jquery/bootstrap-modal.js"}
        {block name="prototype"}{/block}
    {/block}

    {block name="header-js"}
        {block name="js-library"}{/block}
        {script_tag src="/onm/scripts.js"}
        {script_tag src="/modernizr/modernizr-2.5.0.min.js"}
        {script_tag src="/utils.js"}
        {script_tag src="/utils_header.js"}
        {script_tag src="/validation.js"}
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
            {admin_menu}
        </div>
        <div class="global-user-tools pull-right">

            <div class="global-search nofillonhover">
                <form action="{url name=admin_search}">
                    <input type="hidden" name="id" value="0" />
                    <input type="hidden" name="article" value="on" />
                    <input type="hidden" name="opinion" value="on" />
                    <input type="search" name="stringSearch" placeholder="{t}Search...{/t}" class="string-search">
                </form>
            </div>

            {if {count_pending_comments} gt 0}
            <div class="notification-messages">
                <a  class="comments-available" title="{t}There are new comments to moderate{/t}"
                    href="{$smarty.const.SITE_URL_ADMIN}/controllers/comment/comment.php?action=list&amp;category=todos">
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
                            <li><a id="settings" title="{t}Edit my profile{/t}" href="{url name=admin_acl_user_show id=$smarty.session.userid}">{t}Edit my profile{/t}</a></li>
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


    {if Acl::check('USER_ADMIN') eq true}
    {include file="welcome/modals/_modal_users.tpl"}
    {script_tag src="/onm/footer-functions-admin.js"}
    {/if}

    <!--[if lt IE 7 ]>
        <script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.2/CFInstall.min.js"></script>
        <script>window.attachEvent("onload",function(){ CFInstall.check({ mode:"overlay" }) })</script>
    <![endif]-->

</body>
</html>
