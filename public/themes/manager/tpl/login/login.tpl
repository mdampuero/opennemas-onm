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
    {block name="header-css"}
        {stylesheets src="@Common/plugins/pace/pace-theme-flash.css,
                          @Common/plugins/jquery-slider/css/jquery.sidr.light.css,
                          @Common/plugins/webarch/css/animate.min.css,
                          @Common/plugins/bootstrap-select2/select2.css,

                          @Common/plugins/bootstrap/css/bootstrap.min.css,
                          @Common/plugins/font-awesome/css/font-awesome.min.css,
                          @Common/css/bootstrap/bootstrap-fileupload.min.css"
                     filters="cssrewrite"}
            <link rel="stylesheet" type="text/css" href="{$asset_url}">
        {/stylesheets}
        {stylesheets src="@Common/plugins/webarch/css/style.css,
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
                     filters="cssrewrite"}
            <link rel="stylesheet" type="text/css" href="{$asset_url}">
        {/stylesheets}
    {/block}

    {block name="header-js"}
        {javascripts src="@Common/plugins/jquery/jquery.min.js,
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
                          @Common/js/onm/scripts.js"}
            <script type="text/javascript" src="{$asset_url}"></script>
        {/javascripts}


        {javascripts
            src="@Common/js/jquery/select2/select2.min.js,
                @Common/js/libs/modernizr.min.js,
                @Common/js/onm/scripts.js,
                @Common/js/onm/jquery.onm-editor.js"}
            <script type="text/javascript" src="{$asset_url}"></script>
        {/javascripts}
    {/block}

    {block name="footer-js"}
        {javascripts src="@FosJsRoutingBundle/js/router.js,
                          @Common/js/routes.js,
                          @Common/plugins/angular/angular.min.js,
                          @Common/plugins/angular-google-chart/angular-google-chart.js,
                          @Common/plugins/angular-checklist-model/checklist-model.js,
                          @Common/plugins/angular-route/angular-route.min.js,
                          @Common/plugins/angular-translate/angular-translate.min.js,
                          @Common/plugins/angular-quickdate/js/ng-quick-date.min.js,
                          @Common/plugins/angular-tags-input/js/ng-tags-input.min.js,
                          @Common/plugins/angular-ui/ui-bootstrap-tpls.min.js,
                          @Common/plugins/angular-ui/select2.js,

                          @Common/plugins/angular-onm/services/*,

                          @ManagerTheme/js/ManagerApp.js,
                          @ManagerTheme/js/Controllers.js,

                          @ManagerTheme/js/controllers/*
        "}
            <script type="text/javascript" src="{$asset_url}"></script>
        {/javascripts}
    {/block}

</head>
<body class="error-body" id="manager" ng-app="ManagerApp" ng-controller="LoginCtrl">
    <div class="container">
        <div class="row login-container column-seperation">
            <div class="col-md-5 col-md-offset-1">
                <h2>Sign in Opennemas</h2>
                <p>Use Facebook, Twitter or your email to sign in.<br>
                    <a href="#">Sign up Now!</a> for a webarch account,It's free and always will be..</p>
                <br>
                <button class="btn btn-block btn-info col-md-8" type="button">
                    <span class="pull-left"><i class="icon-facebook"></i></span>
                    <span class="bold">Login with Facebook</span> </button>
                <button class="btn btn-block btn-success col-md-8" type="button">
                    <span class="pull-left"><i class="icon-twitter"></i></span>
                    <span class="bold">Login with Twitter</span>
                </button>
            </div>
            <div class="col-md-5 "><br>
                <form id="login-form" class="login-form" action="{url name=manager_login_processform}" method="post" novalidate="novalidate">
                    <div class="row">
                        <div class="form-group col-md-10">
                            <label class="form-label">Username</label>
                            <div class="controls">
                                <input autofocus class="form-control" id="_username" ng-model="username" placeholder="{t}User name{/t}" type="text" value="{$smarty.cookies.login_username|default:""}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-10">
                            <label class="form-label">Password</label>
                            <span class="help"></span>
                            <div class="controls">
                                <div class="input-with-icon    right">
                                    <i class=""></i>
                                    <input class="form-control" id="_password" ng-model="password" placeholder="{t}Password{/t}" type="password" value="{$smarty.cookies.login_password|default:""}">
                                </div>
                            </div>
                        </div>
                    </div>
                    {if $failed_login_attempts >= 3}
                    <div class="row">
                        <div class="form-group col-md-10">
                            <label class="form-label"></label>
                            <div class="controls">
                                <div class="control-group clearfix">
                                    <script type="text/javascript" src="http://www.google.com/recaptcha/api/challenge?k=6LfLDtMSAAAAAEdqvBjFresKMZoknEwdo4mN8T66"></script>
                                    <noscript>
                                        <iframe src="http://www.google.com/recaptcha/api/noscript?k=6LfLDtMSAAAAAEdqvBjFresKMZoknEwdo4mN8T66" height="300" width="500" frameborder="0"></iframe><br>
                                        <textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
                                        <input type="hidden" name="recaptcha_response_field" value="manual_challenge">
                                    </noscript>
                                </div>
                            </div>
                        </div>
                    </div>
                    {/if}
                    <input type="hidden" name="_referer" value="{$referer}">

                    <div class="row">
                        <div class="col-md-10">
                            <button class="btn btn-primary btn-cons pull-right" type="submit">Login</button>
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
</body>
</html>
