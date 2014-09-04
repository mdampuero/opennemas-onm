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
        {stylesheets src="@Common/plugins/pace/pace-theme-minimal.css,
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
                          @Common/js/onm/scripts.js" filters="uglifyjs"}
            <script type="text/javascript" src="{$asset_url}"></script>
        {/javascripts}

        <script type="text/javascript" src="//www.google.com/recaptcha/api/js/recaptcha_ajax.js"></script>
        {javascripts
            src="@Common/js/jquery/select2/select2.min.js,
                @Common/js/libs/modernizr.min.js,
                @Common/js/onm/md5.min.js,
                @Common/js/onm/scripts.js,
                @Common/js/onm/jquery.onm-editor.js"}
            <script type="text/javascript" src="{$asset_url}"></script>
        {/javascripts}
    {/block}

    {block name="footer-js"}
        <script>
            var appVersion = '{$smarty.const.DEPLOYED_AT}';
        </script>

        {javascripts src="@FosJsRoutingBundle/js/router.js,
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
        "  filters="uglifyjs"}
            <script type="text/javascript" src="{$asset_url}"></script>
        {/javascripts}
    {/block}

</head>
<body class="error-body no-top  pace-done"><div class="pace  pace-inactive"><div class="pace-progress" data-progress-text="100%" data-progress="99" style="width: 100%;">
    <div class="pace-progress-inner"></div>
</div>
<div class="pace-activity"></div></div>
<div class="error-wrapper container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-offset-1 col-xs-10">
            <div class="error-container">
                <div class="error-main">
                    <div class="error-number"> 404 </div>
                    <div class="error-description"> We seem to have lost you in the clouds. </div>
                    <div class="error-description-mini"> The page your looking for is not here </div>
                    <br>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="footer">
    <div class="error-container">
        <ul class="footer-links">
            <li><a href="#">About</a></li>
            <li><a href="#">Help &amp; FAQ</a></li>
            <li><a href="#">Privacy </a></li>
            <li><a href="#">Legal</a></li>
        </ul>
        <br>
        <div class="copyright"> © 2014 Openhost S.L. </div>
    </div>
</div>


<script src="assets/plugins/jquery-1.8.3.min.js" type="text/javascript"></script>
<script src="assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="assets/plugins/pace/pace.min.js" type="text/javascript"></script>


</body>
</html>
