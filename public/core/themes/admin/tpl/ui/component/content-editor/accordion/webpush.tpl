<div class="grid-collapse-title ng-cloak pointer" ng-click="expanded.webpush = !expanded.webpush">
  <i class="fa fa-bell m-r-10"></i>{t}Webpush Notifications{/t}
  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.webpush }"></i>
</div>
<div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.webpush }">
  <div class="form-group no-margin">
    <div class="text-center m-b-5 m-t-5" ng-if="!item.content_status">
        <small><i class="fa fa-info-circle text-info"></i> {t}Check it as "Published" to send webpush notifications.{/t}</small>
      </div>
    <div ng-if="item.is_notified_check">
      <div class="text-center" ng-if="item.content_status">
      <button class="btn btn-mini btn-block ng-scope m-b-5 btn-success" ng-click="sendWPNotification(item)" type="button"><i class="fa fa-paper-plane m-r-5"></i>{t}SEND NOW{/t}</button>
      </div>
      <div class="notifications-container">
        <div class=" m-t-5" ng-repeat="notification in item.webpush_notifications.slice().reverse()">
          <div ng-if="notification.status === 0" class="alert alert-warning">
          <i class="fa fa-clock-o"></i>
            {t}Notification scheduled{/t}
            <br>
            <small>
              [% notification.send_date %]
            </small>
          </div>
          <div ng-if="notification.status === 1" class="alert alert-success" id="alerteo">
            <i class="fa fa-check"></i>
            {t}Notification sent{/t}
            <br>
            <small>
              [% notification.send_date %]
            </small>
          </div>
          <div ng-if="notification.status === 2" class="alert alert-danger">
            <i class="fa fa-exclamation-triangle"></i>
            {t}Notification failed{/t}
            <br>
            <small>
              [% notification.send_date %]
            </small>
        </div>
      </div>
    </div>
    <div ng-if="!item.is_notified_check && item.content_status" class="checkbox">
      <input name="is_notified" id="is_notified" ng-false-value="'0'" ng-model="item.is_notified" ng-true-value="'1'" type="checkbox">
      <label for="is_notified">{t}Activate send notifications to subscribers/Activate notifications{/t}</label>
      <small><i class="fa fa-info-circle text-info"></i> {t}Will be sent when it is published.{/t}</small>
    </div>
  </div>
</div>




