<!DOCTYPE html>
<!--[if lt IE 8]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="{$smarty.const.CURRENT_LANGUAGE|default:"en"}"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang="{$smarty.const.CURRENT_LANGUAGE|default:"en"}"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang="{$smarty.const.CURRENT_LANGUAGE|default:"en"}"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="{$smarty.const.CURRENT_LANGUAGE|default:"en"}"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <meta name="author" content="OpenHost,SL">
  <meta name="generator" content="OpenNemas - News Management System">
  <meta name="theme-color" content="#22262e">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="mobile-web-app-capable" content="yes">
  <link rel="apple-touch-icon" href="/assets/images/launcher-icons/IOS-60@2x.png">
  <link rel="apple-touch-icon" sizes="120x120" href="/assets/images/launcher-icons/IOS-60@2x.png">
  <link rel="apple-touch-icon" sizes="152x152" href="/assets/launcher-icons/IOS-60@2x.png">
  <link rel="apple-touch-icon" sizes="76x76" href="/assets/images/launcher-icons/IOS-60@2x.png">
  <link rel="icon" href="/assets/images/favicon.png">
  <link rel="icon" sizes="192x192" href="/assets/images/launcher-icons/IOS-60@2x.png">
  <link rel="manifest" href="/backend_manifest.json">

  {block name="meta"}
    <title>{setting name=site_name} - {t}OpenNeMaS administration{/t}</title>
  {/block}
  <link href="//fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet" type="text/css">
  <link href="/assets/components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

  {block name="header-css"}
    {stylesheets src="@Common/components/bootstrap/dist/css/bootstrap.min.css,
      @Common/components/angular-bootstrap-colorpicker/css/colorpicker.min.css,
      @Common/components/angular-loading-bar/build/loading-bar.min.css,
      @Common/components/angular-ui-select/dist/select.min.css,
      @Common/components/angular-ui-tab-scroll/angular-ui-tab-scroll.css,
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
      @Common/src/angular-tag/less/main.less,
      @Common/src/sidebar/less/main.less,
      @Common/src/opennemas-webarch/css/layout/*,
      @Common/src/photo-editor/css/photo-editor.css,
      @Common/src/opennemas-webarch/less/main.less" filters="cssrewrite,less" output="common"}
    {/stylesheets}
    {stylesheets src="@Common/components/angular-bootstrap/ui-bootstrap-csp.css,
      @Common/components/angular-ui-tree/dist/angular-ui-tree.min.css,
      @Common/components/bootstrap-tabdrop/build/css/tabdrop.css,
      @Common/components/bootstrap-tagsinput/dist/bootstrap-tagsinput.css,
      @Common/components/jasny-bootstrap/dist/css/jasny-bootstrap.min.css,
      @Common/components/jquery-ui/themes/base/jquery-ui.min.css,
      @Common/src/angular-fly-to-cart/less/main.less,
      @Common/src/angular-picker/less/main.less,
      @AdminTheme/less/*" filters="cssrewrite,less" output="admin"}
    {/stylesheets}
  {/block}
  {block name="header-js"}
    <script>
      var appVersion = '{$smarty.const.DEPLOYED_AT}';
      var instanceMedia = '{$smarty.const.INSTANCE_MEDIA}';
      var CKEDITOR_BASEPATH = '/assets/components/ckeditor/';
      var leaveMessage = '{t}You are leaving the current page.{/t}';
      var photoEditorTranslations = {
        transform: '{t}transform{/t}',
        light: '{t}light{/t}',
        landscape: '{t}landscape{/t}',
        portrait: '{t}portrait{/t}',
        free: '{t}free{/t}',
        brightness: '{t}brightness{/t}',
        contrast: '{t}contrast{/t}',
        cancel: '{t}cancel{/t}',
        editImage: '{t}edit image{/t}',
        save: '{t}save{/t}',
        reset: '{t}reset{/t}',
        apply: '{t}apply{/t}'
      };
    </script>
  {/block}
</head>
<body ng-app="BackendApp" ng-controller="MasterCtrl" resizable ng-class="{ 'collapsed': sidebar.isCollapsed(), 'pinned': sidebar.isPinned() }" class="server-sidebar{if $smarty.session._sf2_attributes.sidebar_pinned === false} unpinned-on-server{/if}" ng-init="init('{$smarty.const.CURRENT_LANGUAGE|default:"en"}', '{t}Any{/t}')" >
  {block name="body"}
    <div class="overlay"></div>
    {block name="header"}
    <header class="header navbar navbar-inverse" ng-controller="NotificationCtrl" ng-init="{block name="ng-init"}{/block}getLatest()">
      <div class="navbar-inner">
        {if !in_array('es.openhost.module.whiteLabel', $app.instance->activated_modules)}
        <div class="header-seperation">
          <a class="header-logo pull-left" href="{url name=admin_welcome}">
            <h1>
              open<strong>nemas</strong>
            </h1>
          </a>
        </div>
        {/if}
        <div class="header-quick-nav">
          {block name="header_links"}
            <div class="pull-left">
              <ul class="nav quick-section">
                {acl isAllowed="ADMIN"}
                  <li class="quicklinks">
                    <a href="{url name=backend_account_show}" title="{t}Instance information{/t}">
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
                            <a href="{url name=backend_static_page_create}">
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
                      {is_module_activated name="VIDEO_MANAGER"}
                        {acl isAllowed="VIDEO_CREATE"}
                          <div class="quick-item">
                            <a href="{url name=admin_videos_create}">
                              <i class="fa fa-film"></i>
                              <span class="title">{t}Video{/t}</span>
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
                            <a href="{url name=backend_author_create}">
                              <i class="fa fa-user"></i>
                              <span class="title">{t}Author{/t}</span>
                            </a>
                          </div>
                        {/acl}
                      {/is_module_activated}
                    </div>
                  </li>
                {/block}
                {block name="master_actions_block"}
                {acl isAllowed="MASTER" hasExtension="CACHE_MANAGER"}
                  <li class="quicklinks">
                    <span class="h-seperate"></span>
                  </li>
                  <li class="quicklinks sysops-actions dropdown">
                    <a href="#" data-toggle="dropdown">
                      <i class="fa fa-rebel text-danger master-user"></i>
                      {t}Sysops{/t}
                    </a>
                    <ul  class="dropdown-menu on-left" role="menu">
                      <li>
                        <a href="{url name=admin_cache_manager}"><i class="fa fa-database"></i>Cache manager</a>
                      </li>
                      <li>
                        <a href="{url name=admin_cache_manager_clearcache}">
                          <i class="fa fa-trash-o fa-lg"></i> <span class="hidden-xs">Remove cache</span>
                        </a>
                      </li>
                      <li>
                        <a href="{url name=admin_cache_manager_clearcompiled}">
                          <i class="fa fa-trash-o fa-lg"></i> <span class="hidden-xs">Remove compiles</span>
                        </a>
                      </li>
                      <li>
                        <a href="{url name=admin_cache_manager_banvarnishcache}">
                          <i class="fa fa-trash-o fa-lg"></i> <span class="hidden-xs">Ban varnish caches</span>
                        </a>
                      </li>
                    </ul>
                  </li>
                {/acl}
                {/block}
              </ul>
            </div>
            <div class="pull-right ">
              <ul class="nav quick-section">
                <li class="quicklinks ng-cloak" ng-if="offline">
                  <a href="#" uib-tooltip-html="'{t escape=off}There is not Internet at the moment,<br> please try to save in a few minutes.{/t}'" tooltip-placement="bottom">
                    <i class="animated flash fa fa-bolt" style="color: #ff0000 !important; animation-duration: .5s"></i>
                  </a>
                </li>
                <li class="quicklinks" ng-if="offline">
                  <span class="h-seperate"></span>
                </li>
                {if is_object($app.user)}
                  <li class="quicklinks dropdown dropdown-notifications" ng-click="markAllAsView()">
                    <a href="#" data-toggle="dropdown">
                      <i class="fa fa-bell"></i>
                      <span class="ng-cloak notifications-orb animated bounceIn" ng-class="{ 'bounceIn': bounce, 'pulse': pulse }" ng-if="notViewed.length > 0">
                        [% notViewed.length %]
                      </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-notifications dropdown-menu-with-footer dropdown-menu-with-title ng-cloak">
                      <div class="dropdown-title clearfix">
                          {t}Notifications{/t}
                      </div>
                      <div class="notification-list-placeholder" ng-class="{ 'no-animate': notifications.length > 0 }" ng-show="!notifications || notifications.length == 0">
                        <span class="fa fa-bell fa-2x"></span>
                        <h5>
                          {t}There are no notifications for now.{/t} <br>
                          {capture name="notifications_url"}{url name=backend_notifications_list}{/capture}
                          {t 1=$smarty.capture.notifications_url escape=off}Check your previous notifications <a href="%1">here</a>.{/t}
                        </h5>
                      </div>
                      <ul class="notification-list">
                        <scrollable>
                          <li class="clearfix notification-list-item" id="notification-[% notification.id %]" ng-class="{ 'notification-list-item-with-icon': notification.style.icon }" ng-repeat="notification in notifications" ng-style="{ 'background-color': notification.style.background_color, 'border-color': notification.style.background_color }">
                            <span class="notification-list-item-close pull-right pointer" ng-click="markAsRead($index)" ng-if="notification.fixed == 0">
                              <i class="fa fa-times" style="color: [% notification.style.font_color %] !important;"></i>
                            </span>
                            <a ng-href="[% routing.ngGenerateShort('backend_notifications_list') %]#[% notification.id %]" target="_self">
                              <div class="notification-icon" ng-if="notification.style.icon" ng-style="{ 'background-color': notification.style.font_color }">
                                <i class="fa fa-[% notification.style.icon %]" style="color: [% notification.style.background_color %] !important;"></i>
                              </div>
                              <div class="notification-body" ng-bind-html="notification.title ? notification.title : notification.body" ng-style="{ 'color': notification.style.font_color }"></div>
                            </a>
                          </li>
                        </scrollable>
                      </ul>
                      <div class="dropdown-footer clearfix">
                        <a href="{url name=backend_notifications_list}">
                          {t}See more{/t}
                        </a>
                      </div>
                    </div>
                  </li>
                  <li class="quicklinks">
                    <span class="h-seperate"></span>
                  </li>
                {/if}
                <li class="quicklinks quick-items help-items dropdown">
                  <a href="#" data-toggle="dropdown">
                    <i class="fa fa-support"></i>
                  </a>
                  <div class="dropdown-menu dropdown-menu-with-title">
                     <div class="dropdown-title">{t}Help center{/t}</div>
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
                    {if $app.security->hasPermission('MASTER')}
                      <i class="fa fa-rebel pull-left m-r-5"></i>
                    {/if}
                    <i class="fa fa-angle-down"></i>
                    <div class="profile-pic">
                      {gravatar email=$app.user->email image_dir=$_template->getImageDir() image=true size="25"}
                    </div>
                    <span class="title">
                      {$app.user->name}
                    </span>
                  </span>
                  <ul class="dropdown-menu dropdown-menu-auto dropdown-menu-right no-padding" role="menu">
                    <li>
                      <a href="/" target="_blank">
                        <i class="fa fa-globe"></i>
                        {t}Go to newspaper{/t}
                      </a>
                    </li>
                    <li>
                      <a href="{url name=admin_getting_started}">
                        <i class="fa fa-rocket"></i>
                        {t}Getting started{/t}
                      </a>
                    </li>
                    <li class="divider"></li>
                    <li>
                      {if $app.security->hasPermission('MASTER')}
                        <a ng-href="{get_parameter name=manager_url}manager#/users/{$app.user->id}" target="_blank">
                          <i class="fa fa-user"></i>
                          {t}Profile{/t}
                        </a>
                      {else}
                        {acl isAllowed="USER_EDIT_OWN_PROFILE"}
                        <a href="{url name=backend_user_show id=$app->getUser()->id}">
                          <i class="fa fa-user"></i>
                          {t}Profile{/t}
                        </a>
                        {/acl}
                      {/if}
                    </li>
                    <li class="divider"></li>
                    <li>
                      <a href="#" ng-click="toggleHelp()">
                        <i class="fa" ng-class="{ 'fa-toggle-on': isHelpEnabled(), 'fa-toggle-off': !isHelpEnabled() }"></i>
                        {t}Show help{/t}
                      </a>
                    </li>
                    <li class="divider"></li>
                    <li>
                      <a role="menuitem" tabindex="-1" href="{url name=core_authentication_logout}">
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
    {/block}
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
            <span class="ng-cloak notifications-orb animated bounceIn" ng-class="{ 'no-animate': !sidebar.isCollapsed(), 'bounceIn': bounce, 'pulse': pulse }" ng-show="sidebar.isCollapsed() && notifications.length > 0">
              [% notifications.length %]
            </span>
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
      <!-- @Common/components/modernizr/modernizr.js, -->
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
      @Common/src/photo-editor/js/photo-editor.js,
      @Common/src/sidebar/js/sidebar.js" filters="uglifyjs" output="common"}
    {/javascripts}
    {javascripts src="
      @Common/components/angular-bootstrap-multiselect/angular-bootstrap-multiselect.js,
      @Common/components/angular-file-upload/dist/angular-file-upload.min.js,
      @Common/components/angular-file-model/angular-file-model.js,
      @Common/components/angular-ui-sortable/sortable.min.js,
      @Common/components/angular-ui-tree/dist/angular-ui-tree.min.js,
      @Common/components/angular-ui-tab-scroll/angular-ui-tab-scroll.js,
      @Common/components/angulartics/dist/angulartics-ga.min.js,
      @Common/components/angulartics/dist/angulartics.min.js,
      @Common/components/bootstrap-tabdrop/build/js/bootstrap-tabdrop.min.js,
      @Common/components/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js,
      @Common/components/jasny-bootstrap/dist/js/jasny-bootstrap.min.js,
      @Common/components/jquery-ui/jquery-ui.min.js,
      @Common/components/jquery-validation/dist/jquery.validate.js,
      @Common/components/jqueryui-touch-punch/jquery.ui.touch-punch.min.js,
      @Common/components/tinycon-angularjs/dist/angular-tinycon.min.js,
      @Common/js/jquery/jquery.multiselect.js,
      @Common/js/jquery/localization/messages_es.js,
      @Common/js/onm/jquery.password-strength.js,
      @Common/src/fablock/fablock.js,
      @Common/src/angular-autoform/js/*,
      @Common/src/angular-bootstrap-multiselect/template.js,
      @Common/src/angular-fly-to-cart/js/fly-to-cart.js,
      @Common/src/angular-image-preview/js/image-preview.js,
      @Common/src/angular-localize/localize.js,
      @Common/src/angular-oql-encoder/oql-encoder.js,
      @Common/src/angular-picker/js/picker.js,
      @Common/src/angular-picker/js/content-picker.js,
      @Common/src/angular-picker/js/media-picker.js,
      @Common/src/angular-tag/js/onm-tag.js,
      @Common/src/angular-query-manager/query-manager.js,
      @Common/src/angular-renderer/renderer.js,
      @Common/src/angular-repeat-finish/repeat-finish.js,
      @Common/src/angular-scroll/angular-scroll.js,
      @Common/src/angular-translator/js/translator.js,
      @Common/src/photo-editor/js/photo-editor.js,
      @AdminTheme/js/jquery/jquery_simplecolorpicker/jquery.simplecolorpicker.js,
      @AdminTheme/js/app.js,
      @AdminTheme/js/config.js,
      @AdminTheme/js/controllers/*,
      @AdminTheme/js/directives/*,
      @AdminTheme/js/filters/*,
      @AdminTheme/js/interceptors/*,
      @AdminTheme/js/services/*,
      @AdminTheme/js/admin.js" filters="uglifyjs" output="admin"}
    {/javascripts}
  {/block}
  {block name="footer-js"}{/block}
  {browser_update}
  {uservoice_widget}
</body>
