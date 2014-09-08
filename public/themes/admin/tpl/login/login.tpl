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
        {stylesheets
            src="@Common/css/bootstrap/bootstrap.css,
                @Common/css/fontawesome/font-awesome.min.css,
                @Common/css/style.css,
                @Common/css/loginadmin.css"
            filters="cssrewrite"}
            <link rel="stylesheet" type="text/css" href="{$asset_url}">
        {/stylesheets}
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
        var RecaptchaOptions = { theme : 'white', tabindex: 3, lang: '{$smarty.const.CURRENT_LANGUAGE_SHORT}' };
    </script>
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
        <h2>{t}Log in{/t}</h2>

        {render_messages}

    	<form method="post" autocomplete="off" action="{url name=admin_login_check}" id="loginform" name="loginform" class="clearfix form-horizontal">
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
                            <a href="{url name=admin_acl_user_recover_pass}" class="recover_pass">{t domain=base}Forgot Password?{/t}</a>
                        </div>
                    </div>
                </div>
                <div class="or"><span class="text">{t}or use{/t}</span></div>
                <div class="social-network-buttons row-fluid">
                    <a class="span6 btn" href="{hwi_oauth_login_url name=facebook}">
                        <i class="social-icon icon-facebook"></i> Facebook
                    </a>
                    <a class="span6 btn" href="{hwi_oauth_login_url name=twitter}">
                        <i class="social-icon icon-twitter"></i> Twitter
                    </a>
                </div>
            </div>
            <input type="hidden" id="_token" name="_token" value="{$token}">
            <input type="hidden" id="_referer" name="_referer" value="{$referer}">
        </form>
    </div>

    <footer>
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
    </footer>

    {block name="footer-js"}
        {javascripts
            src="@Common/js/jquery/jquery.min.js,
                @Common/js/libs/bootstrap.js,
                @Common/js/libs/modernizr.min.js,
                @Common/js/onm/md5.min.js,
                @Common/js/admin.js"}
            <script type="text/javascript" src="{$asset_url}"></script>
        {/javascripts}
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            BackendAuthentication.init()
        });
        </script>
        {uservoice_widget}
    {/block}
</body>
</html>
