{extends file="base/admin.tpl"}

{block name="content"}
  <form ng-controller="SubscriberSettingsCtrl">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <a class="no-padding" href="[% routing.generate('backend_subscribers_list') %]">
                  <i class="fa fa-user"></i>
                  {t}Subscribers{/t}
                </a>
              </h4>
            </li>
            <li class="quicklinks hidden-xs">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks hidden-xs">
              <h5>{t}Settings{/t}</h5>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <button class="btn btn-loading btn-success text-uppercase" ng-click="save()" type="button">
                  <span class="fa" ng-class="{ 'fa-save': !saving, 'fa-circle-o-notch fa-spin': saving }"></span>
                  <span class="text">{t}Save{/t}</span>
                </button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="listing-no-contents" ng-hide="!flags.loading">
        <div class="text-center p-b-15 p-t-15">
          <i class="fa fa-4x fa-circle-o-notch fa-spin text-info"></i>
          <h3 class="spinner-text">{t}Loading{/t}...</h3>
        </div>
      </div>
      <div class="listing-no-contents ng-cloak" ng-if="!flags.loading && items.length == 0">
        <div class="text-center p-b-15 p-t-15">
          <i class="fa fa-4x fa-warning text-warning"></i>
          <h3>{t}Unable to find setting for subscribers.{/t}</h3>
        </div>
      </div>
      <div class="grid simple ng-cloak" ng-if="!flags.loading && settings">
        <div class="grid-body">
          <h4 class="no-margin m-b-15">{t}Extra fields{/t}</h4>
          <p class="m-b-15">{t}This fields will be asked during registration and can be edited in the control panel.{/t}</p>
          <div class="row" ng-repeat="field in settings.fields track by $index">
            <div class="form-group col-md-2">
              <label class="form-label" for="label-[% $index %]-name">{t}Internal name{/t}</label>
              <div class="controls">
                <input class="form-control" ng-model="field.name" type="text">
              </div>
            </div>
            <div class="form-group col-md-2">
              <label class="form-label" for="label-[% $index %]-title">{t}Name{/t}</label>
              <div class="controls">
                <input class="form-control" ng-model="field.title" type="text">
              </div>
            </div>
            <div class="form-group col-md-2">
              <label class="form-label" for="type-[% $index %]">{t}Type{/t}</label>
              <div class="controls">
                <select class="form-control" id="type-[% $index %]" ng-model="field.type">
                  <option value="text">{t}Text{/t}</option>
                  <option value="date">{t}Date{/t}</option>
                  <option value="country">{t}Country{/t}</option>
                  <option value="options">{t}Options{/t}</option>
                </select>
              </div>
            </div>
            <div class="form-group col-md-6">
              <div class="pull-left">
                <label class="form-label">&nbsp;</label>
                <div class="controls">
                  <button class="btn btn-danger" ng-click="removeField($index)">
                    <i class="fa fa-trash-o"></i>
                  </button>
                </div>
              </div>
              <div class="m-l-15 pull-left" ng-if="field.type === 'options'">
                <label class="form-label">{t}Options{/t}</label>
                <span class="help">{t}Comma separated list of keys and value (key1:value1, key2:value2,...){/t}</span>
                <div class="controls">
                  <input class="form-control" id="options-[% index %]" ng-model="field.values" type="text">
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-2 col-md-offset-2">
              <button class="btn btn-block btn-success" ng-click="addField()">
                <i class="fa fa-plus m-r-5"></i>
                {t}Add{/t}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
{/block}
