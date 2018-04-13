{extends file="base/admin.tpl"}

{block name="content"}
  <form name="form" ng-controller="UserGroupCtrl" ng-init="getItem({$id})">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-users m-r-10"></i>
              </h4>
            </li>
            <li class="quicklinks">
              <h4>
                <a class="no-padding" href="[% routing.generate('backend_user_groups_list') %]">
                  {t}User groups{/t}
                </a>
              </h4>
            </li>
            <li class="quicklinks hidden-xs m-l-5 m-r-5">
              <h4>
                <i class="fa fa-angle-right"></i>
              </h4>
            </li>
            <li class="quicklinks hidden-xs">
              <h4>
                {if empty($id)}{t}Create{/t}{else}{t}Edit{/t}{/if}
              </h4>
            </li>
          </ul>
          <div class="all-actions pull-right">
            <ul class="nav quick-section">
              <li class="quicklinks">
                <button class="btn btn-loading btn-success text-uppercase" ng-click="save()">
                  <i class="fa fa-save" ng-class="{ 'fa-circle-o-notch fa-spin': flags.http.saving }"></i>
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
      <div class="listing-no-contents ng-cloak" ng-if="!flags.http.loading && item === null">
        <div class="text-center p-b-15 p-t-15">
          <a href="[% routing.generate('backend_user_groups_list') %]">
            <i class="fa fa-4x fa-warning text-warning"></i>
            <h3>{t}Unable to find the item{/t}</h3>
            <h4>{t}Click here to return to the list{/t}</h4>
          </a>
        </div>
      </div>
      <div class="ng-cloak" ng-show="!flags.http.loading && item">
        <div class="row">
          <div class="col-md-4 col-md-push-8">
            <div class="grid simple">
              <div class="grid-body no-padding">
                <div class="grid-collapse-title">
                  <div class="checkbox">
                    <input class="form-control" id="enabled" name="enabled" ng-model="item.enabled" ng-true-value="1" ng-false-value="0" type="checkbox">
                    <label for="enabled" class="form-label">
                      {t}Enabled{/t}
                    </label>
                  </div>
                </div>
                <div class="grid-collapse-title ng-cloak pointer" ng-class="{ 'open': expanded.visibility }" ng-click="expanded.visibility = !expanded.visibility">
                  <i class="fa fa-eye m-r-5"></i>
                  {t}Visibility{/t}
                  <i class="fa fa-chevron-right pull-right m-t-5" ng-class="{ 'fa-rotate-90': expanded.visibility }"></i>
                  <span class="badge badge-default m-r-10 m-t-2 ng-cloak pull-right text-uppercase text-bold" ng-show="!expanded.visibility">
                    <span ng-show="item.private">{t}Private{/t}</span>
                    <span ng-show="!item.private">{t}Public{/t}</span>
                  </span>
                </div>
                <div class="grid-collapse-body ng-cloak" ng-class="{ 'expanded': expanded.visibility }">
                  <div class="form-group no-margin">
                    <div class="checkbox">
                      <input class="form-control" id="private" name="private" ng-false-value="0" ng-model="item.private" ng-true-value="1" type="checkbox">
                      <label for="private" class="form-label">
                        {t}Private{/t}
                      </label>
                    </div>
                    <span class="help m-l-3 m-t-5" ng-show="isHelpEnabled()">
                      <i class="fa fa-info-circle m-r-5 text-info"></i>
                      {t}If enabled, this user group will not be visible in some circunstances{/t}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-8 col-md-pull-4">
            <div class="grid simple">
              <div class="grid-body">
                <div class="form-group">
                  <label for="name" class="form-label">{t}Name{/t}</label>
                  <div class="controls">
                    <input class="form-control" id="name" name="name" ng-model="item.name" required type="text">
                  </div>
                </div>
              </div>
            </div>
            <div class="grid simple">
              <div class="grid-title">
                <h4>{t}Privileges{/t}</h4>
              </div>
              <div class="grid-body" id="privileges">
                <div class="checkbox check-default check-title">
                  <input id="checkbox-all" ng-change="selectAll()" ng-checked="areAllSelected()" ng-model="selected.allSelected" type="checkbox">
                  <label for="checkbox-all">
                    <h5 class="semi-bold text-uppercase">{t}Toggle all privileges{/t}</h5>
                  </label>
                </div>
                <div class="ng-cloak">
                  <div ng-repeat="section in sections">
                    <h5 class="m-t-30 semi-bold text-uppercase">[% section.title %]</h5>
                    <div class="row" ng-repeat="columns in section.rows">
                      <div class="col-sm-3" ng-repeat="name in columns">
                        <div class="col-sm-12 m-b-10">
                          <div class="checkbox check-default check-title">
                            <input id="checkbox-[% name %]" ng-change="selectModule(name)" ng-checked="isModuleSelected(name)" ng-model="selected.all[name]" type="checkbox">
                            <label for="checkbox-[% name %]">
                              <h5 class="semi-bold">[% name %]</h5>
                            </label>
                          </div>
                        </div>
                        <div class="col-sm-12 m-b-5" ng-repeat="privilege in data.extra.modules[name]">
                          <div class="checkbox check-default">
                            <input id="checkbox-[% name + '-' + $index %]" checklist-model="item.privileges" checklist-value="privilege.id" type="checkbox">
                            <label for="checkbox-[% name + '-' + $index %]">
                              [% privilege.description %]
                            </label>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
{/block}
