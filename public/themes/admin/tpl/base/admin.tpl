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
            @Common/components/font-awesome/css/font-awesome.min.css,
            @Common/components/bootstrap/dist/css/bootstrap.min.css,
            @Common/components/pace/themes/blue/pace-theme-minimal.css,
            @Common/components/nanoscroller/bin/css/nanoscroller.css,
            @Common/components/angular-loading-bar/build/loading-bar.min.css,
            @Common/components/ngQuickDate/dist/ng-quick-date.css,
            @Common/components/ngQuickDate/dist/ng-quick-date-default-theme.css,
            @Common/components/ngQuickDate/dist/ng-quick-date-plus-default-theme.css,
            @Common/components/ng-tags-input/ng-tags-input.min.css,
            @Common/components/messenger/build/css/messenger.css,
            @Common/components/messenger/build/css/messenger-theme-flat.css,
            @Common/components/select2/select2.css,

            @Common/src/webarch/css/animate.min.css,
            @Common/src/webarch/css/style.css,
            @Common/src/webarch/css/responsive.css,
            @Common/src/webarch/css/custom-icon-set.css,
            @Common/src/webarch/css/magic_space.css,

            @Common/components/jquery-ui/themes/base/minified/jquery-ui.min.css,

            @Common/components/nanoscroller/bin/css/nanoscroller.css,
            @Common/components/angular-loading-bar/build/loading-bar.min.css,
            @Common/components/ngQuickDate/dist/ng-quick-date.css,
            @Common/components/ngQuickDate/dist/ng-quick-date-default-theme.css,
            @Common/components/ngQuickDate/dist/ng-quick-date-plus-default-theme.css,
            @Common/components/ng-tags-input/ng-tags-input.min.css,
            @Common/components/messenger/build/css/messenger.css,
            @Common/components/messenger/build/css/messenger-theme-flat.css,
            @Common/components/bootstrap-nav-wizard/bootstrap-nav-wizard.css,

            @Common/src/sidebar/css/sidebar.css,
            @Common/src/angular-media-picker/less/main.less,

            @Common/src/opennemas-webarch/base/*,
            @Common/src/opennemas-webarch/components/*,
            @Common/src/opennemas-webarch/layout/*,
            @Common/src/opennemas-webarch/main.less,

            @AdminTheme/less/_comment.less"
        filters="cssrewrite,less"}<link rel="stylesheet" type="text/css" href="{$asset_url}">{/stylesheets}
    {/block}

    {block name="header-js"}
         {javascripts src="
            @Common/components/jquery/jquery.min.js,
            @Common/components/bootstrap/dist/js/bootstrap.min.js
        " filters="uglifyjs"}
            <script type="text/javascript" src="{$asset_url}"></script>
        {/javascripts}
        <script>
            var appVersion = '{$smarty.const.DEPLOYED_AT}';
            var instanceMedia = '{$smarty.const.INSTANCE_MEDIA}';
            var CKEDITOR_BASEPATH = '/assets/components/ckeditor/';
        </script>
    {/block}
</head>
<body ng-app="BackendApp" ng-controller="MasterCtrl" resizable ng-class="{ 'collapsed': sidebar.collapsed }">


    <header class="header navbar navbar-inverse">
        <!-- BEGIN TOP NAVIGATION BAR -->
        <div class="navbar-inner">
            <div class="header-seperation">
                <div class="layout-collapse pull-left">
                    <div class="btn layout-collapse-toggle" ng-click="sidebar.collapsed ? sidebar.collapsed = 0 : (sidebar.forced ? sidebar.collapsed = 1 : sidebar.collapsed = !sidebar.pinned)">
                        <i class="fa fa-bars fa-lg" ng-class="{ 'fa-circle-o-notch fa-spin': changing.dashboard || changing.instances || changing.commands ||  changing.cache || changing.users || changing.groups }"></i>
                    </div>
                </div>
                <a class="header-static-logo" href="{url name=admin_welcome}">
                    <h1>
                        open<strong>nemas</strong>
                    </h1>
                </a>
                <div ng-mouseleave="sidebar.forced ? sidebar.collapsed = 1 : sidebar.collapsed = !sidebar.pinned" ng-mouseenter="sidebar.collapsed = 0">
                    <div class="overlay"></div>
                    <a class="header-logo pull-left" href="{url name=admin_welcome}">
                        <h1 ng-mouseleave="sidebar.forced ? sidebar.collapsed = 1 : sidebar.collapsed = !sidebar.pinned" ng-mouseenter="sidebar.collapsed = 0">
                            <span class="first-char">o</span><span class="title-token">pen<strong>nemas</strong></span>
                        </h1>
                    </a>
                    {if count_pending_comments > 0}
                        <a class="btn btn-link pull-right" title="{count_pending_comments} {t}Pending comments{/t}"  href="{url name=admin_comments}">
                            <i class="fa fa-inbox fa-lg"></i><span class="badge badge-danger">{count_pending_comments}</span>
                        </a>
                    {/if}
                </div>
            </div>
        <!-- END TOP NAVIGATION MENU -->
        </div>
      <!-- END TOP NAVIGATION BAR -->
    </header>

    <!-- BEGIN SIDEBAR -->
    {include file="base/sidebar.tpl"}
    <div class="layout-collapse-border" ng-click="sidebar.pinned = !sidebar.pinned; sidebar.forced ? sidebar.collapsed = 1 : sidebar.collapsed = !sidebar.pinned" ng-swipe-right="sidebar.collapsed = 0" ng-swipe-left="sidebar.collapsed = 1"></div>
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

    {block name="global-js"}
        <script type="text/javascript" src="//www.google.com/recaptcha/api/js/recaptcha_ajax.js"></script>

        {javascripts src="
            @Common/components/jquery-ui/ui/minified/jquery-ui.min.js,

            @Common/components/breakpoints/breakpoints.js,
            @Common/components/ckeditor/ckeditor.js,
            @Common/components/fastclick/lib/fastclick.js,
            @Common/components/nanoscroller/bin/javascripts/jquery.nanoscroller.min.js,
            @Common/components/messenger/build/js/messenger.min.js,
            @Common/components/messenger/build/js/messenger-theme-flat.js,
            @Common/components/moment/min/moment-with-locales.min.js,
            @Common/components/moment-timezone/builds/moment-timezone-with-data.min.js,
            @Common/components/select2/select2.min.js,

            @Common/components/modernizr/modernizr.js,
            @Common/js/libs/tinycon.min.js,
            @AdminTheme/js/jquery/bootstrap-nav-wizard.js,
            @Common/js/onm/md5.min.js,
            @Common/js/onm/scripts.js,
            @Common/components/jquery-validation/dist/jquery.validate.js,

            @FosJsRoutingBundle/js/router.js,
            @Common/js/routes.js,
            @Common/components/angular/angular.min.js,
            @Common/components/angular-animate/angular-animate.min.js,
            @Common/components/angular-checklist-model/checklist-model.js,
            @Common/components/angular-file-upload/angular-file-upload.min.js,
            @Common/components/angular-webstorage/angular-webstorage.min.js,
            @Common/components/angular-google-chart/ng-google-chart.js,
            @Common/components/angular-nanoscroller/scrollable.js,
            @Common/components/angular-loading-bar/build/loading-bar.min.js,
            @Common/components/ngQuickDate/dist/ng-quick-date.min.js,
            @Common/components/angular-recaptcha/release/angular-recaptcha.min.js,
            @Common/components/angular-route/angular-route.min.js,
            @Common/components/ng-tags-input/ng-tags-input.min.js,
            @Common/components/angular-touch/angular-touch.min.js,
            @Common/components/angular-translate/angular-translate.min.js,
            @Common/components/angular-bootstrap/ui-bootstrap-tpls.min.js,
            @Common/components/angular-ui-select/dist/select.min.js,
            @Common/components/angular-ui-sortable/sortable.min.js,

            @Common/src/webarch/js/core.js,
            @Common/src/angular-dynamic-image/dynamic-image.js,
            @Common/src/angular-onm-editor/onm-editor.js,
            @Common/src/angular-oql-encoder/oql-encoder.js,
            @Common/src/angular-query-manager/query-manager.js,
            @Common/src/angular-item-service/itemService.js,
            @Common/src/angular-routing/routing.js,
            @Common/src/angular-media-picker/js/media-picker.js,
            @Common/src/angular-messenger/messenger.js,
            @Common/src/angular-resizable/resizable.js,
            @Common/src/angular-scroll/angular-scroll.js,


            @AdminTheme/js/app.js,
            @AdminTheme/js/config.js,
            @AdminTheme/js/services/*,
            @AdminTheme/js/filters/*,
            @AdminTheme/js/directives/*,
            @AdminTheme/js/controllers/*,

            @Common/js/admin.js

        "}
            <script type="text/javascript" src="{$asset_url}"></script>
        {/javascripts}

        {block name="footer-js"}

        {/block}

        {browser_update}
        <script type="text/javascript">
        Tinycon.setBubble({count_pending_comments});
        </script>
        {uservoice_widget}

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
