<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta name="author"    content="OpenHost,SL">
    <meta name="generator" content="OpenNemas - News Management System">
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    {block name="meta"}
    <title>OpenNeMaS - Manager</title>
    {/block}

    <link rel="icon" href="{$params.COMMON_ASSET_DIR}images/favicon.png">
    {block name="header-css"}
        {stylesheets src="@Common/plugins/pace/pace-theme-flash.css,
                          @Common/plugins/jquery-slider/css/jquery.sidr.light.css,
                          @Common/plugins/webarch/css/animate.min.css,

                          @Common/plugins/bootstrap/css/bootstrap.min.css,
                          @Common/plugins/font-awesome/css/font-awesome.min.css"
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
                          @Common/plugins/angular-route/angular-route.min.js,
                          @Common/plugins/angular-translate/angular-translate.min.js,
                          @Common/plugins/angular-quickdate/js/ng-quick-date.min.js,
                          @Common/plugins/angular-tags-input/js/ng-tags-input.min.js,
                          @Common/plugins/angular-ui/ui-bootstrap-tpls.min.js,

                          @Common/plugins/angular-onm/services/*,

                          @ManagerTheme/js/ManagerApp.js,
                          @ManagerTheme/js/Controllers.js,

                          @ManagerTheme/js/controllers/*

        "}
            <script type="text/javascript" src="{$asset_url}"></script>
        {/javascripts}
    {/block}

</head>
<body id="manager" ng-app="ManagerApp" ng-controller="MasterCtrl">
    <header class="header navbar navbar-inverse ">
        <!-- BEGIN TOP NAVIGATION BAR -->
        <div class="navbar-inner">
            <div class="header-seperation" ng-hide="mini">
                <ul class="nav pull-left notifcation-center" id="main-menu-toggle-wrapper" style="display:none">
                    <li class="dropdown">
                        <a id="main-menu-toggle" href="#main-menu">
                            <div class="iconset top-menu-toggle-white"></div>
                        </a>
                    </li>
                </ul>
                <!-- BEGIN LOGO -->
                <a href="{url name=manager_welcome}">
                    {image_tag src="/assets/images/logo-opennemas-small.png" class="logo" alt=""  data-src="/assets/images/logo-opennemas-small.png" data-src-retina="/assets/images/logo-opennemas-small.png" width="132" height="27"}
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
                    <ul class="nav quick-section">
                        <li class="quicklinks">
                            <span class="a" id="layout-condensed-toggle" ng-click="toggle(!mini);">
                                <div class="iconset top-menu-toggle-dark"></div>
                            </span>
                        </li>
                    </ul>
                    <ul class="nav quick-section">
                        <li class="m-r-10 input-prepend inside search-form no-boarder">
                            <span class="add-on">
                                <span class="iconset top-search"></span>
                            </span>
                            <input name="" type="text"  class="no-boarder " placeholder="Search Dashboard" style="width:250px;">
                        </li>
                    </ul>
                </div>
                <!-- END TOP NAVIGATION MENU -->
                <!-- BEGIN CHAT TOGGLER -->
                <div class="pull-right">
                    <div class="chat-toggler">
                        <span class="a" class="dropdown-toggle" id="my-task-list" data-placement="bottom"  data-content='' data-toggle="dropdown" data-original-title="Notifications">
                            <div class="user-details">
                                <div class="username">
                                    <span class="badge badge-important">3</span> {$smarty.session.realname}
                                </div>
                            </div>
                            <div class="iconset top-down-arrow"></div>
                        </span>
                        <div id="notification-list" style="display:none">
                            <div style="width:300px">
                                <div class="notification-messages info">
                                    <div class="user-profile"> <img src="/assets/plugins/webarch/img/profiles/d.jpg"    alt="" data-src="/assets/plugins/webarch/img/profiles/d.jpg" data-src-retina="/assets/plugins/webarch/img/profiles/d2x.jpg" width="35" height="35"> </div>
                                    <div class="message-wrapper">
                                        <div class="heading"> David Nester - Commented on your wall </div>
                                        <div class="description"> Meeting postponed to tomorrow </div>
                                        <div class="date pull-left"> A min ago </div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="notification-messages danger">
                                    <div class="iconholder"> <i class="icon-warning-sign"></i> </div>
                                    <div class="message-wrapper">
                                        <div class="heading"> Server load limited </div>
                                        <div class="description"> Database server has reached its daily capicity </div>
                                        <div class="date pull-left"> 2 mins ago </div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="notification-messages success">
                                    <div class="user-profile"> <img src="assets/img/profiles/h.jpg"    alt="" data-src="/assets/plugins/webarch/img/profiles/h.jpg" data-src-retina="assets/img/profiles/h2x.jpg" width="35" height="35"> </div>
                                    <div class="message-wrapper">
                                        <div class="heading"> You have got 150 messages </div>
                                        <div class="description"> 150 newly unread messages in your inbox </div>
                                        <div class="date pull-left"> An hour ago </div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                        <div class="profile-pic">
                            {if $smarty.session.avatar_url}
                                <img src="{$smarty.session.avatar_url}" alt="{t}Photo{/t}" width="35" >
                            {else}
                                {gravatar email=$smarty.session.email image_dir="{$params.COMMON_ASSET_DIR}images/" image=true size="35"}
                            {/if}
                            <!-- <img src="assets/img/profiles/avatar_small.jpg"  alt="" data-src="assets/img/profiles/avatar_small.jpg" data-src-retina="assets/img/profiles/avatar_small2x.jpg" width="35" height="35" /> -->
                        </div>
                    </div>
                    <ul class="nav quick-section ">
                        <li class="quicklinks">
                            <span data-toggle="dropdown" class="a dropdown-toggle pull-right " href="#" id="user-options">
                                <i class="fa fa-gear fa-lg"></i>
                            </span>
                            <ul class="dropdown-menu    pull-right" role="menu" aria-labelledby="user-options">
                                <li>
                                    <a ng-href="[% fosJsRouting.ngGenerate('/manager', 'manager_user_show', { id: 'me' }) %]"> My Account</a>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="javascript:salir('{t}Do you really want to exit from backend?{/t}','{url name="manager_logout"  csrf=$smarty.session.csrf}');" title="{t}Logout from control panel{/t}"><i class="fa fa-power-off"></i>&nbsp;&nbsp;{t}Log Out{/t}
                                </a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <!-- END CHAT TOGGLER -->
            </div>
        <!-- END TOP NAVIGATION MENU -->
        </div>
      <!-- END TOP NAVIGATION BAR -->
    </header>
    <div class="page-container row-fluid">
        <!-- BEGIN SIDEBAR -->
        <div class="page-sidebar" id="main-menu" ng-class="{ 'mini': mini }">
            <div class="page-sidebar-wrapper" id="main-menu-wrapper">
                <!-- BEGIN SIDEBAR MENU -->
                <ul>
                    <li class="start" ng-class="{ 'active': false }">
                        <a href="#"><i class="fa fa-home"></i> <span class="title">Dashboard</span></a>
                    </li>
                    <li ng-class="{ 'active': isActive('manager_instances_list') }">
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
                    <li ng-class="{ 'active': isActive('manager_users_list') || isActive('manager_user_groups_list') }">
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
                <div ng-view></div>
                <!-- <div class="toolbar" style="position: fixed; top: 60px; background: #1b1e24 !important; width: 50px; right: 0; height: 100%; text-align: center;">
                    <a class="btn btn-link"><i class="fa fa-save"></i></a>
                    <a class="btn btn-link"><i class="fa fa-edit"></i></a>
                </div> -->
            </div>
        <!-- END PAGE CONTAINER -->
    </div>

    <!--[if lt IE 7 ]>
        <script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.2/CFInstall.min.js"></script>
        <script>window.attachEvent("onload",function(){ CFInstall.check({ mode:"overlay" }) })</script>
    <![endif]-->
</body>
</html>
