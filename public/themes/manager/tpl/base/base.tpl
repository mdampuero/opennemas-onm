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
    <link rel="icon" sizes="192x192" href="{$params.COMMON_ASSET_DIR}images/launcher-icons/IOS-60@2x.png">
    <link rel="apple-touch-icon" href="{$params.COMMON_ASSET_DIR}images/launcher-icons/IOS-60@2x.png">
    <link rel="apple-touch-icon" sizes="76x76" href="{$params.COMMON_ASSET_DIR}images/launcher-icons/IOS-60@2x.png">
    <link rel="apple-touch-icon" sizes="120x120" href="{$params.COMMON_ASSET_DIR}images/launcher-icons/IOS-60@2x.png">
    <link rel="apple-touch-icon" sizes="152x152" href="{$params.COMMON_ASSET_DIR}images/launcher-icons/IOS-60@2x.png">

    {block name="meta"}
    <title>opennemas - Manager</title>
    {/block}

    <link rel="icon" href="{$params.COMMON_ASSET_DIR}images/favicon.png">
    <style>
      @import url(//fonts.googleapis.com/css?family=Open+Sans:400,300,600,700);
    </style>
    {block name="header-css"}
        {stylesheets src="
            @Common/plugins/pace/pace-theme-minimal.css,
            @Common/plugins/jquery-slider/css/jquery.sidr.light.css,
            @Common/plugins/webarch/css/animate.min.css,
            @Common/plugins/bootstrap-select2/select2.css,

            @Common/plugins/bootstrap/css/bootstrap.min.css,

            @Common/plugins/webarch/css/style.css,
            @Common/plugins/font-awesome/css/font-awesome.min.css,
            @Common/css/bootstrap/bootstrap-fileupload.min.css,
            @Common/plugins/webarch/css/responsive.css,
            @Common/plugins/webarch/css/custom-icon-set.css,
            @Common/plugins/webarch/css/magic_space.css,

            @Common/plugins/jquery-nanoscroller/nanoscroller.css,
            @Common/plugins/angular-loading-bar/loading-bar.min.css,
            @Common/plugins/angular-quickdate/css/ng-quick-date.css,
            @Common/plugins/angular-quickdate/css/ng-quick-date-default-theme.css,
            @Common/plugins/angular-quickdate/css/ng-quick-date-plus-default-theme.css,
            @Common/plugins/angular-tags-input/css/ng-tags-input.min.css,
            @Common/plugins/jquery-notifications/css/messenger.css,
            @Common/plugins/jquery-notifications/css/messenger-theme-flat.css,

            @Common/css/manager/base/*,
            @Common/css/manager/layout/*,
            @Common/css/manager/main.css"
        filters="cssrewrite"}<link rel="stylesheet" type="text/css" href="{$asset_url}">{/stylesheets}
    {/block}

    {block name="header-js"}
        <script>
            var appVersion = '{$smarty.const.DEPLOYED_AT}';
        </script>
    {/block}

</head>
<body id="manager" class="error-body" ng-app="ManagerApp" ng-controller="MasterCtrl"  ng-class="{ 'collapsed': sidebar.current }" ng-init="init('{{$smarty.const.CURRENT_LANGUAGE}}')" resizable>
    <div class="application-loading" ng-hide="loaded">
        <div class="loading-message">
            <i class="fa fa-circle-o-notch fa-spin fa-3x"></i>
            <h2>{t}Initializing{/t}</h2>
            <h5>{$loading_message}</h5>
        </div>
    </div>
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
    <header class="header navbar navbar-inverse ng-cloak" ng-show="(loaded && auth.status) || (!auth.status && auth.modal)">
        <!-- BEGIN TOP NAVIGATION BAR -->
        <div class="navbar-inner">
            <div class="header-seperation">
                <div class="layout-collapse pull-left">
                    <div class="btn layout-collapse-toggle" ng-click="sidebar.current ? sidebar.current = 0 : (sidebar.forced ? sidebar.current = 1 : sidebar.current = sidebar.wanted)">
                        <i class="fa fa-bars fa-lg" ng-class="{ 'fa-circle-o-notch fa-spin': changing.dashboard || changing.instances || changing.commands ||  changing.cache || changing.users || changing.groups }"></i>
                    </div>
                </div>
                <a class="header-static-logo" href="{url name=manager_welcome}">
                    <h1>
                        open<strong>nemas</strong>
                    </h1>
                </a>
                <div ng-mouseleave="sidebar.forced ? sidebar.current = 1 : sidebar.current = sidebar.wanted" ng-mouseenter="sidebar.current = 0">
                    <div class="overlay"></div>
                    <a class="header-logo" href="{url name=manager_welcome}">
                        <h1 ng-mouseleave="sidebar.forced ? sidebar.current = 1 : sidebar.current = sidebar.wanted" ng-mouseenter="sidebar.current = 0">
                            <span class="first-char">o</span><span class="title-token">pen<strong>nemas</strong></span>
                        </h1>
                    </a>
                </div>
            </div>
        <!-- END TOP NAVIGATION MENU -->
        </div>
      <!-- END TOP NAVIGATION BAR -->
    </header>
    <!-- BEGIN SIDEBAR -->
    {include file="base/sidebar.tpl"}
    <div class="layout-collapse-border ng-cloak" ng-click="sidebar.wanted = !sidebar.wanted;sidebar.current = sidebar.wanted"></div>
    <!-- END SIDEBAR -->
    <div class="page-container row-fluid ng-cloak" ng-show="auth.status || (!auth.status && auth.modal)">
        <!-- BEGIN PAGE CONTAINER-->
            <div class="page-content">
                <div class="view" id="view" ng-view autoscroll="true"></div>
            </div>
        <!-- END PAGE CONTAINER -->
    </div>
    <div class="container login-container-wrapper ng-cloak" ng-show="!auth.status && !auth.modal">
        <div class="row login-container column-seperation">
            <div class="col-md-5 col-md-offset-1">
                <h2>{t}Opennemas manager{/t}</h2>
                <p>{t}Use manager account to sign in.{/t}<br>
                <br>
                <!--
                <button class="btn btn-block btn-info col-md-8" type="button">
                    <span class="pull-left"><i class="icon-facebook"></i></span>
                    <span class="bold">Login with Facebook</span> </button>
                <button class="btn btn-block btn-success col-md-8" type="button">
                    <span class="pull-left"><i class="icon-twitter"></i></span>
                    <span class="bold">Login with Twitter</span>
                </button>
                -->
            </div>
            <div class="col-md-5 "><br>
                <form action="/managerws/template/login:blank.tpl" class="login-form" method="post" name="loginForm" ng-submit="login()" novalidate form-autofill-fix>
                    <!-- Hack to allow web browsers to remember credentials with AngularJS -->
                    <iframe id="fake-login" ng-src="/managerws/template/login:fake_form.tpl"></iframe>
                    <div class="row">
                        <div class="form-group col-md-10">
                            <label class="form-label">{t}Username{/t}</label>
                            <div class="controls">
                                <input autofocus class="form-control" id="_username" ng-model="username" placeholder="{t}User name{/t}" required type="text" value="{$smarty.cookies.login_username|default:""}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-10">
                            <label class="form-label">{t}Password{/t}</label>
                            <span class="help"></span>
                            <div class="controls">
                                <div class="input-with-icon right">
                                    <i class=""></i>
                                    <input class="form-control" id="_password" ng-model="password" placeholder="{t}Password{/t}" required type="password" value="{$smarty.cookies.login_password|default:""}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" ng-if="attempts > 2">
                        <div class="form-group col-md-10">
                            <label class="form-label"></label>
                            <div class="controls">
                                <div class="control-group clearfix">
                                    <div vc-recaptcha theme="clean" lang="en" key="'6LfLDtMSAAAAAEdqvBjFresKMZoknEwdo4mN8T66'"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                      <div class="form-group col-md-10">
                          <div class="alert alert-[% message.type %]" ng-show="message && loginForm.$pristine">
                              [% message.text %]
                          </div>
                      </div>
                    </div>
                    <input type="hidden" name="_referer" value="{$referer}">
                    <div class="row">
                        <div class="col-md-10">
                            <button class="btn btn-primary pull-right" ng-disabled="loading" type="submit">
                              <i class="fa fa-circle-o-notch fa-spin" ng-show="loading"></i>
                              {t}Login{/t}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script type="text/ng-template" id="modal-login">
        {include file="login/modal_login.tpl"}
    </script>
    <script type="text/ng-template" id="modal-upgrade">
        {include file="common/modal_application_upgrade.tpl"}
    </script>
    <script type="text/ng-template" id="error">
        {include file="error/ws_404.tpl"}
    </script>
    <!--[if lt IE 7 ]>
        <script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.2/CFInstall.min.js"></script>
        <script>window.attachEvent("onload",function(){ CFInstall.check({ mode:"overlay" }) })</script>
    <![endif]-->

    {block name="footer-js"}
        <script type="text/javascript" src="//www.google.com/recaptcha/api/js/recaptcha_ajax.js"></script>

        {javascripts src="
            @Common/plugins/jquery/jquery.min.js,
            @Common/plugins/jquery-ui/jquery-ui.min.js,
            @Common/plugins/bootstrap/js/bootstrap.min.js,
            @Common/plugins/breakpoints/breakpoints.min.js,
            @Common/plugins/fastclick/fastclick.js,
            @Common/plugins/jquery-unveil/jquery.unveil.min.js,
            @Common/plugins/jquery-block-ui/jquery.blockui.min.js,
            @Common/plugins/jquery-lazyload/jquery.lazyload.min.js,

            @Common/plugins/jquery-slider/jquery.sidr.min.js,
            @Common/plugins/jquery-nanoscroller/jquery.nanoscroller.min.js,
            @Common/plugins/jquery-notifications/js/messenger.min.js,
            @Common/plugins/jquery-notifications/js/messenger-theme-flat.js,

            @Common/js/onm/scripts.js,

            @Common/js/jquery/select2/select2.min.js,
            @Common/js/libs/modernizr.min.js,
            @Common/js/onm/md5.min.js,
            @Common/js/onm/scripts.js,
            @Common/js/onm/jquery.onm-editor.js,

            @FosJsRoutingBundle/js/router.js,
            @Common/js/routes.js,
            @Common/plugins/angular/angular.min.js,
            @Common/plugins/angular-animate/angular-animate.min.js,
            @Common/plugins/angular-checklist-model/checklist-model.js,
            @Common/plugins/angular-google-chart/angular-google-chart.js,
            @Common/plugins/angular-nanoscroller/scrollable.js,
            @Common/plugins/angular-loading-bar/loading-bar.min.js,
            @Common/plugins/angular-quickdate/js/ng-quick-date.min.js,
            @Common/plugins/angular-recaptcha/module.js,
            @Common/plugins/angular-recaptcha/directive.js,
            @Common/plugins/angular-recaptcha/service.js,
            @Common/plugins/angular-route/angular-route.min.js,
            @Common/plugins/angular-tags-input/js/ng-tags-input.min.js,
            @Common/plugins/angular-touch/angular-touch.min.js,
            @Common/plugins/angular-translate/angular-translate.min.js,
            @Common/plugins/angular-ui/ui-bootstrap-tpls.min.js,
            @Common/plugins/angular-ui/select2.js,

            @Common/plugins/angular-onm/*,

            @ManagerTheme/js/ManagerApp.js,
            @ManagerTheme/js/Controllers.js,

            @ManagerTheme/js/controllers/*,

            @Common/plugins/webarch/js/core.js,
            @Common/js/manager.js
        " filters="uglifyjs"}
            <script type="text/javascript" src="{$asset_url}"></script>
        {/javascripts}
    {/block}
</body>
</html>
