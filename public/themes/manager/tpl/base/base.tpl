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

    {block name="meta"}
    <title>OpenNeMaS - Manager</title>
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
            @Common/plugins/webarch/css/tiles_responsive.css,

            @Common/plugins/angular-quickdate/css/ng-quick-date.css,
            @Common/plugins/angular-quickdate/css/ng-quick-date-default-theme.css,
            @Common/plugins/angular-quickdate/css/ng-quick-date-plus-default-theme.css,
            @Common/plugins/angular-tags-input/css/ng-tags-input.min.css,
            @Common/plugins/jquery-notifications/css/messenger.css,
            @Common/plugins/jquery-notifications/css/messenger-theme-flat.css,

            @Common/css/opennemas/style.css"
        filters="cssrewrite"}<link rel="stylesheet" type="text/css" href="{$asset_url}">{/stylesheets}
    {/block}

    {block name="header-js"}
        <script>
            var appVersion = '{$smarty.const.DEPLOYED_AT}';
        </script>

        <script type="text/javascript" src="//www.google.com/recaptcha/api/js/recaptcha_ajax.js"></script>
    {/block}

</head>
<body id="manager" class="application-loading" ng-app="ManagerApp" ng-controller="MasterCtrl" ng-class="{ 'error-body': true }" ng-init="init()">
    <header class="header navbar navbar-inverse" ng-show="auth.status || (!auth.status && auth.modal)">
        <!-- BEGIN TOP NAVIGATION BAR -->
        <div class="navbar-inner">
            <div class="header-seperation" ng-hide="mini">
                <ul class="nav pull-left notifcation-center" id="main-menu-toggle-wrapper">
                    <li class="dropdown">
                        <a id="main-menu-toggle" href="#main-menu">
                            <div class="iconset top-menu-toggle-white"></div>
                        </a>
                    </li>
                </ul>
                <!-- BEGIN LOGO -->
                <a href="{url name=manager_welcome}">
                    {image_tag src="/logos/logo-opennemas-small-white.png" class="logo" alt=""  data-src="/assets/images/logos/logo-opennemas-small-white.png" data-src-retina="/assets/images/logos/logo-opennemas-small-white.png" width="112" height="19" common=1}
                </a>
                <!-- END LOGO -->
                <ul class="nav pull-right notifcation-center">
                    <li class="dropdown" id="header_task_bar">
                        <a href="{url name=manager_welcome}" class="dropdown-toggle active" data-toggle="">
                            <div class="iconset top-home"></div>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="header-quick-nav">
                <!-- BEGIN TOP NAVIGATION MENU -->
                <div class="pull-left">
                    <ul class="nav quick-section quick-section-logo">
                        <li class="mini-logo">
                            <a href="{url name=manager_welcome}">
                                {image_tag src="/logos/logo-opennemas-small-black.png" class="logo" alt=""  data-src="/assets/images/logos/logo-opennemas-small-black.png" data-src-retina="/assets/images/logos/logo-opennemas-small-black.png" width="112" height="19" common=1}
                            </a>
                        </li>
                        <li class="quicklinks">
                            <span class="a" id="layout-condensed-toggle" ng-click="toggle(!mini);">
                                <div class="iconset top-menu-toggle-dark"></div>
                            </span>
                        </li>
                    </ul>
                    <!-- <ul class="nav quick-section">
                        <li class="m-r-10 input-prepend inside search-form no-boarder">
                            <span class="add-on">
                                <span class="iconset top-search"></span>
                            </span>
                            <input name="" type="text"  class="no-boarder " placeholder="{t}Search Dashboard{/t}" style="width:250px;">
                        </li>
                    </ul> -->
                </div>
                <!-- END TOP NAVIGATION MENU -->
                <!-- BEGIN CHAT TOGGLER -->
                <div class="pull-right">
                    <div class="chat-toggler">
                      <ul class="nav quick-section">
                          <li class="quicklinks">
                              <span class="a dropdown-toggle pull-right" data-placement="bottom" data-toggle="dropdown">
                                  <div class="profile-pic">
                                      <img class="gravatar" email="[% user.email %]" image="1" size="35" >
                                  </div>

                                  <div class="user-details">
                                      <div class="username">
                                          <span class="badge badge-important hidden">3</span> [% user.name %]
                                      </div>
                                  </div>
                                  <div class="iconset top-down-arrow"></div>
                              </span>
                              <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="user-options">
                                  <li>
                                      <a ng-href="[% fosJsRouting.ngGenerate('/manager', 'manager_user_show', { id: 'me' }) %]"> {t}My Account{/t}</a>
                                  </li>
                                  <li class="divider"></li>
                                  <li>
                                      <span class="a" ng-click="logout();">
                                        <i class="fa fa-power-off"></i>&nbsp;&nbsp;{t}Log Out{/t}
                                      </span>
                                  </li>
                              </ul>
                          </li>
                      </ul>
                    </div>
                </div>
                <!-- END CHAT TOGGLER -->
            </div>
        <!-- END TOP NAVIGATION MENU -->
        </div>
      <!-- END TOP NAVIGATION BAR -->
    </header>
    <div class="page-container row-fluid" ng-show="auth.status || (!auth.status && auth.modal)">
        <!-- BEGIN SIDEBAR -->
        <div class="page-sidebar" id="main-menu" ng-class="{ 'mini': mini }">
            <div class="page-sidebar-wrapper" id="main-menu-wrapper">
                <!-- BEGIN SIDEBAR MENU -->
                <ul>
                    <li class="start" ng-class="{ 'active': false }">
                        <a href="#"><i class="fa fa-home"></i> <span class="title">{t}Dashboard{/t}</span></a>
                    </li>
                    <li ng-class="{ 'active': isActive('manager_instances_list') }" ng-click="go();">
                        <a ng-href="[% fosJsRouting.ngGenerate('/manager', 'manager_instances_list') %]"><i class="fa fa-cubes"></i> <span class="title">{t}Instances{/t}</span></a>
                    </li>
                    <li ng-class="{ 'active': isActive('manager_framework_commands') || isActive('manager_framework_opcache_status') }">
                        <a href="#">
                            <i class="fa fa-flask"></i> <span class="title"> {t}Framework{/t}</span>
                            <span class="arrow" ng-class="{ 'open': isActive('manager_framework_commands') || isActive('manager_framework_opcache_status') }"></span>
                        </a>
                        <ul class="sub-menu">
                            <li ng-class="{ 'active': isActive('manager_framework_commands') }">
                                <a ng-href="[% fosJsRouting.ngGenerate('/manager', 'manager_framework_commands') %]"><i class="fa fa-code"></i> {t}Commands{/t}</a>
                            </li>
                            <li ng-class="{ 'active': isActive('manager_framework_opcache_status') }">
                                <a ng-href="[% fosJsRouting.ngGenerate('/manager', 'manager_framework_opcache_status') %]"><i class="fa fa-database"></i> {t}OPCache Status{/t}</a>
                            </li>
                        </ul>
                    </li>
                    <li ng-class="{ 'active': isActive('manager_users_list') || isActive('manager_user_groups_list'),  'active': isActive('manager_users_list') || isActive('manager_user_groups_list') }">
                        <a href="#">
                            <i class="fa fa-gears"></i>
                            <span class="title">{t}Settings{/t}
                            </span><span class="arrow" ng-class="{ 'open': isActive('manager_users_list') || isActive('manager_user_groups_list') }"></span>
                        </a>
                        <ul class="sub-menu">
                            <li ng-class="{ 'active': isActive('manager_users_list') }">
                                <a ng-href="[% fosJsRouting.ngGenerate('/manager', 'manager_users_list') %]">
                                    <i class="fa fa-user"></i> {t}Users{/t}
                                </a>
                            </li>
                            <li ng-class="{ 'active': isActive('manager_user_groups_list') }">
                                <a ng-href="[% fosJsRouting.ngGenerate('/manager', 'manager_user_groups_list') %]">
                                    <i class="fa fa-users"></i> {t}User groups{/t}
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
                <div class="clearfix"></div>
                <!-- END SIDEBAR MENU -->
            </div>
        </div>
        <!-- END SIDEBAR -->
        <!-- BEGIN PAGE CONTAINER-->
            <div class="page-content" ng-class="{ 'condensed': mini }">
                <div id="view" ng-view autoscroll="true"></div>
            </div>
        <!-- END PAGE CONTAINER -->
    </div>
    <script type="text/ng-template" id="modal-login">
        {include file="login/modal_login.tpl"}
    </script>
    <script type="text/ng-template" id="modal-confirm">
        {include file="common/modal_confirm.tpl"}
    </script>
    <script type="text/ng-template" id="modal-upgrade">
        {include file="common/modal_application_upgrade.tpl"}
    </script>
    <script type="text/ng-template" id="error">
        {include file="error/ws_404.tpl"}
    </script>
    <div class="container login-container-wrapper" ng-show="!auth.status && !auth.modal">
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
                    <iframe id="fake-login" src="/managerws/template/login:fake_form.tpl"></iframe>
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
                          <div class="alert alert-[% message.type %]" ng-show="message">
                              [% message.text %]
                          </div>
                      </div>
                    </div>
                    <input type="hidden" name="_referer" value="{$referer}">
                    <div class="row">
                        <div class="col-md-10">
                            <button class="btn btn-primary btn-cons pull-right" ng-disabled="loading" type="submit">
                              <i class="fa fa-circle-o-notch fa-spin" ng-if="loading"></i>
                              {t}Login{/t}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--[if lt IE 7 ]>
        <script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.2/CFInstall.min.js"></script>
        <script>window.attachEvent("onload",function(){ CFInstall.check({ mode:"overlay" }) })</script>
    <![endif]-->

    {block name="footer-js"}

        {javascripts src="
            @Common/plugins/jquery/jquery.min.js,
            @Common/plugins/jquery-ui/jquery-ui.min.js,
            @Common/plugins/bootstrap/js/bootstrap.min.js,
            @Common/plugins/breakpoints.js,
            @Common/plugins/jquery-unveil/jquery.unveil.min.js,
            @Common/plugins/jquery-block-ui/jqueryblockui.js,
            @Common/plugins/jquery-lazyload/jquery.lazyload.min.js,

            @Common/plugins/jquery-slider/jquery.sidr.min.js,
            @Common/plugins/jquery-slimscroll/jquery.slimscroll.min.js,
            @Common/plugins/jquery-notifications/js/messenger.min.js,
            @Common/plugins/jquery-notifications/js/messenger-theme-flat.js,

            @Common/plugins/webarch/js/core.js,
            @Common/plugins/pace/pace.min.js,
            @Common/js/onm/scripts.js,

            @Common/js/jquery/select2/select2.min.js,
            @Common/js/libs/modernizr.min.js,
            @Common/js/onm/md5.min.js,
            @Common/js/onm/scripts.js,
            @Common/js/onm/jquery.onm-editor.js,


            @FosJsRoutingBundle/js/router.js,
            @Common/js/routes.js,
            @Common/plugins/angular/angular.min.js,
            @Common/plugins/angular-google-chart/angular-google-chart.js,
            @Common/plugins/angular-checklist-model/checklist-model.js,
            @Common/plugins/angular-route/angular-route.min.js,
            @Common/plugins/angular-recaptcha/module.js,
            @Common/plugins/angular-recaptcha/directive.js,
            @Common/plugins/angular-recaptcha/service.js,
            @Common/plugins/angular-translate/angular-translate.min.js,
            @Common/plugins/angular-quickdate/js/ng-quick-date.min.js,
            @Common/plugins/angular-tags-input/js/ng-tags-input.min.js,
            @Common/plugins/angular-ui/ui-bootstrap-tpls.min.js,
            @Common/plugins/angular-ui/select2.js,

            @Common/plugins/angular-onm/*,

            @ManagerTheme/js/ManagerApp.js,
            @ManagerTheme/js/Controllers.js,

            @ManagerTheme/js/controllers/*
        " filters="uglifyjs"}
            <script type="text/javascript" src="{$asset_url}"></script>
        {/javascripts}
    {/block}
</body>
</html>
