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
            .form-wrapper button[type="submit"] {
                float: none;
                max-width: 100px;
                height: 34px;
                margin-bottom: 9px;
            }
        </style>
    {/block}

</head>
<body id="loginpage">

    <div id="logo">
        <h1>OpenNeMaS</h1>
        <div>{t}The journalism CMS{/t}</div>
    </div>

    <div class="form-wrapper">
        {render_messages}

        {if $mailSent}
        <div class="doubleRule">
            <div class="mcSectionHeader opposingFloatControl wrap">
                <h2 class="element1">{t}Check Your E-Mail{/t}</h2>
            </div>
            <div>
                <div class="insetV">
                    <p>
                        {t}We've sent an e-mail to{/t}:<strong>&nbsp;&nbsp;{$user->email}</strong>.
                    </p>
                    <p>
                        {t}Please check your e-mail now for a message with the subject line "Username reminder" from{/t}&nbsp;{setting name="site_title"}.
                    </p>
                    <p>
                        {t}To protect your privacy, we only send this information to the e-mail address associated with this account.{/t}
                    </p>
                </div>
                <div class="insetV">
                    <p>
                        <strong>{t}PLEASE NOTE{/t}:</strong>
                        {t}If this is not the e-mail address associated with your account, click the link to resubmit request with the correct e-mail address{/t}:&nbsp;&nbsp;<a href="{url name=admin_acl_user_recover_user}">{t}Recover your username{/t}</a>
                    </p>
                    <p>
                        {t}If you use e-mail filtering or anti-spam software,please make sure our e-mail is not filtered or blocked.{/t}
                    </p>
                </div>
            </div>
        </div>
        {else}
        <form class="form-horizontal" id="formulario" action="{url name=admin_acl_user_recover_user}" method="POST">

            <div class="input-wrapper">
                <p>
                    {t}Enter your e-mail address and click Submit to recover your username.{/t}
                </p>

                <input type="email" class="input-xlarge" name="email" required="required" autofocus placeholder="{t}E-mail{/t}">

                <button type="submit" class="onm-button blue">{t}Submit{/t}</button>
            </div>
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
