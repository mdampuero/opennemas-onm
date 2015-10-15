<div class="page-navbar actions-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="quicklinks">
          <h4>
            <a ng-href="[% routing.ngGenerate('manager_notifications_list') %]">
              <i class="fa fa-bell fa-lg"></i>
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
          <div class="grid-title">
            <h4>
              <span class="semi-bold" ng-if="notification.id">
                [% notification.title %]
              </span>
              <span class="semi-bold" ng-if="!notification.id">
                {t}New notification{/t}
              </span>
            </h4>
          </div>
          <div class="grid-body notification-form">
            <div class="row">
              <div class="col-sm-12">
                <div class="form-group">
                  <label class="form-label">
                    {t}Title{/t}
                    <span ng-show="notificationForm.name.$invalid">*</span>
                  </label>
                  <div class="controls" ng-class="{ 'error-control': formValidated && notificationForm.title.$invalid }">
                    <input class="form-control" id="name" name="name" ng-model="notification.title" required type="text">
                  </div>
                  <span class="error" ng-show="formValidated && notificationForm.name.$invalid">
                    <label for="name" class="error">{t}This field is required{/t}</label>
                  </span>
                </div>
                <div class="row">
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label for="template" class="form-label">{t}Type{/t}</label>
                      <div class="controls">
                        <select id="style" ng-model="notification.type" ng-options="value.value as value.name for (key, value) in extra.types"></select>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <label for="template" class="form-label">{t}Style{/t}</label>
                      <div class="controls">
                        <select id="style" ng-model="notification.style" ng-options="value.value as value.name for (key, value) in extra.styles"></select>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label class="form-label">
                    {t}Body{/t}
                  </label>
                  <div class="controls" ng-class="{ 'error-control': formValidated && notificationForm.body.$invalid }">
                    <textarea class="form-control" onm-editor onm-editor-preset="simple" id="body" name="body" ng-model="notification.body" rows="5"></textarea>
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
              {t}Options{/t}
            </h4>
          </div>
          <div class="grid-body">
            <div class="form-group">
              <label for="template" class="form-label">{t}Instance{/t}</label>
              <div class="controls">
                <select id="style" ng-model="notification.instance_id" ng-options="value.value as value.name for (key, value) in extra.instances"></select>
              </div>
            </div>
            <div class="form-group">
              <div class="controls">
                <div class="checkbox">
                  <input id="fixed" name="fixed" ng-model="notification.fixed" ng-false-value="0" ng-true-value="1" type="checkbox">
                  <label for="fixed">{t}Fixed{/t} ({t}Notification always visible{/t})</label>
                </div>
              </div>
            </div>
            <div class="form-group" ng-if="notification.fixed == 0">
              <label class="form-label">{t}Starts{/t}</label>
              <div class="controls">
                <quick-datepicker icon-class="fa fa-clock-o" ng-model="notification.start" placeholder="{t}Click to set date{/t}"></quick-datepicker>
              </div>
            </div>
            <div class="form-group" ng-if="notification.fixed == 0">
              <label class="form-label">{t}Ends{/t}</label>
              <div class="controls">
                <quick-datepicker icon-class="fa fa-clock-o" ng-model="notification.end" placeholder="{t}Click to set date{/t}"></quick-datepicker>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
