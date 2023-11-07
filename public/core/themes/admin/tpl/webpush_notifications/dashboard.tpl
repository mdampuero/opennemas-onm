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
        <div class="grid-body ng-cloak">
            <div class="row">
              <div class="col-xs-3">
                <div class="m-t-5">
                  <div class="showcase-info showcase-info-score panel m-b-0">
                    <div class="form-status text-center">
                      <p class="onm-score text-center lead m-b-5">
                        [% settings.webpush_active_subscribers[0] %]
                      </p>
                    </div>
                    <label class="form-label text-center m-t-10">{t}Active Subscribers{/t}</label>
                  </div>
                </div>
              </div>
              <div class="col-xs-3">
                <div class="m-t-5">
                  <div class="showcase-info showcase-info-score panel m-b-0">
                    <div class="form-status text-center">
                      <p class="onm-score text-center lead m-b-5">
                         [% monthlyImpressions %]
                      </p>
                    </div>
                    <label class="form-label text-center m-t-10">
                      {t}Impressions{/t}
                      <i class="fa fa-info-circle text-info" uib-tooltip-html="'{t}Times a notification was{/t}<br>{t}displayed to the user{/t}'" tooltip-placement="bottom"></i>
                      <br>
                      <small class="form-label text-center ">{t}Monthly{/t}</small>
                    </label>
                  </div>
                </div>
              </div>
              <div class="col-xs-3">
                <div class="m-t-5">
                  <div class="showcase-info showcase-info-score panel m-b-0">
                    <div class="form-status text-center">
                      <p class="onm-score text-center lead m-b-5">
                        [% monthlyInteractions %]
                      </p>
                    </div>
                      <label class="form-label text-center m-t-10">
                        {t}Interactions{/t}
                        <i class="fa fa-info-circle text-info" uib-tooltip-html="'{t}Times a notification was{/t}<br>{t}clicked or closed{/t}'" tooltip-placement="bottom"></i>
                        <br>
                        <small class="form-label text-center ">{t}Monthly{/t}</small>
                    </label>
                  </div>
                </div>
              </div>
              <div class="col-xs-3">
                <div class="m-t-5">
                  <div class="showcase-info showcase-info-score panel m-b-0">
                    <div class="form-status text-center">
                      <p class="onm-score text-center lead m-b-5">
                        [% monthlyCTR %]%
                      </p>
                    </div>
                      <label class="form-label text-center m-t-10">
                        {t}CTR{/t}
                        <i class="fa fa-info-circle text-info" uib-tooltip-html="'{t}Interactions (Clicks + Closed){/t}<br>{t}divided by Impressions{/t}'" tooltip-placement="bottom"></i>
                        <br>
                        <small class="form-label text-center ">{t}Monthly{/t}</small>
                    </label>
                  </div>
                </div>
              </div>
            </div>
        <div class="row m-t-30">
          <div class="col-xs-12">
            <div class="panel">
              <div class="panel-heading">{t}New Active Subscribers {/t}</div>
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
