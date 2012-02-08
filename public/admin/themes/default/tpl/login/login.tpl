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
    <meta name="robots" content="noindex, nofollow" />
    <meta name="description" content="OpenNeMaS - An specialized CMS focused in journalism." />
    <meta name="keywords" content="CMS, Opennemas, OpenHost, journalism" />

    <link rel="icon" href="{$params.IMAGE_DIR}favicon.png">

    {block name="meta"}
        <title>{setting name=site_name} - OpenNeMaS - Administration section</title>
    {/block}

    {block name="header-css"}
        {css_tag href="/bp/screen.css" media="screen, projection"}
        {css_tag href="/loginadmin.css"}
        {css_tag href="/bp/print.css" media="print"}
        <!--[if lt IE 8]{css_tag href="/bp/ie.css" media="screen, projection"}[endif]-->
        {css_tag href="/buttons.css"}
    {/block}


    {block name="js-library"}
        {script_tag src="/jquery/jquery.min.js"}
        {script_tag src="/jquery/jquery-ui.min.js"}
        <script type="text/javascript">
            jQuery(document).ready(function ($){
                jQuery.noConflict();
            });
        </script>
    {/block}

    {block name="footer-js"}
        {block name="js-library"}{/block}
        {script_tag src="/modernizr/modernizr-2.0.6.min.js"}
    {/block}

</head>
<body id="loginpage">
	<div id="login-wrapper"  class="span-16 last clearfix">
		<div id="t_a_auth_container" class="clearfix">

		<form method="post" action="login.php" id="loginform" name="loginform" class="clearfix">
			<div class="span-16 last">
                <div id="logo">
					<h1>OpenNeMaS</h1>
					<div>{t}The journalism CMS{/t}</div>
				</div>

				{if isset($message) && !empty($message)}
				<div class="span-16 last">
					<div class="span-14 prepend-1 append-1">
						<div class="notice">{$message}</div>
					</div>
				</div>
				{/if}

				<div class="span-16 last">
					<div class="span-8">
						<label for="user_login">{t}User name:{/t}</label>
					</div>
					<div class="span-8 last">
						<label for="password">{t}Password:{/t}</label>
					</div>
				</div>

				<div class="span-16 last">
					<div class="span-7 append-1">
						<input name="login" id="user_login" type="text" tabindex="1" value="{$smarty.cookies.login_username|default:""}" autofocus>
					</div>
					<div class="span-7 last">
						<input type="password" name="password" id="password" tabindex="2" value="{$smarty.cookies.login_password|default:""}">
					</div>
				</div>

				<div class="span-16 last clearfix submit-remember-block">
					<div class="span-16 last right">
						<button id="submit-button" type="submit" tabindex="4" class="onm-button blue"><span>{t}Enter{/t}</span></button>
					</div>
				</div>
			</div>
			<input type="hidden" id="action" name="action" value="login">
            <input type="hidden" name="token" value="{$smarty.session.csrf}">
            <input type="hidden" name="forward_to" value="{$smarty.get.forward_to}">
		</form>

		</div>
	</div>

    <footer>
        <nav class="left">
            <ul>
                <li>&copy; {strftime("%Y")} OpenHost S.L.</li>
                {foreach from=$languages key=key item=language}
                    <li>
                        <a href="?language={$key}" title="{$language}" {if $key == $current_language}class="active"{/if}>{$language}</a>
                    </li>
                {/foreach}
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
    </footer>
</body>
</html>
