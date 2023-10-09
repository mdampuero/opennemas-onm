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
              <div class="col-md-3">
              {t}Dashboard{/t}
              </div>
            </div>
        </div>
      </div>
    </div>
  </form>
{/block}
