<div class="page-navbar actions-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="quicklinks">
          <h4>
            <a class="no-padding" ng-href="[% routing.ngGenerate('manager_notifications_list') %]">
              <i class="fa fa-bell"></i>
              {t}Notifications{/t}
            </a>
          </h4>
        </li>
        <li class="quicklinks seperate">
          <span class="h-seperate"></span>
        </li>
        <li class="quicklinks">
          <h5>
            <span ng-if="!notification.id">{t}New notification{/t}</span>
            <span ng-if="notification.id">{t}Edit notification{/t}</span>
          </h5>
        </li>
      </ul>
      <div class="all-actions pull-right">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <a class="btn btn-link" ng-href="[% routing.ngGenerate('manager_notifications_list') %]">
              <i class="fa fa-reply"></i>
            </a>
          </li>
          <li class="quicklinks">
            <span class="h-seperate"></span>
          </li>
          <li class="quicklinks">
            <button class="btn btn-primary" ng-click="save();" ng-disabled="saving" ng-if="!notification.id">
              <i class="fa fa-save" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Save{/t}
            </button>
            <button class="btn btn-primary" ng-click="update();" ng-disabled="saving" ng-if="notification.id">
              <i class="fa fa-save" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Save{/t}
            </button>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
<div class="content">
  <form name="notificationForm" novalidate>
    <div class="row">
      <div class="col-md-8">
        <div class="grid simple">
          <div class="grid-body notification-form no-padding">
            <div class="row p-l-15 p-r-15 p-t-15">
              <div class="col-lg-6">
                <div class="form-group">
                  <label for="template" class="form-label">{t}Type{/t}</label>
                  <div class="controls">
                    <select id="style" ng-model="notification.type" ng-options="value.value as value.name for (key, value) in extra.types"></select>
                  </div>
                </div>
                <div class="form-group">
                  <label for="template" class="form-label">{t}Style{/t}</label>
                  <div class="controls">
                    <select id="style" ng-model="notification.style" ng-options="key as value.name for (key, value) in extra.styles"></select>
                  </div>
                </div>
                <div class="form-group">
                  <label for="template" class="form-label">{t}Instance{/t}</label>
                  <div class="controls">
                    <select id="style" ng-model="notification.instance_id" ng-options="value.value as value.name for (key, value) in extra.instances"></select>
                  </div>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <div class="controls">
                    <div class="checkbox">
                      <input id="fixed" name="fixed" ng-model="notification.fixed" ng-false-value="'0'" ng-true-value="'1'" type="checkbox">
                      <label for="fixed">{t}Fixed{/t} ({t}Notification always visible{/t})</label>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label class="form-label">{t}Starts{/t}</label>
                  <div class="controls">
                    <input class="form-control" datetime-picker ng-model="notification.start" placeholder="{t}Click to set date{/t}" type="datetime"/>
                  </div>
                </div>
                <div class="form-group">
                  <label class="form-label">{t}Ends{/t}</label>
                  <div class="controls">
                    <input class="form-control" datetime-picker ng-model="notification.end" placeholder="{t}Click to set date{/t}" type="datetime"/>
                  </div>
                </div>
              </div>
            </div>
            <ul class="fake-tabs">
              <li ng-repeat="(key, value) in languages" ng-class="{ 'active': language === key }" ng-click="changeLanguage(key)">
                [% value%]
                <span class="orb" ng-class="{ 'orb-danger': countStringsLeft(key) > 0, 'orb-success': countStringsLeft(key) === 0 }">
                  <i class="fa fa-check" ng-if="countStringsLeft(key) === 0"></i>
                  <span ng-if="countStringsLeft(key) > 0">[% countStringsLeft(key) %]</span>
                </span>
              </li>
            </ul>
            <div class="row p-l-15 p-r-15 p-t-15">
              <div class="col-md-12">
                <div class="form-group">
                  <label class="form-label">
                    {t}Title{/t}
                    <span ng-show="notificationForm.name.$invalid">*</span>
                  </label>
                  <div class="controls" ng-class="{ 'error-control': formValidated && notificationForm.title[language].$invalid }">
                    <input class="form-control" id="name" name="name" ng-model="notification.title[language]" required type="text">
                  </div>
                  <span class="error" ng-show="formValidated && notificationForm.name.$invalid">
                    <label for="name" class="error">{t}This field is required{/t}</label>
                  </span>
                </div>
                <div class="form-group">
                  <label class="form-label">
                    {t}Body{/t}
                  </label>
                  <div class="controls" ng-class="{ 'error-control': formValidated && notificationForm.body[language].$invalid }">
                    <textarea class="form-control" onm-editor onm-editor-preset="simple" id="body" name="body" ng-model="notification.body[language]" rows="5"></textarea>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="grid simple">
          <div class="grid-title">
            <h4>
              {t}Preview{/t}
            </h4>
          </div>
          <div class="grid-body no-padding">
            <div class="notifications">
              <ul class="notification-list notification-list-preview">
                <li class="clearfix notification-list-item notification-list-item-[% notification.style ? notification.style : 'success' %]">
                  <div class="notification-title">
                    [% notification.title[language] %]
                    <span class="notification-list-item-close pull-right pointer" ng-if="notification.fixed == 0">
                      <i class="fa fa-times"></i>
                    </span>
                  </div>
                  <div class="notification-icon">
                    <i class="fa" ng-class="{ 'fa-comment': notification.type === 'comment', 'fa-database': notification.type === 'media', 'fa-envelope': notification.type === 'email', 'fa-support': notification.type === 'help', 'fa-info': notification.type !== 'comment' && notification.type !== 'media' && notification.type !== 'email' && notification.type !== 'help' && notification.type !== 'user', 'fa-users': notification.type === 'user' }"></i>
                  </div>
                  <div class="notification-body">
                    <div ng-bind-html="notification.body[language]"></div>
                  </div>
                </li>
              </ul>
            </div>
            <div class="tiles grey">
              <div class="tiles-body p-t-15 p-r-30 p-b-15">
                <ul class="cbp_tmtimeline">
                  <li>
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
                          [% notification.title[language] %]
                        </h4>
                        <div class="text-default" ng-bind-html="notification.body[language]"></div>
                      </div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
