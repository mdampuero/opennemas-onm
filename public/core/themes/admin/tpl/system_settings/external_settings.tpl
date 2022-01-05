{extends file="base/admin.tpl"}

{block name="content"}
  <form name="settingForm" ng-controller="SettingsCtrl" ng-init="list()" class="settings">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-cloud fa-lg"></i>
                {t}Settings{/t} > {t}General{/t} > {t}External services{/t}
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
      </div>
    </div>
  </form>
{/block}
