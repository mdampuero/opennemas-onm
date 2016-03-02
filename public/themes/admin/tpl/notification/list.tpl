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
    <div class="page-navbar filters-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section filter-components">
            <li class="m-r-10 input-prepend inside search-input no-boarder">
              <span class="add-on">
                <span class="fa fa-search fa-lg"></span>
              </span>
              <input class="no-boarder" name="title" ng-model="criteria.title_like" ng-keyup="searchByKeypress($event)" placeholder="{t}Search by title{/t}" type="text"/>
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
        <div class="col-lg-11 col-md-10 col-sm-12">
          <ul class="cbp_tmtimeline">
            <li ng-repeat="notification in notifications">
              <time class="cbp_tmtime">
                <span class="date">[% notification.day %]</span>
                <span class="time">
                  [% notification.time %]
                  <strong>[% notification.am %]</strong>
                </span>
              </time>
              <div class="cbp_tmicon animated bounceIn" ng-class="{ 'danger': notification.style === 'error', 'primary': notification.style === 'success','success': notification.style === 'info','warning': notification.style === 'warning' }">
                <i class="fa" ng-class="{ 'fa-comment': notification.type === 'comment', 'fa-database': notification.type === 'media', 'fa-envelope': notification.type === 'email', 'fa-support': notification.type === 'help', 'fa-info': notification.type !== 'comment' && notification.type !== 'media' && notification.type !== 'email' && notification.type !== 'help' && notification.type !== 'user', 'fa-users': notification.type === 'user' }"></i>
              </div>
              <div class="cbp_tmlabel">
                <div class="p-t-15 p-l-30 p-r-30 p-b-30">
                  <h4>
                    [% notification.title %]
                  </h4>
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
