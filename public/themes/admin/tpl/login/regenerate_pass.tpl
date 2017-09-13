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

</head>
<body class="login-body">
  <div class="wrapper">
    <main>
      <form action="{url name=admin_acl_user_reset_pass token=$token}" method="POST">
        <div class="container">
          <div class="row login-container animated fadeInUp">
            <div class="col-md-6 col-md-offset-3 tiles white no-padding">
              <div class="p-t-30 p-l-20 p-b-10 xs-p-t-10 xs-p-l-10 xs-p-b-10">
                <h2 class="normal center">{t}Recover password{/t}</h2>
                <p>{t}Please enter your new password in both fields below, and then click Submit.{/t}</p>
              </div>
              <div class="tiles grey p-t-20 p-b-20 text-black">
                <div class="row m-l-10 m-r-10">
                  <div class="col-sm-12">
                    {render_messages}
                    <div class="form-group">
                      <div class="input-group">
                        <span class="input-group-addon"><span class="fa fa-lock"></span></span>
                        <input autofocus class="form-control" name="password" placeholder="{t}Password{/t}" required tabindex="1" type="password">
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="input-group">
                        <span class="input-group-addon"><span class="fa fa-lock"></span></span>
                        <input class="form-control" name="password-verify" placeholder="{t}Password confirmation{/t}" required tabindex="2" type="password">
                      </div>
                    </div>
                    <div class="form-group text-right">
                      <a href="{url name=admin_acl_user_recover_pass}" class="recover_pass btn btn-link">{t domain=base}Forgot Password?{/t}</a>
                      <button class="btn btn-primary" type="submit" id="forgot-pass-button">
                        {t}Submit{/t}
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
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
            <select name="language" id="language" class="language-selector">
              {foreach from=$locales key=key item=value}
              <option value="{$key}" {if $key == $locale}selected{/if}>{$value}</option>
              {/foreach}
            </select>
          </li>
        </ul>
      </div>
    </footer>
  </div>
  {block name="footer-js"}
    {javascripts src="@Common/js/jquery/jquery.min.js,
        @Common/components/bootstrap/dist/js/bootstrap.min.js,
        @Common/components/modernizr/modernizr.js,
        @Common/js/onm/md5.min.js" output="login"}
    {/javascripts}
  {/block}
</body>
</html>
