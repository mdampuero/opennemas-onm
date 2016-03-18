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
            <button class="btn btn-success text-uppercase" ng-click="!notification.id ? save() : update()" ng-disabled="saving">
              <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i> {t}Save{/t}
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
              <div class="col-md-5">
                <div class="form-group">
                  <label for="template" class="form-label">{t}Icon{/t}</label>
                  <div class="controls dropdown">
                    <button class="btn btn-white dropdown-toggle" data-toggle="dropdown" type="button">
                      <span ng-if="!notification.style.icon">{t}Pick an icon...{/t}</span>
                      <span ng-if="notification.style.icon">
                        <i class="fa fa-lg fa-[% notification.style.icon %]"></i>
                      </span>
                      <i class="fa fa-caret-down m-l-10"></i>
                    </button>
                    <ul class="dropdown-menu no-padding">
                      <li ng-click="notification.style.icon = null">
                        <span class="fake-a text-center">
                          {t}No icon{/t}
                        </span>
                      </li>
                      <li ng-repeat="icon in extra.icons" ng-click="notification.style.icon = icon.value">
                        <span class="fake-a text-center">
                          <i class="fa fa-2x fa-[% icon.value %] m-r-5"></i>
                        </span>
                      </li>
                    </ul>
                  </div>
                </div>
                <div class="form-group">
                  <label for="template" class="form-label">{t}Background{/t}</label>
                  <div class="controls">
                    <div class="input-group">
                      <span class="dropdown input-group-btn">
                        <button class="btn btn-white dropdown-toggle" data-toggle="dropdown" style="background-color: [% notification.style.background_color %] !important;" type="button">
                          &nbsp;&nbsp;&nbsp;&nbsp;
                        </button>
                        <ul class="dropdown-menu dropdown-menu-colors no-padding">
                          <li ng-click="notification.style.background_color='#0aa699'" style="background-color: #0aa699;"></li>
                          <li ng-click="notification.style.background_color='#fdd01c'" style="background-color: #fdd01c;"></li>
                          <li ng-click="notification.style.background_color='#f35958'" style="background-color: #f35958;"></li>
                          <li ng-click="notification.style.background_color='#0090d9'" style="background-color: #0090d9;"></li>
                          <li ng-click="notification.style.background_color='#d1dade'" style="background-color: #d1dade;"></li>
                          <li ng-click="notification.style.background_color='#0dd6c5'" style="background-color: #0dd6c5;"></li>
                          <li ng-click="notification.style.background_color='#fdda4f'" style="background-color: #fdda4f;"></li>
                          <li ng-click="notification.style.background_color='#f68888'" style="background-color: #f68888;"></li>
                          <li ng-click="notification.style.background_color='#0daeff'" style="background-color: #0daeff;"></li>
                          <li ng-click="notification.style.background_color='#e5e5e5'" style="background-color: #e5e5e5;"></li>
                          <li ng-click="notification.style.background_color='#3cf3ea'" style="background-color: #3cf3ea;"></li>
                          <li ng-click="notification.style.background_color='#feea9a'" style="background-color: #feea9a;"></li>
                          <li ng-click="notification.style.background_color='#fccfcf'" style="background-color: #fccfcf;"></li>
                          <li ng-click="notification.style.background_color='#5ac7ff'" style="background-color: #5ac7ff;"></li>
                          <li ng-click="notification.style.background_color='#e5e9ec'" style="background-color: #e5e9ec;"></li>
                        </ul>
                      </span>
                      <input class="form-control" colorpicker="hex" ng-model="notification.style.background_color" type="text">
                      <div class="input-group-btn">
                        <button class="btn btn-default" ng-click="notification.style.background_color= null" type="button">{t}Reset{/t}</button>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="template" class="form-label">{t}Font color{/t}</label>
                  <div class="controls">
                    <div class="input-group">
                      <span class="dropdown input-group-btn">
                        <button class="btn btn-white dropdown-toggle" data-toggle="dropdown" style="background-color: [% notification.style.font_color %] !important;" type="button">
                          &nbsp;&nbsp;&nbsp;&nbsp;
                        </button>
                        <ul class="dropdown-menu dropdown-menu-colors no-padding">
                          <li ng-click="notification.style.font_color='#0aa699'" style="background-color: #0aa699;"></li>
                          <li ng-click="notification.style.font_color='#fdd01c'" style="background-color: #fdd01c;"></li>
                          <li ng-click="notification.style.font_color='#f35958'" style="background-color: #f35958;"></li>
                          <li ng-click="notification.style.font_color='#0090d9'" style="background-color: #0090d9;"></li>
                          <li ng-click="notification.style.font_color='#d1dade'" style="background-color: #d1dade;"></li>
                          <li ng-click="notification.style.font_color='#0dd6c5'" style="background-color: #0dd6c5;"></li>
                          <li ng-click="notification.style.font_color='#fdda4f'" style="background-color: #fdda4f;"></li>
                          <li ng-click="notification.style.font_color='#f68888'" style="background-color: #f68888;"></li>
                          <li ng-click="notification.style.font_color='#0daeff'" style="background-color: #0daeff;"></li>
                          <li ng-click="notification.style.font_color='#e5e5e5'" style="background-color: #e5e5e5;"></li>
                          <li ng-click="notification.style.font_color='#3cf3ea'" style="background-color: #3cf3ea;"></li>
                          <li ng-click="notification.style.font_color='#feea9a'" style="background-color: #feea9a;"></li>
                          <li ng-click="notification.style.font_color='#fccfcf'" style="background-color: #fccfcf;"></li>
                          <li ng-click="notification.style.font_color='#5ac7ff'" style="background-color: #5ac7ff;"></li>
                          <li ng-click="notification.style.font_color='#e5e9ec'" style="background-color: #e5e9ec;"></li>
                        </ul>
                      </span>
                      <input class="form-control" colorpicker="hex" ng-model="notification.style.font_color" type="text">
                      <div class="input-group-btn">
                        <button class="btn btn-default" ng-click="notification.style.font_color= null" type="button">{t}Reset{/t}</button>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="controls">
                    <div class="checkbox">
                      <input id="fixed" name="fixed" ng-model="notification.fixed" ng-false-value="0" ng-true-value="1" type="checkbox">
                      <label for="fixed">{t}Fixed{/t} ({t}Notification always visible in dropdown{/t})</label>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="controls">
                    <div class="checkbox">
                      <input id="forced" name="fixed" ng-model="notification.forced" ng-false-value="0" ng-true-value="1" type="checkbox">
                      <label for="forced">{t}Forced{/t} ({t}Notification always visible before content{/t})</label>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6 col-md-offset-1">
                <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label class="form-label">{t}Starts{/t}</label>
                      <div class="controls">
                        <input class="form-control" datetime-picker ng-model="notification.start" placeholder="{t}Click to set date{/t}" type="datetime"/>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label class="form-label">{t}Ends{/t}</label>
                      <div class="controls">
                        <input class="form-control" datetime-picker ng-model="notification.end" placeholder="{t}Click to set date{/t}" type="datetime"/>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="controls">
                    <div class="checkbox">
                      <input id="users" name="users" ng-model="notification.users" ng-false-value="'0'" ng-true-value="'1'" type="checkbox">
                      <label for="users">{t}Show only for admins{/t}</label>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="template" class="form-label">{t}Instance{/t}</label>
                  <div class="controls">
                    <tags-input add-from-autocomplete-only="true" ng-model="notification.instances" display-property="name" >
                      <auto-complete source="test($query)" min-length="0" load-on-focus="true" load-on-empty="true"></auto-complete>
                    </tags-input>
                  </div>
                </div>
              </div>
            </div>
            <ul class="fake-tabs">
              <li ng-repeat="(key, value) in languages" ng-class="{ 'active': language === key }" ng-click="changeLanguage(key)">[% value%]</li>
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
                    <textarea class="form-control" onm-editor onm-editor-preset="standard" id="body" name="body" ng-model="notification.body[language]" rows="5"></textarea>
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
                <li class="clearfix notification-list-item [% notification.style.icon ? 'notification-list-item-with-icon' : '' %]" ng-style="{ 'background-color': notification.style.background_color, 'border-color': notification.style.background_color }">
                  <span class="notification-list-item-close pull-right pointer" ng-if="notification.fixed == 0 && notification.forced == 0">
                    <i class="fa fa-times" style="color: [% notification.style.font_color %] !important;"></i>
                  </span>
                  <a>
                    <div class="notification-icon" ng-if="notification.style.icon" ng-style="{ 'background-color': notification.style.font_color, 'color': notification.style.background_color }">
                      <i class="fa fa-[% notification.style.icon %]"></i>
                    </div>
                    <div class="notification-body" ng-style="{ 'color': notification.style.font_color }">
                      <div ng-bind-html="notification.title[language] ? notification.title[language] : notification.notification.body[language]"></div>
                    </div>
                  </a>
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
                    <div class="cbp_tmicon animated bounceIn" ng-style="{ 'background-color': notification.style.background_color, 'color': notification.style.font_color }">
                      <i class="fa fa-[% notification.style.icon %]"></i>
                    </div>
                    <div class="cbp_tmlabel">
                      <div class="p-t-15 p-l-30 p-r-30 p-b-30">
                        <h4 ng-bind-html="notification.title[language]"></h4>
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
