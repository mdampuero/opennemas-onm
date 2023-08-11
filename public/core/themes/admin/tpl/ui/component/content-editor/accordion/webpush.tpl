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
      <button class="btn btn-default ng-scope m-b-5" ng-click="sendWPNotification(item)" type="button"><i class="fa fa-bell m-r-5"></i>{t}Send notification{/t}</button>
      </div>
      <div class="grid-collapse-title m-t-5" ng-repeat="notification in item.webpush_notifications.slice().reverse()">
        <span ng-if="notification.status === 0">
          <i class="fa fa-hourglass text-warning"></i>
          {t}Notification will be sent on: [% notification.send_date %]{/t} ({t}Pending{/t})
        </span>
        <span ng-if="notification.status === 1">
          <i class="fa fa-paper-plane text-success"></i>
          {t}Notification sent on: [% notification.send_date %]{/t} ({t}Sent{/t})
        </span>
        <span ng-if="notification.status === 2">
          <i class="fa fa-exclamation-triangle text-danger"></i>
          {t}Notification error on: [% notification.send_date %]{/t} ({t}Not sent{/t})
        </span>
      </div>
    </div>
    <div ng-if="!item.is_notified_check && item.content_status" class="checkbox">
      <input name="is_notified" id="is_notified" ng-false-value="'0'" ng-model="item.is_notified" ng-true-value="'1'" type="checkbox">
      <label for="is_notified">{t}Activate send notifications to subscribers/Activate notifications{/t}</label>
      <small><i class="fa fa-info-circle text-info"></i> {t}Will be sent when it is published.{/t}</small>
    </div>

  </div>
</div>




