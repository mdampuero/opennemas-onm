<!doctype html>
<!--[if lt IE 8]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="{$smarty.const.CURRENT_LANGUAGE|default:"en"}"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang="{$smarty.const.CURRENT_LANGUAGE|default:"en"}"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang="{$smarty.const.CURRENT_LANGUAGE|default:"en"}"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="{$smarty.const.CURRENT_LANGUAGE_SHORT|default:"en"}"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <meta name="author" content="Openhost, S.L.">
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
    <title>[Admin] {setting name="site_name"}{block name="metaTitle"}{/block}</title>
  {/block}
  <link href="//fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet" type="text/css">
  <link href="/assets/components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
  {block name="header-css"}
    {stylesheets src="@Common/components/bootstrap/dist/css/bootstrap.min.css,
      @Common/components/angular-bootstrap-colorpicker/css/colorpicker.min.css,
      @Common/components/angular-loading-bar/build/loading-bar.min.css,
      @Common/components/ui-select/dist/select.min.css,
      @Common/components/angular-ui-tab-scroll/angular-ui-tab-scroll.css,
      @Common/components/animate.css/animate.min.css,
      @Common/components/chart.js/dist/Chart.min.css,
      @Common/components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css,
      @Common/components/messenger/build/css/messenger-theme-flat.css,
      @Common/components/messenger/build/css/messenger.css,
      @Common/components/nanoscroller/bin/css/nanoscroller.css,
      @Common/components/ng-tags-input/build/ng-tags-input.min.css,
      @Common/components/select2/select2.css,
      @Common/src/webarch/css/style.css,
      @Common/src/webarch/css/responsive.css,
      @Common/src/webarch/css/custom-icon-set.css,
      @Common/src/webarch/css/magic_space.css,
      @Common/src/angular-dynamic-image/less/main.less,
      @Common/src/angular-onm-ui/less/main.less,
      @Common/src/sidebar/less/main.less,
      @Common/src/opennemas-webarch/less/main.less" filters="cssrewrite,less" output="common"}
    {/stylesheets}
    {stylesheets src="@Common/components/angular-ui-bootstrap/dist/ui-bootstrap-csp.css,
      @Common/components/angular-ui-tree/dist/angular-ui-tree.min.css,
      @Common/components/jquery-ui-dist/jquery-ui.min.css,
      @Common/components/jquery-ui-dist/jquery-ui.theme.min.css,
      @Common/src/angular-fly-to-cart/less/main.less,
      @Common/src/angular-picker/less/main.less,
      @Common/src/photo-editor/css/photo-editor.css,
      @AdminTheme/less/*" filters="cssrewrite,less" output="admin"}
    {/stylesheets}
  {/block}
  {block name="header-js"}
    <script>
      var appVersion = '{$smarty.const.DEPLOYED_AT}';
      var instanceMedia = '{$smarty.const.INSTANCE_MEDIA}';
      var instanceFolder = '{if $app.instance}{$app.instance->getSubdirectory()}{/if}';
      var CKEDITOR_BASEPATH = '/assets/components/ckeditor4/';
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

      var strings = {
        pagination: {
          of: '{t}of{/t}'
        },
        tags: {
          clear: '{t}Clear{/t}',
          generate: '{t}Generate{/t}',
          newItem: '{t}New tag{/t}',
        },
        forms: {
          'not_valid' : '{t}The form has some missing or invalid fields, please review it.{/t}',
          'not_locale' : '{t}You must have at least 1 languages ​​set for the frontend{/t}',
        }
      };
    </script>
  {/block}
</head>
<body ng-app="BackendApp" ng-controller="MasterCtrl" resizable ng-class="{ 'collapsed': sidebar.isCollapsed(), 'pinned': sidebar.isPinned() }" class="server-sidebar{if $smarty.session && array_key_exists('_sf2_attributes', $smarty.session) && $smarty.session._sf2_attributes.sidebar_pinned === false} unpinned-on-server{/if}" ng-init="init('{$smarty.const.CURRENT_LANGUAGE|default:"en"}', '{t}Any{/t}')" >
  {block name="body"}
    <div class="overlay"></div>
    {block name="page_container"}
    {render_messages}
      <div class="page-container row-fluid">
        <!-- BEGIN PAGE CONTAINER-->
        <div class="page-content">
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
    {javascripts src="
      @Common/components/jquery/dist/jquery.js,
      @Common/components/bootstrap/dist/js/bootstrap.min.js,
      @Common/components/moment/min/moment-with-locales.min.js,
      @Common/components/moment-timezone/builds/moment-timezone-with-data.min.js,
      @Common/components/angular/angular.min.js,
      @Common/components/chart.js/dist/Chart.min.js,
      @Common/components/ckeditor4/ckeditor.js,
      @Common/components/ckeditor4/config.js,
      @Common/components/ckeditor4/lang/en.js,
      @Common/components/ckeditor4/styles.js,
      @Common/components/ckeditor4/plugins/autogrow/plugin.js,
      @Common/components/ckeditor4/plugins/autolink/plugin.js,
      @Common/components/ckeditor4/plugins/justify/plugin.js,
      @Common/components/ckeditor4/plugins/font/plugin.js,
      @Common/components/ckeditor4/plugins/font/lang/en.js,
      @Common/components/ckeditor4/plugins/font/lang/es.js,
      @Common/src/ckeditor-autokeywords/plugin.js,
      @Common/src/ckeditor-autokeywords/lang/en.js,
      @Common/src/ckeditor-autokeywords/lang/es.js,
      @Common/src/ckeditor-autonofollow/plugin.js,
      @Common/src/ckeditor-autonofollow/lang/en.js,
      @Common/src/ckeditor-autonofollow/lang/es.js,
      @Common/src/ckeditor-wordcount/wordcount/plugin.js,
      @Common/src/ckeditor-wordcount/wordcount/lang/en.js,
      @Common/src/ckeditor-wordcount/wordcount/lang/es.js,
      @Common/src/ckeditor-pastespecial/plugin.js,
      @Common/components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js,
      @Common/components/lodash/lodash.min.js,
      @Common/components/messenger/build/js/messenger.min.js,
      @Common/components/messenger/build/js/messenger-theme-flat.js,
      @Common/components/nanoscroller/bin/javascripts/jquery.nanoscroller.js,
      @Common/components/ng-tags-input/build/ng-tags-input.min.js,
      @Common/components/select2/select2.js,
      @Common/components/swfobject/swfobject/swfobject.js,
      @Common/js/silabajs.js,
      @Common/components/angular-animate/angular-animate.min.js,
      @Common/components/angular-bootstrap-colorpicker/js/bootstrap-colorpicker-module.min.js,
      @Common/components/angular-ui-bootstrap/dist/ui-bootstrap-tpls.js,
      @Common/components/angular-chart.js/dist/angular-chart.min.js,
      @Common/components/checklist-model/checklist-model.js,
      @Common/components/angular-loading-bar/build/loading-bar.min.js,
      @Common/components/angular-nanoscroller/scrollable.js,
      @Common/components/angular-recaptcha/release/angular-recaptcha.min.js,
      @Common/components/angular-route/angular-route.min.js,
      @Common/components/angular-sanitize/angular-sanitize.min.js,
      @Common/components/angular-swfobject/angular-swfobject.js,
      @Common/components/angular-touch/angular-touch.min.js,
      @Common/components/angular-translate/dist/angular-translate.min.js,
      @Common/components/ui-select/dist/select.min.js,
      @Common/components/angular-webstorage/angular-webstorage.min.js,
      @Common/src/angular-messenger/messenger.js,
      @Common/src/angular-moment/moment.js,
      @FosJsRoutingBundle/js/router.js,
      @Common/js/routes.js,
      @Common/src/angular-cleaner/cleaner.js,
      @Common/src/angular-datetimepicker/datetimepicker.js,
      @Common/src/angular-dynamic-image/js/dynamic-image.js,
      @Common/src/angular-gravatar/gravatar.js,
      @Common/src/angular-history/history.js,
      @Common/src/angular-http/http.js,
      @Common/src/angular-item-service/itemService.js,
      @Common/src/angular-localize/localize.js,
      @Common/src/angular-onm-ui/js/*,
      @Common/src/angular-oql/oql.js,
      @Common/src/angular-resizable/resizable.js,
      @Common/src/angular-routing/routing.js,
      @Common/src/angular-security/security.js,
      @Common/src/angular-serializer/serializer.js,
      @Common/src/md5/md5.min.js,
      @Common/src/opennemas-webarch/js/core.js,
      @Common/src/sidebar/js/sidebar.js" filters="uglifyjs" output="common"}
    {/javascripts}
    {javascripts src="
      @Common/components/angular-file-model/angular-file-model.js,
      @Common/components/angular-file-upload/dist/angular-file-upload.min.js,
      @Common/components/angular-filter/dist/angular-filter.min.js,
      @Common/components/angular-ui-tab-scroll/angular-ui-tab-scroll.js,
      @Common/components/angular-ui-tree/dist/angular-ui-tree.js,
      @Common/components/angulartics/dist/angulartics-ga.min.js,
      @Common/components/angulartics/dist/angulartics.min.js,
      @Common/components/jquery-ui-dist/jquery-ui.min.js,
      @Common/components/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js,
      @Common/components/pdfjs-dist/build/pdf.js,
      @Common/components/pdfjs-dist/build/pdf.worker.js,
      @Common/components/angular-tinycon/dist/angular-tinycon.min.js,
      @Common/src/fablock/fablock.js,
      @Common/src/angular-autoform/js/*,
      @Common/src/angular-fly-to-cart/js/fly-to-cart.js,
      @Common/src/angular-image-preview/js/image-preview.js,
      @Common/src/angular-picker/js/picker.js,
      @Common/src/angular-picker/js/content-picker.js,
      @Common/src/angular-picker/js/media-picker.js,
      @Common/src/angular-query-manager/query-manager.js,
      @Common/src/angular-renderer/renderer.js,
      @Common/src/angular-repeat-finish/repeat-finish.js,
      @Common/src/angular-scroll/angular-scroll.js,
      @Common/src/angular-translator/js/translator.js,
      @Common/src/photo-editor/js/photo-editor.js,
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
  {uservoice_widget}
</body>
