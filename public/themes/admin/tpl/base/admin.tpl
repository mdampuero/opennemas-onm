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
  <link rel="manifest" href="backend_manifest.json">
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
  <link href='//fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" type="text/css" href="/assets/components/font-awesome/css/font-awesome.min.css">

  {block name="header-css"}
    {stylesheets src="
      @Common/components/bootstrap/dist/css/bootstrap.min.css,
      @Common/components/bootstrap-tagsinput/dist/bootstrap-tagsinput.css,
      @Common/components/pace/themes/blue/pace-theme-minimal.css,
      @Common/components/nanoscroller/bin/css/nanoscroller.css,
      @Common/components/angular-loading-bar/build/loading-bar.min.css,
      @Common/components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css,
      @Common/components/angular-ui-tree/dist/angular-ui-tree.min.css,
      @Common/components/ngQuickDate/dist/ng-quick-date.css,
      @Common/components/ngQuickDate/dist/ng-quick-date-default-theme.css,
      @Common/components/ngQuickDate/dist/ng-quick-date-plus-default-theme.css,
      @Common/components/ng-tags-input/ng-tags-input.min.css,
      @Common/components/messenger/build/css/messenger.css,
      @Common/components/messenger/build/css/messenger-theme-flat.css,
      @Common/components/bootstrap-tabdrop/build/css/tabdrop.css,
      @Common/components/select2/select2.css,
      @Common/components/animate.css/animate.min.css,
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
      @Common/components/angular-ui-select/dist/select.min.css,
      @Common/components/ng-tags-input/ng-tags-input.min.css,
      @Common/components/messenger/build/css/messenger.css,
      @Common/components/messenger/build/css/messenger-theme-flat.css,
      @Common/src/angular-dynamic-image/less/main.less,
      @Common/src/angular-picker/less/main.less,
      @Common/src/sidebar/less/main.less,
      @Common/src/angular-onm-pagination/less/main.less,
      @Common/src/opennemas-webarch/css/layout/*,
      @Common/src/opennemas-webarch/less/main.less,
      @AdminTheme/less/_album.less,
      @AdminTheme/less/_article.less,
      @AdminTheme/less/_comment.less,
      @AdminTheme/less/_image.less"
    filters="cssrewrite,less"}
      <link rel="stylesheet" type="text/css" href="{$asset_url}">
    {/stylesheets}
  {/block}
  {block name="header-js"}
    {javascripts src="
      @Common/components/jquery/jquery.min.js,
      @Common/components/bootstrap/dist/js/bootstrap.min.js"
    filters="uglifyjs"}
      <script type="text/javascript" src="{$asset_url}"></script>
    {/javascripts}

    <script>
      var appVersion = '{$smarty.const.DEPLOYED_AT}';
      var instanceMedia = '{$smarty.const.INSTANCE_MEDIA}';
      var CKEDITOR_BASEPATH = '/assets/components/ckeditor/';
    </script>
  {/block}
</head>
<body ng-app="BackendApp" ng-controller="MasterCtrl" resizable ng-class="{ 'collapsed': sidebar.isCollapsed(), 'pinned': sidebar.isPinned() }" class="server-sidebar{if $smarty.session.sidebar_pinned === false} unpinned-on-server{/if}" ng-init="init('{$smarty.const.CURRENT_LANGUAGE|default:"en"}')">
  {block name="body"}
    <div class="overlay"></div>
    <header class="header navbar navbar-inverse">
      <div class="navbar-inner">
        <div class="header-seperation">
          <a class="header-logo pull-left" href="{url name=admin_welcome}">
            <h1>
              open<strong>nemas</strong>
            </h1>
          </a>
          <div>
            {block name="comments"}
              {if {count_pending_comments} gt 0}
                <ul class="nav pull-right notifcation-center" ng-if="sidebar.isCollapsed()">
                  <li class="dropdown" id="header_inbox_bar">
                    <a href="{url name=admin_comments}" class="dropdown-toggle">
                      <div class="iconset top-messages"></div>
                      <span class="badge animated" id="msgs-badge">{count_pending_comments}</span>
                    </a>
                  </li>
                </ul>
              {/if}
            {/block}
          </div>
        </div>
        <div class="header-quick-nav">
          {block name="header_links"}
            <div class="pull-left">
              <ul class="nav quick-section">
                {acl isAllowed="ROLE_ADMIN"}
                  <li class="quicklinks">
                    <a href="{url name=admin_client_info_page}" title="{t}Instance information{/t}">
                      <i class="fa fa-bullseye"></i>
                      {t}My newspaper{/t}
                    </a>
                  </li>
                {/acl}
                {block name="quick-create"}
                  <li class="quicklinks">
                    <span class="h-seperate"></span>
                  </li>
                  <li class="quicklinks create-items quick-items dropdown">
                    <a href="#" data-toggle="dropdown">
                      <i class="fa fa-plus"></i>
                      {t}Create{/t}
                    </a>
                    <div class="dropdown-menu">
                      {is_module_activated name="ARTICLE_MANAGER"}
                        {acl isAllowed="ARTICLE_CREATE"}
                          <div class="quick-item">
                            <a href="{url name=admin_article_create}">
                              <i class="fa fa-file-text"></i>
                              <span class="title">{t}Article{/t}</span>
                            </a>
                          </div>
                        {/acl}
                      {/is_module_activated}
                      {is_module_activated name="OPINION_MANAGER"}
                        {acl isAllowed="OPINION_CREATE"}
                          <div class="quick-item">
                            <a href="{url name=admin_opinion_create}">
                              <i class="fa fa-quote-right"></i>
                              <span class="title">{t}Opinion{/t}</span>
                            </a>
                          </div>
                        {/acl}
                      {/is_module_activated}
                      {is_module_activated name="POLL_MANAGER"}
                        {acl isAllowed="POLL_CREATE"}
                          <div class="quick-item">
                            <a href="{url name=admin_poll_create}">
                              <i class="fa fa-pie-chart"></i>
                              <span class="title">{t}Poll{/t}</span>
                            </a>
                          </div>
                        {/acl}
                      {/is_module_activated}
                      {is_module_activated name="STATIC_PAGES_MANAGER"}
                        {acl isAllowed="STATIC_PAGE_CREATE"}
                          <div class="quick-item">
                            <a href="{url name=admin_static_pages_create}">
                              <i class="fa fa-file-o"></i>
                              <span class="title">{t}Page{/t}</span>
                            </a>
                          </div>
                        {/acl}
                      {/is_module_activated}
                      {is_module_activated name="SPECIAL_MANAGER"}
                        {acl isAllowed="SPECIAL_CREATE"}
                          <div class="quick-item">
                            <a href="{url name=admin_special_create}">
                              <i class="fa fa-star"></i>
                              <span class="title">{t}Special{/t}</span>
                            </a>
                          </div>
                        {/acl}
                      {/is_module_activated}
                      {is_module_activated name="LETTER_MANAGER"}
                        {acl isAllowed="LETTER_CREATE"}
                          <div class="quick-item">
                            <a href="{url name=admin_letter_create}">
                              <i class="fa fa-envelope"></i>
                              <span class="title">{t}Letter{/t}</span>
                            </a>
                          </div>
                        {/acl}
                      {/is_module_activated}
                      {is_module_activated name="CATEGORY_MANAGER"}
                        {acl isAllowed="CATEGORY_CREATE"}
                          <div class="quick-item">
                            <a href="{url name=admin_category_create}">
                              <i class="fa fa-bookmark"></i>
                              <span class="title">{t}Category{/t}</span>
                            </a>
                          </div>
                        {/acl}
                      {/is_module_activated}
                      {is_module_activated name="ALBUM_MANAGER"}
                        {acl isAllowed="ALBUM_CREATE"}
                          <div class="quick-item">
                            <a href="{url name=admin_album_create}">
                              <i class="fa fa-stack-overflow"></i>
                              <span class="title">{t}Album{/t}</span>
                            </a>
                          </div>
                        {/acl}
                      {/is_module_activated}
                      {is_module_activated name="KIOSKO_MANAGER"}
                        {acl isAllowed="KIOSKO_CREATE"}
                          <div class="quick-item">
                            <a href="{url name=admin_kiosko_create}">
                              <i class="fa fa-newspaper-o"></i>
                              <span class="title">{t}News Stand{/t}</span>
                            </a>
                          </div>
                        {/acl}
                      {/is_module_activated}
                      {is_module_activated name="BOOK_MANAGER"}
                        {acl isAllowed="BOOK_CREATE"}
                          <div class="quick-item">
                            <a href="{url name=admin_books_create}">
                              <i class="fa fa-book"></i>
                              <span class="title">{t}Book{/t}</span>
                            </a>
                          </div>
                        {/acl}
                      {/is_module_activated}
                      {is_module_activated name="OPINION_MANAGER"}
                        {acl isAllowed="AUTHOR_ADMIN"}
                          <div class="quick-item">
                            <a href="{url name=admin_author_create}">
                              <i class="fa fa-user"></i>
                              <span class="title">{t}Author{/t}</span>
                            </a>
                          </div>
                        {/acl}
                      {/is_module_activated}
                    </div>
                  </li>
                 {/block}
              </ul>
            </div>
            <div class="pull-right ">
              <ul class="nav quick-section">
                {*
                <li class="quicklinks notifications dropdown">
                  <a href="#" data-toggle="dropdown" tooltip="{t}Notifications{/t}" tooltip-placement="bottom">
                    <i class="fa fa-bell"></i>
                  </a>
                  <div class="dropdown-menu">
                    <div class="dropdown-title">
                      {t}Notifications{/t}
                    </div>
                    <ul class="notification-list">
                      <li class="notification-success">
                        <div class="title">Success!</div>
                        <p>{t}This is a notification for a success{/t}</p>
                      </li>
                      <li class="notification-error">
                        <div class="title">Error!</div>
                        <p>{t}This notification is an error{/t}</p>
                      </li>
                      <li class="notification-warning">
                        <div class="title">Warning!</div>
                        <p>{t}This notification is a warning{/t}</p>
                      </li>
                    </ul>
                  </div>
                </li>
                <li class="quicklinks">
                  <span class="h-seperate"></span>
                </li>
                *}
                <li class="quicklinks quick-items help-items dropdown">
                  <a href="#" data-toggle="dropdown" tooltip="{t}Help center{/t}" tooltip-placement="bottom">
                    <i class="fa fa-support"></i>
                  </a>
                  <div class="dropdown-menu">
                    <!-- <div class="dropdown-title">{t}Help center{/t}</div> -->
                    <div class="clearfix quick-items-row">
                      <div class="quick-item">
                        <a href="javascript:UserVoice.showPopupWidget();">
                          <i class="fa fa-support" title="{t}Contact us{/t}"></i>
                          <span class="title">{t}Contact us{/t}</span>
                          <span class="subtitle">{t}Ask for help using email{/t}</span>
                        </a>
                      </div>
                      <div class="quick-item">
                        <a href="http://help.opennemas.com" target="_blank" title="{t}F.A.Q.{/t}">
                          <i class="fa fa-question-circle"></i>
                          <span class="title">{t}F.A.Q.{/t}</span>
                          <span class="subtitle">{t}Read what user ask more{/t}</span>
                        </a>
                      </div>
                      <div class="quick-item">
                        <a href="http://www.youtube.com/user/OpennemasPublishing" target="_blank" title="{t}Youtube channel{/t}">
                          <i class="fa fa-youtube"></i>
                          <span class="title">{t}Videotutorials{/t}</span>
                          <span class="subtitle">{t}Use videos to get walkthrough guides{/t}</span>
                        </a>
                      </div>
                    </div>
                  </div>
                </li>
                <li class="quicklinks">
                  <span class="h-seperate"></span>
                </li>
                <li class="quicklinks user-info dropdown">
                  <span class="link" data-toggle="dropdown">
                    {if is_object($smarty.session._sf2_attributes.user) && $smarty.session._sf2_attributes.user->isMaster()}
                      <i class="fa fa-rebel text-danger master-user"></i>
                    {/if}
                    <span class="title">
                      {$smarty.session.realname}
                    </span>
                    <div class="profile-pic">
                      {gravatar email=$smarty.session.email image_dir=$params.IMAGE_DIR image=true size="25"}
                    </div>
                    <i class="fa fa-angle-down"></i>
                  </span>
                  <ul class="dropdown-menu on-right" role="menu">
                    {if is_object($smarty.session._sf2_attributes.user) && $smarty.session._sf2_attributes.user->isMaster()}
                      <li class="text-danger">
                        <span class="dropdown-static-item">
                          {t}This user is a master{/t}
                        </span>
                      </li>
                      <li class="divider"></li>
                    {/if}
                    <li>
                      <a href="/">
                        <i class="fa fa-globe"></i>
                        {t}Go to newspaper{/t}
                      </a>
                    </li>
                    <li class="divider"></li>
                    <li>
                      {if is_object($smarty.session._sf2_attributes.user) && $smarty.session._sf2_attributes.user->isMaster()}
                        <a ng-href="/manager#/user/{$smarty.session.userid}/show">
                          <i class="fa fa-user"></i>
                          {t}Profile{/t}
                        </a>
                      {else}
                        {acl isAllowed="USER_EDIT_OWN_PROFILE"}
                        <a href="{url name=admin_acl_user_show id=me}">
                          <i class="fa fa-user"></i>
                          {t}Profile{/t}
                        </a>
                        {/acl}
                      {/if}
                    </li>
                    <li class="divider"></li>
                    <li>
                      <a href="{url name=admin_getting_started}">
                        <i class="fa fa-rocket"></i>
                        {t}Getting started{/t}
                      </a>
                    </li>
                    <li class="divider"></li>
                    <li>
                      <a role="menuitem" tabindex="-1" href="{url name=admin_logout}">
                        <i class="fa fa-power-off m-r-10"></i>
                        {t}Log out{/t}
                      </a>
                    </li>
                  </ul>
                </li>
              </ul>
            </div>
          {/block}
        </div>
      </div>
    </header>
    {block name="sidebar"}
      {include file="base/sidebar.tpl"}
      <div class="sidebar-border" ng-click="sidebar.pin()" ng-swipe-right="sidebar.swipeOpen()" ng-swipe-left="sidebar.swipeClose()" title="{t}Show/hide sidebar{/t}"></div>
    {/block}
    {block name="page_container"}
    {render_messages}
      <div class="page-container row-fluid">
        <!-- BEGIN PAGE CONTAINER-->
        <div class="page-content">
          <div class="sidebar-toggler ng-cloak" ng-click="sidebar.toggle()">
            <span class="fa fa-bars fa-lg"></span>
          </div>
          <div class="view" id="view" ng-view autoscroll="true">
            {block name="content"}{/block}
          </div>
        </div>
        <!-- END PAGE CONTAINER -->
      </div>
    {/block}
    {block name="modals"}{/block}
  {/block}
  <!--[if lt IE 7 ]>
  <script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.2/CFInstall.min.js"></script>
  <script>window.attachEvent("onload",function(){ CFInstall.check({ mode:"overlay" }) })</script>
  <![endif]-->
  {block name="global-js"}
    <script type="text/javascript" src="//www.google.com/recaptcha/api/js/recaptcha_ajax.js"></script>
    {javascripts src="
      @Common/components/jquery-ui/ui/minified/jquery-ui.min.js,
      @Common/components/jqueryui-touch-punch/jquery.ui.touch-punch.min.js,
      @Common/components/breakpoints/breakpoints.js,
      @Common/components/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js,
      @Common/components/ckeditor/ckeditor.js,
      @Common/components/fastclick/lib/fastclick.js,
      @Common/components/nanoscroller/bin/javascripts/jquery.nanoscroller.min.js,
      @Common/components/messenger/build/js/messenger.min.js,
      @Common/components/messenger/build/js/messenger-theme-flat.js,
      @Common/components/moment/min/moment-with-locales.min.js,
      @Common/components/moment-timezone/builds/moment-timezone-with-data.min.js,
      @Common/components/select2/select2.min.js,
      @Common/components/bootstrap-tabdrop/build/js/bootstrap-tabdrop.min.js,
      @Common/components/swfobject/swfobject/swfobject.js,
      @Common/components/modernizr/modernizr.js,
      @Common/js/libs/tinycon.min.js,
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
      @Common/components/angular-sanitize/angular-sanitize.min.js,
      @Common/components/angulartics/dist/angulartics.min.js,
      @Common/components/angulartics/dist/angulartics-ga.min.js,
      @Common/components/ngQuickDate/dist/ng-quick-date.min.js,
      @Common/components/angular-recaptcha/release/angular-recaptcha.min.js,
      @Common/components/angular-route/angular-route.min.js,
      @Common/components/ng-tags-input/ng-tags-input.min.js,
      @Common/components/angular-touch/angular-touch.min.js,
      @Common/components/angular-translate/angular-translate.min.js,
      @Common/components/angular-bootstrap/ui-bootstrap-tpls.min.js,
      @Common/components/angular-swfobject/angular-swfobject.js,
      @Common/components/angular-ui-select/dist/select.min.js,
      @Common/components/angular-ui-sortable/sortable.min.js,
      @Common/components/angular-ui-tree/dist/angular-ui-tree.min.js,
      @Common/components/angular-bootstrap-multiselect/angular-bootstrap-multiselect.js,
      @Common/src/opennemas-webarch/js/core.js,
      @Common/src/angular-bootstrap-multiselect/template.js,
      @Common/src/angular-dynamic-image/js/dynamic-image.js,
      @Common/src/angular-gravatar/gravatar.js,
      @Common/src/angular-onm-editor/onm-editor.js,
      @Common/src/angular-onm-pagination/js/onm-pagination.js,
      @Common/src/angular-oql-encoder/oql-encoder.js,
      @Common/src/angular-query-manager/query-manager.js,
      @Common/src/angular-item-service/itemService.js,
      @Common/src/angular-renderer/renderer.js,
      @Common/src/angular-routing/routing.js,
      @Common/src/angular-picker/js/picker.js,
      @Common/src/angular-picker/js/content-picker.js,
      @Common/src/angular-picker/js/media-picker.js,
      @Common/src/angular-messenger/messenger.js,
      @Common/src/angular-resizable/resizable.js,
      @Common/src/angular-scroll/angular-scroll.js,
      @Common/src/angular-history/history.js,
      @Common/src/sidebar/js/sidebar.js,
      @AdminTheme/js/app.js,
      @AdminTheme/js/config.js,
      @AdminTheme/js/controllers/*,
      @AdminTheme/js/directives/*,
      @AdminTheme/js/filters/*,
      @AdminTheme/js/interceptors/*,
      @AdminTheme/js/services/*,
      @Common/js/admin.js
    "}
      <script type="text/javascript" src="{$asset_url}"></script>
    {/javascripts}
    {block name="footer-js"}{/block}
    {browser_update}
    <script type="text/javascript">
      Tinycon.setBubble({count_pending_comments});
    </script>
    {uservoice_widget}
  {/block}
</body>
