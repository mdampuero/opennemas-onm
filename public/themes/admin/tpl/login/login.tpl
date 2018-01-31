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
  <link rel="icon" href="{$_template->getImageDir()}/favicon.png">
  {block name="header-css"}
    <link href="//fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="/assets/components/font-awesome/css/font-awesome.min.css">

    {stylesheets src="@Common/components/bootstrap/dist/css/bootstrap.min.css,
      @Common/components/animate.css/animate.min.css,
      @Common/src/webarch/css/style.css,
      @Common/src/webarch/css/responsive.css,
      @Common/src/webarch/css/custom-icon-set.css,
      @Common/src/webarch/css/magic_space.css,
      @AdminTheme/less/_login.less" filters="cssrewrite,less" output="login"}
    {/stylesheets}
  {/block}
  {block name="header-js"}
    <script type="text/javascript">
      var RecaptchaOptions = { theme : 'white', tabindex: 3, lang: '{$smarty.const.CURRENT_LANGUAGE_SHORT}' };
    </script>
  {/block}
</head>
<body class="login-body">
  <div class="wrapper">
    <div class="overlay"></div>
    <main>
      {block name="login_content"}
        <form method="post" autocomplete="off" action="{url name=core_authentication_check}" id="loginform" name="loginform">
          <div class="container">
            <div class="row login-container animated fadeInUp">
              <div class="col-md-6 col-md-offset-3 tiles white no-padding">
                <div class="p-t-30 p-l-20 p-b-10 xs-p-t-10 xs-p-l-10 xs-p-b-10">
                  <h2 class="normal center">{t escape=off}Sign into open<strong>nemas</strong>{/t}</h2>
                  <p>{t}Use Facebook, Twitter or your email to sign in.{/t}<br></p>
                </div>
                <div class="row m-l-5 m-r-5 p-b-20">
                  <div class="col-sm-6">
                    <button class="btn btn-info btn-block" data-url="{hwi_oauth_login_url name=facebook}" onclick="connect(this)" type="button">
                      <i class="social-icon icon-facebook"></i> Facebook
                    </button>
                  </div>
                  <div class="col-sm-6">
                    <button class="btn btn-success btn-block" data-url="{hwi_oauth_login_url name=twitter}" onclick="connect(this)" type="button">
                      <i class="social-icon icon-twitter"></i> Twitter
                    </button>
                  </div>
                </div>
                <div class="tiles grey p-t-20 p-b-20 text-black">
                  <div class="row m-l-10 m-r-10">
                    <div class="col-sm-12">
                      {render_messages}
                      <div class="form-group">
                        <div class="input-group">
                          <span class="input-group-addon"><span class="fa fa-user"></span></span>
                          <input autofocus class="form-control" id="_username" name="_username" value="{$smarty.cookies.login_username|default:""}" placeholder="{t}Username or email{/t}" tabindex="1" type="text">
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="input-group">
                          <span class="input-group-addon">
                            <i class="fa fa-lock"></i>
                          </span>
                          <input class="form-control" id="_password" name="_password" placeholder="{t}Password{/t}" tabindex="2" type="password" value="{$smarty.cookies.login_password|default:""}">
                        </div>
                      </div>
                      {$recaptcha}
                      <div class="form-group text-right">
                        <a href="{url name=admin_acl_user_recover_pass}" class="recover_pass btn btn-link">{t domain=base}Forgot Password?{/t}</a>
                        <button class="btn btn-primary" id="login-button" tabindex="4" type="submit">{t}Log in{/t}</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <input type="hidden" id="_token" name="_token" value="{$token}">
          <input type="hidden" id="_target" name="_target" value="{$referer}">
        </form>
      {/block}
    </main>
    <footer>
      <div class="muted credit">
        <p class="center">&copy; {strftime("%Y")} OpenHost S.L.</p>
        <ul class="center">
          <li><a href="http://www.opennemas.com" target="_blank" title="Go to opennemas website">{t}About{/t}</a></li>
          <li><a href="http://help.opennemas.com" target="_blank" title="{t}Help{/t}">{t}Help{/t}</a></li>
          <li><a href="http://help.opennemas.com/knowledgebase/articles/235300-opennemas-pol%C3%ADtica-de-privacidad" target="_blank" title="{t}Privacy Policy{/t}">{t}Privacy Policy{/t}</a></li>
          <li><a href="http://help.opennemas.com/knowledgebase/articles/235418-terminos-de-uso-de-opennemas" target="_blank" title="{t}Legal{/t}">{t}Legal{/t}</a></li>
          <li>
            <div class="dropup">
              <a class="dropdown-toggle" type="button" id="languageDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                {t}Language{/t}: {$locales[$locale]}
                <span class="caret"></span>
              </a>
              <ul class="dropdown-menu" aria-labelledby="languageDropdown">
                {foreach from=$locales key=key item=language}
                <li class="{if $key == $locale}current{/if}"><a href="?language={$key}">{$language}</a></li>
                {/foreach}
              </ul>
            </div>
          </li>
        </ul>
      </div>
    </footer>
  </div>
  {block name="footer-js"}
    {javascripts src="@Common/components/jquery/jquery.min.js,
        @Common/components/bootstrap/dist/js/bootstrap.min.js,
        @Common/components/modernizr/modernizr.js,
        @Common/js/onm/md5.min.js" output="login"}
      <script type="text/javascript">
        function connect(btn) {
          var win = window.open(
            btn.getAttribute('data-url'),
            btn.getAttribute('id'),
            'height=400, width=400'
          );

          var interval = window.setInterval(function() {
            if (win == null || win.closed) {
              window.clearInterval(interval);
              window.location = '{url name=admin_welcome}';
            }
          }, 1000);
        };

        jQuery(document).ready(function($) {
          'use strict';

          // Encode password in md5 on backend login
          $('#loginform').on('submit', function(e, ui) {
            var form = $('#loginform');

            if (form.find('input[name="_password"]').length > 0) {
              var password = form.find('input[name="_password"]').val();
              if (password_input_val.indexOf('md5:') === -1) {
                password_input_val = 'md5:' + hex_md5(password_input_val);
              }
              form.find('input[name="_password"]').val(password);
            }
          });
        });
      </script>
    {/javascripts}
  {/block}
</body>
</html>
