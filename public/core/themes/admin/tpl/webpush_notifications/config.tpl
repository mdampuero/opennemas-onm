{extends file="base/admin.tpl"}

{block name="content"}
  <form ng-controller="WebPushNotificationsConfigCtrl" ng-init="init()">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <a class="no-padding" href="[% routing.generate('backend_webpush_notifications_list') %]">
                  <i class="fa fa-cog m-r-10"></i>
                  {t}Web Push notifications Configuration{/t}
                </a>
              </h4>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <a class="btn btn-link" href="{url name=backend_webpush_notifications_list}" title="{t}Go back to list{/t}">
                  <i class="fa fa-reply"></i>
                </a>
              </li>
              <li class="quicklinks">
                <span class="h-seperate"></span>
              </li>
              <li class="quicklinks">
                <button class="btn btn-loading btn-success ng-cloak text-uppercase" ng-click="save()" ng-disabled="flags.http.loading || flags.http.saving" type="button">
                  <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.saving }"></i>
                  {t}Save{/t}
                </button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="grid simple onm-shadow">
        <div class="grid-body ng-cloak">
          <div class="row">
            <div class="col-xs-6 col-md-3">
              <h4>{t}Web Push service{/t}</h4>
              <div class="controls">
                <select class="form-control-lg" ng-model="settings.webpush_service.service">
                  <option value="webpushr" selected>{t}Webpushr{/t}</option>
                  <option value="sendpulse">{t}SendPulse{/t}</option>
                </select>
              </div>
            </div>
            <div class="col-xs-6 col-md-3">
              <h4>{t}Automatic sent{/t}</h4>
              <div class="controls">
                <div class="checkbox">
                  <input class="form-control" id="webpush-automatic" name="webpush-automatic" ng-false-value="'0'" ng-model="settings.webpush_automatic" ng-true-value="'1'" type="checkbox"/>
                  <label class="form-label" for="webpush-automatic">
                    {t}Activated{/t}
                  </label>
                </div>
              </div>
              <i class="fa fa-info-circle text-info"></i>
              <small class="text-muted">{t}Will be sent when content is published{/t}</small>
            </div>
            <div class="col-xs-6 col-md-3">
              <h4>{t}Notifications delay time{/t}</h4>
              <div class="controls">
                <select class="form-control-lg" ng-model="settings.webpush_delay" ng-options="option.value as option.label for option in options"></select>
              </div>
            </div>
            <div class="col-xs-6 col-md-3">
              <h4>{t}Restricted hours{/t}</h4>
              <tags-input ng-model="settings.webpush_restricted_hours" add-on-paste="true" add-from-autocomplete-only="true" placeholder="{t}Add an hour{/t}">
                <auto-complete source="loadHours($query)" load-on-focus=true min-length="0" debounce-delay="0"></auto-complete>
              </tags-input>
              <i class="fa fa-info-circle text-info"></i>
              <small class="text-muted">{t}Time zone:{/t} {date_default_timezone_get()}</small>
            </div>
            <div class="col-xs-6 col-md-3 m-b-15">
              <h4>{t}Stop subscribers collection{/t}</h4>
              <div class="controls">
                <div class="checkbox">
                  <input class="form-control" id="webpush_stop_collection" name="webpush_stop_collection" ng-false-value="'0'" ng-model="settings.webpush_stop_collection" ng-true-value="'1'" type="checkbox"/>
                  <label class="form-label" for="webpush_stop_collection">
                    {t}Stop asking to subscribe{/t}
                  </label>
                </div>
              </div>
              <i class="fa fa-info-circle text-info"></i>
              <small class="text-muted">{t}Users will not be able to subscribe to your notifications{/t}</small>
            </div>
          </div>
        </div>
      </div>
      <div class="grid simple onm-shadow">
        <div class="grid-body ng-cloak">
          <div class="row" ng-if="settings.webpush_service.service == 'webpushr'">
            <div class="col-xs-12">
              <div class="col-xs-12">
                <div class="row">
                  <h4>{t}Webpushr service credentials{/t}</h4>
                </div>
              </div>
              <div class="row">
                <div class="controls col-xs-12 col-md-4 m-b-10">
                  <label>{t}API key{/t}</label>
                  <input class="form-control" ng-model="settings.webpush_service.apikey" type="text">
                  <i class="fa fa-info-circle text-info"></i>
                  <small class="text-muted">{t}Can be found in Webpushr APP{/t} | Integration > REST API Keys > Key</small>
                </div>
                <div class="controls col-xs-12 col-md-4 m-b-10">
                  <label>{t}Authentication token{/t}</label>
                  <input class="form-control" ng-model="settings.webpush_service.token" type="text">
                  <i class="fa fa-info-circle text-info"></i>
                  <small class="text-muted">{t}Can be found in Webpushr APP{/t} | Integration > REST API Keys > Authentication Token</small>
                </div>
                <div class="controls col-xs-12 col-md-4 m-b-10">
                  <label>{t}Public key{/t}</label>
                  <input class="form-control" ng-model="settings.webpush_service.publickey" type="text">
                  <i class="fa fa-info-circle text-info"></i>
                  <small class="text-muted">{t}Can be found in Webpushr APP{/t} | Integration > Public key for AMP > Key</small>
                </div>
              </div>
            </div>
            <div class="col-xs-12">
              <div class="p-t-15">
                <div class="text-center">
                  <button class="btn btn-block btn-loading m-t-5" ng-class="{ 'btn-light': !status , 'btn-success': status === 'success' , 'btn-danger': status === 'failure' }" ng-click="check()" ng-disabled="!settings.webpush_service.apikey || !settings.webpush_service.token || !settings.webpush_service.publickey || flags.http.checking" type="button">
                    <i class="fa fa-plug m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.checking }"></i>
                    {t}Connect{/t}
                    <i class="fa fa-check m-l-5" ng-show="status === 'success'"></i>
                    <i class="fa fa-exclamation-circle m-l-5" ng-show="status === 'failure'"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
          <div class="row" ng-if="settings.webpush_service.service == 'sendpulse'">
            <div class="col-xs-12">
              <div class="col-xs-12">
                <div class="row">
                  <h4>{t}SendPulse service credentials{/t}</h4>
                </div>
              </div>
              <div class="row">
                <div class="controls col-xs-12 col-md-4 m-b-10">
                  <label>{t}API ID{/t}</label>
                  <input class="form-control" ng-model="settings.webpush_service.apikey" type="text">
                  <i class="fa fa-info-circle text-info"></i>
                  <small class="text-muted">{t}Can be found in SendPulse Account Settings{/t} | Account Settings > API > ID</small>
                </div>
                <div class="controls col-xs-12 col-md-4 m-b-10">
                  <label>{t}API Secret{/t}</label>
                  <input class="form-control" ng-model="settings.webpush_service.token" type="text">
                  <i class="fa fa-info-circle text-info"></i>
                  <small class="text-muted">{t}Can be found in SendPulse Account Settings{/t} | Account Settings > API > Secret</small>
                </div>
              </div>
            </div>
            <div class="col-xs-6">
              <div class="p-t-15">
                <div class="text-center_">
                  <button class="btn btn-block btn-loading m-t-5" ng-class="{ 'btn-light': !status , 'btn-success': status === 'success' , 'btn-danger': status === 'failure' }" ng-click="check()" type="button">
                    <i class="fa fa-plug m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.checking }"></i>
                    {t}Connect{/t}
                    <i class="fa fa-check m-l-5" ng-show="status === 'success'"></i>
                    <i class="fa fa-exclamation-circle m-l-5" ng-show="status === 'failure'"></i>
                  </button>
                </div>
              </div>
            </div>
            <div class="col-xs-6">
              <div class="p-t-15">
                <div class="text-center_">
                  <button class="btn btn-block btn-loading m-t-5" class="btn-light" ng-click="removeSavedSettings()" type="button">
                    {t}Remove account data{/t}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
{/block}
