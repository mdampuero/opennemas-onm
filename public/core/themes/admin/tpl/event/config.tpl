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
                  {t}If this option is enabled, only events that start or end on or after the current date will be displayed.{/t}
                </div>
              </label>
            </div>
          </div>
          <div class="form-group" ng-if="config.hide_current_events">
            <div>
              <label class="control-label">
                <i class="fa fa-clock-o" aria-hidden="true"></i>
                {t}Time period to hide ongoing events{/t}
              </label>
              <ui-select ng-init="month = [
                { name: '{t}Select period...{/t}', value: null },
                { name: '{t}1 Month{/t}', value: 1 },
                { name: '{t}3 Months{/t}', value: 3 },
                { name: '{t}6 Months{/t}', value: 6 },
                { name: '{t}12 Months{/t}', value: 12 }
              ]" ng-model="config.hide_events_month" theme="select2" class="form-control">
                <ui-select-match>
                  <span ng-show="!$select.selected">
                    <i class="fa fa-calendar-o text-muted"></i>
                    {t}Select period...{/t}
                  </span>
                  <span ng-show="$select.selected">
                    <i class="fa fa-check text-success"></i>
                    <strong>[% $select.selected.name %]</strong>
                  </span>
                </ui-select-match>
                <ui-select-choices repeat="item.value as item in month | filter: { name: $select.search }">
                  <div ng-bind-html="item.name | highlight: $select.search"></div>
                </ui-select-choices>
              </ui-select>
              <small class="help-block text-muted">
                <i class="fa fa-info-circle"></i>
                {t}This option will hide events that start or end before the current date plus the selected period.{/t}
              </small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
{/block}
