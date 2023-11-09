{extends file="base/admin.tpl"}

{block name="content"}
  <form ng-controller="WebPushNotificationsDashboardCtrl" ng-init="init()">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <a class="no-padding" href="[% routing.generate('backend_webpush_notifications_list') %]">
                  <i class="fa fa-tachometer m-r-10"></i>
                  {t}Web Push notifications Dashboard{/t}
                </a>
              </h4>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="grid simple">
        <div class="grid-body bg-transparent ng-cloak">
            <div class="row">
              <div class="col-xs-3">
                <div class="showcase-info showcase-info-score showcase-info-top showcase-info-height-auto panel onm-shadow m-b-0 p-15 bg-white">
                  <label class="form-label">{t}Active Subscribers{/t}</label>
                  <div class="form-status text-left">
                    <p class="onm-score text-left lead m-b-0">
                      <strong>[% settings.webpush_active_subscribers[0] %]</strong>
                    </p>
                  </div>
                </div>
              </div>
              <div class="col-xs-3">
                <div class="showcase-info showcase-info-score showcase-info-top showcase-info-height-auto panel onm-shadow m-b-0 p-15 bg-white">
                  <label class="form-label">
                    {t}Impressions{/t} <small class="form-label">({t}Monthly{/t})</small>
                    <i class="fa fa-info-circle text-info pull-right" uib-tooltip-html="'{t}Times a notification was{/t}<br>{t}displayed to the user{/t}'" tooltip-placement="bottom"></i>
                  </label>
                  <div class="form-status text-left">
                    <p class="onm-score text-left lead m-b-0">
                        <strong>[% monthlyImpressions %]</strong>
                    </p>
                  </div>
                </div>
              </div>
              <div class="col-xs-3">
                <div class="showcase-info showcase-info-score showcase-info-top showcase-info-height-auto panel onm-shadow m-b-0 p-15 bg-white">
                  <label class="form-label">
                    {t}Interactions{/t} <small class="form-label">({t}Monthly{/t})</small>
                    <i class="fa fa-info-circle text-info pull-right" uib-tooltip-html="'{t}Times a notification was{/t}<br>{t}clicked or closed{/t}'" tooltip-placement="bottom"></i>
                  </label>
                  <div class="form-status text-left">
                    <p class="onm-score text-left lead m-b-0">
                      <strong>[% monthlyInteractions %]</strong>
                    </p>
                  </div>
                </div>
              </div>
              <div class="col-xs-3">
                <div class="showcase-info showcase-info-score showcase-info-top showcase-info-height-auto panel onm-shadow m-b-0 p-15 bg-white">
                  <label class="form-label">
                    {t}CTR{/t} <small class="form-label">({t}Monthly{/t})</small>
                    <i class="fa fa-info-circle text-info pull-right" uib-tooltip-html="'{t}Interactions (Clicks + Closed){/t}<br>{t}divided by Impressions{/t}'" tooltip-placement="bottom"></i>
                  </label>
                  <div class="form-status text-left">
                    <p class="onm-score text-left lead m-b-0">
                      <strong>[% monthlyCTR %]%</strong>
                    </p>
                  </div>
                </div>
              </div>
            </div>
        <div class="row m-t-15">
          <div class="col-xs-12">
            <div class="panel bg-white onm-shadow">
              <div class="panel-heading">
                <div class="lead">{t}New Active Subscribers{/t}</div>
              </div>
              <div class="panel-body">
                <canvas id="myChart" class="chart chart-line" chart-data="data" chart-labels="labels" chart-series="series" chart-options="options"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
{/block}
