{extends file="base/admin.tpl"}

{block name="content"}
  <form name="settingForm" ng-controller="ExternalSettingsCtrl" ng-init="list()" class="settings">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-cloud fa-lg"></i>
                {t}External services{/t}
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
                          <div class="col-md-12">
                            <div class="form-group">
                              <label class="form-label">
                                {t}Google Analytics API key{/t}
                              </label>
                              <div class="controls">
                                <input class="form-control" id="google-analytics-[% $index %]-api-key" name="google-analytics-[% $index %]-api-key" ng-model="code.api_key" type="text">
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
              <div class="panel-group" id="panel-group-marfeel-compass" data-toggle="collapse">
                <div class="panel panel-default">
                  <div class="panel-heading collapsed">
                    <h4 class="panel-title">
                      <a class="collapsed" data-parent="#accordion-marfeel-compass" data-toggle="collapse" href="#marfeel-compass">
                        <i class="fa fa-compass"></i>{t}Marfeel Compass{/t}
                      </a>
                    </h4>
                  </div>
                  <div class="panel-collapse collapse" id="marfeel-compass">
                    <div class="panel-body">
                      <div class="form-group">
                        <label class="form-label" for="marfeel-compass-id">
                          {t}Marfeel Compass ID{/t}
                        </label>
                        <div class="controls">
                          <input class="form-control" id="marfeel-compass-id" name="marfeel-compass-id" ng-model="settings.marfeel_compass.id" type="text">
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
              {if $app.security->hasPermission('MASTER')}
              <div class="panel-group" id="panel-group-gfk" data-toggle="collapse">
                <div class="panel panel-default">
                  <div class="panel-heading collapsed">
                    <h4 class="panel-title">
                      <a class="collapsed" data-parent="#panel-group-gfk" data-toggle="collapse" href="#gfk">
                        <i class="fa fa-signal"></i>{t}GFK{/t}
                      </a>
                    </h4>
                  </div>
                  <div class="panel-collapse collapse" id="gfk">
                    <div class="panel-body">
                      <div class="form-group">
                        <label class="form-label" for="gfk-media-id">
                          {t}GFK Media ID{/t}
                        </label>
                        <div class="controls">
                          <input class="form-control" id="gfk-media-id" name="gfk-media-id" ng-model="settings.gfk.media_id" type="text">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="gfk-domain">
                          {t}GFK Domain{/t}
                        </label>
                        <div class="controls">
                          <input class="form-control" id="gfk-domain" name="gfk-domain" ng-model="settings.gfk.domain" type="text">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="gfk-region-id">
                          {t}GFK Region ID{/t}
                        </label>
                        <div class="controls">
                          <input class="form-control" id="gfk-region-id" name="gfk-region-id" ng-model="settings.gfk.region_id" type="text">
                          <div class="help">{t}Default will be set with "es"{/t}</div>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="gfk-content-id">
                          {t}GFK Content ID{/t}
                        </label>
                        <div class="controls">
                          <input class="form-control" id="gfk-content-id" name="gfk-content-id" ng-model="settings.gfk.content_id" type="text">
                          <div class="help">{t}Default will be set with "default"{/t}</div>
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
              {/if}
              {is_module_activated name="es.openhost.module.dataLayerHenneo"}
              <div class="panel-group" id="panel-group-prometeo" data-toggle="collapse">
                <div class="panel panel-default">
                  <div class="panel-heading collapsed">
                    <h4 class="panel-title">
                      <a class="collapsed" data-parent="#panel-group-prometeo" data-toggle="collapse" href="#prometeo">
                        <i class="fa fa-signal"></i>{t}Prometeo{/t}
                      </a>
                    </h4>
                  </div>
                  <div class="panel-collapse collapse" id="prometeo">
                    <div class="panel-body">
                      <div class="form-group">
                        <label class="form-label" for="prometeo-id">
                          {t}Prometeo Media ID{/t}
                        </label>
                        <div class="controls">
                          <input class="form-control" id="prometeo-id" name="prometeo-id" ng-model="settings.prometeo.id" type="text">
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
              {/is_module_activated}
              {if $app.security->hasPermission('MASTER')}
              <div class="panel-group" id="panel-group-gfk" data-toggle="collapse">
                <div class="panel panel-default">
                  <div class="panel-heading collapsed">
                    <h4 class="panel-title">
                      <a class="collapsed" data-parent="#panel-group-adobe_analytics" data-toggle="collapse" href="#adobe_analytics">
                        <i class="fa fa-signal"></i>{t}Adobe analytics{/t}
                      </a>
                    </h4>
                  </div>
                  <div class="panel-collapse collapse" id="adobe_analytics">
                    <div class="panel-body">
                      <div class="form-group">
                        <label class="form-label" for="adobe-analytics-base">
                          {t}Adobe analytics Base File{/t}
                        </label>
                        <div class="controls">
                          <input class="form-control" id="adobe-analytics-base" name="adobe-analitics-base" ng-model="settings.adobe_base" type="text">
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
              {/if}
              {if $app.security->hasPermission('MASTER')}
                <div class="panel-group" id="panel-group-gfk" data-toggle="collapse">
                  <div class="panel panel-default">
                    <div class="panel-heading collapsed">
                      <h4 class="panel-title">
                        <a class="collapsed" data-parent="#panel-group-ga4-native" data-toggle="collapse" href="#ga4_native">
                          <i class="fa fa-google"></i>{t}Google Analytics (Native){/t}
                        </a>
                      </h4>
                    </div>
                    <div class="panel-collapse collapse" id="ga4_native">
                      <div class="panel-body">
                        <div class="form-group">
                          <label class="form-label" for="ga4-native-id">
                            {t}Google analytics ID{/t}
                          </label>
                          <div class="controls">
                            <input class="form-control" id="ga4-native-id" name="ga4-native-id" ng-model="settings.ga4_native_id" type="text">
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="form-label" for="ga4-native-config">
                            {t}Google analytics config File{/t}
                          </label>
                          <div class="controls">
                            <input class="form-control" id="ga4-native-config" name="ga4-native-config" ng-model="settings.ga4_native_config" type="text">
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
              {/if}
              <h4>
                <i class="fa fa-cog"></i>
                {t}Internal settings{/t}
              </h4>
              {if $app.security->hasPermission('MASTER')}
                <div class="panel-group" data-toggle="collapse" id="panel-group-payments">
                  <div class="panel panel-default">
                    <div class="panel-heading">
                      <h4 class="panel-title">
                        <a class="collapsed" data-parent="#panel-group-payments" data-toggle="collapse" href="#payments">
                          <i class="fa fa-credit-card-alt"></i>
                          {t}Global Payments{/t}
                        </a>
                      </h4>
                    </div>
                    <div class="panel-collapse collapse" id="payments">
                      <div class="panel-body">
                        <div class="row">
                          <div class="col-12">
                            <div class="form-group">
                              <label class="form-label" for="payments-merchant-id">
                                {t}Merchant id{/t}
                              </label>
                              <div class="controls">
                                <input class="form-control" id="payments-merchant-id" name="payments-merchant-id" ng-model="settings.payments.merchant_id" type="text">
                              </div>
                            </div>
                          </div>
                          <div class="col-12">
                            <div class="form-group">
                              <label class="form-label" for="payments-shared-secret">
                                {t}Shared secret{/t}
                              </label>
                              <div class="controls">
                                <div class="form-group">
                                  <input class="form-control" id="payments-shared-secret" name="payments-shared-secret" ng-model="settings.payments.shared_secret" type="text">
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-12">
                            <div class="form-group">
                              <label class="form-label" for="payments-amount">
                                {t}Amount{/t}
                              </label>
                              <div class="controls">
                                <div class="form-group">
                                  <input class="form-control" id="payments-amount" name="payments-amount" ng-model="settings.payments.amount" type="text">
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              {/if}
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
                        {t}Google Tag Manager{/t}
                      </a>
                    </h4>
                  </div>
                  <div class="panel-collapse collapse" id="google-tags">
                    <div class="panel-body">
                      <div class="form-group">
                        <label class="form-label" for="google-tags-id">
                          {t}Google Tag Manager container Id{/t}
                        </label>
                        <div class="controls">
                          <input class="form-control" id="google-tags-id" name="google-tags-id" ng-model="settings.google_tags_id" type="text">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="google-tags-id-amp">
                          {t}Google Tag Manager container Id for AMP{/t}
                        </label>
                        <div class="controls">
                          <input class="form-control" id="google-tags-id-amp" name="google-tags-id-amp" ng-model="settings.google_tags_id_amp" type="text">
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
              <div class="panel-group" data-toggle="collapse" id="panel-group-data-layer">
                <div class="panel panel-default">
                  <div class="panel-heading collapsed">
                    <h4 class="panel-title">
                      <a class="collapsed" data-parent="#panel-group-data-layer" data-toggle="collapse" href="#data-layer">
                        <i class="fa fa-cubes"></i>
                        {t}Data Layer{/t}
                      </a>
                    </h4>
                  </div>
                  <div class="panel-collapse collapse" id="data-layer">
                    <div class="panel-body">
                      <div class="form-group">
                        <div class="controls">
                          <div class="row" ng-repeat="variables in settings.data_layer track by $index">
                            <div class="col-lg-6 col-md-9 col-sm-5 col-xs-6 m-b-15">
                              <input class="form-control" ng-model="variables.key" placeholder="{t}Variable key{/t}" type="text" required>
                            </div>
                            <div class="col-lg-4 col-md-9 col-sm-5 col-xs-6 m-b-15">
                              <select name="value" ng-model="variables.value" required>
                                <option value="">{t}Select a value...{/t}</option>
                                <option value="[% key %]" ng-repeat="(key,value) in extra.data_types" ng-selected="[% key === variables.value %]">[% value %]</option>
                              </select>
                            </div>
                            <div class="col-lg-2 col-lg-offset-0 col-md-3 col-md-offset-0 col-sm-2 col-sm-offset-0 col-xs-4 col-xs-offset-4">
                              <button class="btn btn-block btn-danger ng-cloak" ng-click="removeDatalayerVariable($index)" type="button">
                                <i class="fa fa-trash-o"></i>
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
                          <button class="btn btn-block btn-default" ng-click="addDatalayerVariable()" type="button">
                            <i class="fa fa-plus"></i>
                            {t}Add{/t}
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              {is_module_activated name="es.openhost.module.acton"}
              <div class="panel-group" data-toggle="collapse" id="panel-group-acton">
                <div class="panel panel-default">
                  <div class="panel-heading collapsed">
                    <h4 class="panel-title">
                      <a class="collapsed" data-parent="#panel-group-acton" data-toggle="collapse" href="#act-on">
                        <i class="fa fa-tag"></i>
                        {t}Act-On{/t}
                      </a>
                    </h4>
                  </div>
                  <div class="panel-collapse collapse" id="act-on">
                    <div class="panel-body">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="form-label" for="act-on-username">
                              {t}Act-On user name{/t}
                            </label>
                            <div class="controls">
                              <input class="form-control" id="act-on-username" name="act-on-username" ng-model="settings['actOn.authentication'].username" type="text">
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="form-label" for="act-on-password">
                              {t}Act-On password{/t}
                            </label>
                            <div class="controls">
                              <input class="form-control" id="act-on-password" name="" ng-model="settings['actOn.authentication'].password" type="text">
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="form-label" for="act-on-client-id">
                              {t}Act-On client_id{/t}
                            </label>
                            <div class="controls">
                              <input class="form-control" id="act-on-client-id" name="act-on-client-id" ng-model="settings['actOn.authentication'].client_id" type="text">
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label class="form-label" for="act-on-client-secret">
                              {t}Act-On secret{/t}
                            </label>
                            <div class="controls">
                              <input class="form-control" id="act-on-client-secret" name="act-on-client-secret" ng-model="settings['actOn.authentication'].client_secret" type="text">
                            </div>
                          </div>
                        </div>
                      </div>
                      <small class="help">
                        <i class="fa fa-info-circle m-r-5 text-info"></i>
                        {t escape=off}You can get your <strong>Act-On</strong> auth codes from your <a class="external-link" href="http://act-on.com" target="_blank" ng-click="$event.stopPropagation();">the Act-On account</a>.{/t}
                      </small>

                    </div>
                  </div>
                </div>
              </div>
              {/is_module_activated}
            </div>
            <div class="col-md-6">
              <h4>
                <i class="fa fa-thumbs-up"></i>
                {t}Social network integration{/t}
              </h4>
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
              <div class="panel-group" id="panel-group-telegram" data-toggle="collapse">
                <div class="panel panel-default">
                  <div class="panel-heading collapsed">
                    <h4 class="panel-title">
                      <a class="collapsed" data-parent="#panel-group-telegram" data-toggle="collapse" href="#telegram">
                        <i class="fa fa-telegram"></i>{t}Telegram{/t}
                      </a>
                    </h4>
                  </div>
                  <div class="panel-collapse collapse" id="telegram">
                    <div class="panel-body">
                      <div class="form-group">
                        <label class="form-label" for="telegram-page">
                          {t}Telegram Page{/t}
                        </label>
                        <div class="controls">
                          <input class="form-control" id="telegram-page" name="telegram-page" ng-model="settings.telegram_page" type="text">
                          <span class="help">
                            {t escape=off}If you also have a <strong>Telegram page</strong>, add your page url on the form.{/t}
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="panel-group" id="panel-group-whatsapp" data-toggle="collapse">
                <div class="panel panel-default">
                  <div class="panel-heading collapsed">
                    <h4 class="panel-title">
                      <a class="collapsed" data-parent="#panel-group-whatsapp" data-toggle="collapse" href="#whatsapp">
                        <i class="fa fa-whatsapp"></i>{t}Whatsapp{/t}
                      </a>
                    </h4>
                  </div>
                  <div class="panel-collapse collapse" id="whatsapp">
                    <div class="panel-body">
                      <div class="form-group">
                        <label class="form-label" for="whatsapp-page">
                          {t}Whatsapp Page{/t}
                        </label>
                        <div class="controls">
                          <input class="form-control" id="whatsapp-page" name="whatsapp-page" ng-model="settings.whatsapp_page" type="text">
                          <span class="help">
                            {t escape=off}If you also have a <strong>Whatsapp page</strong>, add your page url on the form.{/t}
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="panel-group" id="panel-group-dailymotion" data-toggle="collapse">
                <div class="panel panel-default">
                  <div class="panel-heading collapsed">
                    <h4 class="panel-title">
                      <a class="collapsed" data-parent="#panel-group-tiktok" data-toggle="collapse" href="#tiktok">
                        <i class="fa fa-tiktok"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="12" height="12" fill="var(--second-color)"><path d="M448,209.91a210.06,210.06,0,0,1-122.77-39.25V349.38A162.55,162.55,0,1,1,185,188.31V278.2a74.62,74.62,0,1,0,52.23,71.18V0l88,0a121.18,121.18,0,0,0,1.86,22.17h0A122.18,122.18,0,0,0,381,102.39a121.43,121.43,0,0,0,67,20.14Z"></path></svg></i>{t}TikTok{/t}
                      </a>
                    </h4>
                  </div>
                  <div class="panel-collapse collapse" id="tiktok">
                    <div class="panel-body">
                      <div class="form-group">
                        <label class="form-label" for="tiktok-page">
                          {t}TikTok Page{/t}
                        </label>
                        <div class="controls">
                          <input class="form-control" id="tiktok-page" name="tiktok-page" ng-model="settings.tiktok_page" type="text">
                          <span class="help">
                            {t escape=off}If you also have a <strong>TikTok page</strong>, add your page url on the form.{/t}
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
               <div class="panel-group" id="panel-group-dailymotion" data-toggle="collapse">
                <div class="panel panel-default">
                  <div class="panel-heading collapsed">
                    <h4 class="panel-title">
                      <a class="collapsed" data-parent="#panel-group-dailymotion" data-toggle="collapse" href="#dailymotion">
                        <i class="fa fa-dailymotion"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="12" height="12" fill="var(--second-color)"><path d="M298.93,267a48.4,48.4,0,0,0-24.36-6.21q-19.83,0-33.44,13.27t-13.61,33.42q0,21.16,13.28,34.6t33.43,13.44q20.5,0,34.11-13.78T322,307.47A47.13,47.13,0,0,0,315.9,284,44.13,44.13,0,0,0,298.93,267ZM0,32V480H448V32ZM374.71,405.26h-53.1V381.37h-.67q-15.79,26.2-55.78,26.2-27.56,0-48.89-13.1a88.29,88.29,0,0,1-32.94-35.77q-11.6-22.68-11.59-50.89,0-27.56,11.76-50.22a89.9,89.9,0,0,1,32.93-35.78q21.18-13.09,47.72-13.1a80.87,80.87,0,0,1,29.74,5.21q13.28,5.21,25,17V153l55.79-12.09Z"></path></svg></i>{t}Dailymotion{/t}
                      </a>
                    </h4>
                  </div>
                  <div class="panel-collapse collapse" id="dailymotion">
                    <div class="panel-body">
                      <div class="form-group">
                        <label class="form-label" for="dailymotion-page">
                          {t}Dailymotion Page{/t}
                        </label>
                        <div class="controls">
                          <input class="form-control" id="dailymotion-page" name="dailymotion-page" ng-model="settings.dailymotion_page" type="text">
                          <span class="help">
                            {t escape=off}If you also have a <strong>Dailymotion page</strong>, add your page url on the form.{/t}
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <h4>
                <i class="fa fa-bell"></i>
                {t}WebPush service integration{/t}
              </h4>
              <div class="panel-group" id="panel-group-webpushr" data-toggle="collapse">
                <div class="panel panel-default">
                  <div class="panel-heading collapsed">
                    <h4 class="panel-title">
                      <a class="collapsed" data-parent="#panel-group-webpushr" data-toggle="collapse" href="#webpushr">
                        <i class="fa fa-envelope"></i>
                        {t}Webpushr{/t}
                      </a>
                    </h4>
                  </div>
                  <div class="panel-collapse collapse" id="webpushr">
                    <div class="panel-body">
                      <div class="form-group">
                        <div class="form-group">
                          <label class="form-label" for="webpushr-webpushrKey">
                            {t}Webpushr Api Key{/t}
                          </label>
                          <div class="controls">
                            <input class="form-control" id="webpushr-webpushrKey" name="webpushr-webpushrKey" ng-model="settings.webpush_apikey" type="text">
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="form-label" for="webpushr-webpushrAuthToken">
                            {t}Webpushr Auth Key{/t}
                          </label>
                          <div class="controls">
                            <input class="form-control" id="webpushr-webpushrAuthToken" name="webpushr-webpushrAuthToken" ng-model="settings.webpush_token" type="text">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
          </div>
        </div>
      </div>
    </div>
  </form>
{/block}
