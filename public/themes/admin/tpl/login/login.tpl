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
        <style type="text/css">
            #recaptcha_area {
                float: left;
            }
            #recaptcha_privacy {
                display:none;
            }
        </style>
    {/block}
    {block name="header-js"}
    <script type="text/javascript">
        var RecaptchaOptions = { theme : 'white' };
    </script>
    {/block}

</head>
<body id="loginpage">

    <div id="logo">
        <h1>OpenNeMaS</h1>
        <div>{t}The journalism CMS{/t}</div>
    </div>

    <div class="form-wrapper">
        {render_messages}
    	<form method="post" autocomplete="off" action="{url name=admin_login_processform}" id="loginform" name="loginform" class="clearfix">
			<div class="input-wrapper">
                <input name="login" id="user_login" type="text" class="input-medium" tabindex="1" value="{$smarty.cookies.login_username|default:""}" autofocus placeholder="{t}User name{/t}">
                <input type="password" name="password" id="password" class="input-medium" tabindex="2" value="{$smarty.cookies.login_password|default:""}" placeholder="{t}Password{/t}">
                {if $smarty.session.failed_login_attempts >= 3}
                <button id="submit-button" type="submit" tabindex="4" class="onm-button blue"><span>{t}Enter{/t}</span></button>
                <div class="control-group clearfix">
                    <script type="text/javascript" src="http://www.google.com/recaptcha/api/challenge?k=6LfLDtMSAAAAAEdqvBjFresKMZoknEwdo4mN8T66"></script>
                    <noscript>
                        <iframe src="http://www.google.com/recaptcha/api/noscript?k=6LfLDtMSAAAAAEdqvBjFresKMZoknEwdo4mN8T66" height="300" width="500" frameborder="0"></iframe><br>
                        <textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
                        <input type="hidden" name="recaptcha_response_field" value="manual_challenge">
                    </noscript>
                </div>
                {/if}
                <p class="left {if $smarty.session.failed_login_attempts >= 3}toomuchfails{/if}">
                    <a href="{url name=admin_acl_user_recover_pass}" class="recover_pass">{t domain=base}Forgot Password?{/t}</a>
                </p>
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
                        <li><a href="http://www.opennemas.com" target="_blank" title="Go to opennemas website">{t}About{/t}</a></li>
                        <li><a href="http://help.opennemas.com" target="_blank" title="{t}Help{/t}">{t}Help{/t}</a></li>
                        <li><a href="http://help.opennemas.com/knowledgebase/articles/235300-opennemas-pol%C3%ADtica-de-privacidad"
                               target="_blank" title="{t}Privacy Policy{/t}">{t}Privacy Policy{/t}</a></li>
                        <li><a href="http://help.opennemas.com/knowledgebase/articles/235418-terminos-de-uso-de-opennemas"
                               target="_blank" title="{t}Legal{/t}">{t}Legal{/t}</a></li>
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
