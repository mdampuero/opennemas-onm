{extends file="base/admin.tpl"}

{block name="content"}
  <form name="settingForm" ng-controller="LanguageSettingsCtrl" ng-init="list()" class="settings">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-globe fa-lg"></i>
                {t}Language & time{/t}
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
        <div class="grid-body ng-cloak">
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
                          <option value="[% code %]" ng-repeat="(code,name) in extra.locales.frontend" ng-selected="[% code === settings.locale.frontend.language.selected %]">[% name %]</option>
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
                  <div class="showcase-info showcase-info-score showcase-info-top showcase-info-height-auto panel onm-shadow m-t-20">
                    <div class="row" >
                      <div class="col-md-4">
                        <div class="form-group">
                          <label class="form-label">
                            {t}Default service{/t}
                          </label>
                          <div class="controls">
                            <select class="form-control" ng-model="settings.translatorsDefault.translator">
                              <option value="">{t}Select a service...{/t}</option>
                              <option value="[% service.translator %]" ng-repeat="service in extra.translation_services">[% service.translator %]</option>
                            </select>
                          </div>
                        </div>
                      </div>
                       <div class="col-sm-4"  ng-repeat="(translationParam, text) in settings.parametersDefault">
                        <div class="form-group">
                          <label class="form-label">
                            [% text %]
                          </label>
                          <div class="controls">
                            <input class="form-control" ng-model="settings.translatorsDefault.config[translationParam]" type="text">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div ng-repeat="code in settings.translators track by $index">
                    <div class="showcase-info showcase-info-score showcase-info-top showcase-info-height-auto panel onm-shadow m-t-20">
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
                        <div class="col-md-4">
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
                        <div class="col-sm-6 col-md-4">
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
                        <div class="col-sm-6 col-md-4">
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
                  </div>
                  <div class="row p-t-20 text-center">
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
      </div>
    </div>
  </form>
{/block}
