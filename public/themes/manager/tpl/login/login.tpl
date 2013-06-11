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
        {css_tag href="/style-navbar.css" media="screen"}
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

        <form method="post" autocomplete="off" action="{url name=manager_login_processform}" id="loginform" name="loginform" class="clearfix">

            <div class="input-wrapper">
                <input name="login" id="user_login" type="text" class="input-medium" tabindex="1" value="{$smarty.cookies.login_username|default:""}" autofocus placeholder="{t}User name{/t}">
                <input type="password" name="password" id="password" class="input-medium" tabindex="2" value="{$smarty.cookies.login_password|default:""}" placeholder="{t}Password{/t}">

                <button id="submit-button" type="submit" tabindex="3" class="onm-button blue"><span>{t}Enter{/t}</span></button>
            </div>
        <input type="hidden" name="token" value="{$smarty.session.csrf}">
        <input type="hidden" name="forward_to" value="{$smarty.get.forward_to}">
        <input type="hidden" name="time" value="{$smarty.now}">
        </form>
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
