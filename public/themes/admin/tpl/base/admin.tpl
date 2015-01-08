<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="{$smarty.const.CURRENT_LANGUAGE|default:"en"}"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang="{$smarty.const.CURRENT_LANGUAGE|default:"en"}"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang="{$smarty.const.CURRENT_LANGUAGE|default:"en"}"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="{$smarty.const.CURRENT_LANGUAGE|default:"en"}"> <!--<![endif]-->
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
    <title>{setting name=site_name} - {t}OpenNeMaS administration{/t}</title>
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

        {stylesheets
            src="@Common/css/jquery/jquery-ui.css,
                @Common/css/jquery/select2/select2-bootstrap.css,
                @Common/css/jquery/messenger/messenger.css,
                @Common/css/jquery/messenger/messenger-spinner.css,
                @Common/css/jquery/bootstrap-checkbox/bootstrap-checkbox.css,
                @AdminTheme/css/jquery/bootstrap-nav-wizard.css,
                @Common/css/style.cs"
            filters="cssrewrite"}
            <link rel="stylesheet" type="text/css" href="{$asset_url}">
        {/stylesheets}
        <!--[if IE]>{css_tag href="/ie.css"}<![endif]-->
    {/block}

    {block name="header-js"}
        <script>
            var appVersion = '{$smarty.const.DEPLOYED_AT}';
        </script>
    {/block}

    {block name="js-library"}
        {javascripts
            src="@Common/js/jquery/jquery.min.js,
                @Common/js/libs/bootstrap.js,
                @Common/js/jquery/select2/select2.min.js,
                @Common/js/jquery-onm/jquery.onmvalidate.js,
                @Common/js/libs/jquery.tools.min.js,
                @Common/js/libs/tinycon.min.js,
                @Common/js/libs/modernizr.min.js,
                @Common/js/onm/scripts.js,
                @Common/js/onm/footer-functions.js,
                @Common/js/onm/jquery.onm-editor.js,
                @AdminTheme/js/jquery/bootstrap-nav-wizard.js"}
            <script type="text/javascript" src="{$asset_url}"></script>
        {/javascripts}
    {/block}

    {block name="header-js"}
    {/block}
</head>
<body>
    <header class="header navbar navbar-inverse">
        <!-- BEGIN TOP NAVIGATION BAR -->
        <div class="navbar-inner">
            <div class="header-seperation">
                <div class="layout-collapse pull-left">
                    <div class="btn layout-collapse-toggle" ng-click="sidebar.current ? sidebar.current = 0 : (sidebar.forced ? sidebar.current = 1 : sidebar.current = sidebar.wanted)">
                        <i class="fa fa-bars fa-lg" ng-class="{ 'fa-circle-o-notch fa-spin': changing.dashboard || changing.instances || changing.commands ||  changing.cache || changing.users || changing.groups }"></i>
                    </div>
                </div>
                <a class="header-static-logo" href="{url name=admin_welcome}">
                    <h1>
                        open<strong>nemas</strong>
                    </h1>
                </a>
                <div ng-mouseleave="sidebar.forced ? sidebar.current = 1 : sidebar.current = sidebar.wanted" ng-mouseenter="sidebar.current = 0">
                    <div class="overlay"></div>
                    <a class="header-logo" href="{url name=admin_welcome}">
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
    <div class="layout-collapse-border" ng-click="sidebar.wanted = !sidebar.wanted; sidebar.forced ? sidebar.current = 1 : sidebar.current = sidebar.wanted" ng-swipe-right="sidebar.current = 0" ng-swipe-left="sidebar.current = 1"></div>
    <!-- END SIDEBAR -->

    <div class="page-container row-fluid" ng-show="auth.status || (!auth.status && auth.modal)">
        <!-- BEGIN PAGE CONTAINER-->
            <div class="page-content">
                <div class="view" id="view" ng-view autoscroll="true">
                    {block name="content"}{/block}
                </div>
            </div>
        <!-- END PAGE CONTAINER -->
    </div>

    <!--[if lt IE 7 ]>
        <script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.2/CFInstall.min.js"></script>
        <script>window.attachEvent("onload",function(){ CFInstall.check({ mode:"overlay" }) })</script>
    <![endif]-->

    {block name="footer-js"}
        <script type="text/javascript" src="//www.google.com/recaptcha/api/js/recaptcha_ajax.js"></script>

        {javascripts src="
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
            @Common/plugins/angular-webstorage/angular-webstorage.min.js,
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

            @Common/plugins/webarch/js/core.js,
        " filters="uglifyjs"}
            <script type="text/javascript" src="{$asset_url}"></script>
        {/javascripts}

        {browser_update}
        <script type="text/javascript">
        Tinycon.setBubble({count_pending_comments});
        </script>
        {uservoice_widget}
        <script>
        var CKEDITOR_BASEPATH = '/assets/js/ckeditor/';
        </script>
        {script_tag src="/ckeditor/ckeditor.js" common=1}
        <script type="text/javascript">
        $(function() {
            $.onmEditor({
                language: '{$smarty.const.CURRENT_LANGUAGE_SHORT}' ,
            });

            $('.select2').select2({
                formatSelection: function(state) {
                    var element = state.element;
                    if ($(element).parents('.select2').data('label') != null) {
                        return $(element).parents('.select2').data('label')
                            + ': ' + state.text;
                    }

                    return state.text
                }
            });
        })
        </script>
    {/block}