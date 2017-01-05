<div class="page-navbar actions-navbar">
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <ul class="nav quick-section">
        <li class="quicklinks">
          <h4>
            <a class="no-padding" ng-href="[% routing.ngGenerate('manager_settings_list') %]">
              <i class="fa fa-flip-horizontal fa-gears"></i>
              {t}Settings{/t}
            </a>
          </h4>
        </li>
      </ul>
      <div class="all-actions pull-right">
        <ul class="nav quick-section">
          <li class="quicklinks" ng-if="security.hasPermission('SETTING_UPDATE')">
            <button class="btn btn-loading btn-success text-uppercase" ng-click="save();">
              <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': saving }"></i>
              {t}Save{/t}
            </button>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
<div class="content ng-hide" ng-show="settings">
  <div class="row">
    <div class="col-lg-4">
      <div class="grid simple">
        <div class="grid-title">
          <h4>{t}Date & Time{/t}</h4>
        </div>
        <div class="grid-body">
          <div class="form-group">
            <label class="form-label" for="time_zone">{t}Time Zone{/t}</label>
            <div class="controls">
              <select class="form-control" id="time_zone" name="time_zone" ng-model="settings.time_zone">
                <option ng-repeat="(key, name) in extra.time_zones" ng-selected="key == settings.time_zone" value="[% key %]" >[% name %]</option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="grid simple">
        <div class="grid-title">
          <h4>{t}Language{/t}</h4>
        </div>
        <div class="grid-body">
          <div class="form-group">
            <label class="form-label" for="site_language">{t}Site language{/t}</label>
            <div class="controls">
              <select class="form-control" id="site_language" name="site_language" ng-model="settings.site_language">
                <option ng-repeat="(key, name) in extra.languages" ng-selected="[% key == settings.site_language %]" value="[% key %]">[% name %]</option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="grid simple">
        <div class="grid-title">
          <h4>{t}Image{/t}</h4>
        </div>
        <div class="grid-body">
          <div class="form-group">
            <label class="form-label" for="site_language">{t}Dimensions{/t}</label>
            <div class="controls row">
              <input class="pull-left" ng-model="settings.max_width" placeholder="{t}Width{/t}" type="text">
              <i class="fa fa-times m-l-5 m-r-5 m-t-10 pull-left"></i>
              <input class="m-r-5 pull-left" ng-model="settings.max_height" placeholder="{t}Width{/t}" type="text">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/ng-template" id="modal-confirm">
  {include file="common/modal_confirm.tpl"}
</script>
