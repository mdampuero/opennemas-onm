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
                  <i class="fa fa-camera"></i>
                  {t}Web Push notifications{/t}
                </a>
              </h4>
            </li>
            <li class="quicklinks hidden-xs m-l-5 m-r-5">
              <h4>
                <i class="fa fa-angle-right"></i>
              </h4>
            </li>
            <li class="quicklinks">
              <h4>{t}Configuration{/t}</h4>
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
      <div class="grid simple">
        <div class="grid-body ng-cloak">
            <div class="row">
              <div class="col-md-4">
                <h4>{t}Web Push service{/t}</h4>
                <div class="controls col-xs-4">
                  <select class="form-control-lg" ng-model="settings.webpush_service.service">
                    <option value="">{t}Seleccione una opción{/t}</option>
                    <option value="webpushr">{t}Webpushr{/t}</option>
                  </select>
                </div>
              </div>
              <div class="col-md-3">
                <h4>{t}Automatic sent{/t}</h4>
                <div class="controls">
                  <div class="checkbox">
                    <input class="form-control" id="webpush-automatic" name="webpush-automatic" ng-false-value="'0'" ng-model="settings.webpush_automatic" ng-true-value="'1'" type="checkbox"/>
                    <label class="form-label" for="webpush-automatic">
                      {t}Activated{/t}
                    </label>
                  </div>
                </div>
              </div>
              <div class="col-md-5">
                <h4>{t}Delay{/t}</h4>
                <div class="controls">
                  <div class="radio">
                    <input class="form-control" id="delay-1" ng-model="settings.webpush_delay" ng-value="'1'" type="radio"/>
                    <label for="delay-1">
                      {t}1 min{/t}
                    </label>
                  </div>
                  <div class="radio">
                    <input class="form-control" id="delay-5" ng-model="settings.webpush_delay" ng-value="'5'" type="radio"/>
                    <label for="delay-5">
                      {t}5 mins{/t}
                    </label>
                  </div>
                  <div class="radio">
                    <input class="form-control" id="delay-10" ng-model="settings.webpush_delay" ng-value="'10'" type="radio"/>
                    <label for="delay-10">
                      {t}10 mins{/t}
                    </label>
                  </div>
                  <div class="radio">
                    <input class="form-control" id="delay-30" ng-model="settings.webpush_delay" ng-value="'30'" type="radio"/>
                    <label for="delay-30">
                      {t}30 mins{/t}
                    </label>
                  </div>
                  <div class="radio">
                    <input class="form-control" id="delay-60" ng-model="settings.webpush_delay" ng-value="'60'" type="radio"/>
                    <label for="delay-60">
                      {t}60 mins{/t}
                    </label>
                  </div>
                </div>
              </div>
            </div>
            <div class="row m-t-30" ng-if="settings.webpush_service.service == 'webpushr'">
              <div class="col-md-6">
                <h4 class="no-margin">{t}Webpushr service credentials{/t}</h4>
                <div class="controls col-xs-8 m-t-10">
                  <label>{t}API key{/t}</label>
                  <input class="form-control" ng-model="settings.webpush_service.apikey" type="text">
                </div>
                <div class="controls col-xs-8 m-t-10 m-b-15">
                  <label>{t}Authentication token{/t}</label>
                  <input class="form-control" ng-model="settings.webpush_service.token" type="text">
                </div>
              </div>
            </div>
        </div>
      </div>
    </div>
  </form>
{/block}
