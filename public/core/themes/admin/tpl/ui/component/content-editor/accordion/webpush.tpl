<div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.webpush = !expanded.webpush" ng-if="!hasMultilanguage()">
  <i class="fa fa-bell m-r-10"></i>{t}Webpush Notifications{/t}
  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.webpush }"></i>
  <i ng-if="item.is_notified == 1" class="fa fa-check fa-lg m-r-10 text-success pull-right"></i>
</div>
<div class="grid-collapse-body ng-cloak" ng-if="!hasMultilanguage()" ng-class="{ 'expanded': expanded.webpush }">
  <div class="form-group no-margin">
    <div class="text-center m-b-5 m-t-5" ng-if="!item.content_status">
      <small><i class="fa fa-info-circle text-info"></i> {t}Check it as "Published" to send webpush notifications.{/t}</small>
    </div>
    <div>
      <div class="text-center" ng-if="item.content_status">
        <button class="btn btn-mini btn-block ng-scope m-b-5 btn-success" ng-click="openNotificationModal(item, true)" ng-if="!hasPendingNotifications() && getContentScheduling(item) == 0 || getContentScheduling(item) == -1" type="button"><i class="fa fa-paper-plane m-r-5"></i>{t}SEND NOTIFICATION{/t}</button>
        <button class="btn btn-mini btn-block ng-scope m-b-5 btn-success" ng-click="openNotificationModal(item, false)" ng-if="!hasPendingNotifications() && getContentScheduling(item) == 1" type="button"><i class="fa fa-paper-plane m-r-5"></i>{t}ADD SCHEDULED NOTIFICATION{/t}</button>
        <button class="btn btn-mini btn-block ng-scope m-b-5 btn-success" ng-click="openNotificationModal(item, true)" ng-if="hasPendingNotifications() && getContentScheduling(item) == 0 || getContentScheduling(item) == -1" type="button"><i class="fa fa-paper-plane m-r-5"></i>{t}SEND SCHEDULED NOTIFICATION{/t}</button>
        <button class="btn btn-mini btn-block ng-scope m-b-5 btn-success" ng-click="openNotificationModal(item, false)" ng-if="hasPendingNotifications() && getContentScheduling(item) == 1" type="button"><i class="fa fa-paper-plane m-r-5"></i>{t}CHANGE SCHEDULED NOTIFICATION{/t}</button>
      </div>
      <div class="menu-dragable-accordion" id="webpush-container">
        <div class=" m-t-5" ng-repeat="notification in item.webpush_notifications.slice().reverse()">
          <div ng-if="notification.status === 0" class="alert alert-warning">
          <i class="fa fa-clock-o"></i>
            {t}Notification scheduled{/t}
            <small>
              [% notification.send_date | moment : 'YYYY-MM-DD HH:mm:ss': null : '{$app.locale->getTimeZone()->getName()}' %]
            </small>
            <button type="button" class="close" data-dismiss="alert" ng-click="removePendingNotification(true)" >
            </button>
          </div>
          <div ng-if="notification.status === 1" class="alert alert-success" id="alerteo">
            <i class="fa fa-check"></i>
            {t}Notification sent{/t}
            <small>
              [% notification.send_date | moment : 'YYYY-MM-DD HH:mm:ss': null : '{$app.locale->getTimeZone()->getName()}' %]
            </small>
          </div>
          <div ng-if="notification.status === 2" class="alert alert-danger">
            <i class="fa fa-exclamation-triangle"></i>
            {t}Notification failed{/t}
            <small>
              [% notification.send_date | moment : 'YYYY-MM-DD HH:mm:ss': null : '{$app.locale->getTimeZone()->getName()}' %]
            </small>
        </div>
      </div>
    </div>
  </div>
</div>




