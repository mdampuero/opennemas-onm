<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <meta name="author"    content="OpenHost,SL">
  <meta name="generator" content="OpenNemas - News Management System">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta name="theme-color" content="#22262e">
  <link rel="manifest" href="manager_manifest.json">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <link rel="icon" href="/assets/images/favicon.png">
  <link rel="icon" sizes="192x192" href="/assets/images/launcher-icons/IOS-60@2x.png">
  <link rel="apple-touch-icon" href="/assets/images/launcher-icons/IOS-60@2x.png">
  <link rel="apple-touch-icon" sizes="76x76" href="/assets/images/launcher-icons/IOS-60@2x.png">
  <link rel="apple-touch-icon" sizes="120x120" href="/assets/images/launcher-icons/IOS-60@2x.png">
  <link rel="apple-touch-icon" sizes="152x152" href="/assets/images/launcher-icons/IOS-60@2x.png">

  {block name="meta"}
    <title>opennemas - Manager</title>
  {/block}

  {block name="header-css"}
    <link href='//fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="/assets/components/font-awesome/css/font-awesome.min.css">
    {stylesheets src="@Common/components/bootstrap/dist/css/bootstrap.min.css,
      @Common/components/angular-bootstrap-colorpicker/css/colorpicker.min.css,
      @Common/components/angular-loading-bar/build/loading-bar.min.css,
      @Common/components/angular-ui-select/dist/select.min.css,
      @Common/components/animate.css/animate.min.css,
      @Common/components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css,
      @Common/components/messenger/build/css/messenger-theme-flat.css,
      @Common/components/messenger/build/css/messenger.css,
      @Common/components/nanoscroller/bin/css/nanoscroller.css,
      @Common/components/ng-tags-input/ng-tags-input.min.css,
      @Common/components/pace/themes/blue/pace-theme-minimal.css,
      @Common/components/select2/select2.css,
      @Common/components/spinkit/css/spinkit.css,
      @Common/src/webarch/css/style.css,
      @Common/src/webarch/css/responsive.css,
      @Common/src/webarch/css/custom-icon-set.css,
      @Common/src/webarch/css/magic_space.css,
      @Common/src/angular-dynamic-image/less/main.less,
      @Common/src/angular-onm-pagination/less/main.less,
      @Common/src/sidebar/less/main.less,
      @Common/src/opennemas-webarch/css/layout/*,
      @Common/src/opennemas-webarch/less/main.less" filters="cssrewrite,less" output="common"}
    {/stylesheets}
    {stylesheets src="@Common/components/flag-sprites/dist/css/flag-sprites.min.css,
      @ManagerTheme/less/main.less" filters="cssrewrite,less" output="manager"}
    {/stylesheets}
  {/block}
  {block name="header-js"}
    <script>
      var appVersion = '{$smarty.const.DEPLOYED_AT}';
      var CKEDITOR_BASEPATH = '/assets/components/ckeditor/';
    </script>
  {/block}
</head>
<body id="manager" ng-class="{ 'collapsed': sidebar.isCollapsed(), 'login-body': !auth.status, 'pinned': sidebar.isPinned(), 'unauthorized': !auth.status }" ng-app="ManagerApp" ng-controller="MasterCtrl" ng-init="init('{{$smarty.const.CURRENT_LANGUAGE}}')" resizable ng-class="{ 'collapsed': sidebar.isCollapsed() }">
  <div class="application-loading" ng-hide="loaded">
    <div class="loading-message">
      <i class="fa fa-circle-o-notch fa-spin fa-3x"></i>
      <h2>{t}Initializing{/t}</h2>
      <h5>{$loading_message}</h5>
    </div>
  </div>
  <form action="/managerws/template/login:blank.tpl" class="login-form" method="post" name="loginForm" ng-class="{ 'hidden': auth.status }" ng-submit="login()" novalidate form-autofill-fix>
    <div class="container">
      <div class="row login-container animated fadeInUp">
        <div class="col-md-6 col-md-offset-3 tiles white no-padding">
          <div class="p-t-30 p-b-10 xs-p-t-10 xs-p-l-10 xs-p-b-10">
            <h2 class="normal center">
              <i class="fa fa-rebel fa-3x"></i>
              <div class="p-t-15">{t}Welcome, Master{/t}</div>
            </h2>
          </div>
          <div class="tiles grey p-b-20 p-l-30 p-r-30 p-t-20 text-black">
            <!-- Hack to allow web browsers to remember credentials with AngularJS -->
            <iframe class="hidden" id="fake-login" ng-src="/managerws/template/login:fake_form.tpl"></iframe>
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-user"></i>
                </span>
                <input autofocus class="form-control" id="_username" ng-model="username" placeholder="{t}User name{/t}" required type="text" value="{$smarty.cookies.login_username|default:""}">
              </div>
            </div>
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon">
                  <i class="fa fa-lock"></i>
                </span>
                <input class="form-control" id="_password" ng-model="password" placeholder="{t}Password{/t}" required type="password" value="{$smarty.cookies.login_password|default:""}">
              </div>
            </div>
            <div class="form-group col-md-10" ng-if="attempts > 2">
              <label class="form-label"></label>
              <div class="controls">
                <div class="control-group clearfix">
                  <div vc-recaptcha theme="clean" lang="en" key="'6LfLDtMSAAAAAEdqvBjFresKMZoknEwdo4mN8T66'"></div>
                </div>
              </div>
            </div>
            <div class="form-group">
              <div class="alert alert-[% message.type %]" ng-show="message && loginForm.$pristine">
                [% message.text %]
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6 col-sm-offset-3">
                <button class="btn btn-block btn-loading btn-success" ng-disabled="loginLoading" type="submit">
                  <i class="fa fa-absolute fa-circle-o-notch fa-spin m-l-15 m-t-15" ng-if="loginLoading"></i>
                  <h4 class="text-uppercase text-white">
                    {t}Login{/t}
                  </h4>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
  <div class="nocss hidden">
    {t}Your browser was unable to load all of Opennemas's resources. They may have been blocked by your firewall, proxy or browser configuration.{/t}
    <br>
    {t}Press Ctrl+F5 or Ctrl+Shift+R to have your browser try again.{/t}
    <hr>
  </div>
  <div class="nojs" ng-hide="true">
    <noscript class="big-message text-center">
      <h1>Opennemas</h1>
      <p>To use Opennemas, please enable JavaScript.</p>
    </noscript>
  </div>
  <header class="header navbar navbar-inverse" ng-class="{ 'hidden': !auth.status }">
    <div class="navbar-inner">
      <div class="header-seperation">
        <a class="header-logo pull-left" href="{url name=manager_welcome}">
          <h1>
            open<strong>nemas</strong>
          </h1>
        </a>
      </div>
      <div class="header-quick-nav">
        <!-- BEGIN TOP NAVIGATION MENU -->
        <div class="pull-left">
          <ul class="nav quick-section">
            <li class="quicklinks quick-items create-items dropdown" ng-if="security.hasPermission('MASTER')">
              <a href="#" data-toggle="dropdown">
                <i class="fa fa-plus"></i>
                {t}Create{/t}
              </a>
              <div class="dropdown-menu">
                <div class="clearfix quick-items-row">
                  <div class="quick-item">
                    <a ng-href="[% routing.ngGenerate('manager_instance_create') %]">
                      <i class="fa fa-cube"></i>
                      <span class="title">{t}Instance{/t}</span>
                    </a>
                  </div>
                  <div class="quick-item">
                    <a ng-href="[% routing.ngGenerate('manager_module_create') %]">
                      <i class="fa fa-flip-horizontal fa-plug"></i>
                      <span class="title">{t}Module{/t}</span>
                    </a>
                  </div>
                  <div class="quick-item">
                    <a ng-href="[% routing.ngGenerate('manager_notification_create') %]">
                      <i class="fa fa-bell"></i>
                      <span class="title">{t}Notification{/t}</span>
                    </a>
                  </div>
                  <div class="quick-item">
                    <a ng-href="[% routing.ngGenerate('manager_client_create') %]">
                      <i class="fa fa-user"></i>
                      <span class="title">{t}Client{/t}</span>
                    </a>
                  </div>
                </div>
                <div class="clearfix quick-items-row">
                  <div class="quick-item">
                    <a ng-href="[% routing.ngGenerate('manager_user_group_create') %]">
                      <i class="fa fa-users"></i>
                      <span class="title">{t}Group{/t}</span>
                    </a>
                  </div>
                  <div class="quick-item">
                    <a ng-href="[% routing.ngGenerate('manager_user_create') %]">
                      <i class="fa fa-user"></i>
                      <span class="title">{t}User{/t}</span>
                    </a>
                  </div>
                </div>
              </div>
            </li>
          </ul>
        </div>
        <div class="pull-right" ng-if="security.user.id">
          <ul class="nav quick-section">
            <li class="quicklinks user-info dropdown">
              <span class="link" data-toggle="dropdown">
                <i class="fa fa-rebel text-danger pull-left"></i>
                <span class="title">
                  [% security.user.name %]
                </span>
                <i class="fa fa-angle-down"></i>
                <div class="profile-pic">
                  <gravatar ng-model="security.user.email" size="25"></gravatar>
                </div>
              </span>
              <ul class="dropdown-menu dropdown-menu-right no-padding">
                <li class="text-danger">
                    <span class="fake-a fake-a-static text-danger">
                    {t}You are a master{/t}
                  </span>
                </li>
                <li class="divider" ng-if="security.hasPermission('USER_EDIT_OWN_PROFILE')"></li>
                <li ng-if="security.hasPermission('USER_EDIT_OWN_PROFILE')">
                  <a ng-href="[% routing.ngGenerate('manager_user_show', { id: security.user.id }) %]">
                    <i class="fa fa-user"></i>
                    {t}Profile{/t}
                  </a>
                </li>
                <li class="divider"></li>
                <li>
                  <a href="#" ng-click="logout()" tabindex="-1">
                    <i class="fa fa-power-off m-r-10"></i>
                    {t}Log out{/t}
                  </a>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </header>
  <sidebar class="sidebar" ng-class="{ 'hidden': !$parent.auth.status }" footer="true" id="sidebar" ng-model="sidebar" position="left" src="manager_ws_sidebar_list" swipeable="true" pinnable="true"></sidebar>
  <div class="page-container row-fluid ng-cloak" ng-class="{ 'hidden': !auth.status }">
    <!-- BEGIN PAGE CONTAINER-->
    <div class="page-content">
      <div class="sidebar-toggler ng-cloak" ng-click="sidebar.toggle()">
        <span class="fa fa-bars fa-lg"></span>
      </div>
      <div class="view" id="view" ng-view autoscroll="true"></div>
    </div>
    <!-- END PAGE CONTAINER -->
  </div>
  <script type="text/ng-template" id="modal-login">
    {include file="login/modal_login.tpl"}
  </script>
  <script type="text/ng-template" id="modal-logout">
    {include file="login/modal_logout.tpl"}
  </script>
  <script type="text/ng-template" id="modal-upgrade">
    {include file="common/modal_application_upgrade.tpl"}
  </script>
  <script type="text/ng-template" id="403">
    {include file="error/403.tpl"}
  </script>
  <script type="text/ng-template" id="404">
    {include file="error/404.tpl"}
  </script>
  <!--[if lt IE 7 ]>
      <script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.2/CFInstall.min.js"></script>
      <script>window.attachEvent("onload",function(){ CFInstall.check({ mode:"overlay" }) })</script>
      <![endif]-->

  {block name="footer-js"}
    <script type="text/javascript" src="//www.google.com/recaptcha/api/js/recaptcha_ajax.js"></script>
    {javascripts src="
      @Common/components/jquery2/dist/jquery.min.js,
      @Common/components/bootstrap/dist/js/bootstrap.min.js,
      @Common/components/breakpoints/breakpoints.js,
      @Common/components/moment/min/moment-with-locales.min.js,
      @Common/components/moment-timezone/builds/moment-timezone-with-data.min.js,
      @Common/components/angular/angular.min.js,
      @Common/components/ckeditor/ckeditor.js,
      @Common/components/ckeditor/config.js,
      @Common/components/ckeditor/lang/en.js,
      @Common/components/ckeditor/styles.js,
      @Common/components/ckeditor/plugins/autogrow/plugin.js,
      @Common/components/ckeditor/plugins/autolink/plugin.js,
      @Common/components/ckeditor/plugins/notification/plugin.js,
      @Common/components/ckeditor/plugins/notification/lang/en.js,
      @Common/components/ckeditor/plugins/justify/plugin.js,
      @Common/components/ckeditor/plugins/justify/lang/en.js,
      @Common/components/ckeditor/plugins/justify/lang/es.js,
      @Common/components/ckeditor/plugins/font/plugin.js,
      @Common/components/ckeditor/plugins/font/lang/en.js,
      @Common/components/ckeditor/plugins/font/lang/es.js,
      @Common/components/imageresize/plugin.js,
      @Common/src/ckeditor-autokeywords/plugin.js,
      @Common/src/ckeditor-autokeywords/lang/en.js,
      @Common/src/ckeditor-autokeywords/lang/es.js,
      @Common/src/ckeditor-wordcount/wordcount/plugin.js,
      @Common/src/ckeditor-wordcount/wordcount/lang/en.js,
      @Common/src/ckeditor-wordcount/wordcount/lang/es.js,
      @Common/src/ckeditor-pastespecial/plugin.js,
      @Common/components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js,
      @Common/components/fastclick/lib/fastclick.js,
      @Common/components/lodash/dist/lodash.min.js,
      @Common/components/messenger/build/js/messenger.min.js,
      @Common/components/messenger/build/js/messenger-theme-flat.js,
      @Common/components/nanoscroller/bin/javascripts/jquery.nanoscroller.min.js,
      @Common/components/ng-tags-input/ng-tags-input.min.js,
      @Common/components/select2/select2.min.js,
      @Common/components/swfobject/swfobject/swfobject.js,

      @Common/components/angular-animate/angular-animate.min.js,
      @Common/components/angular-bootstrap-colorpicker/js/bootstrap-colorpicker-module.min.js,
      @Common/components/angular-bootstrap/ui-bootstrap-tpls.min.js,
      @Common/components/angular-checklist-model/checklist-model.js,
      @Common/components/angular-loading-bar/build/loading-bar.min.js,
      @Common/components/angular-nanoscroller/scrollable.js,
      @Common/components/angular-recaptcha/release/angular-recaptcha.min.js,
      @Common/components/angular-route/angular-route.min.js,
      @Common/components/angular-sanitize/angular-sanitize.min.js,
      @Common/components/angular-swfobject/angular-swfobject.js,
      @Common/components/angular-touch/angular-touch.min.js,
      @Common/components/angular-translate/angular-translate.min.js,
      @Common/components/angular-ui-select/dist/select.min.js,
      @Common/components/angular-webstorage/angular-webstorage.min.js,
      @Common/src/angular-messenger/messenger.js,
      @Common/src/angular-moment/moment.js,
      @Common/js/onm/md5.min.js,
      @FosJsRoutingBundle/js/router.js,
      @Common/js/routes.js,
      @Common/src/angular-cleaner/cleaner.js,
      @Common/src/angular-datetimepicker/datetimepicker.js,
      @Common/src/angular-dynamic-image/js/dynamic-image.js,
      @Common/src/angular-gravatar/gravatar.js,
      @Common/src/angular-history/history.js,
      @Common/src/angular-http/http.js,
      @Common/src/angular-item-service/itemService.js,
      @Common/src/angular-onm-editor/onm-editor.js,
      @Common/src/angular-onm-pagination/js/onm-pagination.js,
      @Common/src/angular-oql/oql.js,
      @Common/src/angular-resizable/resizable.js,
      @Common/src/angular-routing/routing.js,
      @Common/src/angular-security/security.js,
      @Common/src/angular-serializer/serializer.js,
      @Common/src/opennemas-webarch/js/core.js,
      @Common/src/sidebar/js/sidebar.js" filters="uglifyjs" output="common"}
    {/javascripts}
    {javascripts src="
      @Common/components/angular-file-model/angular-file-model.js,
      @Common/components/angular-google-chart/ng-google-chart.js,
      @Common/components/angular-jwt/dist/angular-jwt.js,
      @Common/src/angular-authentication/authService.js,
      @Common/src/angular-form-autofill/formAutoFill.js,
      @Common/src/angular-http-interceptor/http-interceptor.js,
      @Common/src/angular-image-preview/js/image-preview.js,
      @ManagerTheme/js/app.js,
      @ManagerTheme/js/config.js,
      @ManagerTheme/js/controllers/*,
      @ManagerTheme/js/module/*,
      @ManagerTheme/js/routing.js" filters="uglifyjs" output="manager"}
    {/javascripts}
  {/block}
</body>
</html>
