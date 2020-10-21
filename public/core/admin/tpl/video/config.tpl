{extends file="base/admin.tpl"}

{block name="content"}
<form name="form" ng-controller="VideoConfigCtrl" ng-init="init()" ng-submit="save()">
  <div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
      <div class="navbar-inner">
        <ul class="nav quick-section">
          <li class="quicklinks">
            <h4>
              <a class="no-padding" href="[% routing.generate('backend_videos_list') %]">
                <i class="fa fa-quote-right"></i>
                {t}Videos{/t}
              </a>
            </h4>
          </li>
          <li class="quicklinks hidden-xs m-l-5 m-r-5">
            <h4>
              <i class="fa fa-angle-right"></i>
            </h4>
          </li>
          <li class="quicklinks">
            <h4>{t}Configuration{/t}</h4>
          </li>
        </ul>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <button class="btn btn-loading btn-success text-uppercase" ng-click="save($event)" ng-disabled="flags.http.loading || flags.http.saving" type="button">
                <i class="fa fa-save m-r-5" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.saving }"></i>
                {t}Save{/t}
              </button>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="content">
    <div class="grid simple">
      <div class="grid-body">
        <div class="form-group">
          <label for="settings.total_front_more" class="form-label">{t}Number of videos in frontpage{/t}</label>
          <span class="help">{t}Total number of videos to show per page in the frontpage{/t}</span>
          <div class="controls">
            <input type="number" name="settings[total_front_more]" ng-model="settings.total_front_more" required />
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
{/block}
