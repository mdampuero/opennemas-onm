{extends file="base/admin.tpl"}

{block name="content"}
  <div ng-app="BackendApp" ng-controller="NotificationCtrl" ng-init="disableForced();list();">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-bell"></i>
                {t}Notifications{/t}
              </h4>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="spinner-wrapper" ng-if="loading">
        <div class="loading-spinner"></div>
        <div class="spinner-text">{t}Loading{/t}...</div>
      </div>
      <div class="row ng-cloak" ng-if="!loading">
        <div class="col-lg-8 col-md-10 col-sm-12">
          <ul class="cbp_tmtimeline">
            <li ng-repeat="notification in notifications">
              <time class="cbp_tmtime">
                <span class="date">[% notification.day %]</span>
                <span class="time">
                  [% notification.time %]
                  <strong>[% notification.am %]</strong>
                </span>
              </time>
              <div class="cbp_tmicon animated bounceIn" ng-style="{ 'background-color': notification.style.background_color }">
                <i class="fa fa-[% notification.style.icon %]" ng-style="{ 'color': notification.style.font_color }"></i>
              </div>
              <div class="cbp_tmlabel">
                <div class="p-t-15 p-l-30 p-r-30 p-b-30">
                  <h4 ng-bind-html="notification.title"></h4>
                  <div ng-bind-html="notification.body"></div>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <script type="text/ng-template" id="modal-delete">
      {include file="common/modals/_modalDelete.tpl"}
    </script>
    <script type="text/ng-template" id="modal-delete-selected">
      {include file="common/modals/_modalBatchDelete.tpl"}
    </script>
    <script type="text/ng-template" id="modal-update-selected">
      {include file="common/modals/_modalBatchUpdate.tpl"}
    </script>
  </div>
{/block}
