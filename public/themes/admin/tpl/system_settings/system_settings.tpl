{extends file="base/admin.tpl"}

{block name="content"}
  <form name="settingForm" ng-controller="SettingsCtrl" ng-init="list()" class="settings">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-cogs fa-lg"></i>
                {t}Settings{/t}
              </h4>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <button class="btn btn-loading btn-primary" ng-click="save()" ng-disabled="settingForm.$invalid" type="button">
                  <i class="fa fa-save" ng-class="{ 'fa-circle-o-notch fa-spin': saving}"></i>
                  <span class="text">{t}Save{/t}</span>
                </button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="content ng-cloak no-animate" ng-if="loading">
      <div class="spinner-wrapper">
        <div class="loading-spinner"></div>
        <div class="spinner-text">{t}Loading{/t}...</div>
      </div>
    </div>
    <div class="content ng-cloak" ng-if="!loading">
      <div class="grid simple settings">
        <div class="grid-body no-padding ng-cloak">
          <uib-tabset>
            <uib-tab>
              <uib-tab-heading>
                <i class="fa fa-cog"></i>
                {t}General{/t} & SEO
              </uib-tab-heading>
              <div class="tab-wrapper">
                <div class="row">
                  <div class="col-md-6">
                    <div class="p-r-15">
                      <h4>
                        <i class="fa fa-cog m-r-5"></i>
                        {t}General{/t}
                      </h4>
                      <div class="form-group">
                        <label class="form-label" for="site-name">
                          {t}Site name{/t}
                        </label>
                        <span class="help">
                          {t}This will be displayed as your site name.{/t}
                        </span>
                        <div class="controls">
                          <input class="form-control" id="site-name" name="site-name" ng-model="settings.site_name" type="text">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="site-agency">
                          {t}Site agency{/t}
                        </label>
                        <span class="help">
                          {t}This will be displayed as the default article signature.{/t}
                        </span>
                        <div class="controls">
                          <input class="form-control"  id="site-agency" name="site-agency" ng-model="settings.site_agency" type="text">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="site-footer">
                          {t}Footer text{/t}
                        </label>
                        <span class="help">
                          {t}Text showed at the bottom of your page. Usually used for copyright notice.{/t}
                        </span>
                        <div class="controls">
                          <textarea class="form-control" id="site_footer" name="site-footer" ng-model="settings.site_footer" onm-editor onm-editor-preset="simple"></textarea>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="p-l-15">
                      <h4>
                        <i class="fa fa-line-chart"></i>
                        {t}SEO options{/t}
                      </h4>
                      <div class="form-group">
                        <label class="form-label" for="site-title">
                          {t}Site title{/t}
                        </label>
                        <div class="controls">
                          <input class="form-control" id="site-title" name="site-title" ng-model="settings.site_title" type="text">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="site-keywords">
                          {t}Site keywords{/t}
                        </label>
                        <div class="controls">
                          <input class="form-control" id="site-keywords" name="site-keywords" ng-model="settings.site_keywords" type="text">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="site-description">
                          {t}Site description{/t}
                        </label>
                        <div class="controls">
                          <input class="form-control" id="site-description" name="site-description" ng-model="settings.site_description" type="text">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="refresh-interval">
                          {t}Refresh page interval{/t}
                          <small>({t}seconds{/t})</small>
                        </label>
                        <span class="help">
                          {t}When a user visits pages and stay on it for a while, this setting allows to refresh the loaded page for updated it.{/t}
                        </span>
                        <div class="controls">
                          <input class="form-control" id="refresh-interval" name="refresh-interval" ng-model="settings.refresh_interval" type="number">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="webmaster-tools-google">
                          {t}Google Web Master Tools{/t}
                        </label>
                        <div class="controls">
                          <input class="form-control" id="webmaster-tools-google" name="webmaster-tools-google" ng-model="settings.webmastertools_google" type="text">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="webmastertools-bing">
                          {t}Bing Web Master Tools{/t}
                        </label>
                        <div class="controls">
                          <input class="form-control"  id="webmastertools-bing" name="webmastertools-bing" ng-model="settings.webmastertools_bing" type="text">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </uib-tab>
            <uib-tab>
              <uib-tab-heading>
                <i class="fa fa-magic m-r-5"></i>
                {t}Appearance{/t}
              </uib-tab-heading>
              <div class="tab-wrapper">
                <div class="row">
                  <div class="col-md-6">
                    <div class="p-r-15">
                      <div class="row">
                        <div class="col-md-12">
                          <h4>
                            <i class="fa fa-paint-brush"></i>
                            {t}Colors{/t}
                          </h4>
                          <div class="form-group col-md-10">
                            <label class="form-label" for="site-color">
                              {t}Site color{/t}
                            </label>
                            <span class="help">
                              {t}Color used for links, menus and some widgets.{/t}
                            </span>
                            <div class="controls">
                              <div class="input-group">
                                <span class="input-group-addon" ng-style="{ 'background-color': settings.site_color }">
                                  &nbsp;&nbsp;&nbsp;&nbsp;
                                </span>
                                <input class="form-control" colorpicker="hex" id="site-color" name="site-color" ng-model="settings.site_color" type="text">
                                <div class="input-group-btn">
                                  <button class="btn btn-default" ng-click="settings.site_color = backup.site_color" type="button">{t}Reset{/t}</button>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="form-group col-md-10">
                            <label class="form-label" for="site-color-secondary">
                              {t}Site secondary color{/t}
                            </label>
                            <span class="help">
                              {t}Color used for custom elements.{/t}
                            </span>
                            <div class="controls">
                              <div class="input-group">
                                <span class="input-group-addon" ng-style="{ 'background-color': settings.site_color_secondary }">
                                  &nbsp;&nbsp;&nbsp;&nbsp;
                                </span>
                                <input class="form-control" colorpicker="hex" id="site-color-secondary" name="site-color-secondary" ng-model="settings.site_color_secondary" type="text">
                                <div class="input-group-btn">
                                  <button class="btn btn-default" ng-click="settings.site_color_secondary = backup.site_color_secondary" type="button">{t}Reset{/t}</button>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-12">
                          <h4>
                            <i class="fa fa-picture-o"></i>
                            {t}Logo{/t}
                          </h4>
                          <div class="form-group">
                            <div class="checkbox">
                              <input class="form-control" id="logo-enabled" name="logo-enabled" ng-false-value="0" ng-model="settings.logo_enabled" ng-true-value="1"  type="checkbox"/>
                              <label class="form-label" for="logo-enabled">
                                {t}Use custom logo{/t}
                              </label>
                            </div>
                          </div>
                        </div>
                        <div class="form-group col-md-12" ng-show="settings.logo_enabled">
                          <label class="form-label" for="site-logo">{t}Large logo{/t}</label>
                          <div class="controls">
                            <input class="hidden" id="site-logo" name="site-logo" file-model="settings.site_logo" type="file"/>
                            <div class="thumbnail-wrapper">
                              <div class="overlay photo-overlay ng-cloak" ng-class="{ 'open': overlay.site_logo }"></div>
                              <div class="confirm-dialog ng-cloak" ng-class="{ 'open': overlay.site_logo }">
                                <p>{t}Are you sure?{/t}</p>
                                <div class="confirm-actions">
                                  <button class="btn btn-link" ng-click="toggleOverlay('site_logo')" type="button">
                                    <i class="fa fa-times fa-lg"></i>
                                    {t}No{/t}
                                  </button>
                                  <button class="btn btn-link" ng-click="removeFile('site_logo'); toggleOverlay('site_logo')" type="button">
                                    <i class="fa fa-check fa-lg"></i>
                                    {t}Yes{/t}
                                  </button>
                                </div>
                              </div>
                              <label for="site-logo" ng-if="!settings.site_logo">
                                <div class="thumbnail-placeholder">
                                  <div class="img-thumbnail">
                                    <div class="thumbnail-empty">
                                      <i class="fa fa-picture-o fa-3x block"></i>
                                      <h5>{t}Pick an image{/t}</h5>
                                    </div>
                                  </div>
                                </div>
                              </label>
                              <div class="img-thumbnail text-center img-thumbnail-center no-animate" ng-if="settings.site_logo" style="max-width: 100%; height: 100%">
                                <div class="text-center" ng-if="settings.site_logo" ng-preview="settings.site_logo">
                                  <div class="thumbnail-actions ng-cloak" ng-if="settings.site_logo">
                                    <div class="thumbnail-action remove-action" ng-click="toggleOverlay('site_logo')">
                                      <i class="fa fa-trash-o fa-2x"></i>
                                    </div>
                                    <label class="thumbnail-action" for="site-logo">
                                      <i class="fa fa-camera fa-2x"></i>
                                    </label>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="form-group col-md-12" ng-if="settings.logo_enabled">
                          <label class="form-label" for="mobile_logo">{t}Small logo{/t}</label>
                          <div class="controls">
                            <input class="hidden" id="mobile-logo" name="mobile-logo" file-model="settings.mobile_logo" type="file"/>
                            <div class="thumbnail-wrapper">
                              <div class="overlay photo-overlay ng-cloak" ng-class="{ 'open': overlay.mobile_logo }"></div>
                              <div class="confirm-dialog ng-cloak" ng-class="{ 'open': overlay.mobile_logo }">
                                <p>{t}Are you sure?{/t}</p>
                                <div class="confirm-actions">
                                  <button class="btn btn-link" ng-click="toggleOverlay('mobile_logo')" type="button">
                                    <i class="fa fa-times fa-lg"></i>
                                    {t}No{/t}
                                  </button>
                                  <button class="btn btn-link" ng-click="removeFile('mobile_logo'); toggleOverlay('mobile_logo')" type="button">
                                    <i class="fa fa-check fa-lg"></i>
                                    {t}Yes{/t}
                                  </button>
                                </div>
                              </div>
                              <label for="mobile-logo" ng-if="!settings.mobile_logo">
                                <div class="thumbnail-placeholder">
                                  <div class="img-thumbnail">
                                    <div class="thumbnail-empty">
                                      <i class="fa fa-picture-o fa-3x block"></i>
                                      <h5>{t}Pick an image{/t}</h5>
                                    </div>
                                  </div>
                                </div>
                              </label>
                              <div class="img-thumbnail text-center img-thumbnail-center no-animate" ng-if="settings.mobile_logo" style="max-width: 100%; height: 100%">
                                <div class="text-center" ng-if="settings.mobile_logo" ng-preview="settings.mobile_logo">
                                  <div class="thumbnail-actions ng-cloak" ng-if="settings.mobile_logo">
                                    <div class="thumbnail-action remove-action" ng-click="toggleOverlay('mobile_logo')">
                                      <i class="fa fa-trash-o fa-2x"></i>
                                    </div>
                                    <label class="thumbnail-action" for="mobile-logo">
                                      <i class="fa fa-camera fa-2x"></i>
                                    </label>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="form-group col-md-12" ng-if="settings.logo_enabled">
                          <label class="form-label" for="favico">{t}Favico{/t}</label>
                          <div class="controls">
                            <input class="hidden" id="favico" name="favico" file-model="settings.favico" type="file"/>
                            <div class="thumbnail-wrapper">
                              <div class="overlay photo-overlay ng-cloak" ng-class="{ 'open': overlay.favico }"></div>
                              <div class="confirm-dialog ng-cloak" ng-class="{ 'open': overlay.favico }">
                                <p>{t}Are you sure?{/t}</p>
                                <div class="confirm-actions">
                                  <button class="btn btn-link" ng-click="toggleOverlay('favico')" type="button">
                                    <i class="fa fa-times fa-lg"></i>
                                    {t}No{/t}
                                  </button>
                                  <button class="btn btn-link" ng-click="removeFile('favico'); toggleOverlay('favico')" type="button">
                                    <i class="fa fa-check fa-lg"></i>
                                    {t}Yes{/t}
                                  </button>
                                </div>
                              </div>
                              <label for="favico" ng-if="!settings.favico">
                                <div class="thumbnail-placeholder">
                                  <div class="img-thumbnail">
                                    <div class="thumbnail-empty">
                                      <i class="fa fa-picture-o fa-3x block"></i>
                                      <h5>{t}Pick an image{/t}</h5>
                                    </div>
                                  </div>
                                </div>
                              </label>
                              <div class="img-thumbnail text-center img-thumbnail-center no-animate" ng-if="settings.favico" style="max-width: 100%; height: 100%">
                                <div class="text-center" ng-if="settings.favico" ng-preview="settings.favico">
                                  <div class="thumbnail-actions ng-cloak" ng-if="settings.favico">
                                    <div class="thumbnail-action remove-action" ng-click="toggleOverlay('favico')">
                                      <i class="fa fa-trash-o fa-2x"></i>
                                    </div>
                                    <label class="thumbnail-action" for="favico">
                                      <i class="fa fa-camera fa-2x"></i>
                                    </label>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="p-l-15">
                      <h4>
                        <i class="fa fa-eye"></i>
                        {t}Cookies agreement{/t}
                      </h4>
                      <div class="form-group">
                        <label class="form-label" for="cookies-hint-url">
                          {t}Cookie agreement page URL{/t}
                        </label>
                        <div class="controls">
                          <input class="form-control" id="cookies-hint-url" name="cookies-hint-url" ng-model="settings.cookies_hint_url" type="text">
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="checkbox">
                          <input class="form-control" id="cmp-script" name="cmp-script" ng-false-value="'0'" ng-model="settings.cmp_script" ng-true-value="'1'"  type="checkbox"/>
                          <label class="form-label" for="cmp-script">
                            {t}Use Consent Management Platform (CMP){/t}
                          </label>
                        </div>
                      </div>
                      <h4>
                        <i class="fa fa-list"></i>
                        {t}Listing{/t}
                      </h4>
                      <div class="row">
                        <div class="col-md-6 form-group">
                          <label class="form-label" for="items-per-page">
                            {t}Items per page{/t}
                          </label>
                          <div class="controls">
                            <input class="form-control" id="items-per-page" name="items-per-page" ng-model="settings.items_per_page" type="number">
                          </div>
                        </div>
                        {is_module_activated name="FRONTPAGES_LAYOUT"}
                        <div class="col-md-6 form-group">
                          <label class="form-label" for="items-in-blog">
                            {t}Items per blog page{/t}
                          </label>
                          <div class="controls">
                            <input class="form-control" id="items-in-blog" name="items-in-blog" ng-model="settings.items_in_blog" type="number">
                          </div>
                        </div>
                        {/is_module_activated}
                      </div>
                      {if $app.security->hasPermission('MASTER')}
                      <h4>
                        <i class="fa fa-rss"></i>
                        RSS
                      </h4>
                      <div class="form-group">
                        <label class="form-label" for="elements-in-rss">
                          {t}Items in RSS{/t}
                        </label>
                        <div class="controls">
                          <input class="form-control" id="elements-in-rss" name="elements-in-rss" ng-model="settings.elements_in_rss" type="number">
                        </div>
                      </div>
                      {/if}
                    </div>
                  </div>
                </div>
              </div>
            </uib-tab>
            <uib-tab>
              <uib-tab-heading>
                <i class="fa fa-globe"></i>
                {t}Language & time{/t}
              </uib-tab-heading>
              <div class="tab-wrapper">
                <div class="row">
                  <div class="col-md-6">
                    <h4>
                      <i class="fa fa-language m-r-5"></i>
                      {t}Language{/t}
                    </h4>
                    <div class="form-group">
                      <label class="form-label" for="locale-backend">
                        {t}Control Panel Language{/t}
                      </label>
                      <span class="help">
                        {t}Used for messages, interface and units in the control panel.{/t}
                      </span>
                      <div class="controls">
                        <select id="locale-backend" name="locale-backend" ng-model="settings.locale.backend.language.selected" required>
                          <option value="[% code %]" ng-repeat="(code,name) in extra.locales.backend" ng-selected="[% code === settings.locale.backend.language.selected %]">[% name %]</option>
                        </select>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-xs-10">
                        <div class="form-group">
                          <label class="form-label" for="frontend-language">
                            {t}Newspaper Language{/t}
                          </label>
                          <span class="help">
                            {t}Used for messages, interface and units in the newspaper.{/t}
                          </span>
                          <div class="controls">
                            {if !$multilanguage}
                              <select id="locale-frontend" name="locale-frontend" ng-model="settings.locale.frontend.language.selected" required>
                                <option value="">{t}Select a language...{/t}</option>
                                <option value="[% code %]" ng-repeat="(code,name) in extra.locales.backend" ng-selected="[% code === settings.locale.frontend.language.selected %]">[% name %]</option>
                              </select>
                            {/if}
                            {if $multilanguage}
                              <div class="input-group">
                                <span class="input-group-addon">
                                  <i class="fa fa-search" ng-class="{ 'fa-circle-o-notch fa-spin': searching }"></i>
                                </span>
                                <input class="form-control" ng-model="l" placeholder="{t}Search a language{/t}..." type="text" typeahead-on-select="addLocale($item, $model, $label); l = ''" typeahead-wait-ms="250" uib-typeahead="locale.id as locale.name for locale in getLocales($viewValue)">
                              </div>
                            {/if}
                          </div>
                        </div>
                      </div>
                    </div>
                    {is_module_activated name="es.openhost.module.multilanguage"}
                      <div class="form-group m-b-100" ng-show="settings.locale.frontend.language.available.length > 0">
                        <label class="form-label">{t}Main language{/t}</label>
                        <span class="help">
                          <i class="fa fa-circle-info text-info"></i>
                          {t}When no language in the URL, the main language will be used{/t}
                        </span>
                        <div class="row m-b-5" ng-repeat="item in settings.locale.frontend.language.available">
                          <div class="col-xs-12">
                            <div class="row">
                              <div class="col-xs-12">
                                <div class="p-t-10 radio">
                                  <input id="radio-[% $index %]" ng-model="settings.locale.frontend.language.selected" type="radio" value="[% item.code %]">
                                  <label for="radio-[% $index %]">
                                    [% item.name %] ([% item.code %])
                                    <strong ng-show="settings.locale.frontend.language.selected == item.code">({t}Main{/t})</strong>
                                  </label>
                                </div>
                              </div>
                            </div>
                            <div class="row m-b-10">
                              <div class="col-xs-10">
                                <div class="form-group" ng-class="{ 'has-error': settingForm['slug-' + $index].$invalid }">
                                  <input class="form-control" name="slug-[% $index %]" ng-maxlength="2" ng-minlength="2" ng-model="settings.locale.frontend.language.slug[item.code]" placeholder="{t}Customize the language appears in the URL (e.g. en).{/t}" required type="text">
                                  <div class="absolute help" ng-if="settingForm['slug-' + $index].$valid">
                                    <i class="fa fa-info-circle text-info m-l-5 m-r-5"></i>
                                    {t 1="[% settings.locale.frontend.language.slug[item.code] %]"}URLs will look like http://newspaper.opennemas.com/%1/<slug>{/t}
                                  </div>
                                  <div class="absolute help" ng-if="!settingForm['slug-' + $index].$valid">
                                    <i class="fa fa-exclamation-circle text-danger m-l-5 m-r-5"></i>
                                    <span class="no-animate text-danger">{t}Locale needs 2 characters{/t}</span>
                                  </div>
                                </div>
                              </div>
                              <div class="col-xs-2">
                                <button class="btn btn-white" ng-click="removeLocale($index)" type="button">
                                  <i class="fa fa-times text-danger"></i>
                                </button>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    {/is_module_activated}
                  </div>
                  <div class="col-md-6">
                    <h4>
                      <i class="fa fa-map-marker m-r-5"></i>
                      {t}Location{/t}
                    </h4>
                    <div class="form-group">
                      <label class="form-label" for="country">{t}Country{/t}</label>
                      <div class="controls">
                        <select id="country" name="country" ng-model="instance.country" required>
                          <option value="">{t}Select a country{/t}...</option>
                          <option value="[% code %]" ng-repeat="(code,name) in extra.countries" ng-selected="[% code === instance.country %]">[% name %]</option>
                        </select>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="timezone">
                        {t}Time Zone{/t}
                      </label>
                      <span class="help">
                        {t}Used for all the dates used in your webpage.{/t}
                      </span>
                      <div class="controls">
                        <select name="timezone" ng-change="settings.locale.frontend.timezone = settings.locale.backend.timezone" ng-model="settings.locale.backend.timezone" required>
                          <option value="">{t}Select a timezone...{/t}</option>
                          <option value="[% timezone %]" ng-repeat="timezone in extra.timezones" ng-selected="[% timezone === settings.locale.backend.timezone %]">[% timezone %]</option>
                        </select>
                      </div>
                    </div>
                    {is_module_activated name="es.openhost.module.translation"}
                      <h4>
                        <i class="fa fa-globe m-r-5"></i>
                        {t}Automatic translations{/t}
                      </h4>
                      <div class="form-group">
                        <label class="form-label" for="frontend-language">
                          {t}Services for automatic translations{/t}
                        </label>
                        <span class="help">
                          {t}Services to translate your contents to a especific language.{/t}
                        </span>
                        <div ng-repeat="code in settings.translators track by $index">
                          <div class="row">
                            <div class="col-xs-12">
                              <h4 class="pull-left">
                              [% (settings.translators[$index].from != undefined) ? extra.locales.frontend[settings.translators[$index].from] : '{t}Not defined{/t}' %]
                              &rarr;
                              [% (settings.translators[$index].to != undefined) ? extra.locales.frontend[settings.translators[$index].to] : '{t}Not defined{/t}' %]
                              <strong class="small" ng-show="settings.translators[$index].default == true">({t}Default{/t})</strong>
                              </h4>

                              <button class="btn btn-white pull-right" ng-click="removeTranslator($index)" type="button">
                                <i class="fa fa-times text-danger"></i>
                              </button>
                              <hr>
                            </div>
                          </div>
                          <div class="row" >
                            <div class="col-sm-6 col-md-6">
                              <div class="form-group">
                                <label class="form-label">
                                  {t}Translate from{/t}
                                </label>
                                <div class="controls">
                                  <select class="form-control" ng-model="settings.translators[$index].from" required>
                                    <option value="">{t}Select from language{/t}</option>
                                    <option value="[% item.code %]" ng-repeat="item in settings.locale.frontend.language.available">[% item.name %]</option>
                                  </select>
                                </div>
                              </div>
                            </div>
                            <div class="col-sm-6 col-md-6">
                              <div class="form-group">
                                <label class="form-label">
                                  {t}Translate to{/t}
                                </label>
                                <div class="controls">
                                  <select class="form-control" ng-model="settings.translators[$index].to" ng-disabled="!settings.translators[$index].from" required>
                                    <option value="">{t}Select to language{/t}</option>
                                    <option value="[% item.code %]" ng-repeat="item in filterFromLanguages($index)">[% item.name %]</option>
                                  </select>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="row" ng-show="settings.translators[$index].translator && getParameters($index).length > 0">
                            <div class="col-md-4">
                              <div class="form-group">
                                <label class="form-label">
                                  {t}Required params for Service{/t}
                                </label>
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-sm-4">
                              <div class="form-group">
                                <label class="form-label">
                                  {t}Service{/t}
                                </label>
                                <div class="controls">
                                  <select class="form-control" ng-model="settings.translators[$index].translator" required>
                                    <option value="">{t}Select a service...{/t}</option>
                                    <option value="[% service.translator %]" ng-repeat="service in extra.translation_services">[% service.translator %]</option>
                                  </select>
                                </div>
                              </div>
                            </div>
                            <div class="col-sm-4" ng-repeat="(translationParam, text) in getParameters($index)" ng-show="settings.translators[$parent.$index].translator">
                              <div class="form-group">
                                <label class="form-label">
                                  [% text %]
                                </label>
                                <div class="controls">
                                  <input class="form-control" ng-model="settings.translators[$parent.$index].config[translationParam]" type="text">
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="row" ng-show="settings.translators[$index].from.length > 0 && settings.translators[$index].to.length > 0">
                            <div class="p-t-10 radio">
                              <input id="translator-default-[% $index %]" ng-model="settings.translators[$index].default" ng-value="true" type="radio" ng-click="toggleDefaultTranslator($index)">
                              <label for="translator-default-[% $index %]">
                                {t 1="[% settings.translators[\$index].from %]-[% settings.translators[\$index].to %]"}Default translator for "%1"{/t}
                              </label>
                            </div>
                          </div>
                        </div>
                        <div class="row p-t-30 text-center">
                          <div class="col-xs-12" ng-show="settings.locale.frontend && settings.locale.frontend.language.available.length > 1" type="button">
                            <button class="btn btn-block btn-white" ng-click="addTranslator()">
                              <i class="fa fa-plus"></i>
                              {t}Add new translator{/t}
                            </button>
                          </div>
                          <div class="col-md-12" ng-show="settings.locale.frontend && settings.locale.frontend.language.available.length == 1">
                            <h5>{t}Must have at least 2 languages to configure the translation system{/t}</h5>
                          </div>
                        </div>
                      </div>
                    {/is_module_activated}
                  </div>
                </div>
              </div>
            </uib-tab>
            <uib-tab>
              <uib-tab-heading>
                <i class="fa fa-cube m-r-5"></i>
                {t}Internal{/t}
              </uib-tab-heading>
              <div class="tab-wrapper">
                <div class="row">
                  <div class="col-md-6">
                    <h4>
                      <i class="fa fa-microphone"></i>
                      {t}Opennemas News Agency{/t}
                    </h4>
                    <div class="row">
                      <div class="col-md-6 form-group">
                        <label class="form-label" for="onm-digest-user">
                          {t}User{/t}
                        </label>
                        <div class="controls">
                          <div class="input-group">
                            <span class="input-group-addon">
                              <i class="fa fa-user"></i>
                            </span>
                            <input class="form-control" id="onm-digest-user" name="onm-digest-user" ng-model="settings.onm_digest_user" type="text">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6 form-group">
                        <label class="form-label" for="onm-digest-pass">
                          {t}Password{/t}
                        </label>
                        <div class="controls">
                          <div class="input-group">
                            <span class="input-group-btn">
                              <button class="btn btn-default" ng-click="onm_digest_pass_visible = !onm_digest_pass_visible" type="button">
                                <i class="fa fa-lock" ng-class="{ 'fa-unlock-alt': onm_digest_pass_visible }"></i>
                              </button>
                            </span>
                            <input class="form-control" id="onm-digest-pass" name="onm-digest-pass" ng-model="settings.onm_digest_pass" type="[% onm_digest_pass_visible ? 'text' : 'password' %]">
                          </div>
                        </div>
                      </div>
                    </div>
                    {is_module_activated name="FORM_MANAGER"}
                    <h4>
                      <i class="fa fa-file-text-o"></i>
                      {t}Form Module{/t}
                    </h4>
                    <div class="row">
                      <div class="col-md-12">
                        <div class="form-group">
                          <label class="form-label" for="contact_email">
                            {t}Contact email{/t}
                          </label>
                          <div class="controls">
                            <input class="form-control" id="contact_email" name="contact_email" ng-model="settings.contact_email" type="text">
                          </div>
                        </div>
                      </div>
                    </div>
                    {/is_module_activated}
                    {is_module_activated name="es.openhost.module.advancedAdvertisement"}
                    <h4>{t}RTB Media Integration{/t}</h4>
                    <div class="form-group">
                      <label class="form-label" for="rtbFiles">{t}RTB Files{/t}</label>
                      <span class="help">
                        {t}Search files using the input below and click "Add" to integrate them as RTB Ad files{/t}
                      </span>
                      <div class="controls">
                        <div class="form-group ng-cloak">
                          <div class="input-group" >
                            <span class="input-group-addon">
                              <i class="fa fa-search" ng-class="{ 'fa-circle-o-notch fa-spin': searching }"></i>
                            </span>
                            <input class="form-control" autocomplete="off" placeholder="{t}Search by name{/t}" ng-model="rtb" typeahead-min-length="3" typeahead-on-select="addRTBFile($item, $model, $label); rtb = ''" uib-typeahead="file.id as file.filename for file in getFiles($viewValue)" type="text">
                          </div>
                        </div>
                        <div class="form-group m-b-16 no-animate" ng-repeat="file in settings.rtb_files">
                          <label class="form-label">/ftlocal/[% file.filename %]</label>
                          <div class="input-group">
                            <input class="form-control" ng-model="file.filename" readonly type="text">
                            <span class="input-group-btn">
                              <button class="btn btn-danger" ng-click="removeRTBFile($index)">
                                <i class="fa fa-trash-o"></i>
                              </button>
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                    {/is_module_activated}
                  </div>
                </div>
              </div>
            </uib-tab>
            <uib-tab>
              <uib-tab-heading>
                <i class="fa fa-cloud m-r-5"></i>
                {t}External services{/t}
              </uib-tab-heading>
              <div class="tab-wrapper">
                <div class="row">
                  <div class="col-md-6">
                    <h4>
                      <i class="fa fa-pie-chart"></i>
                      {t}Analytic system integration{/t}
                    </h4>
                    <div class="panel-group" id="panel-group-google-analytics" data-toggle="collapse">
                      <div class="panel panel-default">
                        <div class="panel-heading collapsed">
                          <h4 class="panel-title">
                            <a class="collapsed" data-parent="#accordion-google-analytics" data-toggle="collapse" href="#goggle-analytics">
                              <i class="fa fa-google"></i>{t}Google Analytics{/t}
                            </a>
                          </h4>
                        </div>
                        <div class="panel-collapse collapse" id="goggle-analytics">
                          <div class="panel-body">
                            <div ng-repeat="code in settings.google_analytics track by $index">
                              <div class="row" ng-model="settings.google_analytics[$index]">
                                <div class="col-md-6">
                                  <div class="form-group">
                                    <label class="form-label">
                                      {t}Google Analytics API key{/t}
                                    </label>
                                    <div class="controls">
                                      <input class="form-control" id="google-analytics-[% $index %]-api-key" name="google-analytics-[% $index %]-api-key" ng-model="code.api_key" type="text">
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group">
                                    <label class="form-label">
                                      {t}Google Analytics Base domain{/t}
                                    </label>
                                    <div class="controls">
                                      <input class="form-control" id="google_analytics-[% $index %]-base_domain" name="google_analytics-[% $index %]-base-domain" ng-model="code.base_domain" type="text">
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="row" ng-show="{if $app.security->hasPermission('MASTER')}true{/if}">
                                <div class="col-md-6">
                                  <div class="form-group">
                                    <label class="form-label">
                                      {t}Category targeting{/t}
                                    </label>
                                    <div class="row">
                                      <div class="col-md-4">
                                        <div class="help">
                                          {t}Index{/t}
                                        </div>
                                        <div class="controls">
                                          <input class="form-control" name="google_analytics[% $index %]-category-index" type="text" ng-model="code.category.index">
                                        </div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="help">
                                          {t}Key{/t}
                                        </div>
                                        <div class="controls">
                                          <input class="form-control" name="google_analytics[% $index %]-category-key" type="text" ng-model="code.category.key">
                                        </div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="help">
                                          {t}Scope{/t}
                                        </div>
                                        <div class="controls">
                                          <input class="form-control" name="google_analytics[% $index %]-category-scope" type="text" ng-model="code.category.scope">
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group">
                                    <label class="form-label">
                                      {t}Extension targeting{/t}
                                    </label>
                                    <div class="row">
                                      <div class="col-md-4">
                                        <div class="help">
                                          {t}Index{/t}
                                        </div>
                                        <div class="controls">
                                          <input class="form-control" name="google_analytics[% $index %]-extension-index" type="text" ng-model="code.extension.index">
                                        </div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="help">
                                          {t}Key{/t}
                                        </div>
                                        <div class="controls">
                                          <input class="form-control" name="google_analytics[% $index %]-extension-key" type="text" ng-model="code.extension.key">
                                        </div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="help">
                                          {t}Scope{/t}
                                        </div>
                                        <div class="controls">
                                          <input class="form-control" name="google_analytics[% index %]-extension-scope" type="text" ng-model="code.extension.scp">
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="row" ng-show="{if $app.security->hasPermission('MASTER')}true{/if}">
                                <div class="col-md-12">
                                  <div class="form-group">
                                    <label class="form-label">
                                      {t}Google Analytics Custom variables{/t}
                                    </label>
                                    <div class="controls">
                                      <textarea class="form-control" name="google_analytics[[% $index %]][custom_var]" type="text" class="input-xlarge" ng-model="code.custom_var" value="[% code.custom_var %]"></textarea>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-xs-12 col-sm-6 col-sm-offset-3 m-b-30" ng-if="settings.google_analytics.length > 1">
                                  <button class="btn btn-block btn-danger" ng-click="removeGanalytics($index)" type="button">
                                    <i class="fa fa-trash-o"></i>
                                    {t}Delete{/t}
                                  </button>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-xs-12 col-sm-6 col-sm-offset-3 form-group text-center" ng-show="settings.google_analytics.length > 0 && settings.google_analytics[0].api_key">
                                <button class="btn btn-block btn-white" ng-click="addGanalytics()" type="button">
                                  <i class="fa fa-plus"></i>
                                  {t}Add{/t}
                                </button>
                              </div>
                            </div>
                            <p>{t escape=off}You can get your Google Analytics Site ID from <a class="external-link" href="https://www.google.com/analytics/" target="_blank" ng-click="$event.stopPropagation();">GAnalytics site</a> under the General Overview list (should be something like UA-546457-3).{/t}</p>
                            <small class="help">
                              <i class="fa fa-info-circle m-r-5 text-info"></i>
                              {t}We are not responsible of the stats or of any third party services{/t}
                            </small>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="panel-group" data-toggle="collapse" id="panel-group-comscore">
                      <div class="panel panel-default">
                        <div class="panel-heading collapsed">
                          <h4 class="panel-title">
                            <a class="collapsed" data-parent="#panel-group-comscore" data-toggle="collapse" href="#comscore">
                              <i class="fa fa-area-chart"></i>{t}ComScore Statistics{/t}
                            </a>
                          </h4>
                        </div>
                        <div id="comscore" class="panel-collapse collapse">
                          <div class="panel-body">
                            <div class="form-group">
                              <label class="form-label" for="comscore-page-id">
                                {t}comScore Page ID{/t}
                              </label>
                              <div class="controls">
                                <input class="form-control" id="comscore-page-id" name="comscore-page-id" ng-model="settings.comscore.page_id" type="text">
                                <div class="help">
                                  {t escape=off}If you also have a <strong>comScore statistics service</strong>, add your page id{/t}
                                </div>
                              </div>
                            </div>
                            <small class="help">
                              <i class="fa fa-info-circle m-r-5 text-info"></i>
                              {t}We are not responsible of the stats or of any third party services{/t}
                            </small>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="panel-group" data-toggle="collapse" id="panel-group-ojd">
                      <div class="panel panel-default">
                        <div class="panel-heading collapsed">
                          <h4 class="panel-title">
                            <a class="collapsed" data-parent="#panel-group-ojd" data-toggle="collapse" href="#ojd">
                              <i class="fa fa-line-chart"></i>{t}OJD Statistics{/t}
                            </a>
                          </h4>
                        </div>
                        <div class="panel-collapse collapse" id="ojd">
                          <div class="panel-body">
                            <div class="form-group">
                              <label class="form-label" for="ojd-page-id">
                                {t}OJD Page ID{/t}
                              </label>
                              <div class="controls">
                                <input class="form-control" id="ojd-page-id" name="ojd-page-id" ng-model="settings.ojd.page_id" type="text">
                                <div class="help">{t escape=off}If you also have a <strong>OJD statistics service</strong>, add your page id{/t}</div>
                              </div>
                            </div>
                            <small class="help">
                              <i class="fa fa-info-circle m-r-5 text-info"></i>
                              {t}We are not responsible of the stats or of any third party services{/t}
                            </small>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="panel-group" id="panel-group-charbeat" data-toggle="collapse">
                      <div class="panel panel-default">
                        <div class="panel-heading collapsed">
                          <h4 class="panel-title">
                            <a class="collapsed" data-parent="#panel-group-charbeat" data-toggle="collapse" href="#chartbeat">
                              <i class="fa fa-bar-chart"></i>{t}Chartbeat{/t}
                            </a>
                          </h4>
                        </div>
                        <div class="panel-collapse collapse" id="chartbeat">
                          <div class="panel-body">
                            <div class="form-group">
                              <label class="form-label" for="chartbeat-id">
                                {t}Chartbeat Account ID{/t}
                              </label>
                              <div class="controls">
                                <input class="form-control" id="chartbeat-id" name="chartbeat-id" ng-model="settings.chartbeat.id" type="text">
                                <div class="help">{t escape=off}If you also have a <strong>Charbeat statistics service</strong>, add your account id{/t}</div>
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="form-label" for="chartbeat-domain">
                                {t}Chartbeat Domain{/t}
                              </label>
                              <div class="controls">
                                <input class="form-control" id="chartbeat-domain" name="chartbeat-domain" ng-model="settings.chartbeat.domain" type="text">
                              </div>
                            </div>
                            <small class="help">
                              <i class="fa fa-info-circle m-r-5 text-info"></i>
                              {t}We are not responsible of the stats or of any third party services{/t}
                            </small>
                          </div>
                        </div>
                      </div>
                    </div>
                    <h4>
                      <i class="fa fa-cog"></i>
                      {t}Internal settings{/t}
                    </h4>
                    <div class="panel-group" data-toggle="collapse" id="panel-group-recaptcha">
                      <div class="panel panel-default">
                        <div class="panel-heading">
                          <h4 class="panel-title">
                            <a class="collapsed" data-parent="#panel-group-recaptcha" data-toggle="collapse" href="#recaptcha">
                              <i class="fa fa-check-square"></i>
                              {t}Google Recaptcha{/t}
                            </a>
                          </h4>
                        </div>
                        <div class="panel-collapse collapse" id="recaptcha">
                          <div class="panel-body">
                            <div class="row">
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label class="form-label" for="recaptcha-public-key">
                                    {t}Public key{/t}
                                  </label>
                                  <div class="controls">
                                    <input class="form-control" id="recaptcha-public-key" name="recaptcha-public-key" ng-model="settings.recaptcha.public_key" type="text">
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label class="form-label" for="recaptcha-private-key">
                                    {t}Private key{/t}
                                  </label>
                                  <div class="controls">
                                    <div class="form-group">
                                      <input class="form-control" id="recaptcha-private-key" name="recaptcha-private-key" ng-model="settings.recaptcha.private_key" type="text">
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <small class="help">
                                <i class="fa fa-info-circle m-r-5 text-info"></i>
                                {t escape=off}Get your reCaptcha key from <a class="external-link" href="https://www.google.com/recaptcha/admin#whyrecaptcha" target="_blank" ng-click="$event.stopPropagation();">this page</a>.{/t} {t}Used when we want to test if the user is an human and not a robot.{/t}
                              </small>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    {is_module_activated name="PAYWALL"}
                      <div class="panel-group" data-toggle="collapse" id="panel-group-paypal">
                        <div class="panel panel-default">
                          <div class="panel-heading collapsed">
                            <h4 class="panel-title">
                              <a class="collapsed" data-parent="#panel-group-paypal" data-toggle="collapse" href="#paypal">
                                <i class="fa fa-paypal"></i>
                                {t}Paypal Settings{/t}
                              </a>
                            </h4>
                          </div>
                        </div>
                        <div class="panel-collapse collapse" id="paypal">
                          <div class="panel-body">
                            <div class="form-group">
                              <label class="form-label" for="paypal-mail">
                                {t}Account email:{/t}
                              </label>
                              <div class="controls">
                                <div class="form-group">
                                  <input class="form-control" id="paypal-mail" name="paypal-mail" ng-model="settings.paypal_mail" type="text">
                                </div>
                              </div>
                              <small class="help">
                                <i class="fa fa-info-circle m-r-5 text-info"></i>
                                {t escape=off}You can get your PayPal account email from <a class="external-link" href="https://www.paypal.com/us/cgi-bin/webscr?cmd=_registration-run" target="_blank" ng-click="$event.stopPropagation();">PayPal site</a>. This must be a business account for receiving payments{/t}
                              </small>
                            </div>
                          </div>
                        </div>
                      </div>
                    {/is_module_activated}
                    <div class="panel-group" data-toggle="collapse" id="panel-group-google-search">
                      <div class="panel panel-default">
                        <div class="panel-heading collapsed">
                          <h4 class="panel-title">
                            <a class="collapsed" data-parent="#panel-group-google-search" data-toggle="collapse" href="#google-search">
                              <i class="fa fa-search"></i>
                              {t}Google Search{/t}
                            </a>
                          </h4>
                        </div>
                        <div class="panel-collapse collapse" id="google-search">
                          <div class="panel-body">
                            <div class="form-group">
                              <label class="form-label" for="google-custom-search-api-key">
                                {t}Google Search API key{/t}
                              </label>
                              <div class="controls">
                                <input class="form-control" id="google-custom-search-api-key" name="google-custom-search-api-key" ng-model="settings.google_custom_search_api_key" type="text">
                              </div>
                            </div>
                            <small class="help">
                              <i class="fa fa-info-circle m-r-5 text-info"></i>
                              {t escape=off}You can get your Google <strong>Search</strong> API Key from <a class="external-link" href="http://www.google.com/cse/manage/create" target="_blank" ng-click="$event.stopPropagation();">Google Search sign up website</a>.{/t}
                            </small>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="panel-group" data-toggle="collapse" id="panel-group-google-news">
                      <div class="panel panel-default">
                        <div class="panel-heading collapsed">
                          <h4 class="panel-title">
                            <a class="collapsed" data-parent="#panel-group-google-news" data-toggle="collapse" href="#google-news">
                              <i class="fa fa-newspaper-o"></i>
                              {t}Google News{/t}
                            </a>
                          </h4>
                        </div>
                        <div class="panel-collapse collapse" id="google-news">
                          <div class="panel-body">
                            <div class="form-group">
                              <label class="form-label" for="google-news-name">
                                {t}Publication name in Google News{/t}
                              </label>
                              <div class="controls">
                                <input class="form-control" id="google-news-name" name="google-news-name" ng-model="settings.google_news_name" type="text">
                              </div>
                            </div>
                            <small class="help">
                              <i class="fa fa-info-circle m-r-5 text-info"></i>
                              {t escape=off}You can get your Publication name in <a class="external-link" href="https://www.google.es/search?num=100&hl=es&safe=off&gl=es&tbm=nws&q={$smarty.server.HTTP_HOST}&oq={$smarty.server.HTTP_HOST}" target="_blank" ng-click="$event.stopPropagation();">Google News search</a> for your site.{/t}
                            </small>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="panel-group" data-toggle="collapse" id="panel-group-google-maps">
                      <div class="panel panel-default">
                        <div class="panel-heading collapsed">
                          <h4 class="panel-title">
                            <a class="collapsed" data-parent="#panel-group-google-maps" data-toggle="collapse" href="#google-maps">
                              <i class="fa fa-map"></i>
                              {t}Google Maps{/t}
                            </a>
                          </h4>
                        </div>
                        <div class="panel-collapse collapse" id="google-maps">
                          <div class="panel-body">
                            <div class="form-group">
                              <label class="form-label" for="google-maps-api-key">
                                {t}Google Maps API key{/t}
                              </label>
                              <div class="controls">
                                <input class="form-control" id="google-maps-api-key" name="google-maps-api-key" ng-model="settings.google_maps_api_key" type="text">
                              </div>
                            </div>
                            <small class="help">
                              <i class="fa fa-info-circle m-r-5 text-info"></i>
                              {t escape=off}You can get your Google <strong>Maps</strong> API Key from <a class="external-link" href="http://code.google.com/apis/maps/signup.html" target="_blank" ng-click="$event.stopPropagation();">Google maps sign up website</a>.{/t}
                            </small>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="panel-group" data-toggle="collapse" id="panel-group-google-tags">
                      <div class="panel panel-default">
                        <div class="panel-heading collapsed">
                          <h4 class="panel-title">
                            <a class="collapsed" data-parent="#panel-group-google-tags" data-toggle="collapse" href="#google-tags">
                              <i class="fa fa-tag"></i>
                              {t}Google Tags{/t}
                            </a>
                          </h4>
                        </div>
                        <div class="panel-collapse collapse" id="google-tags">
                          <div class="panel-body">
                            <div class="form-group">
                              <label class="form-label" for="google-tags-id">
                                {t}Google Tags container Id{/t}
                              </label>
                              <div class="controls">
                                <input class="form-control" id="google-tags-id" name="google-tags-id" ng-model="settings.google_tags_id" type="text">
                              </div>
                            </div>
                            <small class="help">
                              <i class="fa fa-info-circle m-r-5 text-info"></i>
                              {t escape=off}You can get your Google <strong>Tags</strong> container Id from <a class="external-link" href="https://tagmanager.google.com/#/home" target="_blank" ng-click="$event.stopPropagation();">Google tags sign up website</a>.{/t}
                            </small>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="panel-group" data-toggle="collapse" id="panel-group-acton">
                      <div class="panel panel-default">
                        <div class="panel-heading collapsed">
                          <h4 class="panel-title">
                            <a class="collapsed" data-parent="#panel-group-acton" data-toggle="collapse" href="#act-on">
                              <i class="fa fa-tag"></i>
                              {t}Act-ON{/t}
                            </a>
                          </h4>
                        </div>
                        <div class="panel-collapse collapse" id="act-on">
                          <div class="panel-body">
                            <div class="row">
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label class="form-label" for="act-on-username">
                                    {t}Act-ON user name{/t}
                                  </label>
                                  <div class="controls">
                                    <input class="form-control" id="act-on-username" name="act-on-username" ng-model="settings.act_on_configuration.username" type="text">
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label class="form-label" for="act-on-password">
                                    {t}Act-ON password{/t}
                                  </label>
                                  <div class="controls">
                                    <input class="form-control" id="act-on-password" name="" ng-model="settings.act_on_configuration.password" type="text">
                                  </div>
                                </div>
                              </div>
                            </div>

                            <div class="row">
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label class="form-label" for="act-on-client-id">
                                    {t}Act-ON client_id{/t}
                                  </label>
                                  <div class="controls">
                                    <input class="form-control" id="act-on-client-id" name="act-on-client-id" ng-model="settings.act_on_configuration.client_id" type="text">
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label class="form-label" for="act-on-client-secret">
                                    {t}Act-ON secret{/t}
                                  </label>
                                  <div class="controls">
                                    <input class="form-control" id="act-on-client-secret" name="act-on-client-secret" ng-model="settings.act_on_configuration.client_secret" type="text">
                                  </div>
                                </div>
                              </div>
                            </div>


                            <small class="help">
                              <i class="fa fa-info-circle m-r-5 text-info"></i>
                              {t escape=off}You can get your <strong>Act-ON</strong> auth codes from your <a class="external-link" href="http://act-on.com" target="_blank" ng-click="$event.stopPropagation();">the Act-ON account</a>.{/t}
                            </small>

                          </div>
                        </div>
                      </div>
                    </div>


                  </div>
                  <div class="col-md-6">
                    <h4>
                      <i class="fa fa-thumbs-up"></i>
                      {t}Social network integration{/t}
                    </h4>
                    <div class="panel-group" id="panel-group-google-plus" data-toggle="collapse">
                      <div class="panel panel-default">
                        <div class="panel-heading collapsed">
                          <h4 class="panel-title">
                            <a class="collapsed" data-toggle="collapse" data-parent="#panel-group-google-plus" href="#goggle-plus">
                              <i class="fa fa-google-plus"></i>
                              {t}Google+{/t}
                            </a>
                          </h4>
                        </div>
                        <div class="panel-collapse collapse" id="goggle-plus">
                          <div class="panel-body">
                            <div class="form-group">
                              <label class="form-label" for="google-page">
                                {t}Google+ Page Url{/t}
                              </label>
                              <div class="controls">
                                <input class="form-control" id="google-page" name="google-page" ng-model="settings.google_page" type="text">
                                <span class="help">
                                  {t escape=off}If you have a <strong>Google+ page</strong>, please complete this input.{/t}
                                </span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="panel-group" id="panel-group-youtube" data-toggle="collapse">
                      <div class="panel panel-default">
                        <div class="panel-heading collapsed">
                          <h4 class="panel-title">
                            <a class="collapsed" data-parent="#panel-group-youtube" data-toggle="collapse" href="#youtube">
                              <i class="fa fa-youtube"></i>
                              {t}YouTube{/t}
                            </a>
                          </h4>
                        </div>
                        <div class="panel-collapse collapse" id="youtube">
                          <div class="panel-body">
                            <div class="form-group">
                              <div class="form-group">
                                <label class="form-label" for="youtube-page">
                                  {t}YouTube Page Url{/t}
                                </label>
                                <div class="controls">
                                  <input class="form-control" id="youtube-page" name="youtube-page" ng-model="settings.youtube_page" type="text">
                                  <span class="help">
                                    {t escape=off}If you have a <strong>Youtube page</strong>, please complete the form with your youtube page url.{/t}
                                  </span>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="panel-group" id="panel-group-facebook" data-toggle="collapse">
                      <div class="panel panel-default">
                        <div class="panel-heading collapsed">
                          <h4 class="panel-title">
                            <a class="collapsed" data-parent="#panel-group-facebook" data-toggle="collapse" href="#facebook">
                              <i class="fa fa-facebook"></i>{t}Facebook{/t}
                            </a>
                          </h4>
                        </div>
                        <div class="panel-collapse collapse" id="facebook">
                          <div class="panel-body">
                            <div class="form-group">
                              <label class="form-label" for="facebook-page">
                                {t}Facebook Page Url{/t}
                              </label>
                              <div class="controls">
                                <input class="form-control" id="facebook-page" name="facebook-page" ng-model="settings.facebook.page" type="text">
                                <span class="help">
                                  {t escape=off}If you have a <strong>facebook page</strong>, please complete the form with your facebook page url and Id.{/t}
                                </span>
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="form-label" for="facebook-id">
                                {t}Facebook Id{/t}
                              </label>
                              <div class="controls">
                                <input class="form-control" id="facebook-id" name="facebook-id" ng-model="settings.facebook.id" type="text">
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="form-label" for="facebook-api-key">
                                {t}APP key{/t}
                              </label>
                              <div class="controls">
                                <input class="form-control" id="facebook-api_key" name="facebook-api-key" ng-model="settings.facebook.api_key" type="text">
                                <span class="help">
                                  {t escape=off}You can get your Facebook App Keys from <a class="external-link" href="https://developers.facebook.com/apps" target="_blank" ng-click="$event.stopPropagation();">Facebook Developers website</a>.{/t}
                                </span>
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="form-label" for="facebook-secret-key">
                                {t}Secret key{/t}
                              </label>
                              <div class="controls">
                                <input class="form-control" id="facebook-secret-key" name="facebook-secret-key" ng-model="settings.facebook.secret_key" type="text">
                              </div>
                            </div>
                            {is_module_activated name="FIA_MODULE"}
                            <div class="form-group">
                              <label class="form-label" for="facebook-instant-articles-tag">
                                {t}Instant Articles (fb:pages meta tag){/t}
                              </label>
                              <div class="controls">
                                <input class="form-control" id="facebook-instant-articles-tag" name="facebook-instant-articles-tag]" ng-model="settings.facebook.instant_articles_tag" type="text">
                              </div>
                            </div>
                            {/is_module_activated}
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="panel-group" id="panel-group-twitter" data-toggle="collapse">
                      <div class="panel panel-default">
                        <div class="panel-heading collapsed">
                          <h4 class="panel-title">
                            <a class="collapsed" data-parent="#panel-group-twitter" data-toggle="collapse" href="#twitter">
                              <i class="fa fa-twitter"></i>{t}Twitter{/t}
                            </a>
                          </h4>
                        </div>
                        <div class="panel-collapse collapse" id="twitter">
                          <div class="panel-body">
                            <div class="form-group">
                              <label class="form-label" for="twitter-page">
                                {t}Twitter Page{/t}
                              </label>
                              <div class="controls">
                                <input class="form-control" id="twitter-page" name="twitter-page" ng-model="settings.twitter_page" type="text">
                                <span class="help">
                                  {t escape=off}If you also have a <strong>Twitter page</strong>, add your page url on the form. Default will be set with Opennemas.{/t}
                                </span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="panel-group" id="panel-group-instagram" data-toggle="collapse">
                      <div class="panel panel-default">
                        <div class="panel-heading collapsed">
                          <h4 class="panel-title">
                            <a class="collapsed" data-parent="#panel-group-instagram" data-toggle="collapse" href="#instagram">
                              <i class="fa fa-instagram"></i>{t}Instagram{/t}
                            </a>
                          </h4>
                        </div>
                        <div class="panel-collapse collapse" id="instagram">
                          <div class="panel-body">
                            <div class="form-group">
                              <label class="form-label" for="instagram-page">
                                {t}Instagram Page{/t}
                              </label>
                              <div class="controls">
                                <input class="form-control" id="instagram-page" name="instagram-page" ng-model="settings.instagram_page" type="text">
                                <span class="help">
                                  {t escape=off}If you also have a <strong>Instagram page</strong>, add your page url on the form.{/t}
                                </span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="panel-group" id="panel-group-pinterest" data-toggle="collapse">
                      <div class="panel panel-default">
                        <div class="panel-heading collapsed">
                          <h4 class="panel-title">
                            <a class="collapsed" data-parent="#panel-group-pinterest" data-toggle="collapse" href="#pinterest">
                              <i class="fa fa-pinterest-square"></i>{t}Pinterest{/t}
                            </a>
                          </h4>
                        </div>
                        <div class="panel-collapse collapse" id="pinterest">
                          <div class="panel-body">
                            <div class="form-group">
                              <label class="form-label" for="pinterest-page">
                                {t}Pinterest Page{/t}
                              </label>
                              <div class="controls">
                                <input class="form-control" id="pinterest-page" name="pinterest-page" ng-model="settings.pinterest_page" type="text">
                                <span class="help">
                                  {t escape=off}If you also have a <strong>Pinterest page</strong>, add your page url on the form.{/t}
                                </span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="panel-group" id="panel-group-vimeo" data-toggle="collapse">
                      <div class="panel panel-default">
                        <div class="panel-heading collapsed">
                          <h4 class="panel-title">
                            <a class="collapsed" data-parent="#panel-group-vimeo" data-toggle="collapse" href="#vimeo">
                              <i class="fa fa-vimeo-square"></i>{t}Vimeo{/t}
                            </a>
                          </h4>
                        </div>
                        <div class="panel-collapse collapse" id="vimeo">
                          <div class="panel-body">
                            <div class="form-group">
                              <label class="form-label" for="vimeo-page">
                                {t}Vimeo Page{/t}
                              </label>
                              <div class="controls">
                                <input class="form-control" id="vimeo-page" name="vimeo-page" ng-model="settings.vimeo_page" type="text">
                                <span class="help">
                                  {t escape=off}If you also have a <strong>Vimeo page</strong>, add your page url on the form.{/t}
                                </span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="panel-group" id="panel-group-linkedin" data-toggle="collapse">
                      <div class="panel panel-default">
                        <div class="panel-heading collapsed">
                          <h4 class="panel-title">
                            <a class="collapsed" data-parent="#panel-group-linkedin" data-toggle="collapse" href="#linkedin">
                              <i class="fa fa-linkedin"></i>{t}LinkedIn{/t}
                            </a>
                          </h4>
                        </div>
                        <div class="panel-collapse collapse" id="linkedin">
                          <div class="panel-body">
                            <div class="form-group">
                              <label class="form-label" for="linkedin-page">
                                {t}LinkedIn Page{/t}
                              </label>
                              <div class="controls">
                                <input class="form-control" id="linkedin-page" name="linkedin-page" ng-model="settings.linkedin_page" type="text">
                                <span class="help">
                                  {t escape=off}If you also have a <strong>LinkedIn page</strong>, add your page url on the form. Default will be set with Opennemas.{/t}
                                </span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </uib-tab>
            {if $app.security->hasPermission('MASTER')}
              <uib-tab>
                <uib-tab-heading>
                  <i class="fa fa-rebel m-r-5"></i>
                  {t}Only masters{/t}
                </uib-tab-heading>
                <div class="tab-wrapper">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-md-6">
                          <h4>
                            <i class="fa fa-android"></i>
                            Robots.txt
                          </h4>
                          <div class="form-group">
                            <label class="form-label" for="robots-txt-rules">
                              {t}Robots.txt rules{/t}
                            </label>
                            <span class="help">
                              {t escape=off}Add custom robots.txt rules like 'Disallow: /tag'. Refer to the <a href="http://www.robotstxt.org/robotstxt.html" target="_blank" ng-click="$event.stopPropagation();">documentation</a>.{/t}
                            </span>
                            <div class="controls">
                              <textarea class="form-control" id="robots-txt-rules" name="robots-txt-rules" ng-model="settings.robots_txt_rules" rows="6"></textarea>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <h4>
                            <i class="fa fa-retweet"></i>
                            {t}Redirection{/t}
                          </h4>
                          <div class="form-group">
                            <div class="checkbox">
                              <input {if $configs['redirection'] eq "1"}checked{/if} id="redirection" name="redirection" ng-false-value="0" ng-model="settings.redirection" ng-true-value="'1'" type="checkbox">
                              <label for="redirection">
                                {t}Redirect to frontpage non-migrated contents{/t}
                              </label>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-md-6">
                          <h3>
                            <i class="fa fa-code"></i>
                            Scripts
                          </h3>
                          <div class="form-group">
                            <label class="form-label" for="header-script">
                              {t}Scripts in header{/t}
                              <span class="help">{t}This scripts will be included before the </head> tag{/t}</span>
                            </label>
                            <div class="controls">
                              <textarea class="form-control" id="header-script" name="header-script" ng-model="settings.header_script" rows="6"></textarea>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="form-label" for="body-start-script">
                              {t}Scripts at body start{/t}
                              <span class="help">{t}This scripts will be included before the <body> tag{/t}</span>
                            </label>
                            <div class="controls">
                              <textarea class="form-control" id="body-start-script" name="body-start-script" ng-model="settings.body_start_script" rows="6"></textarea>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="form-label" for="body-end-script">
                              {t}Scripts at body end{/t}
                              <span class="help">{t}This scripts will be included before the </body> tag{/t}</span>
                            </label>
                            <div class="controls">
                              <textarea class="form-control" id="body-end-script" name="body-end-script" ng-model="settings.body_end_script" rows="6"></textarea>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <h4>
                            <i class="fa fa-paint-brush"></i>
                            {t}Style{/t}
                          </h4>
                          <div class="form-group" ng-if="extra.theme_skins.length !== 0">
                            <label class="form-label" for="site-color">
                              {t}Default skin{/t}
                            </label>
                            <span class="help">
                              {t}Your theme offers multiple skins to slightly change your theme. Select which one do you want.{/t}
                            </span>
                            <div class="controls">
                              <div class="input-group">
                                <select id="theme-style" name="theme-style" ng-model="settings.theme_skin" required>
                                  <option value="[% code %]" ng-repeat="(code,style) in extra.theme_skins" ng-selected="[% code === settings.theme_skin || settings.theme_skin == undefined %]">[% style.name %]</option>
                                </select>
                              </div>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="form-label" for="custom-css">
                              {t}Custom CSS{/t}
                              <span class="help">{t}This sripts will be included in the global.css file.{/t}</span>
                              <span class="text-danger">Not functional for now</span>
                            </label>
                            <div class="controls">
                              <textarea class="form-control" id="custom-css" name="custom_css" ng-model="settings.custom_css" disabled="disabled" readonly="readonly"></textarea>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </uib-tab>
            {/if}
          </uib-tabset>
        </div>
      </div>
    </div>
  </form>
{/block}
