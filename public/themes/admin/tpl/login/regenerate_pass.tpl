<!doctype html>
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
    <meta charset="utf-8">

    <title>{setting name=site_name} - OpenNeMaS - Administration section</title>

    <meta name="author"    content="OpenHost,SL">
    <meta name="generator" content="OpenNemas - News Management System">
    <meta name="viewport"  content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="robots"    content="noindex, nofollow" />
    <meta name="description" content="OpenNeMaS - An specialized CMS focused in journalism." />
    <meta name="keywords" content="CMS, Opennemas, OpenHost, journalism" />

    <link rel="icon" href="{$params.IMAGE_DIR}favicon.png">

    {block name="header-css"}
        {css_tag href="/bootstrap/bootstrap.css" media="screen" common=1}
        {css_tag href="/style.css" media="screen" common=1}
        {css_tag href="/loginadmin.css" media="screen" common=1}
    {/block}

</head>
<body id="loginpage">

    <div id="logo">
        <h1>OpenNeMaS</h1>
        <div>{t}The journalism CMS{/t}</div>
    </div>

    <div class="form-wrapper">
        {render_messages}

        {if $userNotValid == true}
        <form method="post" action="{url name=admin_login_processform}" id="loginform" name="loginform" class="clearfix">
            <div class="input-wrapper">
                <input name="login" id="user_login" type="text" class="input-medium" tabindex="1" value="{$smarty.cookies.login_username|default:""}" autofocus placeholder="{t}User name{/t}">
                <input type="password" name="password" id="password" class="input-medium" tabindex="2" value="{$smarty.cookies.login_password|default:""}" placeholder="{t}Password{/t}">
                <button id="login-submit-button" type="submit" tabindex="3" class="onm-button blue"><span>{t}Enter{/t}</span></button>
                <br><br><br>
                <p class="right">
                    <a href="{url name=admin_acl_user_recover_pass}">{t domain=base}Forgot Password?{/t}</a>
                </p>
            </div>
            <input type="hidden" name="token" value="{$smarty.session.csrf}">
            <input type="hidden" name="forward_to" value="{$smarty.get.forward_to}">
            <input type="hidden" name="time" value="{$smarty.now}">
        </form>
        {else}
        <form class="form-horizontal" action="{url name=admin_acl_user_reset_pass token=$token}" method="POST">
            <p>
                {t}Please enter your new password in both fields below, and then click Submit.{/t}
            </p>

            <input type="password" name="password" class="input-medium" required="required" tabindex="1" value="" placeholder="{t}Password{/t}">
            <input type="password" name="password-verify" class="input-medium" required="required" tabindex="2" value="" placeholder="{t}Re-Enter Password{/t}">

            <button type="submit" tabindex="3" class="onm-button blue">{t}Submit{/t}</button>
        </form>
        {/if}
    </div>

    <footer>
        <div class="container">
            <div class="muted credit">
                &copy; {strftime("%Y")} OpenHost S.L.
                <nav>
                    <ul>
                        <li><a href="http://www.openhost.es/opennemas" title="Go to opennemas website">{t}About{/t}</a></li>
                        <li><a href="#help" title="{t}Help{/t}">{t}Help{/t}</a></li>
                        <li><a href="#privacypolicy" title="{t}Privacy Policy{/t}">{t}Privacy Policy{/t}</a></li>
                        <li><a href="#legal" title="{t}Legal{/t}">{t}Legal{/t}</a></li>
                    </ul>
                </nav>
                <select name="language" id="language" class="input-small">
                    {foreach from=$languages key=key item=language}
                        <option value="{$key}" {if $key == $current_language}selected{/if}>{$language}</option>
                    {/foreach}
                </select>
            </div>
        </div>
    </footer>

    {block name="footer-js"}
        {script_tag src="/jquery/jquery.min.js" common=1}
        {script_tag src="/libs/bootstrap.js" common=1}
        {script_tag src="/libs/modernizr.min.js" common=1}
        {script_tag src="/onm/md5.min.js" common=1}
        {script_tag src="/admin.js" common=1}
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            BackendAuthentication.init()
        });
        </script>
    {/block}
</body>
</html>
