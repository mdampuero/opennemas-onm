{extends file="base/admin.tpl"}

{block name="content"}
  <form name="settingForm" ng-controller="InternalSettingsCtrl" ng-init="list()" class="settings">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-cube fa-lg"></i>
                {t}Internal{/t}
              </h4>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <button class="btn btn-loading btn-primary" ng-click="save()" ng-disabled="settingForm.$invalid" type="button">
                  <i class="fa fa-save" ng-class="{ 'fa-circle-o-notch fa-spin': saving}"></i>
                  <span class="text">{t}Save{/t}</span>
                </button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="content ng-cloak no-animate" ng-if="loading">
      <div class="spinner-wrapper">
        <div class="loading-spinner"></div>
        <div class="spinner-text">{t}Loading{/t}...</div>
      </div>
    </div>
    <div class="content ng-cloak" ng-if="!loading">
      <div class="grid simple settings">
        <div class="grid-body ng-cloak">
          <div class="row">
            <div class="col-md-6">
              <h4>
                <i class="fa fa-microphone"></i>
                {t}Opennemas News Agency{/t}
              </h4>
              <div class="row">
                <div class="col-md-6 form-group">
                  <label class="form-label" for="onm-digest-user">
                    {t}User{/t}
                  </label>
                  <div class="controls">
                    <div class="input-group">
                      <span class="input-group-addon">
                        <i class="fa fa-user"></i>
                      </span>
                      <input class="form-control" id="onm-digest-user" name="onm-digest-user" ng-model="settings.onm_digest_user" type="text">
                    </div>
                  </div>
                </div>
                <div class="col-md-6 form-group">
                  <label class="form-label" for="onm-digest-pass">
                    {t}Password{/t}
                  </label>
                  <div class="controls">
                    <div class="input-group">
                      <span class="input-group-btn">
                        <button class="btn btn-default" ng-click="onm_digest_pass_visible = !onm_digest_pass_visible" type="button">
                          <i class="fa fa-lock" ng-class="{ 'fa-unlock-alt': onm_digest_pass_visible }"></i>
                        </button>
                      </span>
                      <input class="form-control" id="onm-digest-pass" name="onm-digest-pass" ng-model="settings.onm_digest_pass" type="[% onm_digest_pass_visible ? 'text' : 'password' %]">
                    </div>
                  </div>
                </div>
              </div>
              {is_module_activated name="FORM_MANAGER"}
              <h4>
                <i class="fa fa-file-text-o"></i>
                {t}Form Module{/t}
              </h4>
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label class="form-label" for="contact_email">
                      {t}Contact email{/t}
                    </label>
                    <div class="controls">
                      <input class="form-control" id="contact_email" name="contact_email" ng-model="settings.contact_email" type="text">
                    </div>
                  </div>
                </div>
              </div>
              {/is_module_activated}
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
{/block}
