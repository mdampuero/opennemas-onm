{extends file="base/admin.tpl"}

{block name="content"}
  <form method="post" name="subscriptionForm" ng-controller="SubscriptionCtrl" ng-init="init({$id})">
    <div class="page-navbar actions-navbar">
      <div class="navbar navbar-inverse">
        <div class="navbar-inner">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <h4>
                <i class="fa fa-check-square-o"></i>
                {t}Subscriptions{/t}
              </h4>
            </li>
            <li class="quicklinks hidden-xs ng-cloak" ng-if="!flag.loading">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks hidden-xs ng-cloak" ng-if="!flags.loading">
              <h5 class="ng-cloak">
                [% item.pk_user_group ? '{t}Edit subscription{/t}' : '{t}Create subscription{/t}'  %]
              </h5>
            </li>
          </ul>
        </div>
        <div class="all-actions pull-right">
          <ul class="nav quick-section">
            <li class="quicklinks">
              <a class="btn btn-link" href="{url name="backend_subscriptions_list"}" title="{t}Go back{/t}">
                <i class="fa fa-reply"></i>
              </a>
            </li>
            <li class="quicklinks">
              <span class="h-seperate"></span>
            </li>
            <li class="quicklinks">
              <button class="btn btn-primary btn-loading" ng-click="save()">
                <i class="fa fa-save" ng-class="{ 'fa-circle-o-notch fa-spin': flags.saving }"></i>
                <span class="text">{t}Save{/t}</span>
              </button>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="listing-no-contents" ng-hide="!loading">
        <div class="text-center p-b-15 p-t-15">
          <i class="fa fa-4x fa-circle-o-notch fa-spin text-info"></i>
          <h3 class="spinner-text">{t}Loading{/t}...</h3>
        </div>
      </div>
      <div class="ng-cloak" ng-if="!loading">
        <div class="row">
          <div class="col-md-8">
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
          </div>
          <div class="col-md-4">
            <div class="grid simple">
              <div class="grid-title">
                {t}Settings{/t}
              </div>
              <div class="grid-body">
                <div class="form-group">
                  <div class="checkbox">
                    <input class="form-control" id="enabled" name="enabled" ng-model="item.enabled" ng-true-value="1" ng-false-value="0" type="checkbox">
                    <label for="enabled" class="form-label">
                      {t}Enabled{/t}
                    </label>
                  </div>
                  <div class="controls">
                  </div>
                </div>
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
                <h5>{t}Toggle all privileges{/t}</h5>
              </label>
            </div>
            <div class="ng-cloak">
              <div ng-repeat="section in sections">
                <h5>{t}[% section.title %]{/t}</h5>
                <div class="row" ng-repeat="columns in section.rows">
                  <div class="col-sm-3" ng-repeat="name in columns">
                    <div class="col-sm-12 m-b-10">
                      <div class="checkbox check-default check-title">
                        <input id="checkbox-[% name %]" ng-change="selectModule(name)" ng-checked="isModuleSelected(name)" ng-model="selected.all[name]" type="checkbox">
                        <label for="checkbox-[% name %]">
                          <h5>[% name %]</h5>
                        </label>
                      </div>
                    </div>
                    <div class="col-sm-12 m-b-5" ng-repeat="privilege in extra.modules[name]">
                      <div class="checkbox check-default">
                        <input id="checkbox-[% name + '-' + $index %]" checklist-model="user_group.privileges" checklist-value="privilege.id" type="checkbox">
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
    <input type="hidden" name="privileges" ng-value="permissions">
  </form>
{/block}
