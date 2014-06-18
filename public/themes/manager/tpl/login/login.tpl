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

    <link rel="icon" href="{$params.COMMON_ASSET_DIR}images/favicon.png">

    {block name="header-css"}
        {css_tag href="/bootstrap/bootstrap.css" media="screen" common=1}
        {css_tag href="/style.css" media="screen" common=1}
        {css_tag href="/loginadmin.css" media="screen" common=1}
    {/block}

</head>
<body id="loginpage">

    <header class="clearfix">
        <div class="navbar navbar-inverse global-nav" style="position:fixed">
            <div class="navbar-inner">
                <a class="btn btn-navbar" data-toggle="collapse" data-target=".navbar-inverse-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>

                <a  href="{url name=admin_welcome}" class="brand ir logoonm" title="{t}Go to admin main page{/t}">OpenNemas</a><div class="nav-collapse collapse navbar-inverse-collapse">
                <ul class="nav pull-left">
                    <li>
                        <a href="http://www.opennemas.com">{t}The CMS for journalism{/t}</a>
                    </li>
                </ul>
                <div class="nav-collapse collapse navbar-inverse-collapse">
                    <ul class="nav pull-right">
                        <li class="help dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <span class="icon-large icon-question-sign"></span> {t}Help{/t}
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="http://help.opennemas.com">{t}FAQ{/t}</a>
                                </li>
                                <li>
                                    <a href="javascript:UserVoice.showPopupWidget();" class="support-button">{t}Contact support{/t}</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <div class="form-wrapper">
        <h2>{t}Log in{/t} <span class="pull-right muted">Manager</span></h2>
        {render_messages}
        <form method="post" autocomplete="off" action="{url name=manager_login_processform}" id="loginform" name="loginform" class="clearfix form-horizontal">
            <div class="input-wrapper">
                <div class="control-group">
                    <label class="control-label" for="_username">{t}Username or email{/t}</label>
                    <div class="controls">
                        <input name="_username" id="_username" type="text" class="input-medium" tabindex="1" value="{$smarty.cookies.login_username|default:""}" autofocus placeholder="{t}User name{/t}">
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label" for="_password">{t}Password{/t}</label>
                    <div class="controls">
                        <input type="password" name="_password" id="_password" class="input-medium" tabindex="2" value="{$smarty.cookies.login_password|default:""}" placeholder="{t}Password{/t}">
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label"></label>
                    <div class="controls">
                        {if $failed_login_attempts >= 3}
                        <div class="control-group clearfix">
                            <script type="text/javascript" src="http://www.google.com/recaptcha/api/challenge?k=6LfLDtMSAAAAAEdqvBjFresKMZoknEwdo4mN8T66"></script>
                            <noscript>
                                <iframe src="http://www.google.com/recaptcha/api/noscript?k=6LfLDtMSAAAAAEdqvBjFresKMZoknEwdo4mN8T66" height="300" width="500" frameborder="0"></iframe><br>
                                <textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
                                <input type="hidden" name="recaptcha_response_field" value="manual_challenge">
                            </noscript>
                        </div>
                        {/if}

                        <div class="submit">
                            <button id="submit-button" type="submit" tabindex="4" class="onm-button blue"><span>{t}Log in{/t}</span></button>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="_token" value="{$token}">
            <input type="hidden" name="_referer" value="{$referer}">
        </form>
    </div>

    <footer>
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
