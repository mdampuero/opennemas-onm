{extends file="base/admin.tpl"}
{block name="content"}
  <form name="form" ng-controller="EventConfigCtrl" ng-init="init()">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-tags m-r-10"></i>
              </h4>
            </li>
            <li class="quicklinks">
              <h4>
                <a class="no-padding" href="[% routing.generate('backend_events_list') %]">
                  {t}Events{/t}
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
          <div class="ng-cloak pull-right" ng-if="!flags.http.loading">
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
      <div class="listing-no-contents" ng-hide="!flags.http.loading">
        <div class="text-center p-b-15 p-t-15">
          <i class="fa fa-4x fa-circle-o-notch fa-spin text-info"></i>
          <h3 class="spinner-text">{t}Loading{/t}...</h3>
        </div>
      </div>
      <div class="grid simple ng-cloak" ng-show="!flags.http.loading">
        <div class="grid-body">
          <div class="form-group">
            <div class="checkbox">
              <input id="hide_current_events" name="hide-current-events" type="checkbox" ng-model="config.hide_current_events">
              <label class="form-label" for="hide_current_events">
                <span class="checkbox-title">{t}Hide ongoing events{/t}</span>
                <div class="help">
                  {t}If set, only events starting the day after the current date will be displayed.{/t}
                </div>
              </label>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
{/block}
